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
 * The class CLLOLevel is intended to store the information from a row in the
 * database about a possible level/category for a CLLO.
 */
class CLLOLevel extends DatabaseObject {
    /**************************************************************************
     * Static Functions
     **************************************************************************/
    /** 
     * This function uses the values in a database row to create a new 
     * User object and set the values of the member variables.
     *
     * @param $argArray        The course row from the database
     */ 
    public static function buildFromDBRow($argArray) {
        $id = $argArray[CMD::TABLE_CLLOLEVEL_ID];
        $name = $argArray[CMD::TABLE_CLLOLEVEL_NAME];
        $acronym = $argArray[CMD::TABLE_CLLOLEVEL_ACRONYM];
        $rank = $argArray[CMD::TABLE_CLLOLEVEL_RANK];
         
        return new CLLOLevel($id, $name, $acronym, $rank);
    }
    
    /**
     * 
     * @return type
     */
    public static function getSortFunction() {
        return function($a, $b) { 
                return $a->getRank() < $b->getRank();
            };        
    }    
    
     
    /**************************************************************************
     * Member Variables
     **************************************************************************/
    protected $name = null;
    protected $acronym = null;
    protected $rank = null;
     
 
    /**************************************************************************
     * Constructor
     **************************************************************************/
    /** 
     * This constructor sets all of the member variables using the arguments.
     *
     * @param type $argID
     * @param type $argName
     * @param type $argRank
     */ 
    public function __construct($argID, $argName, $argAcronym, $argRank) {
        parent::__construct($argID);
         
        $this->name = $argName;
        $this->acronym = $argAcronym;
        $this->rank = $argRank;
    }

     
    /**************************************************************************
     * Get and Set Methods
     **************************************************************************/
    /** 
     * The get method for the level's name.
     *
     * @return  The string name
     */ 
    public function getName() {
        return $this->name;   
    } 
    
    /** 
     * The get method for the level's acronym.
     *
     * @return  The string acronym
     */ 
    public function getAcronym() {
        return $this->acronym;   
    }    

    /** 
     * The get method for the level's rank.
     *
     * @return  The int rank
     */ 
    public function getRank() {
        return $this->rank;   
    }                     
}