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

use Managers\CurriculumMappingDatabase as CMD;

/** 
 * The class PlanLevelLearningOutcome represents a PLLO as stored in the database.
 */
class PlanLevelLearningOutcome extends LearningOutcome {
    /**************************************************************************
     * Constants
     **************************************************************************/
    public const DEFAULT_PREFIX = "PLLO";
    

    /**************************************************************************
     * Static Functions
     **************************************************************************/
    /**
     * This function uses the values in $_POST following an 'Add' or 'Edit'
     * form submission to create a new ProgeamLearningOutcome object and set the
     * values of the member variables.
     *
     * @return      A new PlanLevelLearningOutcome object with those values
     */
    public static function buildFromPLLOPostData() {
        $missingArray = array();

        // These values are required and, if they're not in the form data,
        // an error should be recorded.
        $id = qsc_core_extract_form_value(INPUT_POST, QSC_CMP_FORM_PLLO_ID, FILTER_SANITIZE_NUMBER_INT, "ID", $missingArray);
        $number = qsc_core_extract_form_value(INPUT_POST, QSC_CMP_FORM_PLLO_NUMBER, FILTER_SANITIZE_STRING, "number", $missingArray);
        $text = qsc_core_extract_form_value(INPUT_POST, QSC_CMP_FORM_PLLO_TEXT, FILTER_SANITIZE_STRING, "text", $missingArray);

        if (!empty($missingArray)) {
            qsc_core_log_and_display_error("The following information was not received properly from the form: " . join(", ", $missingArray) . ".");
            return null;
        }

        // These values are optional and can be null
        $notes = qsc_core_extract_form_value(INPUT_POST, QSC_CMP_FORM_PLLO_NOTES, FILTER_SANITIZE_STRING);
        $prefix = qsc_core_extract_form_value(INPUT_POST, QSC_CMP_FORM_PLLO_PREFIX, FILTER_SANITIZE_STRING);
        $parentDBID = qsc_core_extract_form_value(INPUT_POST, QSC_CMP_FORM_PLLO_PARENT_PLLO_SELECT, FILTER_SANITIZE_NUMBER_INT);

        return new PlanLevelLearningOutcome($id, $number, $text, $notes, $prefix, $parentDBID);
    }    
    
    /** 
     * This function uses the values in a database row to create a new 
     * PlanLevelLearningOutcome object and set the values of the member variables.
     *
     * @param $argArray        The course row from the database
     */ 
    public static function buildFromDBRow($argArray) {
        $id = $argArray[CMD::TABLE_PLLO_ID];
        $number = $argArray[CMD::TABLE_PLLO_NUMBER];
        $text = $argArray[CMD::TABLE_PLLO_TEXT];
        $notes = $argArray[CMD::TABLE_PLLO_NOTES];
        $prefix = $argArray[CMD::TABLE_PLLO_PREFIX];
        $parentDBID = $argArray[CMD::TABLE_PLLO_PARENT_ID];
        
        return new PlanLevelLearningOutcome($id, $number, $text, $notes, $prefix, $parentDBID);
    }

    /**
     * 
     * @return boolean
     */
    public static function sortAfterInitialization() {
        return true;
    }
    
    /**
     * 
     * @return type
     */
    public static function getSortFunction() {
        return function($a, $b) {
            $aNumber = $a->getNumber();
            $bNumber = $b->getNumber();
            
            $numCmp = strcmp($aNumber, $bNumber);
            
            return ($numCmp == 0) ?
                strcmp($a->getPrefix(), $b->getPrefix()) :
                $numCmp;
        };        
    }      
    
    
    /**************************************************************************
     * Member Variables
     **************************************************************************/
    protected $prefix = null;
    protected $hasPrefix = false;
     

