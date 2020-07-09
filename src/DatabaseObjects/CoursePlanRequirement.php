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
        $name = $argArray[CMD::TABLE_CPR_NAME];
        $units = $argArray[CMD::TABLE_CPR_UNITS];
        $connector = $argArray[CMD::TABLE_CPR_CONNECTOR];
        $type = $argArray[CMD::TABLE_CPR_TYPE];
        $text = $argArray[CMD::TABLE_CPR_TEXT];
        $notes = $argArray[CMD::TABLE_CPR_NOTES];

        $cprClass = $argArray[CMD::TABLE_CPR_CLASS];
        
        return new CoursePlanRequirement($id, $name, $units, $connector, $type, $text, $notes);
    }
    
    
    /**************************************************************************
     * Member Variables
     **************************************************************************/
    protected $units = null;
    protected $connector = null;


    /**************************************************************************
     * Constructor
     **************************************************************************/
    /**
     * This constructor sets all of the member variables except for the parent
     * ID, which may be left as null/unset/empty.
     *
     * @param $argDBID         The requirement's database integer ID
     * @param $argName         The requirement's string name
     * @param $argUnits        The requirement's units
     * @param $argConnector    The requirement's connector
     * @param $argType         The requirement's type
     * @param $argText         The requirement's text (default value of null)
     * @param $argNotes        The requirement's notes (default value of null)
     */
    protected function __construct($argDBID, $argName, $argUnits, 
        $argConnector, $argType = null, $argText = null, $argNotes = null) {
        parent::__construct($argDBID, $argName, $argType, $argText, $argNotes);

        $this->units = $argUnits;
        $this->connector = $argConnector;
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
    
}
