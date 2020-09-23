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


abstract class PlanRequirementList extends DatabaseObject {
    /*************************************************************************
     * Static Functions
     ************************************************************************/        
    /**
     * 
     * @return type
     */
    public static function getSortFunction() {
        return function($a, $b) { 
                return strcmp($a->getNumber(), $b->getNumber());
            };        
    }     
            

    /**************************************************************************
     * Member Variables
     **************************************************************************/
    protected $number = null;
    protected $type = null;
    protected $notes = null;
    
    // All child PlanRequirements
    protected $childPRArray = array();
    
    // All child PlanRequirementLists
    protected $childPRListArray = array();           
    

    /**************************************************************************
     * Constructor
     **************************************************************************/
    /**
     * This constructor sets all of the member variables except for the parent
     * ID.
     *
     * @param type $argDBID
     * @param type $argNumber
     * @param type $argType
     * @param type $argNotes
     */
    protected function __construct($argDBID, $argNumber, $argType = null, 
            $argNotes = null) {
        parent::__construct($argDBID);
        
        $this->number = empty($argNumber) ? '' : $argNumber;
        $this->type = $argType;
        $this->notes = $argNotes;
    }
      

    /**************************************************************************
     * Get and Set Methods
     **************************************************************************/
    /** 
     * The get method for the list's number.
     *
     * @return  The string name
     */ 
    public function getNumber() {
        return $this->number;           
    }
    
    /** 
     * The get method for the list's type.
     *
     * @return  The string type
     */ 
    public function getType() {
        return $this->type;           
    }
                
    /** 
     * The get method for the list's notes.
     *
     * @param type $noneOption
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
     * @return type
     */
    public function getTableHeadingID() {
        $dbid = $this->getDBID();
        $thtype = preg_replace('/\s+/', '-', strtolower($this->type));
        
        return "$thtype-$dbid";
    }
    
    /**
     * 
     */
    protected function getAllPlanRequirementsRecursive() {
        $allPRArray = $this->childPRArray;
        
        foreach ($this->childPRListArray as $childPRList) {
            $allPRArray = array_merge($allPRArray, $childPRList->getAllPlanRequirementsRecursive());
        }        
        
        return $allPRArray;        
    }
            
}
