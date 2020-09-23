<?php
namespace DatabaseObjects;

/****************************************************************************
 * Copyright (C) 2020, Sarah-Jane Whittaker (sarah@cs.queensu.ca)
 *
 * This program is free software: you can redistribute it and/or modify it 
 * under the terms of the GNU Affero General Public License as published by 
 * the Free Software Foundation, either version 3 of the License, or (at 
 * your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU Affero 
 * General Public License for more details.
 * 
 * You should have received a copy of the GNU Affero General Public License 
 * along with this program in the file gnu-agpl-3.0.txt. If not, 
 * see <https://www.gnu.org/licenses/>.
 * 
 * This program was developed with the input and support of the Queen's
 * University School of Computing <https://www.cs.queensu.ca/>, Faculty
 * of Arts and Science <https://www.queensu.ca/artsci/> and the Office of 
 * the Vice-Provost (Teaching and Learning) 
 * <https://www.queensu.ca/provost/teaching-and-learning>.
 ***************************************************************************/

use Managers\SubsetManager;
use Managers\CurriculumMappingDatabase as CMD;

use \NumberFormatter;


/**
 * The class CoursePlanRequirement represents a CPR as stored in the database.
 */
class CoursePlanRequirement extends PlanRequirement {
    /*************************************************************************
     * Static Functions
     ************************************************************************/
    /**
     * This function uses the values in a database row to create a new
     * CoursePlanRequirement object and set the values of the member variables.
     *
     * @param $argArray        The course row from the database
     * @return                 A new CoursePlanRequirement object with those
     *                         values
     */
    public static function buildFromDBRow($argArray) {       
        $id = $argArray[CMD::TABLE_CPR_ID];
        $number = $argArray[CMD::TABLE_CPR_NUMBER];
        $units = $argArray[CMD::TABLE_CPR_UNITS];
        $connector = $argArray[CMD::TABLE_CPR_CONNECTOR];
        $text = $argArray[CMD::TABLE_CPR_TEXT];
        $notes = $argArray[CMD::TABLE_CPR_NOTES];

        return new CoursePlanRequirement($id, $number, $units, $connector, $text, $notes);
    }
    
    
    /**************************************************************************
     * Member Variables
     **************************************************************************/
    protected $units = null;
    protected $connector = null;
    
    protected $childCPRListArray = array();    


    /**************************************************************************
     * Constructor
     **************************************************************************/
    /**
     * This constructor sets all of the member variables except for the parent
     * ID, which may be left as null/unset/empty.
     *
     * @param $argDBID         The requirement's database integer ID
     * @param $argNumber       The requirement's string number
     * @param $argUnits        The requirement's units
     * @param $argConnector    The requirement's connector
     * @param $argText         The requirement's text (default value of null)
     * @param $argNotes        The requirement's notes (default value of null)
     */
    protected function __construct($argDBID, $argNumber, $argUnits, 
        $argConnector, $argText = null, $argNotes = null) {
        parent::__construct($argDBID, $argNumber, $argText, $argNotes);

        $this->units = $argUnits;
        $this->connector = $argConnector;
    }
    
    
    /*************************************************************************
     * Initialize
     *************************************************************************/
    /**
     * Initializes the member variables that can't be set by a single DB row.
     * 
     * @param type $dbCurriculum
     */
    public function initialize($dbCurriculum, $argArray = array()) {
        // Fetch and initialize the child CPR lists
        $this->childPRListArray = $dbCurriculum->getChildCPRListsForCPR($this->dbID);        
    }    


    /**************************************************************************
     * Get and Set Methods
     **************************************************************************/
    /**
     * The get method for the requirement's units.
     *
     * @return  The decimal units
     */
    public function getUnits() {
        return $this->units;
    }

    /**
     * The get method for the requirement's connector.
     *
     * @return  The string connector
     */
    public function getConnector() {
        return $this->connector;
    }
    
    /** 
     * The get method for the requirement's text. This overrides the parent
     * which does not assume a possible null value.
     *
     * @param type $noneOption
     * @return  The string text
     */
    public function getText($noneOption = null) {
       return qsc_core_get_none_if_empty($this->text, $noneOption);   
    }

    /**
     * 
     * @return type
     */
    public function getChildCPRListArray() {
        return $this->childPRListArray;
    }    


