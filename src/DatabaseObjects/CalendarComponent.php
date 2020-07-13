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
 * The abstract class CalendarComponent contains elements and functionality 
 * common to all components in the calendar.
 */
abstract class CalendarComponent extends DatabaseObject {
    /**************************************************************************
     * Static Functions
     **************************************************************************/
    /**
     * 
     * @return type
     */
    public static function getSortFunction() {
        return function($a, $b) { 
                return strcmp($a->getName(), $b->getName());
            };        
    } 


    /**************************************************************************
     * Member Variables
     **************************************************************************/
    protected $name = null;

    
    /**************************************************************************
     * Abstract Methods
     **************************************************************************/
    abstract protected function getLinkToView();     
     
 
    /**************************************************************************
     * Constructor
     **************************************************************************/
    /** 
     * This constructor sets all of the member variables
     *
     * @param $argDBID         The component's database integer ID
     * @param $argName         The component's name 
     */ 
    protected function __construct($argDBID, $argName) {
        parent::__construct($argDBID);

        $this->name = $argName;
    }

     
    /**************************************************************************
     * Get and Set Methods
     **************************************************************************/
    /** 
     * The get method for the component's name.
     *
     * @return  The string name
     */ 
    public function getName() {
        return $this->name;   
    } 
     

    /**************************************************************************
     * Member Functions
     **************************************************************************/
    /**
     * 
     * @return type
     */
    public function getAnchorToView() {
        return '<a href="'.$this->getLinkToView().'">'.$this->getName().'</a>';
    }
    
}