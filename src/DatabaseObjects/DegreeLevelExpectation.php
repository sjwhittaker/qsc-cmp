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
 * The class DegreeLevelExpectation represents a DLE as stored in the database.
 */
class DegreeLevelExpectation extends LearningOutcome {
    /**************************************************************************
     * Static Functions
     **************************************************************************/
    /** 
     * This function uses the values in a database row to create a new 
     * DegreeLevelExpectation object and set the values of the member variables.
     *
     * @param $argArray        The course row from the database
     */ 
     public static function buildFromDBRow($argArray) {
         $id = $argArray[CMD::TABLE_DLE_ID];
         $number = $argArray[CMD::TABLE_DLE_NUMBER];
         $text = $argArray[CMD::TABLE_DLE_TEXT];
         $notes = $argArray[CMD::TABLE_DLE_NOTES];
         $parentDBID = $argArray[CMD::TABLE_DLE_PARENT_ID];
         
         return new DegreeLevelExpectation($id, $number, $text, $notes, $parentDBID);
     }
     
 
    /**************************************************************************
     * Constructor
     **************************************************************************/
    /** 
     * This constructor sets all of the member variables except for the parent
     * ID, which may be left as null/unset/empty.
     *
     * @param $argDBID         The expectation's database integer ID
     * @param $argNumber       The expectation's string 'number'
     * @param $argText         The expectation's text
     * @param $argNotes        The expectation's notes
     * @param $argParentDBID   The expectation's parent's database ID (default
     *                         value of null)
     */ 
     protected function __construct($argDBID, $argNumber, $argText, $argNotes, $argParentDBID = null) {
         parent::__construct($argDBID, $argNumber, $argText, $argNotes, $argParentDBID);
     }

     
    /**************************************************************************
     * Get and Set Methods
     **************************************************************************/
    /** 
     * The get method for the DLE's name, which includes 'DLE ' followed by
     * the number.
     *
     * @return  The string name
     */ 
    public function getName() {
        return "DLE ".$this->number;   
    }   

    /** 
     * The get method for a text snippet of the DLE, which includes the name
     * followed by a colon and a snippet of the DLE's text.
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
     * Creates a link to view this DLE using its ID.
     *
     * @return      A string containing the link
     */
    public function getLinkToView() {
        return self::getLinkWithID(QSC_CMP_DLE_VIEW_PAGE_LINK);
    } 
    
    /**
     * Compares this DegreeLevelExpectation against an updated version and 
     * returns the set of revisions made (<em>i.e.</em>, all changes).
     *
     * @param $updatedPLLO      The updated DegreeLevelExpectation object
     * @param $userID           The string ID of the user who made the
     *                          changes
     * @param $dateAndTime      The string date and time that the changes
     *                          were made
     * @return                  An array of Revision objects
     */
    public function getRevisions($updatedDLE, $userID, $dateAndTime, $tableName = CMD::TABLE_DLE) {
       // DLEs have no additional member variables, so just return the
       // results from the parent object.
       return parent::getRevisions($updatedDLE, $userID, $dateAndTime, $tableName);        
    }      
}
