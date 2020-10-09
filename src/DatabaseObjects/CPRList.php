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


class CPRList extends PlanRequirementList {
    /*************************************************************************
     * Static Functions
     ************************************************************************/
    /**
     * This function checks the class in the database row and calls the
     * corresponding function in the correct child class to create the 
     * CPRList object.
     * 
     * NOTE: this only handles a row from the 'cprlist' table; it doesn't
     * initialize the particular class of CPRList.
     *
     * @param $argArray        The cpr row from the database
     * @return                 A new CPRList object with those values
     */
    public static function buildFromDBRow($argArray) {
        $id = $argArray[CMD::TABLE_CPRLIST_ID];
        $number = $argArray[CMD::TABLE_CPRLIST_NUMBER];
        $type = $argArray[CMD::TABLE_CPRLIST_TYPE];
        $notes = $argArray[CMD::TABLE_CPRLIST_NOTES];

        return new CPRList($id, $number, $type, $notes);
    }
        

    /**************************************************************************
     * Member Variables
     **************************************************************************/
    protected $subPlanArray = array();
    

    /**************************************************************************
     * Constructor
     **************************************************************************/


    /*************************************************************************
     * Initialize
     *************************************************************************/
    /**
     * Initializes the member variables that can't be set by a single DB row.
     * 
     * @param type $dbCurriculum
     */
    public function initialize($dbCurriculum, $argArray = array()) {
        // Get the CPRs that are directly associated with this list
        $this->childPRArray = $dbCurriculum->getChildCPRsForCPRList($this->dbID);
                
        // Fetch and initialize the child CPR lists
        $this->childPRListArray = $dbCurriculum->getChildCPRListsForCPRList($this->dbID);        
        
        if ($this->hasSubPlans()) {
            $this->subPlanArray = $dbCurriculum->getChildPlansInCPRList($this->dbID);
        }
    }
       

    /**************************************************************************
     * Get and Set Methods
     **************************************************************************/    
    /**
     * 
     * @return type
     */
    public function getChildCPRArray() {
        return $this->childPRArray;
    }
    
    /**
     * 
     * @return type
     */
    public function getChildCPRListArray() {
        return $this->childPRListArray;
    }
    
    /**
     * 
     * @return type
     */
    public function getSubPlanArray() {
        return $this->subPlanArray;
    }    

    
    /**************************************************************************
     * Member Functions
     **************************************************************************/
    /**
     * 
     * @return type
     */
    public function getTotalUnits() {
        // Go through each child CPR and add up the units
        $totalUnits = array_sum(
            qsc_core_map_member_function($this->childPRArray, 'getUnits')); 
        
        // Do the same for child CPRLists
        $totalUnits += array_sum(
            qsc_core_map_member_function($this->childPRListArray, 'getTotalUnits')); 
                
        return $totalUnits;
    }
    
    /**
     * 
     * @return type
     */
    public function getTotalUnitsToDisplay() {
        return number_format($this->getTotalUnits(), 1);
    }    
    
    /**
     * 
     * @return boolean
     */
    public function hasSubPlans() {
        // Test this list
        return ($this->type == QSC_CMP_CPRLIST_TYPE_SUB_PLANS);
    }
    
    /**
     * 
     * @return type
     */
    public function getAllCPRsRecursive() {
        return $this->getAllPlanRequirementsRecursive();
    }
    
    /**
     * 
     * @return boolean
     */
    public function hasCPRWithSubLists() {
        // Check the child CPRs first
        foreach ($this->childPRArray as $childPR) {
            if ($childPR->hasSubLists()) {
                return true;
            }
        }
        
        // Now check the child CPRLists
        foreach ($this->childPRListArray as $childPRList) {
            if ($childPRList->hasCPRWithSubLists()) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * 
     */
    public function hasChildCPRLists() {
        return (! empty($this->childPRListArray));
    }
    
               
}
