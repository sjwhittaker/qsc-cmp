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
 * The class CourseLevelLearningOutcome represents a CLLO as stored in the database.
 */
class CourseLevelLearningOutcome extends LearningOutcome {
    /*************************************************************************
     * Static Functions
     ************************************************************************/
    /**
     * This function uses the values in $_POST following an 'Add' or 'Edit'
     * form submission to create a new CourseLevelLearningOutcome object and 
     * set the values of the member variables.
     *
     * @return      A new CourseLevelLearningOutcome object with those values
     */
    public static function buildFromCLLOPostData() {
        $missingArray = array();

        // These values are required and, if they're not in the form data,
        // an error should be recorded.
        $id = qsc_core_extract_form_value(INPUT_POST, QSC_CMP_FORM_CLLO_ID, FILTER_SANITIZE_NUMBER_INT, "ID", $missingArray);
        $number = qsc_core_extract_form_value(INPUT_POST, QSC_CMP_FORM_CLLO_NUMBER, FILTER_SANITIZE_STRING, "number", $missingArray);
        $text = qsc_core_extract_form_value(INPUT_POST, QSC_CMP_FORM_CLLO_TEXT, FILTER_SANITIZE_STRING, "text", $missingArray);
        $type = qsc_core_extract_form_value(INPUT_POST, QSC_CMP_FORM_CLLO_TYPE, FILTER_SANITIZE_STRING, "type", $missingArray);
        $ioa = qsc_core_extract_form_value(INPUT_POST, QSC_CMP_FORM_CLLO_IOA, FILTER_SANITIZE_STRING, "ioa", $missingArray);

        if (!empty($missingArray)) {
            qsc_core_log_and_display_error("The following information was not received properly from the form: " . join(", ", $missingArray) . ".");
            return null;
        }

        // These values are optional and can be null
        $parentDBID = qsc_core_extract_form_value(INPUT_POST, QSC_CMP_FORM_CLLO_PARENT_SELECT, FILTER_SANITIZE_NUMBER_INT);
        $notes = qsc_core_extract_form_value(INPUT_POST, QSC_CMP_FORM_CLLO_NOTES, FILTER_SANITIZE_STRING);

        return new CourseLevelLearningOutcome($id, $number, $text, $type, $ioa, $notes, $parentDBID);
    }

    /**
     * This function uses the values in a database row to create a new
     * CourseLevelLearningOutcome object and set the values of the member 
     * variables.
     *
     * @param $argArray        The course row from the database
     * @return                 A new CourseLevelLearningOutcome object with 
     *                         those values
     */
     public static function buildFromDBRow($argArray) {
         $id = $argArray[CMD::TABLE_CLLO_ID];
         $number = $argArray[CMD::TABLE_CLLO_NUMBER];
         $text = $argArray[CMD::TABLE_CLLO_TEXT];
         $type = $argArray[CMD::TABLE_CLLO_TYPE];
         $ioa = $argArray[CMD::TABLE_CLLO_IOA];
         $notes = $argArray[CMD::TABLE_CLLO_NOTES];
         $parentDBID = $argArray[CMD::TABLE_CLLO_PARENT_ID];

         return new CourseLevelLearningOutcome($id, $number, $text, $type, $ioa, $notes, $parentDBID);
     }


     /**************************************************************************
     * Member Variables
     **************************************************************************/
     protected $type = null;
     protected $ioa = null;


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
     * @param $argType         The outcome's type
     * @param $argIOA          The outcome's indicator of achievement
     * @param $argNotes        The outcome's notes
     * @param $argParentDBID   The outcome's parent's database ID (default
     *                         value of null)
     */
    protected function __construct($argDBID, $argNumber, $argText, $argType, $argIOA, $argNotes, $argParentDBID = null) {
        parent::__construct($argDBID, $argNumber, $argText, $argNotes, $argParentDBID);
         
        $this->type = $argType;
        $this->ioa = $argIOA;
    }


    /**************************************************************************
     * Get and Set Methods
     **************************************************************************/
    /**
     * The get method for the outcome's type.
     *
     * @return  The string type
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Determines whether this outcome has an indicator of achievement.
     *
     * @return      A boolean representing whether the IOA is empty
     */
    public function hasIOA() {
        return (! empty($this->ioa));
    }

    /**
     * The get method for the outcome's indicator of achievement.
     *
     * @param type $noneOption
     * @return  The string indicator of achievement
     */
    public function getIOA($noneOption = null) {
        return qsc_core_get_none_if_empty($this->ioa, $noneOption);
    }

    /**
     * The get method for the CLLO's name, which includes 'CLLO ' followed by
     * the number.
     *
     * @return  The string name
     */
    public function getName() {
        return "CLLO ".$this->number;
    }

    /**
     * The get method for a text snippet of the CLLO, which includes the name
     * followed by a colon and a snippet of the CLLO's text.
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
     * Creates a link to view this CLLO using its ID.
     *
     * @return      A string containing the link
     */
    public function getLinkToView() {
        return self::getLinkWithID(QSC_CMP_CLLO_VIEW_PAGE_LINK);
    }

    /**
     * Compares this CourseLevelLearningOutcome against an updated version and
     * returns the set of revisions made (<em>i.e.</em>, all changes).
     *
     * @param $updatedCLLO      The updated CourseLevelLearningOutcome object
     * @param $userID           The string ID of the user who made the
     *                          changes
     * @param $dateAndTime      The string date and time that the changes
     *                          were made
     * @return                  An array of Revision objects
     */
    public function getRevisions($updatedCLLO, $userID, $dateAndTime, $tableName = CMD::TABLE_CLLO) {
       // Begin by getting the revisions from the parent object
       $revisionArray = parent::getRevisions($updatedCLLO, $userID, $dateAndTime, $tableName);

       // Go through each member variable and create a revision for each change
       if ($this->type != $updatedCLLO->getType()) {
           $revisionArray[] = new Revision(
               DatabaseObject::NEW_OBJECT_TEMP_ID, $userID,
               CMD::TABLE_CLLO, CMD::TABLE_CLLO_TYPE,
               array(CMD::TABLE_CLLO_ID => $this->getDBID()),
               CMD::TABLE_REVISION_ACTION_EDITED, $this->type,
               $dateAndTime, $updatedCLLO->getType()
           );
       }
       if ($this->ioa != $updatedCLLO->getIOA()) {
           $revisionArray[] = new Revision(
               DatabaseObject::NEW_OBJECT_TEMP_ID, $userID,
               CMD::TABLE_CLLO, CMD::TABLE_CLLO_IOA,
               array(CMD::TABLE_CLLO_ID => $this->getDBID()),
               CMD::TABLE_REVISION_ACTION_EDITED, $this->ioa,
               $dateAndTime, $updatedCLLO->getIOA()
           );
       }

       return $revisionArray;
    }

    /**
     * 
     * @return type
     */
    public function getDeletionRevisionPriorValue() {
        $dataArray = array($this->dbID, $this->number, $this->type, 
            $this->ioa, $this->parentDBID, $this->text, $this->notes);

        $dataString = join(CMD::TABLE_REVISION_ALL_DATA_SEPARATOR, $dataArray);
        return substr($dataString, 0, CMD::TABLE_REVISION_PRIOR_VALUE_MAX_LENGTH);
    }

}
