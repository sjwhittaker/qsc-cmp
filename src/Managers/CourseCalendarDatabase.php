<?php                    
namespace Managers;

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

use DatabaseObjects\CalendarCourse;


/** 
 * This class manages a connection and queries to the course calendar database.
 *
 * NOTE: This database will be a single, separate database on another machine 
 * at some point in the future. Until development is complete, it's been
 * uploaded locally.
 */
class CourseCalendarDatabase extends DatabaseManager {
    /**************************************************************************
     * Constants                
     **************************************************************************/ 
    public const TABLE_COURSES = "courses"; 
    public const TABLE_COURSES_CODE = "code"; 
    public const TABLE_COURSES_NAME = "name"; 
    public const TABLE_COURSES_UNITS = "units"; 
    public const TABLE_COURSES_DESCRIPTION = "description"; 
    public const TABLE_COURSES_PREREQ = "prereq"; 
    public const TABLE_COURSES_COREQ = "coreq"; 
    public const TABLE_COURSES_NOTE = "note"; 
    public const TABLE_COURSES_EXCLUSION = "exclusion"; 
    public const TABLE_COURSES_RECCOMEND = "reccomend"; 
    public const TABLE_COURSES_ONEWAY = "oneWay"; 
    public const TABLE_COURSES_LEARNING_HOURS = "learnHours"; 
    public const TABLE_COURSES_EQUIVALENCY = "equivalency"; 
    public const TABLE_COURSES_WEBSITE = "website";    
        
     
    /**************************************************************************
     * Constructor
     **************************************************************************/
    /** 
     * This constructor connects to the course calendar database using the
     * defined constants. 
     */
    public function __construct() {
        parent::__construct(
            qsc_cmp_get_ccd_database_name(),
            qsc_cmp_get_ccd_host_name(),
            qsc_cmp_get_ccd_user_name());
    }

    /**************************************************************************
     * Member Functions
     **************************************************************************/
    /** 
     * Returns the password needed to connect to the database.
     *
     * @return      A string with the password
     */ 
    protected function getDatabasePassword() {
        return qsc_cmp_get_ccd_database_password();  
    }
    
    
    /**************************************************************************
     * Course Information
     **************************************************************************/      
    /**
     * Queries the database for the single course with the given IDs, which have
     * the format 'CODE NUMBER'.
     *
     * @param type $code
     * @return      An associative array with all of the course's column/value
     *              pairs
     */
    public function getCourseFromID($code) {
        $courses = self::TABLE_COURSES;
        $courses_code = self::TABLE_COURSES_CODE;
                
        $query = "SELECT * FROM $courses WHERE $courses_code = ?";

        $courseRow = $this->getQueryResult($query, array($code));
        return (empty($courseRow)) ? null : CalendarCourse::buildFromDBRow($courseRow); 
    }
        
    /**
     * 
     * @param type $codeArray
     * @return type
     */
    public function getCoursesFromIDs($codeArray) {
        $courses = self::TABLE_COURSES;
        $courses_code = self::TABLE_COURSES_CODE;
                
        $query = "SELECT * FROM $courses WHERE $courses_code IN ".self::getQuestionMarkString($codeArray);

        $courseArray = array();
        $courseRowArray = $this->getQueryResults($query, $codeArray);
        foreach ($courseRowArray as $courseRow) {
            $courseArray[] = CalendarCourse::buildFromDBRow($courseRow);
        }
        
        return $courseArray;
    }    
    
    /**
     * 
     * @param type $codeArray
     * @return type
     */
    public function getAllSubjects($codeArray) {
        $courses = self::TABLE_COURSES;
        $courses_code = self::TABLE_COURSES_CODE;
        $delimeter = QSC_CMP_COURSE_CODE_DELIMETER;
                
        $query = "SELECT DISTINCT SUBSTRING_INDEX($courses_code, $delimeter, 1) FROM $courses";

        return $this->getQueryResults($query);
    }
    
    /**
     * 
     * @param type $code
     */
    public function getUnitsForCourse($code) {
        $courses = self::TABLE_COURSES;
        $courses_code = self::TABLE_COURSES_CODE;
        $courses_units = self::TABLE_COURSES_UNITS;
        
        $query = "SELECT $courses_units FROM $courses WHERE $courses_code = ?";
        $courseRow = $this->getQueryResult($query, array($code));
        return (empty($courseRow)) ? 0 : $courseRow[$courses_units];         
    }

}

 