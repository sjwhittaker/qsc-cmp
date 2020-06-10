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
 * The class InstitutionLearningOutcome represents a ILO as stored in the 
 * database.
 */
class InstitutionLearningOutcome extends LearningOutcome {
    /**************************************************************************
     * Static Functions
     **************************************************************************/
    /** 
     * This function uses the values in a database row to create a new 
     * InstitutionLearningOutcome object and set the values of the member
     * variables.
     *
     * @param $argArray        The course row from the database
     */ 
    public static function buildFromDBRow($argArray) {
        $id = $argArray[CMD::TABLE_ILO_ID];
        $number = $argArray[CMD::TABLE_ILO_NUMBER];
        $text = $argArray[CMD::TABLE_ILO_TEXT];
        $description = $argArray[CMD::TABLE_ILO_DESCRIPTION];
        $notes = $argArray[CMD::TABLE_ILO_NOTES];
        $parentDBID = $argArray[CMD::TABLE_ILO_PARENT_ID];
         
        return new InstitutionLearningOutcome($id, $number, $text, $description, $notes, $parentDBID);
    }
     
     
    /**************************************************************************
     * Member Variables
     **************************************************************************/
    protected $description = null;
     
 
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
     * @param $argDescription  The outcome's description
     * @param $argNotes        The outcome's notes
     * @param $argParentDBID   The outcome's parent's database ID (default
     *                         value of null)
     */ 
    protected function __construct($argDBID, $argNumber, $argText, 
        $argDescription, $argNotes, $argParentDBID = null) {
        parent::__construct($argDBID, $argNumber, $argText, $argNotes, $argParentDBID);
        $this->description = $argDescription;
    }

     
    /**************************************************************************
     * Get and Set Methods
     **************************************************************************/
    /** 
     * The get method for the ILO's description.
     *
     * @return  The string type
     */ 
    public function getDescription($noneOption = null) {
        return self::getNoneIfEmpty($this->description, $noneOption);   
    } 
          
    /** 
     * The get method for the ILO's name, which includes 'ILO ' followed by
     * the number.
     *
     * @return  The string name
     */ 
    public function getName() {
        return "ILO ".$this->number;   
    }
     
    /** 
     * The get method for a text snippet of the ILO, which includes the name
     * followed by a colon and a snippet of the ILO's text.
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
     * Determines whether this ILO has a description.
     *
     * @return      A boolean representing whether a description exists
     */
    public function hasDescription() {
        return (! empty($this->description));    
    }
     
    /**
     * Creates a link to view this ILO using its ID.
     *
     * @return      A string containing the link
     */
    public function getLinkToView() {
        return self::getLinkWithID(QSC_CMP_ILO_VIEW_PAGE_LINK);
    }
    
    /**
     * Compares this InstitutionLearningOutcome against an updated version and
     * returns the set of revisions made (<em>i.e.</em>, all changes).
     *
     * @param $updatedILO       The updated InstitutionLearningOutcome object
     * @param $userID           The string ID of the user who made the
     *                          changes
     * @param $dateAndTime      The string date and time that the changes
     *                          were made
     * @param type $tableName
     * @return                  An array of Revision objects
     */
    public function getRevisions($updatedILO, $userID, $dateAndTime, 
        $tableName = CMD::TABLE_ILO) {
       // Begin by getting the revisions from the parent object
       $revisionArray = parent::getRevisions($updatedILO, $userID, $dateAndTime, $tableName);
        
       // Go through each member variable and create a revision for each change
       if ($this->description != $updatedILO->getDescription()) {
           $revisionArray[] = new Revision(
               DatabaseObject::NEW_OBJECT_TEMP_ID, $userID,
               CMD::TABLE_ILO, CMD::TABLE_ILO_DESCRIPTION,
               array(CMD::TABLE_ILO_ID => $this->getDBID()),
               CMD::TABLE_REVISION_ACTION_EDITED, $this->description,
               $dateAndTime, $updatedILO->getDescription()
           );
       } 
       
       return $revisionArray;
    }      
    
}