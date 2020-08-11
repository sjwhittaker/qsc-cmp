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
use Managers\CourseCalendarDatabase as CCD;


/**
 * The class Course is intended to store the information about a course
 * which may be cross-referenced. It's a collection of CourseEntries.
 */
class Course {
    /*************************************************************************
     * Static Functions
     *************************************************************************/
    /**
     * 
     * @param type $courseEntryArray
     * @return 
     */
    public static function buildCoursesFromCourseEntries($courseEntryArray) {        
        if (empty($courseEntryArray)) {
            return array();
        }
        
        $courseArray = array();
        $courseEntry2DArray = array();        
        
        foreach ($courseEntryArray as $courseEntry) {
            $entryID = $courseEntry->getDBID();
            if (! array_key_exists($entryID, $courseEntry2DArray)) {
                $courseEntry2DArray[$entryID] = array();
            }
            
            $courseEntry2DArray[$entryID][] = $courseEntry;
        }
        
        foreach ($courseEntry2DArray as $courseEntryGroup) {
            $courseArray[] = new Course($courseEntryGroup);
        } 
        
        return $courseArray;
    }
    
    /**
     * 
     * @param type $courseArray
     * @return type
     */
    public static function getTotalUnitsForCourses($courseArray) {
        return array_sum(qsc_core_map_member_function($courseArray, 'getUnitsAsFloat'));
    }
    

    /*************************************************************************
     * Member Variables
     /*************************************************************************/
    protected $courseEntryArray = array();
    protected $courseEntryArraySize = 0;
    
    protected $units = 0;

    
    /*************************************************************************
     * Constructor
     /*************************************************************************/
    /**
     * This constructor stores a collection of CourseEntry objects using the
     * given array argument.
     *
     * @param type $argArray
     */
    public function __construct($argArray) {
        $this->courseEntryArray = $argArray;
        $this->courseEntryArraySize = count($argArray);
    }
    
    
    /*************************************************************************
     * Initialize
     *************************************************************************/
    /**
     * 
     * @param type $dbCalendar
     */
    public function initializeUnits($dbCalendar) {
        if (! $this->courseEntryArraySize) {
            return;
        }
        
        $completeUnitValue = $dbCalendar->getUnitsForCourse($this->courseEntryArray[0]->getCalendarCourseDBID());
        for ($i = 1; $i < $this->courseEntryArraySize; $i++) {
            $tempUnitValue = $dbCalendar->getUnitsForCourse($this->courseEntryArray[$i]->getCalendarCourseDBID());
            if (strcmp($tempUnitValue, $completeUnitValue) > 0) {
                $completeUnitValue = $tempUnitValue;
            }
        }
        
        $resultUnitValue = floatval($completeUnitValue);
        $this->units = ($resultUnitValue > 0) ?
            $resultUnitValue :
            QSC_CMP_COURSE_UNITS_DEFAULT;
    }

    
    /*************************************************************************
     * Get and Set Methods
     *************************************************************************/
    /**
     * The get method for the object's ID in the database. This will be the 
     * ID of all of the CourseEntries, so the ID of the first element will
     * work well.
     *
     * @return      The string or numeric database ID
     */
    public function getDBID() {
        return $this->courseEntryArray[0]->getDBID();
    }
        
    /**
     * 
     * @return type
     */
    public function getCourseEntries() {
        return $this->courseEntryArray;
    }    
    
    /**
     * 
     * @return type
     */
    public function getNumberOfCourseEntries() {
        return $this->courseEntryArraySize;
    }    
    
    /**
     * 
     * @return boolean
     */
    public function getNumber() {
        $courseEntryNumber = $this->courseEntryArray[0]->getNumber();

        for ($i = 1; $i < $this->courseEntryArraySize; $i++) {
            if ($this->courseEntryArray[$i]->getNumber() !== $courseEntryNumber) {
                return false;
            }
        }
        
        return $courseEntryNumber;
    }    
    
    /**
     * 
     * @return type
     */
    public function getName() {
        $courseName = '';
        
        // If there's one CourseEntry, use it
        if ($this->courseEntryArraySize === 1) {
            $courseName = $this->courseEntryArray[0]->getName();
        }
        else {
            // This is a cross-reference course - do they have the same number?
            $courseNumber = $this->getNumber();
            
            if ($courseNumber !== false) {
                $courseName = implode(QSC_CMP_COURSE_CROSS_REFERENCE_DELIMETER,
                    qsc_core_map_member_function($this->courseEntryArray, 'getSubject'));
                $courseName .= QSC_CMP_COURSE_CODE_DELIMETER;
                $courseName .= $courseNumber;                
            }
            else {
                $courseName = implode(QSC_CMP_COURSE_CROSS_REFERENCE_DELIMETER,
                    qsc_core_map_member_function($this->courseEntryArray, 'getCode'));                
            }
        }
        
        return $courseName;
    }
    
    /**
     * 
     * @return type
     */
    public function getCode() {
        return $this->getName();
    }
    
    /**
     * 
     * @param type $dbCalendar
     * @return type
     */
    public function getCalendarName($dbCalendar) {
        // Get all of the course codes from the CourseEntries
        $courseCodeArray = qsc_core_map_member_function($this->courseEntryArray,
            'getCode');

        // Get the corresponding calendar courses
        $calendarCourseArray = $dbCalendar->getCoursesFromIDs($courseCodeArray);

        // Get the names from the CalendarCourses and join them together
        // without repeats
        return implode(
            QSC_CMP_COURSE_CROSS_REFERENCE_DELIMETER,
            array_unique(
                    qsc_core_map_member_function($calendarCourseArray, 'getName')
            )
        );
    }
    
    /**
     * 
     * @return type
     */
    public function getCalendarCourseDBIDs() {
        return qsc_core_map_member_function($this->courseEntryArray, 'getCalendarCourseDBID');
    }
    
    /**
     * 
     * @return type
     */
    public function getUnitsAsString() {
        return number_format($this->units, 1);
    }

    /**
     * 
     * @return type
     */
    public function getUnitsAsFloat() {
        return $this->units;
    }
    

    /*************************************************************************
     * Member Functions
     *************************************************************************/
    /**
     * Creates a link to view this course using its ID.
     *
     * @return      A string containing the link
     */
    public function getLinkToView() {
        return qsc_core_create_link_with_id(QSC_CMP_COURSE_VIEW_PAGE_LINK, $this->getDBID());
    }
    
    /**
     * 
     * @param type $dbCalendar
     * @return string
     */
    public function getAnchorToView($dbCalendar = null) {
        $calendarName = ($dbCalendar) ? $this->getCalendarName($dbCalendar) : '';
        
        $anchor = '<a href="'.$this->getLinkToView().'">'.$this->getName();
        if ($this->getUnitsAsFloat()) {
            $anchor .= '/'.$this->getUnitsAsString();
        }
        $anchor .= '</a>';
        if ($calendarName) {            
            $anchor .= ': '.$calendarName;
        }
        
        return $anchor;
    }
    
    /**
     * 
     * @param type $subject
     */
    public function getCourseEntryWithSubject($subject) {
        foreach ($this->courseEntryArray as $courseEntry) {
            if ($courseEntry->getSubject() == $subject) {
                return $courseEntry;
            }
        }
        
        return null;
    }
}
