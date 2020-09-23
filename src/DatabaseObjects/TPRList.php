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


class TPRList extends PlanRequirementList {
    /*************************************************************************
     * Static Functions
     ************************************************************************/
    /**
     * This function checks the class in the database row and calls the
     * corresponding function in the correct child class to create the 
     * TPRList object.
     * 
     * NOTE: this only handles a row from the 'tprlist' table; it doesn't
     * initialize the particular class of TPRList.
     *
     * @param $argArray        The tpr row from the database
     * @return                 A new TPRList object with those values
     */
    public static function buildFromDBRow($argArray) {
        $id = $argArray[CMD::TABLE_TPRLIST_ID];
        $number = $argArray[CMD::TABLE_TPRLIST_NUMBER];
        $type = $argArray[CMD::TABLE_TPRLIST_TYPE];
        $notes = $argArray[CMD::TABLE_TPRLIST_NOTES];

        return new TPRList($id, $number, $type, $notes);
    }
        

    /**************************************************************************
     * Member Variables
     **************************************************************************/
    

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
        // Get the TPRs that are directly associated with this list
        $this->childPRArray = $dbCurriculum->getChildTPRsForTPRList($this->dbID);
        
        // Fetch and initialize the child TPR lists
        $this->childPRListArray = $dbCurriculum->getChildTPRListsForTPRList($this->dbID);        
    }
       

    /**************************************************************************
     * Get and Set Methods
     **************************************************************************/    
    /**
     * 
     * @return type
     */
    public function getChildTPRArray() {
        return $this->childPRArray;
    }
    
    /**
     * 
     * @return type
     */
    public function getChildTPRListArray() {
        return $this->childPRListArray;
    }

    
    /**************************************************************************
     * Member Functions
     **************************************************************************/
   
        
}