    /**************************************************************************
     * Constructor
     **************************************************************************/
    /** 
     * This constructor sets all of the member variables except for the parent
     * ID, which may be left as null/unset/empty.
     *
     * @param $argDBID         The outcome's database integer ID
     * @param $argNumber       The outcome's string 'number'
     * @param $argText         The outcome's text
     * @param $argNotes        The outcome's notes
     * @param $argPrefix       The outcome's prefix (default
     *                         value of null)
     * @param $argParentDBID   The outcome's parent's database ID (default
     *                         value of null)
     */ 
    protected function __construct($argDBID, $argNumber, $argText, $argNotes, $argPrefix = null, $argParentDBID = null) {
        parent::__construct($argDBID, $argNumber, $argText, $argNotes, $argParentDBID);
        
        $this->prefix = $argPrefix;
    }
    
    
    /*************************************************************************
     * Initialize
     *************************************************************************/
    /**
     * Initializes the member variables that can't be set by a single DB row;
     * in this case, the prefix.
     * 
     * @param type $dbCurriculum
     */
    public function initialize($dbCurriculum, $argArray = array()) {
        // If a unique prefix was defined for a PLLO in its row in the DB,
        // use it.
        if ($this->prefix) {
            $this->hasPrefix = true;
            return;
        }
        
        // Otherwise, try to create a prefix from its plan codes if there 
        // are only 1 or 2
        $planArray = $dbCurriculum->getPlansFromPLLO($this->getDBID());
        $numberOfPlans = count($planArray);
        if ($numberOfPlans && ($numberOfPlans < 3)) {
            $planCodeArray = qsc_core_map_member_function($planArray, 'getCode');
            $this->prefix = join(QSC_CMP_PLLO_PREFIX_DELIMETER, $planCodeArray);
            return;
        }
        
        // If that fails, see if any administering department has a code 
        // defined.
        $departmentCodeArray = $dbCurriculum->getAdministrativeDepartmentCodesForPLLO($this->getDBID());
        $numberOfCodes = count($departmentCodeArray);
        if ($numberOfCodes && ($numberOfCodes < 3)) {
            $this->prefix = join(QSC_CMP_PLLO_PREFIX_DELIMETER, $departmentCodeArray);
            return;
        }
        
        // If all else fails, use the default
        $this->prefix = self::DEFAULT_PREFIX;
    }    

     
    /**************************************************************************
     * Get and Set Methods
     **************************************************************************/
    /**
     * 
     * @return type
     */
    public function hasCustomPrefix() {
        return $this->hasPrefix;
    }
    
    /**
     * 
     * @return type
     */
    public function getPrefix() {
        return $this->prefix;   
    }     
    
    /** 
     * The get method for the PLLO's name, which includes 'PLLO ' followed by
     * the number.
     *
     * @return  The string name
     */ 
    public function getName() {
        return $this->prefix." ".$this->number;   
    }   

    /** 
     * The get method for a text snippet of the PLLO, which includes the name
     * followed by a colon and a snippet of the PLLO's text.
     *
     * @return  The string snippet 
     */ 
    public function getShortSnippet() {
        return $this->getName().": ".qsc_core_get_snippet($this->text, QSC_CORE_STRING_SNIPPET_LENGTH_SHORT);   
    }   
       
     
    /**************************************************************************
     * Member Functions
     **************************************************************************/
    /**
     * Creates a link to view this PLLO using its ID.
     *
     * @return      A string containing the link
     */
    public function getLinkToView() {
        return self::getLinkWithID(QSC_CMP_PLLO_VIEW_PAGE_LINK);
    }
        
    /**
     * Compares this PlanLevelLearningOutcome against an updated version and 
     * returns the set of revisions made (<em>i.e.</em>, all changes).
     *
     * @param $updatedPLLO      The updated PlanLevelLearningOutcome object
     * @param $userID           The string ID of the user who made the
     *                          changes
     * @param $dateAndTime      The string date and time that the changes
     *                          were made
     * @param type $tableName
     * @return                  An array of Revision objects
     */
    public function getRevisions($updatedPLLO, $userID, $dateAndTime, $tableName = CMD::TABLE_PLLO) {
       $revisionArray = parent::getRevisions($updatedPLLO, $userID, $dateAndTime, $tableName);        

       // Go through each member variable and create a revision for each change
       $thisPrefixValue = $this->hasCustomPrefix() ? $this->getPrefix() : null;
       $updatedPLLOPrefixValue = $updatedPLLO->hasCustomPrefix() ? $updatedPLLO->getPrefix() : null;
       
       if ($thisPrefixValue != $updatedPLLOPrefixValue) {
           $revisionArray[] = new Revision(
               DatabaseObject::NEW_OBJECT_TEMP_ID, $userID,
               CMD::TABLE_PLLO, CMD::TABLE_PLLO_PREFIX,
               array(CMD::TABLE_PLLO_ID => $this->getDBID()),
               CMD::TABLE_REVISION_ACTION_EDITED, $thisPrefixValue,
               $dateAndTime, $updatedPLLOPrefixValue
           );
       }
       
       return $revisionArray;       
    }
    
    /**
     * 
     * @return type
     */
    public function getDeletionRevisionPriorValue() {
        $dataArray = array($this->dbID, $this->number, $this->parentDBID, $this->text, $this->notes);

        $dataString = join(CMD::TABLE_REVISION_ALL_DATA_SEPARATOR, $dataArray);
        return substr($dataString, 0, CMD::TABLE_REVISION_PRIOR_VALUE_MAX_LENGTH);
    }    
    
}