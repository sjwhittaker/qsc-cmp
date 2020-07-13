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
 * The class User is intended to store the information from a row in the
 * database about a user.
 */
class User extends DatabaseObject {
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
        $id = $argArray[CMD::TABLE_USER_ID];
        $firstName = $argArray[CMD::TABLE_USER_FIRST_NAME];
        $lastName = $argArray[CMD::TABLE_USER_LAST_NAME];
        $role = $argArray[CMD::TABLE_USER_ROLE];
        $active = $argArray[CMD::TABLE_USER_ACTIVE];
         
        return new User($id, $firstName, $lastName, $role, $active);
    }
    
    /**
     * 
     * @return type
     */
    public static function getSortFunction() {
        return function($a, $b) { 
                $sortValue = strcmp($a->getLastName(), $b->getLastName());
                return ($sortValue === 0) ? 
                    strcmp($a->getFirstName(), $b->getFirstName()) : $sortValue;
            };        
    }    
    
     
    /**************************************************************************
     * Member Variables
     **************************************************************************/
    protected $firstName = null;
    protected $lastName = null;
    protected $role = null;
    protected $active = null;
     
 
    /**************************************************************************
     * Constructor
     **************************************************************************/
    /** 
     * This constructor sets all of the member variables using the arguments.
     *
     * @param type $argID
     * @param type $argFirstName
     * @param type $argLastName
     * @param type $argRole
     * @param type $argActive
     */ 
    public function __construct($argID, $argFirstName, $argLastName, $argRole, $argActive) {
        parent::__construct($argID);
         
        $this->firstName = $argFirstName;
        $this->lastName = $argLastName;
        $this->role = $argRole;
        $this->active = $argActive;
    }

     
    /**************************************************************************
     * Get and Set Methods
     **************************************************************************/
    /** 
     * The get method for the user's first name.
     *
     * @return  The string first name
     */ 
    public function getFirstName() {
        return $this->firstName;   
    } 

    /** 
     * The get method for the user's last name.
     *
     * @return  The string last name
     */ 
    public function getLastName() {
        return $this->lastName;   
    } 
     
    /** 
     * The get method for the user's full name
     *
     * @return  The string name
     */ 
    public function getName() {
        return $this->firstName." ".$this->lastName;   
    } 
               
}