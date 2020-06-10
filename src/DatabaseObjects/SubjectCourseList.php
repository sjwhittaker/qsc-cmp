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
 * The class SubjectCourseList represents a course list associated with a
 * subject (e.g., CISC 300 or above).
 */
class SubjectCourseList extends CourseList {
    /**************************************************************************
     * Member Variables
     **************************************************************************/
    protected $subject = null;       
    
           
    /*************************************************************************
     * Initialize
     *************************************************************************/
    /**
     * Initializes the member variables that can't be set by a single DB row.
     * Note that Subject lists are leaves and have no children.
     * 
     * @param type $dbCurriculum
     */
    public function initialize($dbCurriculum, 
        $level = CMD::TABLE_COURSELIST_AND_COURSELIST_LEVEL_NONE, 
        $orAbove = false) {
        // Set the level and 'or above' option
        $this->level = $level;
        $this->orAbove = $orAbove;
        
        // Get the subject for this course list
        $this->subject = $dbCurriculum->getCourseListSubject($this->dbID);
        
        // Get the courses that are associated with this list
        $this->childCourseArray = $dbCurriculum->getCoursesWithSubject(
            $this->subject, $this->level, $this->orAbove);
        
        // Initialize these courses using the calendar database
        $this->initializeChildCoursesWithCalendar();
        
        // Update the name to include the level
        // NOTE: an Option list is expected to have a name specified
        if (! $this->name) {
            $this->name = $this->subject;
        }

        $this->name .= $this->getLevelAndOrAbovePostfix();        
        
        // Set the link/HTML for this course list
        $this->html = '<a href="'.$this->getLinkToView().'">'.$this->name.'</a>';        
    }
    
}

