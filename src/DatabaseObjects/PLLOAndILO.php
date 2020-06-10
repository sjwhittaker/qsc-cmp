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
 * The class PLLOAndILO represents a relationship between a PLLO and ILO as 
 * stored in the database.
 */
class PLLOAndILO extends BiRelationship {
    /**************************************************************************
     * Static Functions
     **************************************************************************/
    /** 
     * This function uses the values in a database row to create a new 
     * PLLOAndILO object and set the values of the member variables.
     *
     * @param $argArray        The course row from the database
     */ 
    public static function buildFromDBRow($argArray) {
        $pllo_id = $argArray[CMD::TABLE_PLLO_AND_ILO_PLLO_ID];
        $ilo_id = $argArray[CMD::TABLE_PLLO_AND_ILO_ILO_ID];         
         
        return new PLLOAndILO($pllo_id, $ilo_id);
    }
          
 
    /**************************************************************************
     * Constructor
     **************************************************************************/
    /** 
     * This constructor sets all of the member variables using the arguments.
     *
     * @param type $argPCMDBID
     * @param type $argICMDBID
     */
    public function __construct($argPCMDBID, $argICMDBID) {
        parent::__construct($argPCMDBID, $argICMDBID);
    }

     
    /**************************************************************************
     * Get and Set Methods
     **************************************************************************/
    /** 
     * The get method for the PLLO's database ID.
     *
     * @return      The string ID
     */ 
    public function getPCMDBID() {
        return $this->firstDBID;   
    } 

    /** 
     * The get method for the ILO's database ID.
     *
     * @return      The string ID
     */ 
    public function getICMDBID() {
        return $this->secondDBID;   
    } 

}