    /**************************************************************************
     * Member Functions
     **************************************************************************/
    /**
     * Creates a link to view this CPR using its ID.
     *
     * @return      A string containing the link
     */
    public function getLinkToView() {
        return self::getLinkWithID(QSC_CMP_CPR_VIEW_PAGE_LINK);
    }
    
    /**
     * 
     * @param type $db_curriculum
     * @return type
     */
    public function getRequiredCourses($db_curriculum) {
        // Get the course list
        $courselist = $db_curriculum->getCourseListForCPR($this->getDBID());
        if (! $courselist) {
            return array();
        }
        
        // Check the number of subsets in the courselist - if there's
        // only 1, all courses therein are required
        $courseSubsetManager = $courselist->getAllCourseSubsets(floatval($this->units));
        $courseSubsetArray = $courseSubsetManager->getSubsetArray();
        if ((! $courseSubsetManager->maximumRecursiveCallsMade()) && (count($courseSubsetArray) == 1)) {
            return $courseSubsetArray[0];
        }
        
        // The other option is the the courselist has an 'and' Relationship
        // with some direct courses
        if (($courselist instanceof RelationshipCourseList) && ($courselist->isAnd())) {
            return qsc_core_clone_array($courselist->getChildCourseArray());
        }
                        
        return array();
    }
    
    /**
     * 
     * @return type
     */
    public function hasSubLists() {
        return (! empty($this->childPRListArray));
    }
    
    /**
     * 
     * @return type
     */
    public function getUnitsToDisplay() {
        return number_format($this->units, 1);
    }
    
    /**
     * 
     * @return array
     */
    public function getSubListNames($parentCPRListNumber) {
        $subListNameArray = array();
        
        $prefix = "$parentCPRListNumber.";
        $prefix .= $this->number.'.';
        foreach ($this->childPRListArray as $childCPRList) {
            $subListName = $prefix.$childCPRList->getNumber().'.';
            
            $subListNameArray[] = $subListName;
        }
                       
        return $subListNameArray;
    }
    
    /**
     * 
     * @param type $parentCPRListNumber
     * @return type
     */
    public function getSubListNamesHTML($parentCPRListNumber) {
        $listNameArray = $this->getSubListNames($parentCPRListNumber);
        $listNameArray = array_map(function($a) {
            return '<span class="sub-list-name">' . $a . '</span>';
        }, $listNameArray);
        
        return join($listNameArray, '; ');
    }
    
    /**
     * 
     */
    protected function getSubListsRequired($getMin = true) {
        if (! $this->hasSubLists()) {
            return false;
        }
        
        $numRequired = 0;
        $cprUnits = $this->units;
        $numSubLists = count($this->childPRListArray);
        
        // Get all of the units from the sub-lists and sort
        $totalUnitsArray = qsc_core_map_member_function(
            $this->childPRListArray, 'getTotalUnits');
        if ($getMin) {
            rsort($totalUnitsArray);
        }
        else {
            sort($totalUnitsArray);            
        }
                       
        // Retrieve the maximum 
        while (($numRequired < $numSubLists) && ($cprUnits > 0)) {
            $cprUnits -= $totalUnitsArray[$numRequired];
            $numRequired++;
        }

        return ($cprUnits > 0) ? false : $numRequired;
    }    
    
    /**
     * 
     * @return type
     */
    public function getMinSubListsRequired() {
        return $this->getSubListsRequired(true);
    }

    /**
     * 
     * @return type
     */
    public function getMaxSubListsRequired() {
        return $this->getSubListsRequired(false);
    }
    
    /**
     * 
     * @return string
     */
    public function getSubListsRequiredHTML($capitalizeFirst = false) {
        $requiredHTML = '';
        
        $minRequired = $this->getMinSubListsRequired();
        $maxRequired = $this->getMaxSubListsRequired();
        
        $numberFormatter = new NumberFormatter(
            locale_get_display_language(null), NumberFormatter::SPELLOUT);
        $requiredHTML = ($minRequired == $maxRequired) ?
            $numberFormatter->format($minRequired) :
            $numberFormatter->format($minRequired).' - '. $numberFormatter->format($maxRequired);
        
        if ($capitalizeFirst) {
            $requiredHTML = ucfirst($requiredHTML);
        }

        return '<span class="sub-list-num-required">'.$requiredHTML.'</span>';
    }
    
}
