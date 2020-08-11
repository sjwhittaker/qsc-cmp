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
 * The class PLLOAndPlan represents a relationship between a plan and a PLLO as 
 * stored in the database.
 */
class PlanAndPLLO extends BiRelationship {
    /**************************************************************************
     * Static Functions
     **************************************************************************/
    /** 
     * This function uses the values in a database row to create a new 
     * PLLOAndPlan object and set the values of the member variables.
     *
     * @param $argArray        The course row from the database
     */ 
     public static function buildFromDBRow($argArray) {
         $plan_id = $argArray[CMD::TABLE_PLAN_AND_PLLO_PLAN_ID];         
         $pllo_id = $argArray[CMD::TABLE_PLAN_AND_PLLO_PLLO_ID];
         
         return new PlanAndPLLO($plan_id, $pllo_id);
     }
          
 
    /**************************************************************************
     * Constructor
     **************************************************************************/
    /** 
     * This constructor sets all of the member variables using the arguments.
     *
     * @param type $argPlanDBID
     * @param type $argPLLODBID
     */
    public function __construct($argPlanDBID, $argPLLODBID) {
        parent::__construct($argPlanDBID, $argPLLODBID);
    }

     
    /**************************************************************************
     * Get and Set Methods
     **************************************************************************/
    /** 
     * The get method for the plan's database ID.
     *
     * @return      The string ID
     */ 
    public function getPlanDBID() {
        return $this->firstDBID;   
    } 

    /** 
     * The get method for the PLLO's database ID.
     *
     * @return      The string ID
     */ 
    public function getPLLODBID() {
        return $this->secondDBID;   
    } 

}