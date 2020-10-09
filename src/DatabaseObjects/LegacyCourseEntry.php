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


/** 
 * The class LegacyCourseEntry is intended to store the information from a 
 * row in the database about a single course that is no longer offered.
 * 
 * NOTE: a CourseEntry can represent one offering of a Course, which may be 
 * cross-referenced.
 */
class LegacyCourseEntry extends CourseEntry {
    /**************************************************************************
     * Static Functions
     **************************************************************************/
             
     
    /**************************************************************************
     * Member Variables
     **************************************************************************/
     
 
    /**************************************************************************
     * Constructor
     **************************************************************************/

     
    /**************************************************************************
     * Get and Set Methods
     **************************************************************************/
    

    /**************************************************************************
     * Member Functions
     **************************************************************************/
    /**
     * 
     * @return type
     */
    public function getNameHTML() {
        return '<span class="legacy-course">'.$this->getName().'</span>';
    }
                 
}