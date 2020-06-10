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
 * The abstract class LearningOutcome contains elements and functionality 
 * common to all learning outcomes.
 */
abstract class LearningOutcome extends DatabaseObject {
    /**************************************************************************
     * Member Variables
     **************************************************************************/
    protected $number = null;
    protected $text = null;
    protected $notes = null;
    protected $parentDBID = null;
    

    /**************************************************************************
     * Abstract Methods
     **************************************************************************/
    abstract protected function getName();
    abstract protected function getLinkToView();     
     
 
    /**************************************************************************
     * Constructor
     **************************************************************************/
    /** 
     * This constructor sets all of the member variables except for the parent
     * ID, which may be left as null/unset/empty.
     *
     * @param $argDBID         The outcome's database integer ID
     * @param $argNumber       The outcome's string 'number'
     * @param $argText         The outcome's text description
     * @param $argNotes        The outcome's notes
     * @param $argParentDBID   The outcome's parent's database ID (default
     *                         value of null)
     */ 
    protected function __construct($argDBID, $argNumber, $argText, $argNotes, $argParentDBID = null) {
        parent::__construct($argDBID);

        $this->number = $argNumber;
        $this->text = $argText;
        $this->notes = $argNotes;
        if ($argParentDBID !== null) {
            $this->parentDBID = $argParentDBID;
        }
    }

     
    /**************************************************************************
     * Get and Set Methods
     **************************************************************************/
    /** 
     * The get method for the outcome's number.
     *
     * @return  The string 'number'
     */ 
    public function getNumber() {
        return $this->number;   
    } 
     
    /** 
     * The get method for the outcome's text.
     *
     * @return  The string text
     */ 
    public function getText() {
        return $this->text;   
    } 

    /** 
     * The get method for the outcome's notes.
     *
     * @param type $noneOption
     * @return  The string notes
     */
    public function getNotes($noneOption = null) {
        return self::getNoneIfEmpty($this->notes, $noneOption);   
    } 
     
    /** 
     * The get method for the outcome's parent's ID in the database.
     *
     * @return  The numeric database ID (may be null)
     */ 
    public function getParentDBID() {
        return $this->parentDBID;   
    }
    
          
    /**************************************************************************
     * Member Functions
     **************************************************************************/
    /** 
     * Determines whether this outcome has a parent.
     *
     * @return      A boolean representing whether the parent ID isn't null
     */ 
    public function hasParent() {
        return (! empty($this->parentDBID));   
    }
     
    /** 
     * Determines whether this outcome doesn't have a parent.
     *
     * @return      A boolean representing whether the parent ID is null
     */ 
    public function isTopLevel() {
        return (! $this->hasParent());   
    } 
     
    /** 
     * Determines whether this outcome has notes.
     *
     * @return      A boolean representing whether the notes are empty
     */ 
    public function hasNotes() {
        return (! empty($this->notes));   
    }
     
    /**
     * Compare this LearningOutcome against an updated version and returns
     * the set of revisions made (<em>i.e.</em>, all changes).
     *
     * @param $updatedLO        The updated LearningOutcome object
     * @param $userID           The string ID of the user who made the
     *                          changes
     * @param $dateAndTime      The string date and time that the changes
     *                          were made
     * @param type $tableName
     * @return                  An array of Revision objects
     */
    public function getRevisions($updatedLO, $userID, $dateAndTime, $tableName) {
       $revisionArray = array();
      
       // Go through each member variable and create a revision for each change
       if ($this->number != $updatedLO->getNumber()) {
           $revisionArray[] = new Revision(
               DatabaseObject::NEW_OBJECT_TEMP_ID, $userID,
               $tableName, CMD::COLUMN_LO_NUMBER, 
               array(CMD::COLUMN_LO_ID => $this->getDBID()),
               CMD::TABLE_REVISION_ACTION_EDITED, $this->number, 
               $dateAndTime, $updatedLO->getNumber() 
           );
       }
       if ($this->text != $updatedLO->getText()) {
           $revisionArray[] = new Revision(
               DatabaseObject::NEW_OBJECT_TEMP_ID, $userID,
               $tableName, CMD::COLUMN_LO_TEXT,
               array(CMD::COLUMN_LO_ID => $this->getDBID()),
               CMD::TABLE_REVISION_ACTION_EDITED, $this->text,
               $dateAndTime, $updatedLO->getText()
           );
       }       
       if ($this->notes != $updatedLO->getNotes()) {
           $revisionArray[] = new Revision(
               DatabaseObject::NEW_OBJECT_TEMP_ID, $userID,
               $tableName, CMD::COLUMN_LO_NOTES,
               array(CMD::COLUMN_LO_ID => $this->getDBID()),
               CMD::TABLE_REVISION_ACTION_EDITED, $this->notes,
               $dateAndTime, $updatedLO->getNotes()
           );
       }
       if ($this->parentDBID != $updatedLO->getParentDBID()) {
           $revisionArray[] = new Revision(
               DatabaseObject::NEW_OBJECT_TEMP_ID, $userID,
               $tableName, CMD::COLUMN_LO_PARENT_ID,
               array(CMD::COLUMN_LO_ID => $this->getDBID()),
               CMD::TABLE_REVISION_ACTION_EDITED, $this->parentDBID,
               $dateAndTime, $updatedLO->getParentDBID()
           );
       }
       
       return $revisionArray;
    }
    
    /**
     * 
     * @param type $withText
     * @return type
     */
    public function getAnchorToView($withText = false) {
        return '<a href="'.$this->getLinkToView().'">'.$this->getName().'</a>'.($withText ? ': '.$this->text : '');
    }
}