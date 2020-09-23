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
 * The class CourseList represents a course list as stored in the database.
 */
class OptionCourseList extends CourseList {
    /**************************************************************************
     * Static Functions
     **************************************************************************/    
    /**
     * 
     * @return boolean
     */
    public static function sortAfterInitialization() {
        return true;
    }
    
    
    /*************************************************************************
     * Initialize
     *************************************************************************/
    /**
     * Initializes the member variables that can't be set by a single DB row.
     * 
     * @param type $dbCurriculum
     */
    public function initialize($dbCurriculum, $argArray = array(
            CMD::TABLE_COURSELIST_TO_COURSELIST_LEVEL => CMD::TABLE_COURSELIST_TO_COURSELIST_LEVEL_NONE, 
            CMD::TABLE_COURSELIST_TO_COURSELIST_OR_ABOVE => false)) {
        // Set the level and 'or above' option
        $this->level = $argArray[CMD::TABLE_COURSELIST_TO_COURSELIST_LEVEL];
        $this->orAbove = $argArray[CMD::TABLE_COURSELIST_TO_COURSELIST_OR_ABOVE];
        
        // Get the courses that are directly associated with this list
        $this->childCourseArray = $dbCurriculum->getCoursesInCourseList(
            $this->dbID, $this->level, $this->orAbove);

        // Initialize these courses using the calendar database
        $this->initializeChildCoursesWithCalendar();
        
        // Fetch and initialize the child course lists
        $this->childCourseListArray = $dbCurriculum->getChildCourseLists($this->dbID);

        // Update the name to include the level
        // NOTE: an Option list is expected to have a name specified
        $this->name .= $this->getLevelAndOrAbovePostfix();
        
        // Set the link/HTML for this course list
        $this->html = '<a href="'.$this->getLinkToView().'">'.$this->name.'</a>';        
    }    
}
