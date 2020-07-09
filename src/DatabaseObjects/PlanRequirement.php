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
 * The abstract class PlanRequirement contains elements and functionality 
 * common to all plan requirements.
 */
abstract class PlanRequirement extends DatabaseObject {
    /**************************************************************************
     * Member Variables
     **************************************************************************/
    protected $name = null;
    protected $type = null;
    protected $text = null;
    protected $notes = null;    
    

    /**************************************************************************
     * Abstract Methods
     **************************************************************************/
    abstract protected function getLinkToView();     
     
 
    /**************************************************************************
     * Constructor
     **************************************************************************/
    /** 
     * This constructor sets all of the member variables except for the parent
     * ID, which may be left as null/unset/empty.
     *
     * @param $argDBID         The requirement's database integer ID
     * @param $argName         The requirement's string 'name'
     * @param $argType         The requirement's type
     * @param $argText         The requirement's text description
     * @param $argNotes        The outcome's notes (default value of null)
     */ 
    protected function __construct($argDBID, $argName, $argType, $argText, $argNotes = null) {
        parent::__construct($argDBID);

        $this->name = $argName;
        $this->type = $argType;
        $this->text = $argText;
        $this->notes = $argNotes;        
    }

     
    /**************************************************************************
     * Get and Set Methods
     **************************************************************************/
    /** 
     * The get method for the requirement's name.
     *
     * @return  The string 'name'
     */ 
    public function getName() {
        return $this->name;
    }

    /** 
     * The get method for the requirement's type.
     *
     * @return  The string type
     */ 
    public function getType() {
        return $this->type;
    }

    /** 
     * The get method for the requirement's text.
     *
     * @return  The string text
     */ 
    public function getText() {
        return $this->text;   
    }
    
    /** 
     * The get method for the requirement's notes.
     *
     * @return  The string notes
     */ 
    public function getNotes($noneOption = null) {
       return qsc_core_get_none_if_empty($this->notes, $noneOption);   
    }     
     
          
    /**************************************************************************
     * Member Functions
     **************************************************************************/
    /**
     * 
     * @param type $withText
     * @return type
     */
    public function getAnchorToView($withText = false) {
        return '<a href="'.$this->getLinkToView().'">'.$this->getName().'</a>'.($withText ? ': '.$this->text : '');
    }
    
}