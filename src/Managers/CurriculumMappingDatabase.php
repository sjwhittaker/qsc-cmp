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

use DatabaseObjects\CLLOAndCourse;
use DatabaseObjects\CLLOAndILO;
use DatabaseObjects\CLLOAndPLLO;
use DatabaseObjects\Course;
use DatabaseObjects\CourseEntry;
use DatabaseObjects\CourseList;
use DatabaseObjects\CourseLevelLearningOutcome as CLLO;
use DatabaseObjects\CoursePlanRequirement as CPR;
use DatabaseObjects\DatabaseObject;
use DatabaseObjects\Degree;
use DatabaseObjects\DegreeLevelExpectation as DLE;
use DatabaseObjects\Department;
use DatabaseObjects\Faculty;
use DatabaseObjects\InstitutionLearningOutcome as ILO;
use DatabaseObjects\OptionCourseList;
use DatabaseObjects\Plan;
use DatabaseObjects\PlanLevelLearningOutcome as PLLO;
use DatabaseObjects\PLLOAndDLE;
use DatabaseObjects\PLLOAndILO;
use DatabaseObjects\Program;
use DatabaseObjects\RelationshipCourseList;
use DatabaseObjects\Revision;
use DatabaseObjects\SubjectCourseList;
use DatabaseObjects\TextPlanRequirement as TPR;
use DatabaseObjects\User;

use Managers\SessionManager;


/**
 * This class manages a connection and queries to the curriculum mapping
 * database.
 */
class CurriculumMappingDatabase extends DatabaseManager {
    /**************************************************************************
     * Constants
     **************************************************************************/
    public const TABLE_CLLO = "cllo";
    public const TABLE_CLLO_ID = "id";
    public const TABLE_CLLO_NUMBER = "number";
    public const TABLE_CLLO_NUMBER_MAX_LENGTH = 15;
    public const TABLE_CLLO_TEXT = "text";
    public const TABLE_CLLO_TEXT_MAX_LENGTH = 500;
    public const TABLE_CLLO_TYPE = "type";
    public const TABLE_CLLO_TYPE_NONE = "None";
    public const TABLE_CLLO_TYPE_DETAIL = "Detail";
    public const TABLE_CLLO_TYPE_CORE = "Core";
    public const TABLE_CLLO_IOA = "ioa";
    public const TABLE_CLLO_IOA_MAX_LENGTH = 250;
    public const TABLE_CLLO_NOTES = "notes";
    public const TABLE_CLLO_NOTES_MAX_LENGTH = 500;
    public const TABLE_CLLO_PARENT_ID = "parent_id";

    public const TABLE_CLLO_AND_ILO = "cllo_and_ilo";
    public const TABLE_CLLO_AND_ILO_CLLO_ID = "cllo_id";
    public const TABLE_CLLO_AND_ILO_ILO_ID = "ilo_id";

    public const TABLE_CLLO_AND_COURSE = "cllo_and_course";
    public const TABLE_CLLO_AND_COURSE_CLLO_ID = "cllo_id";
    public const TABLE_CLLO_AND_COURSE_COURSE_ID = "course_id";

    public const TABLE_CLLO_AND_PLLO = "cllo_and_pllo";
    public const TABLE_CLLO_AND_PLLO_CLLO_ID = "cllo_id";
    public const TABLE_CLLO_AND_PLLO_PLLO_ID = "pllo_id";

    public const TABLE_COURSE = "course";
    public const TABLE_COURSE_ID = "id";
    public const TABLE_COURSE_SUBJECT = "subject";
    public const TABLE_COURSE_SUBJECT_MAX_LENGTH = 10;
    public const TABLE_COURSE_NUMBER = "number";
    public const TABLE_COURSE_NUMBER_MAX_LENGTH = 10;
    
    public const TABLE_COURSELIST = "courselist";
    public const TABLE_COURSELIST_ID = "id";
    public const TABLE_COURSELIST_CLASS = "class";
    public const TABLE_COURSELIST_CLASS_OPTION = "Option";
    public const TABLE_COURSELIST_CLASS_SUBJECT = "Subject";
    public const TABLE_COURSELIST_CLASS_RELATIONSHIP = "Relationship";
    public const TABLE_COURSELIST_NAME = "name";
    public const TABLE_COURSELIST_NAME_MAX_LENGTH = 100;
    public const TABLE_COURSELIST_NOTES = "notes";
    public const TABLE_COURSELIST_NOTES_MAX_LENGTH = 500;

    public const TABLE_COURSELIST_AND_COURSE = "courselist_and_course";
    public const TABLE_COURSELIST_AND_COURSE_COURSELIST_ID = "courselist_id";
    public const TABLE_COURSELIST_AND_COURSE_COURSE_ID = "course_id";

    public const TABLE_COURSELIST_AND_COURSELIST = "courselist_and_courselist";
    public const TABLE_COURSELIST_AND_COURSELIST_PARENT_ID = "parent_id";
    public const TABLE_COURSELIST_AND_COURSELIST_CHILD_ID = "child_id";
    public const TABLE_COURSELIST_AND_COURSELIST_LEVEL = "level";
    public const TABLE_COURSELIST_AND_COURSELIST_LEVEL_NONE = "None";
    public const TABLE_COURSELIST_AND_COURSELIST_LEVEL_P = "P";
    public const TABLE_COURSELIST_AND_COURSELIST_LEVEL_100 = "100";
    public const TABLE_COURSELIST_AND_COURSELIST_LEVEL_200 = "200";
    public const TABLE_COURSELIST_AND_COURSELIST_LEVEL_300 = "300";
    public const TABLE_COURSELIST_AND_COURSELIST_LEVEL_400 = "400";
    public const TABLE_COURSELIST_AND_COURSELIST_LEVEL_500 = "500";
    public const TABLE_COURSELIST_AND_COURSELIST_OR_ABOVE = "or_above";
        
    public const TABLE_COURSELIST_AND_RELATIONSHIP = "courselist_and_relationship";
    public const TABLE_COURSELIST_AND_RELATIONSHIP_COURSELIST_ID = "courselist_id";
    public const TABLE_COURSELIST_AND_RELATIONSHIP_RELATIONSHIP = "relationship";
    public const TABLE_COURSELIST_AND_RELATIONSHIP_RELATIONSHIP_AND = "and";
    public const TABLE_COURSELIST_AND_RELATIONSHIP_RELATIONSHIP_OR = "or";
    public const TABLE_COURSELIST_AND_RELATIONSHIP_RELATIONSHIP_ANY = "any";

    public const TABLE_COURSELIST_AND_SUBJECT = "courselist_and_subject";
    public const TABLE_COURSELIST_AND_SUBJECT_COURSELIST_ID = "courselist_id";
    public const TABLE_COURSELIST_AND_SUBJECT_SUBJECT = "subject";
    public const TABLE_COURSELIST_AND_SUBJECT_SUBJECT_MAX_LENGTH = 10;
        
    public const TABLE_CPR = "cpr";
    public const TABLE_CPR_ID = "id";
    public const TABLE_CPR_NAME = "name";
    public const TABLE_CPR_NAME_MAX_LENGTH = 10;
    public const TABLE_CPR_TYPE_CORE = "Core";
    public const TABLE_CPR_TYPE_OPTION = "Option";
    public const TABLE_CPR_TYPE_SUPPPORTING = "Supporting";
    public const TABLE_CPR_UNITS = "units";
    public const TABLE_CPR_CONNECTOR = "connector";
    public const TABLE_CPR_CONNECTOR_FROM = "from";
    public const TABLE_CPR_CONNECTOR_IN = "in";
    public const TABLE_CPR_TYPE = "type";
    public const TABLE_CPR_TYPE_MAX_LENGTH = 50;
    public const TABLE_CPR_TEXT = "text";
    public const TABLE_CPR_TEXT_MAX_LENGTH = 100;
    public const TABLE_CPR_NOTES = "notes";
    public const TABLE_CPR_NOTES_MAX_LENGTH = 500;
    public const TABLE_CPR_CLASS = "class";
    public const TABLE_CPR_CLASS_MAX_LENGTH = 25;

    public const TABLE_CPR_AND_COURSELIST = "cpr_and_courselist";
    public const TABLE_CPR_AND_COURSELIST_CPR_ID = "cpr_id";
    public const TABLE_CPR_AND_COURSELIST_COURSELIST_ID = "courselist_id";
    public const TABLE_CPR_AND_COURSELIST_LEVEL = self::TABLE_COURSELIST_AND_COURSELIST_LEVEL;
    public const TABLE_CPR_AND_COURSELIST_LEVEL_NONE = self::TABLE_COURSELIST_AND_COURSELIST_LEVEL_NONE;
    public const TABLE_CPR_AND_COURSELIST_LEVEL_P = self::TABLE_COURSELIST_AND_COURSELIST_LEVEL_P;
    public const TABLE_CPR_AND_COURSELIST_LEVEL_100 = self::TABLE_COURSELIST_AND_COURSELIST_LEVEL_100;
    public const TABLE_CPR_AND_COURSELIST_LEVEL_200 = self::TABLE_COURSELIST_AND_COURSELIST_LEVEL_200;
    public const TABLE_CPR_AND_COURSELIST_LEVEL_300 = self::TABLE_COURSELIST_AND_COURSELIST_LEVEL_300;
    public const TABLE_CPR_AND_COURSELIST_LEVEL_400 = self::TABLE_COURSELIST_AND_COURSELIST_LEVEL_400;
    public const TABLE_CPR_AND_COURSELIST_LEVEL_500 = self::TABLE_COURSELIST_AND_COURSELIST_LEVEL_500;
    public const TABLE_CPR_AND_COURSELIST_OR_ABOVE = self::TABLE_COURSELIST_AND_COURSELIST_OR_ABOVE;
        
    public const TABLE_DEGREE = "degree";
    public const TABLE_DEGREE_ID = "id";
    public const TABLE_DEGREE_NAME = "name";
    public const TABLE_DEGREE_NAME_MAX_LENGTH = 75;    
    public const TABLE_DEGREE_CODE = "code";
    public const TABLE_DEGREE_CODE_MAX_LENGTH = 10;    
    public const TABLE_DEGREE_TYPE = "type";
    public const TABLE_DEGREE_TYPE_ARTS = "Arts";
    public const TABLE_DEGREE_TYPE_SCIENCE = "Science";
    public const TABLE_DEGREE_TYPE_COMPUTING = "Computing";
    public const TABLE_DEGREE_TYPE_MUSIC_THEATRE = "Music Theatre";
    public const TABLE_DEGREE_TYPE_PHYSICAL_EDUCATION = "Physical Education";
    public const TABLE_DEGREE_HONOURS = "honours";

    public const TABLE_DEGREE_AND_FACULTY = "degree_and_faculty";
    public const TABLE_DEGREE_AND_FACULTY_DEGREE_ID = "degree_id";
    public const TABLE_DEGREE_AND_FACULTY_FACULTY_ID = "faculty_id";

    public const TABLE_DEPARTMENT = "department";
    public const TABLE_DEPARTMENT_ID = "id";
    public const TABLE_DEPARTMENT_NAME = "name";
    public const TABLE_DEPARTMENT_NAME_MAX_LENGTH = 150;    

    public const TABLE_DEPARTMENT_AND_FACULTY = "department_and_faculty";
    public const TABLE_DEPARTMENT_AND_FACULTY_DEPARTMENT_ID = "department_id";
    public const TABLE_DEPARTMENT_AND_FACULTY_FACULTY_ID = "faculty_id";
    
    public const TABLE_DEPARTMENT_AND_PLAN = "department_and_plan";
    public const TABLE_DEPARTMENT_AND_PLAN_DEPARTMENT_ID = "department_id";
    public const TABLE_DEPARTMENT_AND_PLAN_PLAN_ID = "plan_id";
    public const TABLE_DEPARTMENT_AND_PLAN_ROLE = "role";
    public const TABLE_DEPARTMENT_AND_PLAN_ROLE_ADMINISTRATOR = "Administrator";
    public const TABLE_DEPARTMENT_AND_PLAN_ROLE_PARTNER = "Partner";    

    public const TABLE_DEPARTMENT_AND_SUBJECT = "department_and_subject";
    public const TABLE_DEPARTMENT_AND_SUBJECT_DEPARTMENT_ID = "department_id";
    public const TABLE_DEPARTMENT_AND_SUBJECT_SUBJECT = "subject";

    public const TABLE_DLE = "dle";
    public const TABLE_DLE_ID = "id";
    public const TABLE_DLE_NUMBER = "number";
    public const TABLE_DLE_NUMBER_MAX_LENGTH = 15;
    public const TABLE_DLE_TEXT = "text";
    public const TABLE_DLE_TEXT_MAX_LENGTH = 100;
    public const TABLE_DLE_NOTES = "notes";
    public const TABLE_DLE_NOTES_MAX_LENGTH = 500;
    public const TABLE_DLE_PARENT_ID = "parent_id";
    
    public const TABLE_FACULTY = "faculty";
    public const TABLE_FACULTY_ID = "id";
    public const TABLE_FACULTY_NAME = "name";
    public const TABLE_FACULTY_NAME_MAX_LENGTH = 100;    

    public const TABLE_ILO = "ilo";
    public const TABLE_ILO_ID = "id";
    public const TABLE_ILO_NUMBER = "number";
    public const TABLE_ILO_NUMBER_MAX_LENGTH = 15;
    public const TABLE_ILO_TEXT = "text";
    public const TABLE_ILO_TEXT_MAX_LENGTH = 150;
    public const TABLE_ILO_DESCRIPTION = "description";
    public const TABLE_ILO_DESCRIPTION_MAX_LENGTH = 500;
    public const TABLE_ILO_NOTES = "notes";
    public const TABLE_ILO_NOTES_MAX_LENGTH = 500;
    public const TABLE_ILO_PARENT_ID = "parent_id";
    
    public const TABLE_PLAN = "plan";
    public const TABLE_PLAN_ID = "id";
    public const TABLE_PLAN_NAME = "name";
    public const TABLE_PLAN_NAME_MAX_LENGTH = 75;    
    public const TABLE_PLAN_CODE = "code";
    public const TABLE_PLAN_CODE_MAX_LENGTH = 10;    
    public const TABLE_PLAN_TYPE = "type";
    public const TABLE_PLAN_TYPE_MAJOR = "Major";
    public const TABLE_PLAN_TYPE_MINOR = "Minor";
    public const TABLE_PLAN_TYPE_SPECIALIZATION = "Specialization";
    public const TABLE_PLAN_TYPE_MEDIAL = "Medial";
    public const TABLE_PLAN_TYPE_GENERAL = "General";
    public const TABLE_PLAN_TYPE_SUB_PLAN = "Sub-Plan";
    public const TABLE_PLAN_INTERNSHIP = "internship";
    public const TABLE_PLAN_PRIOR_TO = "prior_to";
    public const TABLE_PLAN_TEXT = "text";
    public const TABLE_PLAN_TEXT_MAX_LENGTH = 500;    
    public const TABLE_PLAN_NOTES = "notes";
    public const TABLE_PLAN_NOTES_MAX_LENGTH = 500;    

    public const TABLE_PLAN_AND_PLAN = "plan_and_plan";
    public const TABLE_PLAN_AND_PLAN_PARENT_ID = "parent_id";
    public const TABLE_PLAN_AND_PLAN_CHILD_ID = "child_id";    
    
    public const TABLE_PLAN_AND_PLLO = "plan_and_pllo";
    public const TABLE_PLAN_AND_PLLO_PLAN_ID = "plan_id";
    public const TABLE_PLAN_AND_PLLO_PLLO_ID = "pllo_id";
    
    public const TABLE_PLAN_AND_CPR = "plan_and_cpr";
    public const TABLE_PLAN_AND_CPR_PLAN_ID = "plan_id";
    public const TABLE_PLAN_AND_CPR_CPR_ID = "cpr_id";

    public const TABLE_PLAN_AND_TPR = "plan_and_tpr";
    public const TABLE_PLAN_AND_TPR_PLAN_ID = "plan_id";
    public const TABLE_PLAN_AND_TPR_TPR_ID = "tpr_id";    
    
    public const TABLE_PLLO = "pllo";
    public const TABLE_PLLO_ID = "id";
    public const TABLE_PLLO_NUMBER = "number";
    public const TABLE_PLLO_NUMBER_MAX_LENGTH = 15;
    public const TABLE_PLLO_TEXT = "text";
    public const TABLE_PLLO_TEXT_MAX_LENGTH = 100;
    public const TABLE_PLLO_NOTES = "notes";
    public const TABLE_PLLO_NOTES_MAX_LENGTH = 500;
    public const TABLE_PLLO_PARENT_ID = "parent_id";

    public const TABLE_PLLO_AND_DLE = "pllo_and_dle";
    public const TABLE_PLLO_AND_DLE_PLLO_ID = "pllo_id";
    public const TABLE_PLLO_AND_DLE_DLE_ID = "dle_id";

    public const TABLE_PLLO_AND_ILO = "pllo_and_ilo";
    public const TABLE_PLLO_AND_ILO_PLLO_ID = "pllo_id";
    public const TABLE_PLLO_AND_ILO_ILO_ID = "ilo_id";
            
    public const TABLE_PROGRAM = "program";
    public const TABLE_PROGRAM_ID = "id";
    public const TABLE_PROGRAM_NAME = "name";
    public const TABLE_PROGRAM_NAME_MAX_LENGTH = 75;    
    public const TABLE_PROGRAM_TYPE = "type";
    public const TABLE_PROGRAM_TYPE_MAX_LENGTH = 50;    
    public const TABLE_PROGRAM_CODE = "code";
    public const TABLE_PROGRAM_CODE_MAX_LENGTH = 20;    
    public const TABLE_PROGRAM_TEXT = "text";
    public const TABLE_PROGRAM_TEXT_MAX_LENGTH = 500;    
    public const TABLE_PROGRAM_NOTES = "notes";
    public const TABLE_PROGRAM_NOTES_MAX_LENGTH = 500;
    
    public const TABLE_PROGRAM_AND_DEGREE = "program_and_degree";
    public const TABLE_PROGRAM_AND_DEGREE_PROGRAM_ID = "program_id";
    public const TABLE_PROGRAM_AND_DEGREE_DEGREE_ID = "degree_id";

    public const TABLE_PROGRAM_AND_PLAN = "program_and_plan";
    public const TABLE_PROGRAM_AND_PLAN_PROGRAM_ID = "program_id";
    public const TABLE_PROGRAM_AND_PLAN_PLAN_ID = "plan_id";    
    
    public const TABLE_REVISION = "revision";
    public const TABLE_REVISION_ID = "id";
    public const TABLE_REVISION_USER_ID = "user_id";
    public const TABLE_REVISION_REV_TABLE = "rev_table";
    public const TABLE_REVISION_REV_COLUMN = "rev_column";
    public const TABLE_REVISION_KEY_COLUMNS = "key_columns";
    public const TABLE_REVISION_KEY_VALUES = "key_values";
    public const TABLE_REVISION_KEY_SEPARATOR = "|,|";
    public const TABLE_REVISION_ACTION = "action";
    public const TABLE_REVISION_ACTION_ADDED = "added";
    public const TABLE_REVISION_ACTION_EDITED = "edited";
    public const TABLE_REVISION_ACTION_DELETED = "deleted";
    public const TABLE_REVISION_PRIOR_VALUE = "prior_value";
    public const TABLE_REVISION_PRIOR_VALUE_MAX_LENGTH = 500;
    public const TABLE_REVISION_DATE_AND_TIME = "date_and_time";
    public const TABLE_REVISION_ALL_DATA_SEPARATOR = "[,]";
    
    public const TABLE_TPR = "tpr";
    public const TABLE_TPR_ID = "id";
    public const TABLE_TPR_NAME = "name";
    public const TABLE_TPR_NAME_MAX_LENGTH = 10;
    public const TABLE_TPR_TYPE = "type";
    public const TABLE_TPR_TYPE_ADDITIONAL = "Additional";
    public const TABLE_TPR_TYPE_SUBSTITUTIONS = "Substitutions";
    public const TABLE_TPR_TYPE_NOTES = "Notes";
    public const TABLE_TPR_TEXT = "text";
    public const TABLE_TPR_TEXT_MAX_LENGTH = 750;
    public const TABLE_TPR_NOTES = "notes";
    public const TABLE_TPR_NOTES_MAX_LENGTH = 500;

    public const TABLE_USER = "user";
    public const TABLE_USER_ID = "id";
    public const TABLE_USER_FIRST_NAME = "first_name";
    public const TABLE_USER_LAST_NAME = "last_name";
    public const TABLE_USER_ROLE = "role";
    public const TABLE_USER_ACTIVE = "active";

    public const TABLE_USER_ACCESS = "user_access";
    public const TABLE_USER_ACCESS_USER_ID = "user_id";
    public const TABLE_USER_ACCESS_LOGGED_IN = "logged_in";

    public const COLUMN_LO_ID = "id";
    public const COLUMN_LO_NUMBER = "number";
    public const COLUMN_LO_TEXT = "text";
    public const COLUMN_LO_NOTES = "notes";
    public const COLUMN_LO_PARENT_ID = "parent_id";


    /**************************************************************************
     * Static Functions
     **************************************************************************/
    /**
     * 
     * @param type $rowArray
     * @param type $column
     * @return type
     */
    public static function extractValueFromDBRows($rowArray, $column) {
        $valueArray = array();
        foreach ($rowArray as $row) {
            $valueArray[] = $row[$column];
        }

        return $valueArray;
    }

    /**
     * 
     * @param type $rowArray
     * @param type $objectType
     * @return type
     */
    public static function buildFromDBRows($rowArray, $objectType) {
        $objectArray = array();
        foreach ($rowArray as $row) {
            $objectArray[] = call_user_func('\\DatabaseObjects\\'.$objectType."::buildFromDBRow", $row);
        }

        return $objectArray;
    }
    
    /**
     * 
     * @return type
     */
    public static function getCLLOTypes() {
        return array(self::TABLE_CLLO_TYPE_NONE,
            self::TABLE_CLLO_TYPE_DETAIL ,
            self::TABLE_CLLO_TYPE_CORE);
    }
    
    /**
     * 
     * @return type
     */
    public static function getCourseListLevels() {
        return array(self::TABLE_COURSELIST_AND_COURSELIST_LEVEL_P,
            self::TABLE_COURSELIST_AND_COURSELIST_LEVEL_100,
            self::TABLE_COURSELIST_AND_COURSELIST_LEVEL_200,
            self::TABLE_COURSELIST_AND_COURSELIST_LEVEL_300,
            self::TABLE_COURSELIST_AND_COURSELIST_LEVEL_400,
            self::TABLE_COURSELIST_AND_COURSELIST_LEVEL_500);        
    }
    
    /**
     * 
     */
    public static function getCourseListRelationships() {
        return array(self::TABLE_COURSELIST_AND_RELATIONSHIP_RELATIONSHIP_AND,
            self::TABLE_COURSELIST_AND_RELATIONSHIP_RELATIONSHIP_OR,
            self::TABLE_COURSELIST_AND_RELATIONSHIP_RELATIONSHIP_ANY);        
    }
    
    /**
     * 
     * @return type
     */
    public static function getCPRConnectors() {
        return array(self::TABLE_CPR_CONNECTOR_FROM,
            self::TABLE_CPR_CONNECTOR_IN);
    }
    
    /**
     * 
     * @return type
     */
    public static function getCPRTypes() {
        return array(self::TABLE_CPR_TYPE_CORE, 
            self::TABLE_CPR_TYPE_OPTION,
            self::TABLE_CPR_TYPE_SUPPPORTING);
    }
    
    /**
     * 
     * @return type
     */
    public static function getDegreeTypes() {
        return array(self::TABLE_DEGREE_TYPE_ARTS,
            self::TABLE_DEGREE_TYPE_SCIENCE,
            self::TABLE_DEGREE_TYPE_COMPUTING,
            self::TABLE_DEGREE_TYPE_MUSIC_THEATRE,
            self::TABLE_DEGREE_TYPE_PHYSICAL_EDUCATION);        
    }

    /**
     * 
     * @return type
     */
    public static function getPlanTypes() {
        return array(self::TABLE_PLAN_TYPE_MAJOR,
            self::TABLE_PLAN_TYPE_MINOR,
            self::TABLE_PLAN_TYPE_SPECIALIZATION,
            self::TABLE_PLAN_TYPE_MEDIAL.
            self::TABLE_PLAN_TYPE_GENERAL,
            self::TABLE_PLAN_TYPE_SUB_PLAN);        
    }
    
    /**
     * 
     * @return type
     */
    public static function getTPRTypes() {
        return array(self::TABLE_TPR_TYPE_ADDITIONAL, 
            self::TABLE_TPR_TYPE_SUBSTITUTIONS, 
            self::TABLE_TPR_TYPE_NOTES);
    }
    
    /**
     * 
     * @param type $level
     * @param type $orAbove
     * @return string
     */
    public static function getCourseLevelCondition(
        $level = self::TABLE_COURSELIST_AND_COURSELIST_LEVEL_NONE, 
        $orAbove = false) {
        
        $course = self::TABLE_COURSE;
        $courseNumber = self::TABLE_COURSE_NUMBER;
        $courseLevelP = self::TABLE_COURSELIST_AND_COURSELIST_LEVEL_P;
        $courseLevelNone = self::TABLE_COURSELIST_AND_COURSELIST_LEVEL_NONE;
        
        // NOTE: 'P or above' is the same as no condition as P is the 
        // lowest level                     
        if (($level == $courseLevelNone) || (($level == $courseLevelP) && (! $orAbove))) {
            return '';
        }
                    
        if ($orAbove) {
            // Any 'numeric' level or above
            $condition = "STRCMP($level, $course.$courseNumber) <= 0";
        }
        else {
            // Any level
            $condition = "$course.$courseNumber LIKE CONCAT(LEFT($level, 1), '%')";
        }

        return "($condition)";
    }
    
    /**
     * 
     * @param type $table
     * @param type $level
     * @param type $orAbove
     * @return string
     */
    public static function getCourseListLevelCondition($table, 
        $level = self::TABLE_COURSELIST_AND_COURSELIST_LEVEL_NONE, 
        $orAbove = false) {
        
        $cacLevel = self::TABLE_COURSELIST_AND_COURSELIST_LEVEL;
        $cacOrAbove = self::TABLE_COURSELIST_AND_COURSELIST_OR_ABOVE;
        
        if ($level == self::TABLE_COURSELIST_AND_COURSELIST_LEVEL_NONE) {
            return '';
        }
        
        // Create a level condition based on the parameters
        $condition = "AND $table.$cacLevel = ?";
        if ($orAbove) {
            $condition .= " AND $table.$cacOrAbove = ?";
        }
        
        return $condition;
    }

    /**
     * 
     * @param type $level
     * @param type $orAbove
     * @return type
     */
    public static function getCourseListLevelArguments(
        $level = self::TABLE_COURSELIST_AND_COURSELIST_LEVEL_NONE, 
        $orAbove = false) {
        $argumentArray = array();
        
        // Create a level condition based on the parameters
        if ($level != self::TABLE_COURSELIST_AND_COURSELIST_LEVEL_NONE) {
            $argumentArray[] = $level;
            if ($orAbove) {
                $argumentArray[] = $orAbove;
            }
        }
        
        return $argumentArray;
    }
    

    /**************************************************************************
     * Constructor
     **************************************************************************/
    /**
     * This constructor connects to the course calendar database using the
     * defined constants.
     */
    public function __construct() {
        parent::__construct(
            qsc_cmp_get_cmd_database_name(),
            qsc_cmp_get_cmd_host_name(),
            qsc_cmp_get_cmd_user_name());
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
        return qsc_cmp_get_cmd_database_password();
    }


    /**************************************************************************
     * Courses and Subjects
     **************************************************************************/    
    /**
     * Extracts one row per each unique course subject.
     *
     * @return  A 2D associative array of string subjects
     */
    public function getAllSubjects() {
        $course = self::TABLE_COURSE;
        $subject = self::TABLE_COURSE_SUBJECT;

        $query = "SELECT DISTINCT $subject FROM $course ORDER BY $subject ASC";
        return self::extractValueFromDBRows(
                $this->getQueryResults($query), 
                self::TABLE_COURSE_SUBJECT);
    }
    
    /**
     * 
     * @param type $departmentIDValue
     * @return type
     */
    public function getSubjectsForDepartment($departmentIDValue) {
        $das = self::TABLE_DEPARTMENT_AND_SUBJECT;
        $dasDepartmentID = self::TABLE_DEPARTMENT_AND_SUBJECT_DEPARTMENT_ID;
        $dasSubject = self::TABLE_DEPARTMENT_AND_SUBJECT_SUBJECT;
        
        $query = "SELECT $dasSubject FROM $das WHERE $dasDepartmentID = ? ORDER BY $dasSubject ASC";
        return self::extractValueFromDBRows(
            $this->getQueryResults($query, array($departmentIDValue)), 
            self::TABLE_DEPARTMENT_AND_SUBJECT_SUBJECT);
    }

    
    /**
     * 
     * @param type $subjectValue
     * @return type
     */
    function getDepartmentsForSubject($subjectValue) {
        $department = self::TABLE_DEPARTMENT;
        $departmentID = self::TABLE_DEPARTMENT_ID;
        $departmentName = self::TABLE_DEPARTMENT_NAME;

        $das = self::TABLE_DEPARTMENT_AND_SUBJECT;
        $dasDepartmentID = self::TABLE_DEPARTMENT_AND_SUBJECT_DEPARTMENT_ID;
        $dasSubject = self::TABLE_DEPARTMENT_AND_SUBJECT_SUBJECT;
        
        $query = "SELECT $department.* FROM $department JOIN (SELECT * FROM $das WHERE $das.$dasSubject = ?) AS $das ON $department.$departmentID = $das.$dasDepartmentID ORDER BY $department.$departmentName ASC";
        return self::buildFromDBRows(
            $this->getQueryResults($query, array("$subjectValue")),
            'Department');                
    }
    
    /**
     * Returns one row for each course with the given subject
     *
     * @param $subject   The string subject
     * @return           A 2D associative array of rows for courses with that
     *                   subject
     */
    public function getCoursesWithSubject($subjectValue, 
        $levelValue = self::TABLE_COURSELIST_AND_COURSELIST_LEVEL_NONE, 
        $orAboveValue = false) {
        $course = self::TABLE_COURSE;
        $id = self::TABLE_COURSE_ID;
        $subject = self::TABLE_COURSE_SUBJECT;
        $number = self::TABLE_COURSE_NUMBER;
        
        $levelCondition = self::getCourseLevelCondition($levelValue, $orAboveValue);        
        
        $query = "SELECT * FROM $course WHERE $id IN (SELECT $id FROM $course WHERE $subject = ?)";
        if ($levelCondition) {
            $query .= " AND $levelCondition";
        }        
        $query .= " ORDER BY $subject, $number ASC";

        $courseEntryArray = self::buildFromDBRows(
            $this->getQueryResults($query, array($subjectValue)), 
            'CourseEntry');
        return Course::createArrayFromCourseEntries($courseEntryArray);
    }
        
    /**
     * 
     * @param type $departmentIDValue
     * @return type
     */
    public function getCoursesInDepartment($departmentIDValue) {
        $course = self::TABLE_COURSE;
        $courseID = self::TABLE_COURSE_ID;
        $courseSubject = self::TABLE_COURSE_SUBJECT;
        $courseNumber = self::TABLE_COURSE_NUMBER;        
        
        $das = self::TABLE_DEPARTMENT_AND_SUBJECT;
        $dasDepartmentID = self::TABLE_DEPARTMENT_AND_SUBJECT_DEPARTMENT_ID;
        $dasSubject = self::TABLE_DEPARTMENT_AND_SUBJECT_SUBJECT;
        
        $query = "SELECT $course.* FROM $course JOIN (SELECT * FROM $das WHERE $das.$dasDepartmentID = ?) AS $das ON $course.$courseSubject = $das.$dasSubject ORDER BY $course.$courseSubject, $course.$courseNumber ASC";
        $courseEntryArray = self::buildFromDBRows(
            $this->getQueryResults($query, array($departmentIDValue)), 
            'CourseEntry');
        return Course::createArrayFromCourseEntries($courseEntryArray);     
    }    
    
    
    /**************************************************************************
     * Degrees, Plans and Programs
     **************************************************************************/
    /**
     * 
     * @param type $degreeIDValue
     * @return type
     */
    public function getFacultiesFromDegree($degreeIDValue) {
        $faculty = self::TABLE_FACULTY;
        $facultyID = self::TABLE_FACULTY_ID;
        $facultyName = self::TABLE_FACULTY_NAME;
        
        $daf = self::TABLE_DEGREE_AND_FACULTY;
        $dafDegreeID = self::TABLE_DEGREE_AND_FACULTY_DEGREE_ID;
        $dafFacultyID = self::TABLE_DEGREE_AND_FACULTY_FACULTY_ID;
        
        $query = "SELECT $faculty.* FROM $faculty JOIN (SELECT * FROM $daf WHERE $daf.$dafDegreeID = ?) AS $daf ON $faculty.$facultyID = $daf.$dafFacultyID ORDER BY $faculty.$facultyName ASC";
        return self::buildFromDBRows(
            $this->getQueryResults($query, array($degreeIDValue)), 
            'Faculty');
    }
    
    /**
     * 
     * @param type $facultyIDValue
     * @return type
     */
    public function getDegreesInFaculty($facultyIDValue) {
        $degree = self::TABLE_DEGREE;
        $degreeID = self::TABLE_DEGREE_ID;
        $degreeName = self::TABLE_DEGREE_NAME;
        
        $daf = self::TABLE_DEGREE_AND_FACULTY;
        $dafDegreeID = self::TABLE_DEGREE_AND_FACULTY_DEGREE_ID;
        $dafFacultyID = self::TABLE_DEGREE_AND_FACULTY_FACULTY_ID;
        
        $query = "SELECT $degree.* FROM $degree JOIN (SELECT * FROM $daf WHERE $daf.$dafFacultyID = ?) AS $daf ON $degree.$degreeID = $daf.$dafDegreeID ORDER BY $degree.$degreeName ASC";
        return self::buildFromDBRows(
            $this->getQueryResults($query, array($facultyIDValue)), 
            'Degree');
    }
    
    /**
     * 
     * @param type $facultyIDValue
     * @return type
     */
    public function getProgramsInFaculty($facultyIDValue) {
        $program = self::TABLE_PROGRAM;
        $programID = self::TABLE_PROGRAM_ID;
        $programName = self::TABLE_PROGRAM_NAME;                
        
        $daf = self::TABLE_DEGREE_AND_FACULTY;
        $dafDegreeID = self::TABLE_DEGREE_AND_FACULTY_DEGREE_ID;
        $dafFacultyID = self::TABLE_DEGREE_AND_FACULTY_FACULTY_ID;
        
        $pad = self::TABLE_PROGRAM_AND_DEGREE;
        $padDegreeID = self::TABLE_PROGRAM_AND_DEGREE_DEGREE_ID;
        $padProgramID = self::TABLE_PROGRAM_AND_DEGREE_PROGRAM_ID;
        
        $query = "SELECT $program.* FROM $pad JOIN (SELECT * FROM $daf WHERE $daf.$dafFacultyID = ?) AS $daf ON $pad.$padDegreeID = $daf.$dafDegreeID JOIN $program ON $program.$programID = $pad.$padProgramID ORDER BY $program.$programName ASC";
        $programArray = self::buildFromDBRows(
            $this->getQueryResults($query, array($facultyIDValue)), 
            'Program');
        Program::initializeAndSort($programArray, $this);
        
        return $programArray;
    }

    /**
     * 
     * @param type $programIDValue
     * @return type
     */
    public function getPlanForProgram($programIDValue) {
        $plan = self::TABLE_PLAN;
        $planID = self::TABLE_PLAN_ID;
        $planName = self::TABLE_PLAN_NAME;
                
        $pap = self::TABLE_PROGRAM_AND_PLAN;
        $papPlanID = self::TABLE_PROGRAM_AND_PLAN_PLAN_ID;
        $papProgramID = self::TABLE_PROGRAM_AND_PLAN_PROGRAM_ID;
        
        $query = "SELECT $plan.* FROM $plan JOIN (SELECT * FROM $pap WHERE $pap.$papProgramID = ?) AS $pap ON $pap.$papPlanID = $plan.$planID ORDER BY $plan.$planName ASC";
        $resultRow = $this->getQueryResult($query, array($programIDValue));
        
        $resultPlan = Plan::buildFromDBRow($resultRow);
        if ($resultPlan) {
            $resultPlan->initialize($this);
        }
        
        return $resultPlan;  
    }
    
    /**
     * 
     * @param type $plloIDValue
     * @return type
     */
    public function getPlansFromPLLO($plloIDValue) {
        $plan = self::TABLE_PLAN;
        $planID = self::TABLE_PLAN_ID;
        $planName = self::TABLE_PLAN_NAME;
                
        $pap = self::TABLE_PLAN_AND_PLLO;
        $papPlanID = self::TABLE_PLAN_AND_PLLO_PLAN_ID;
        $papPLLOID = self::TABLE_PLAN_AND_PLLO_PLLO_ID;
        
        $query = "SELECT $plan.* FROM $plan JOIN (SELECT * FROM $pap WHERE $pap.$papPLLOID = ?) AS $pap ON $pap.$papPlanID = $plan.$planID ORDER BY $plan.$planName ASC";
        $resultPlans = self::buildFromDBRows(
            $this->getQueryResults($query, array($plloIDValue)), 
            'Plan');
        
        qsc_core_map_member_function($resultPlans, 'initialize', array($this));
        return $resultPlans;        
    }
    
    /**
     * 
     * @param type $departmentIDValue
     * @return type
     */
    public function getPlansFromDepartment($departmentIDValue) {
        $plan = self::TABLE_PLAN;
        $planID = self::TABLE_PLAN_ID;
        $planName = self::TABLE_PLAN_NAME;
        $planType = self::TABLE_PLAN_TYPE;
        $planTypeSubPlan = self::TABLE_PLAN_TYPE_SUB_PLAN;
                
        $dap = self::TABLE_DEPARTMENT_AND_PLAN;
        $dapPlanID = self::TABLE_DEPARTMENT_AND_PLAN_PLAN_ID;
        $dapDepartmentID = self::TABLE_DEPARTMENT_AND_PLAN_DEPARTMENT_ID;
        $dapRole = self::TABLE_DEPARTMENT_AND_PLAN_ROLE;
        
        $query = "SELECT $plan.* FROM (SELECT * FROM $plan WHERE $planType <> '$planTypeSubPlan') AS $plan JOIN (SELECT * FROM $dap WHERE $dap.$dapDepartmentID = ?) AS $dap ON $dap.$dapPlanID = $plan.$planID ORDER BY $dap.$dapRole, $plan.$planName ASC";
        $resultPlans = self::buildFromDBRows(
            $this->getQueryResults($query, array($departmentIDValue)), 
            'Plan');
        
        qsc_core_map_member_function($resultPlans, 'initialize', array($this));
        return $resultPlans;
    }
    
    
    /**
     * 
     * @param type $programIDValue
     * @return type
     */
    public function getDegreeForProgram($programIDValue) {
        $degree = self::TABLE_DEGREE;
        $degreeID = self::TABLE_DEGREE_ID;
        $degreeName = self::TABLE_DEGREE_NAME;
                
        $pad = self::TABLE_PROGRAM_AND_DEGREE;
        $padDegreeID = self::TABLE_PROGRAM_AND_DEGREE_DEGREE_ID;
        $padProgramID = self::TABLE_PROGRAM_AND_DEGREE_PROGRAM_ID;
        
        $query = "SELECT $degree.* FROM $degree JOIN (SELECT * FROM $pad WHERE $pad.$padProgramID = ?) AS $pad ON $pad.$padDegreeID = $degree.$degreeID ORDER BY $degree.$degreeName ASC";
        return Degree::buildFromDBRow($this->getQueryResult($query, array($programIDValue)));
    }
    
    /**
     * 
     * @param type $degreeIDValue
     * @return type
     */
    public function getProgramsFromDegree($degreeIDValue) {
        $degree = self::TABLE_DEGREE;
        $degreeID = self::TABLE_DEGREE_ID;
        $degreeName = self::TABLE_DEGREE_NAME;

        $program = self::TABLE_PROGRAM;
        $programID = self::TABLE_PROGRAM_ID;
        $programName = self::TABLE_PROGRAM_NAME;                
        
        $pad = self::TABLE_PROGRAM_AND_DEGREE;
        $padDegreeID = self::TABLE_PROGRAM_AND_DEGREE_DEGREE_ID;
        $padProgramID = self::TABLE_PROGRAM_AND_DEGREE_PROGRAM_ID;
        
        $query = "SELECT $program.* FROM $program JOIN (SELECT * FROM $pad WHERE $pad.$padDegreeID = ?) AS $pad ON $program.$programID = $pad.$padProgramID ORDER BY $program.$programName ASC";
        $programArray = self::buildFromDBRows(
            $this->getQueryResults($query, array($degreeIDValue)), 
            'Program');
        Program::initializeAndSort($programArray, $this);
        
        return $programArray;
    }
    
    /**
     * 
     * @param type $planIDValue
     * @return type
     */
    public function getProgramsFromPlan($planIDValue) {
        $plan = self::TABLE_PLAN;
        $planID = self::TABLE_PLAN_ID;
        $planName = self::TABLE_PLAN_NAME;

        $program = self::TABLE_PROGRAM;
        $programID = self::TABLE_PROGRAM_ID;
        $programName = self::TABLE_PROGRAM_NAME;                
        
        $pap = self::TABLE_PROGRAM_AND_PLAN;
        $papPlanID = self::TABLE_PROGRAM_AND_PLAN_PLAN_ID;
        $papProgramID = self::TABLE_PROGRAM_AND_PLAN_PROGRAM_ID;
        
        $query = "SELECT $program.* FROM $program JOIN (SELECT * FROM $pap WHERE $pap.$papPlanID = ?) AS $pap ON $program.$programID = $pap.$papProgramID ORDER BY $program.$programName ASC";
        $programArray = self::buildFromDBRows(
            $this->getQueryResults($query, array($planIDValue)), 
            'Program');
        Program::initializeAndSort($programArray, $this);
        
        return $programArray;
    }
    
    /**
     * 
     * @param type $planIDValue
     * @return type
     */
    public function getDepartmentsForPlan($planIDValue) {
        $dap = self::TABLE_DEPARTMENT_AND_PLAN;
        $dapDepartmentID = self::TABLE_DEPARTMENT_AND_PLAN_DEPARTMENT_ID;
        $dapPlanID = self::TABLE_DEPARTMENT_AND_PLAN_PLAN_ID;
        $dapRole = self::TABLE_DEPARTMENT_AND_PLAN_ROLE;

        $department = self::TABLE_DEPARTMENT;
        $departmentID = self::TABLE_DEPARTMENT_ID;
        $departmentName = self::TABLE_DEPARTMENT_NAME;
        
        $query = "SELECT $department.* FROM $department JOIN (SELECT * FROM $dap WHERE $dap.$dapPlanID = ?) AS $dap ON $dap.$dapDepartmentID = $department.$departmentID ORDER BY $dap.$dapRole, $department.$departmentName ASC";
        return self::buildFromDBRows(
            $this->getQueryResults($query, array($planIDValue)), 
            'Department');
    }
    
    /**
     * 
     * @param type $planIDValue
     * @return type
     */
    public function getAdministrativeDepartmentsForPlan($planIDValue) {
        $dap = self::TABLE_DEPARTMENT_AND_PLAN;
        $dapDepartmentID = self::TABLE_DEPARTMENT_AND_PLAN_DEPARTMENT_ID;
        $dapPlanID = self::TABLE_DEPARTMENT_AND_PLAN_PLAN_ID;
        $dapRole = self::TABLE_DEPARTMENT_AND_PLAN_ROLE;
        $dapRoleAdministrator = self::TABLE_DEPARTMENT_AND_PLAN_ROLE_ADMINISTRATOR;

        $department = self::TABLE_DEPARTMENT;
        $departmentID = self::TABLE_DEPARTMENT_ID;
        $departmentName = self::TABLE_DEPARTMENT_NAME;
        
        $query = "SELECT $department.* FROM $department JOIN (SELECT * FROM $dap WHERE $dap.$dapPlanID = ? AND $dap.$dapRole = '$dapRoleAdministrator') AS $dap ON $dap.$dapDepartmentID = $department.$departmentID ORDER BY $dap.$dapRole, $department.$departmentName ASC";
        return self::buildFromDBRows(
            $this->getQueryResults($query, array($planIDValue)), 
            'Department');
    }
    
    /**
     * 
     * @param type $planIDValue
     * @return type
     */
    public function getPartnerDepartmentsForPlan($planIDValue) {
        $dap = self::TABLE_DEPARTMENT_AND_PLAN;
        $dapDepartmentID = self::TABLE_DEPARTMENT_AND_PLAN_DEPARTMENT_ID;
        $dapPlanID = self::TABLE_DEPARTMENT_AND_PLAN_PLAN_ID;
        $dapRole = self::TABLE_DEPARTMENT_AND_PLAN_ROLE;
        $dapRolePartner = self::TABLE_DEPARTMENT_AND_PLAN_ROLE_PARTNER;

        $department = self::TABLE_DEPARTMENT;
        $departmentID = self::TABLE_DEPARTMENT_ID;
        $departmentName = self::TABLE_DEPARTMENT_NAME;
        
        $query = "SELECT $department.* FROM $department JOIN (SELECT * FROM $dap WHERE $dap.$dapPlanID = ? AND $dap.$dapRole = '$dapRolePartner') AS $dap ON $dap.$dapDepartmentID = $department.$departmentID ORDER BY $dap.$dapRole, $department.$departmentName ASC";
        return self::buildFromDBRows(
            $this->getQueryResults($query, array($planIDValue)), 
            'Department');
    }        
    
    /**
     * 
     * @param type $programIDValue
     * @return type
     */
    public function getDepartmentsForProgram($programIDValue) {
        $pap = self::TABLE_PROGRAM_AND_PLAN;
        $papPlanID = self::TABLE_PROGRAM_AND_PLAN_PLAN_ID;
        $papProgramID = self::TABLE_PROGRAM_AND_PLAN_PROGRAM_ID;
        
        $dap = self::TABLE_DEPARTMENT_AND_PLAN;
        $dapDepartmentID = self::TABLE_DEPARTMENT_AND_PLAN_DEPARTMENT_ID;
        $dapPlanID = self::TABLE_DEPARTMENT_AND_PLAN_PLAN_ID;
        $dapRole = self::TABLE_DEPARTMENT_AND_PLAN_ROLE;

        $department = self::TABLE_DEPARTMENT;
        $departmentID = self::TABLE_DEPARTMENT_ID;
        $departmentName = self::TABLE_DEPARTMENT_NAME;
                                
        $query = "SELECT $department.* FROM (SELECT * FROM $pap WHERE $pap.$papProgramID = ?) AS $pap JOIN $dap AS $dap ON $pap.$papPlanID = $dap.$dapPlanID JOIN $department ON $dap.$dapDepartmentID = $department.$departmentID ORDER BY $dap.$dapRole, $department.$departmentName ASC";
        return self::buildFromDBRows(
            $this->getQueryResults($query, array($programIDValue)), 
            'Department');
    }
        
    /**
     * 
     * @param type $departmentIDValue
     * @param type $planIDValue
     * @return type
     */
    public function getRoleForDepartmentAndPlan($departmentIDValue, $planIDValue) {
        $dap = self::TABLE_DEPARTMENT_AND_PLAN;
        $dapDepartmentID = self::TABLE_DEPARTMENT_AND_PLAN_DEPARTMENT_ID;
        $dapPlanID = self::TABLE_DEPARTMENT_AND_PLAN_PLAN_ID;
        $dapRole = self::TABLE_DEPARTMENT_AND_PLAN_ROLE;

        $query = "SELECT $dapRole FROM $dap WHERE ($dapDepartmentID = ?)  AND ($dapPlanID = ?)";
        $result = $this->getQueryResult($query, array($departmentIDValue, $planIDValue));
        
        return $result[self::TABLE_DEPARTMENT_AND_PLAN_ROLE];
    }
    
    /**
     * 
     * @return type
     */
    public function getAllParentPlans() {
        $plan = self::TABLE_PLAN;
        $planType = self::TABLE_PLAN_TYPE;
        $planName = self::TABLE_PLAN_NAME;

        $planTypeSubPlan = self::TABLE_PLAN_TYPE_SUB_PLAN;
        
        $query = "SELECT * FROM $plan WHERE $planType <> '$planTypeSubPlan' ORDER BY $planName ASC";
        $queryResultArray = $this->getQueryResults($query, array());

        $planArray = array();
        foreach ($queryResultArray as $queryResult) {
            $plan = Plan::buildFromDBRow($queryResult);
            $plan->initialize($this);
            $planArray[] = $plan;
        }
        
        return $planArray;        
    }    
    
    /**
     * 
     * @param type $planIDValue
     * @return type
     */
    public function getSubPlans($planIDValue) {
        $plan = self::TABLE_PLAN;
        $planID = self::TABLE_PLAN_ID;
        $planName = self::TABLE_PLAN_NAME;

        $pap = self::TABLE_PLAN_AND_PLAN;
        $papParentID = self::TABLE_PLAN_AND_PLAN_PARENT_ID;
        $papChildID = self::TABLE_PLAN_AND_PLAN_CHILD_ID;

        $query = "SELECT $plan.* FROM $plan JOIN (SELECT * FROM $pap WHERE $pap.$papParentID = ?) AS $pap ON $plan.$planID = $pap.$papChildID ORDER BY $plan.$planName ASC";
        $queryResultArray = $this->getQueryResults($query, array($planIDValue));

        $planArray = array();
        foreach ($queryResultArray as $queryResult) {
            $plan = Plan::buildFromDBRow($queryResult);
            $plan->initialize($this);
            $planArray[] = $plan;
        }
        
        return $planArray;        
    }
    
    /**
     * 
     * @param type $planIDValue
     * @return type
     */
    public function getParentPlan($planIDValue) {
        $plan = self::TABLE_PLAN;
        $planID = self::TABLE_PLAN_ID;
        $planName = self::TABLE_PLAN_NAME;

        $pap = self::TABLE_PLAN_AND_PLAN;
        $papParentID = self::TABLE_PLAN_AND_PLAN_PARENT_ID;
        $papChildID = self::TABLE_PLAN_AND_PLAN_CHILD_ID;

        $query = "SELECT $plan.* FROM $plan JOIN (SELECT * FROM $pap WHERE $pap.$papChildID = ?) AS $pap ON $plan.$planID = $pap.$papParentID";
        $queryResult = $this->getQueryResult($query, array($planIDValue));
        
        $parentPlan = null;
        if ($queryResult) {
            $parentPlan = Plan::buildFromDBRow($queryResult);
            $parentPlan->initialize($this);
        }
        
        return $parentPlan;        
    }    

    
    /**************************************************************************
     * Plan Requiremments and Course Lists
     **************************************************************************/
    /**
     * 
     * @param type $planIDValue
     * @param type $cprTypeValue
     * @return type
     */
    public function getCPRsForPlan($planIDValue, $cprTypeValue = null) {
        $cpr = self::TABLE_CPR;
        $cprID = self::TABLE_CPR_ID;
        $cprName = self::TABLE_CPR_NAME;
        $cprType = self::TABLE_CPR_TYPE;
        
        $pac = self::TABLE_PLAN_AND_CPR;
        $pacPlanID = self::TABLE_PLAN_AND_CPR_PLAN_ID;
        $pacCPRID = self::TABLE_PLAN_AND_CPR_CPR_ID;
        
        $cprTable = $cpr;
        $valueArray = array($planIDValue);
        if ($cprTypeValue) {
            $cprTable = "(SELECT * FROM $cpr WHERE $cpr.$cprType = ?) AS $cpr";
            $valueArray = array($cprTypeValue, $planIDValue);
        }

        $query = "SELECT $cpr.* FROM $cprTable JOIN (SELECT * FROM $pac WHERE $pac.$pacPlanID = ?) AS $pac ON $cpr.$cprID = $pac.$pacCPRID ORDER BY $cpr.$cprType, $cpr.$cprName ASC";
        return self::buildFromDBRows(
            $this->getQueryResults($query, $valueArray), 
            'CoursePlanRequirement');        
    }
    
    /**
     * 
     * @param type $planIDValue
     * @param type $tprTypeValue
     * @return type
     */
    public function getTPRsForPlan($planIDValue, $tprTypeValue = null) {
        $tpr = self::TABLE_TPR;
        $tprID = self::TABLE_TPR_ID;
        $tprName = self::TABLE_TPR_NAME;
        $tprType = self::TABLE_TPR_TYPE;
        
        $pat = self::TABLE_PLAN_AND_TPR;
        $patPlanID = self::TABLE_PLAN_AND_TPR_PLAN_ID;
        $patTPRID = self::TABLE_PLAN_AND_TPR_TPR_ID;
        
        $tprTable = $tpr;
        $valueArray = array($planIDValue);
        if ($tprTypeValue) {
            $tprTable = "(SELECT * FROM $tpr WHERE $tpr.$tprType = ?) AS $tpr";
            $valueArray = array($tprTypeValue, $planIDValue);
        }

        $query = "SELECT $tpr.* FROM $tprTable JOIN (SELECT * FROM $pat WHERE $pat.$patPlanID = ?) AS $pat ON $tpr.$tprID = $pat.$patTPRID ORDER BY $tpr.$tprType, $tpr.$tprName ASC";
        return self::buildFromDBRows(
            $this->getQueryResults($query, $valueArray), 
            'TextPlanRequirement');        
    }    
    
    /**
     * 
     * @param type $planIDValue
     * @param type $cprTypeValue
     * @return type
     */
    public function getTotalUnitsInPlan($planIDValue, $cprTypeValue = null) {
        $cpr = self::TABLE_CPR;
        $cprID = self::TABLE_CPR_ID;
        $cprType = self::TABLE_CPR_TYPE;
        $cprUnits = self::TABLE_CPR_UNITS;
        
        $pac = self::TABLE_PLAN_AND_CPR;
        $pacPlanID = self::TABLE_PLAN_AND_CPR_PLAN_ID;
        $pacCPRID = self::TABLE_PLAN_AND_CPR_CPR_ID;
        
        $cprTable = $cpr;
        $valueArray = array($planIDValue);
        if ($cprTypeValue) {
            $cprTable = "(SELECT * FROM $cpr WHERE $cpr.$cprType = ?) AS $cpr";
            $valueArray = array($cprTypeValue, $planIDValue);
        }

        $query = "SELECT SUM($cpr.$cprUnits) FROM $cprTable JOIN (SELECT * FROM $pac WHERE $pac.$pacPlanID = ?) AS $pac ON $cpr.$cprID = $pac.$pacCPRID";
        $result_array = $this->getQueryResult($query, $valueArray);
        return reset($result_array);        
    }            
    
    /**
     * 
     * @param type $cprIDValue
     * @return type
     */
    public function getPlanForCPR($cprIDValue) {
        $plan = self::TABLE_PLAN;
        $planID = self::TABLE_PLAN_ID;
        $planName = self::TABLE_PLAN_NAME;
        
        $pac = self::TABLE_PLAN_AND_CPR;
        $pacPlanID = self::TABLE_PLAN_AND_CPR_PLAN_ID;
        $pacCPRID = self::TABLE_PLAN_AND_CPR_CPR_ID;
        
        $query = "SELECT $plan.* FROM $plan JOIN (SELECT * FROM $pac WHERE $pac.$pacCPRID = ?) AS $pac ON $plan.$planID = $pac.$pacPlanID ORDER BY $plan.$planName ASC";
        $resultRow = $this->getQueryResult($query, array($cprIDValue));
        
        $resultPlan = Plan::buildFromDBRow($resultRow);
        if ($resultPlan) {
            $resultPlan->initialize($this);
        }
        
        return $resultPlan;        
    }

    /**
     * 
     * @param type $tprIDValue
     * @return type
     */
    public function getPlanForTPR($tprIDValue) {
        $plan = self::TABLE_PLAN;
        $planID = self::TABLE_PLAN_ID;
        $planName = self::TABLE_PLAN_NAME;
        
        $pat = self::TABLE_PLAN_AND_TPR;
        $patPlanID = self::TABLE_PLAN_AND_TPR_PLAN_ID;
        $patTPRID = self::TABLE_PLAN_AND_TPR_TPR_ID;
        
        $query = "SELECT $plan.* FROM $plan JOIN (SELECT * FROM $pat WHERE $pat.$patTPRID = ?) AS $pat ON $plan.$planID = $pat.$patPlanID ORDER BY $plan.$planName ASC";
        $resultRow = $this->getQueryResult($query, array($tprIDValue));
        
        $resultPlan = Plan::buildFromDBRow($resultRow);
        if ($resultPlan) {
            $resultPlan->initialize($this);
        }
        
        return $resultPlan;        
    }
    
    /**
     * 
     * @param type $cprIDValue
     * @return type
     */
    public function getCourseListForCPR($cprIDValue) {
        $cl = self::TABLE_COURSELIST;
        $clID = self::TABLE_COURSELIST_ID;
        $clName = self::TABLE_COURSELIST_NAME;
        
        $cac = self::TABLE_CPR_AND_COURSELIST;
        $cacCourseListID = self::TABLE_CPR_AND_COURSELIST_COURSELIST_ID;
        $cacCPRID = self::TABLE_CPR_AND_COURSELIST_CPR_ID;
        $cacLevel = self::TABLE_CPR_AND_COURSELIST_LEVEL;
        $cacOrAbove = self::TABLE_CPR_AND_COURSELIST_OR_ABOVE;

        $query = "SELECT $cl.*, $cac.$cacLevel, $cac.$cacOrAbove FROM $cl JOIN (SELECT * FROM $cac WHERE $cac.$cacCPRID = ?) AS $cac ON $cl.$clID = $cac.$cacCourseListID ORDER BY $cl.$clName ASC";
        $queryResult = $this->getQueryResult($query, array($cprIDValue));
        if (empty($queryResult)) {
            return null;
        }
        
        $courseList = CourseList::buildFromDBRow($queryResult);
        $courseList->initialize($this, $queryResult[$cacLevel], $queryResult[$cacOrAbove]);
        return $courseList;
    }    
    
    /**
     * 
     * @param type $courseListIDValue
     * @param type $levelValue
     * @param type $orAboveValue
     * @return type
     */
    public function getCoursesInCourseList($courseListIDValue, 
        $levelValue = self::TABLE_COURSELIST_AND_COURSELIST_LEVEL_NONE, 
        $orAboveValue = false) {
        $course = self::TABLE_COURSE;
        $courseID = self::TABLE_COURSE_ID;
        $courseSubject = self::TABLE_COURSE_SUBJECT;        
        $courseNumber = self::TABLE_COURSE_NUMBER;        
        
        $cac = self::TABLE_COURSELIST_AND_COURSE;
        $cacCourseListID = self::TABLE_COURSELIST_AND_COURSE_COURSELIST_ID;
        $cacCourseID = self::TABLE_COURSELIST_AND_COURSE_COURSE_ID;
        
        $levelCondition = self::getCourseLevelCondition($levelValue, $orAboveValue);
        
        $query = "SELECT $course.* FROM $course JOIN (SELECT * FROM $cac WHERE $cac.$cacCourseListID = ?) AS $cac ON $course.$courseID = $cac.$cacCourseID"; 
        if ($levelCondition) {
            $query .= " WHERE $levelCondition";
        }        
        $query .= " ORDER BY $course.$courseSubject, $course.$courseNumber ASC";
        
        $courseEntryArray = self::buildFromDBRows(
            $this->getQueryResults($query, array($courseListIDValue)), 
            'CourseEntry');
        return Course::createArrayFromCourseEntries($courseEntryArray);     
    }

    /**
     * 
     * @param type $courseListIDValue
     * @return type
     */
    public function getChildCourseLists($courseListIDValue) {
        $cl = self::TABLE_COURSELIST;
        $clID = self::TABLE_COURSELIST_ID;
        $clName = self::TABLE_COURSELIST_NAME;

        $cac = self::TABLE_COURSELIST_AND_COURSELIST;
        $cacParentID = self::TABLE_COURSELIST_AND_COURSELIST_PARENT_ID;
        $cacChildID = self::TABLE_COURSELIST_AND_COURSELIST_CHILD_ID;
        $cacLevel = self::TABLE_COURSELIST_AND_COURSELIST_LEVEL;
        $cacOrAbove = self::TABLE_COURSELIST_AND_COURSELIST_OR_ABOVE;

        $query = "SELECT $cl.*, $cac.$cacLevel, $cac.$cacOrAbove FROM $cl JOIN (SELECT * FROM $cac WHERE $cac.$cacParentID = ?) AS $cac ON $cl.$clID = $cac.$cacChildID ORDER BY $cl.$clName ASC";
        $queryResultArray = $this->getQueryResults($query, array($courseListIDValue));

        $courseListArray = array();
        foreach ($queryResultArray as $queryResult) {
            $courseList = CourseList::buildFromDBRow($queryResult);
            $courseList->initialize($this, $queryResult[$cacLevel], $queryResult[$cacOrAbove]);
            $courseListArray[] = $courseList;
        }
        
        return $courseListArray;        
    }
    
    /**
     * 
     * @param type $courseListIDValue
     * @param type $levelValue
     * @param type $orAboveValue
     * @return type
     */
    public function getParentCourseLists($courseListIDValue, 
        $levelValue = self::TABLE_COURSELIST_AND_COURSELIST_LEVEL_NONE, 
        $orAboveValue = false) {
        $cl = self::TABLE_COURSELIST;
        $clID = self::TABLE_COURSELIST_ID;
        $clName = self::TABLE_COURSELIST_NAME;

        $cac = self::TABLE_COURSELIST_AND_COURSELIST;
        $cacParentID = self::TABLE_COURSELIST_AND_COURSELIST_PARENT_ID;
        $cacChildID = self::TABLE_COURSELIST_AND_COURSELIST_CHILD_ID;
        
        // Create a level condition based on the parameters
        $levelCondition = self::getCourseListLevelCondition($cac, $levelValue, $orAboveValue);
        $queryArguments = array_merge(array($courseListIDValue),
            self::getCourseListLevelArguments($levelValue, $orAboveValue));
       
        $query = "SELECT $cl.* FROM ($cl JOIN (SELECT * FROM $cac WHERE $cac.$cacChildID = ? $levelCondition) AS $cac ON $cl.$clID = $cac.$cacParentID) ORDER BY $cl.$clName ASC";                
        return self::buildFromDBRows(
            $this->getQueryResults($query, $queryArguments),
            'CourseList');        
    }        
    
    /**
     * 
     * @param type $parentCourseListID
     * @param type $childCourseListID
     * @return type
     */
    public function getCourseListLevel($parentCourseListID, $childCourseListID) {
        $cal = self::TABLE_COURSELIST_AND_COURSELIST_LEVEL;
        $calParentID = self::TABLE_COURSELIST_AND_COURSELIST_LEVEL_PARENT_ID;
        $calChildID = self::TABLE_COURSELIST_AND_COURSELIST_LEVEL_CHILD_ID;
        $calLevel = self::TABLE_COURSELIST_AND_COURSELIST_LEVEL_LEVEL;
        $calOrAbove = self::TABLE_COURSELIST_AND_COURSELIST_LEVEL_OR_ABOVE;

        $query = "SELECT $calLevel, $calOrAbove FROM $cal WHERE ($calParentID = ?) AND ($calChildID = ?)";
        
        return $this->getQueryResult($query, array($parentCourseListID, $childCourseListID));
    }
        
    /**
     * 
     * @param type $courseListID
     * @return type
     */
    public function getCourseListRelationship($courseListID) {
        $car = self::TABLE_COURSELIST_AND_RELATIONSHIP;
        $carListID = self::TABLE_COURSELIST_AND_RELATIONSHIP_COURSELIST_ID;
        $carRelationship = self::TABLE_COURSELIST_AND_RELATIONSHIP_RELATIONSHIP;

        $query = "SELECT $carRelationship FROM $car WHERE ($carListID = ?)";

        $query_result = $this->getQueryResult($query, array($courseListID));
                
        return (is_array($query_result) && 
            array_key_exists(self::TABLE_COURSELIST_AND_RELATIONSHIP_RELATIONSHIP, $query_result)) ?
               $query_result[self::TABLE_COURSELIST_AND_RELATIONSHIP_RELATIONSHIP] : null;
        
    }
        
    /**
     * 
     * @param type $clIDValue
     * @param type $levelValue
     * @param type $orAboveValue
     * @return type
     */
    public function getCPRsForCourseList($clIDValue, 
        $levelValue = self::TABLE_COURSELIST_AND_COURSELIST_LEVEL_NONE, 
        $orAboveValue = false) {
        $cprResultArray = array();
        
        $cpr = self::TABLE_CPR;
        $cprID = self::TABLE_CPR_ID;
        $cprName = self::TABLE_CPR_NAME;        
        
        $cac = self::TABLE_CPR_AND_COURSELIST;
        $cacCourseListID = self::TABLE_CPR_AND_COURSELIST_COURSELIST_ID;
        $cacCPRID = self::TABLE_CPR_AND_COURSELIST_CPR_ID;
        
        // Create a level condition based on the parameters
        $levelCondition = self::getCourseListLevelCondition($cac, $levelValue, $orAboveValue);
        $queryArguments = array_merge(array($clIDValue),
            self::getCourseListLevelArguments($levelValue, $orAboveValue));
        
        // Start by finding all CPRs that have this list as their direct child                
        $query = "SELECT $cpr.* FROM ($cpr JOIN (SELECT * FROM $cac WHERE $cac.$cacCourseListID = ? $levelCondition) AS $cac ON $cpr.$cprID = $cac.$cacCPRID) ORDER BY $cpr.$cprName ASC";
        $queryResultArray = $this->getQueryResults($query, $queryArguments);
                
        // Add these CPRs to the result; index by the ID to prevent duplicates
        foreach ($queryResultArray as $queryResult) {
            $cprResult = CPR::buildFromDBRow($queryResult);
            $cprResultArray[$cprResult->getDBID()] = $cprResult;
        }
                
        // Now find all CourseLists that have this list as a direct child
        $parentCourseListArray = $this->getParentCourseLists($clIDValue, $levelValue, $orAboveValue);
        
        // Go through each of these and find their CPR ancestors
        foreach ($parentCourseListArray as $parentCourseList) {
            $recursiveCPRList = $this->getCPRsForCourseList($parentCourseList->getDBID());
            
            foreach ($recursiveCPRList as $recursiveCPR) {
                $cprResultArray[$recursiveCPR->getDBID()] = $recursiveCPR;
            }            
        }
                
        return array_values($cprResultArray);
    }   
    
    /**
     * 
     * @param type $courseListID
     * @return type
     */
    public function getCourseListSubject($courseListID) {
        $cas = self::TABLE_COURSELIST_AND_SUBJECT;
        $casListID = self::TABLE_COURSELIST_AND_SUBJECT_COURSELIST_ID;
        $casSubject = self::TABLE_COURSELIST_AND_SUBJECT_SUBJECT;

        $query = "SELECT $casSubject FROM $cas WHERE ($casListID = ?)";

        $query_result = $this->getQueryResult($query, array($courseListID));
                
        return (is_array($query_result) && 
            array_key_exists(self::TABLE_COURSELIST_AND_SUBJECT_SUBJECT, $query_result)) ?
               $query_result[self::TABLE_COURSELIST_AND_SUBJECT_SUBJECT] : null;
        
    }    
            
        
    /**************************************************************************
     * Get Rows from ID
     **************************************************************************/
    /**
     * Queries the database for the single course with the given ID.
     *
     * @param $idValue   The course's ID (string or numeric)
     * @return     
     */
    public function getCourseFromID($idValue) {
        $course = self::TABLE_COURSE;
        $id = self::TABLE_COURSE_ID;
        $subject = self::TABLE_COURSE_SUBJECT;
        $number = self::TABLE_COURSE_NUMBER;
        
        $query = "SELECT * FROM $course WHERE $id = ? ORDER BY $subject, $number ASC";
        $courseEntryArray = self::buildFromDBRows(
            $this->getQueryResults($query, array($idValue)), 
            'CourseEntry');
        return new Course($courseEntryArray);    
    }

    /**
     * Queries the database for the single CLLO with the given ID.
     *
     * @param $idValue   The CLLO's ID (string or numeric)
     * @return      
     */
    public function getCLLOFromID($idValue) {
        $row = $this->getRowFromID(self::TABLE_CLLO, self::TABLE_CLLO_ID, $idValue);

        return ($row) ? CLLO::buildFromDBRow($row) : null;
    }

    /**
     * Queries the database for the single PLLO with the given ID.
     *
     * @param $idValue   The PLLO's ID (string or numeric)
     * @return 
     */
    public function getPLLOFromID($idValue) {
        $row = $this->getRowFromID(self::TABLE_PLLO, self::TABLE_PLLO_ID, $idValue);

        return ($row) ? PLLO::buildFromDBRow($row) : null;
    }

    /**
     * Queries the database for the single ILO with the given ID.
     *
     * @param $idValue   The ILO's ID (string or numeric)
     * @return  
     */
    public function getILOFromID($idValue) {
        $row = $this->getRowFromID(self::TABLE_ILO, self::TABLE_ILO_ID, $idValue);

        return ($row) ? ILO::buildFromDBRow($row) : null;
    }

    /**
     * Queries the database for the single DLE with the given ID.
     *
     * @param $idValue   The DLE's ID (string or numeric)
     * @return      
     */
    public function getDLEFromID($idValue) {
        $row = $this->getRowFromID(self::TABLE_DLE, self::TABLE_DLE_ID, $idValue);

        return ($row) ? DLE::buildFromDBRow($row) : null;
    }

    /**
     * Queries the database for the single user with the given ID.
     *
     * @param $idValue    The user's ID (string)
     * @return
     */
    public function getUserFromID($idValue) {
        $row = $this->getRowFromID(self::TABLE_USER, self::TABLE_USER_ID, $idValue);

        return ($row) ? User::buildFromDBRow($row) : null;
    }

    /**
     * Queries the database for the single revision with the given ID.
     *
     * @param $idValue   The revision's ID (string or numeric)
     * @return      
     */
    public function getRevisionFromID($idValue) {
        $row = $this->getRowFromID(self::TABLE_REVISION, self::TABLE_REVISION_ID, $idValue);

        return ($row) ? Revision::buildFromDBRow($row) : null;
    }
    
    /**
     * Queries the database for the single department with the given ID.
     *
     * @param $idValue   The department's ID (string or numeric)
     * @return    
     */
    public function getDepartmentFromID($idValue) {
        $row = $this->getRowFromID(self::TABLE_DEPARTMENT, self::TABLE_DEPARTMENT_ID, $idValue);

        return ($row) ? Department::buildFromDBRow($row) : null;
    }   
    
    /**
     * Queries the database for the single faculty with the given ID.
     *
     * @param $idValue   The faculty's ID (string or numeric)
     * @return      
     */
    public function getFacultyFromID($idValue) {
        $row = $this->getRowFromID(self::TABLE_FACULTY, self::TABLE_FACULTY_ID, $idValue);

        return ($row) ? Faculty::buildFromDBRow($row) : null;
    }
    
    /**
     * Queries the database for the single degree with the given ID.
     *
     * @param $idValue   The degree's ID (string or numeric)
     * @return      
     */
    public function getDegreeFromID($idValue) {
        $row = $this->getRowFromID(self::TABLE_DEGREE, self::TABLE_DEGREE_ID, $idValue);

        return ($row) ? Degree::buildFromDBRow($row) : null;
    }
    
    /**
     * Queries the database for the single plan with the given ID.
     *
     * @param $idValue   The plan's ID (string or numeric)
     * @return      
     */
    public function getPlanFromID($idValue) {
        $row = $this->getRowFromID(self::TABLE_PLAN, self::TABLE_PLAN_ID, $idValue);
        if (! $row) {
            return null;
        }
        
        $plan = Plan::buildFromDBRow($row);
        if ($plan) {
            $plan->initialize($this);
        }
        
        return $plan; 
    }

    /**
     * Queries the database for the single program with the given ID.
     *
     * @param $idValue   The program's ID (string or numeric)
     * @return      
     */
    public function getProgramFromID($idValue) {
        $row = $this->getRowFromID(self::TABLE_PROGRAM, self::TABLE_PROGRAM_ID, $idValue);
        $program = Program::buildFromDBRow($row);
        if ($program) {
            $program->initialize($this);
        }

        return $program;
    } 
    
    /**
     * Queries the database for the single CPR with the given ID.
     *
     * @param $idValue   The CPR's ID (string or numeric)
     * @return      
     */
    public function getCPRFromID($idValue) {
        $row = $this->getRowFromID(self::TABLE_CPR, self::TABLE_CPR_ID, $idValue);
        return CPR::buildFromDBRow($row);
    } 

    /**
     * Queries the database for the single TPR with the given ID.
     *
     * @param $idValue   The TPR's ID (string or numeric)
     * @return      
     */
    public function getTPRFromID($idValue) {
        $row = $this->getRowFromID(self::TABLE_TPR, self::TABLE_TPR_ID, $idValue);
        return TPR::buildFromDBRow($row);
    }     

    /**
     * Queries the database for the single CourseList with the given ID.
     *
     * @param $idValue   The CourseList's ID (string or numeric)
     * @return      
     */
    public function getCourseListFromID($idValue, 
        $levelValue = self::TABLE_COURSELIST_AND_COURSELIST_LEVEL_NONE, 
        $orAboveValue = false) {
        $row = $this->getRowFromID(self::TABLE_COURSELIST, self::TABLE_COURSELIST_ID, $idValue);
        if (empty($row)) {
            return null;
        }
        
        $courseList = CourseList::buildFromDBRow($row);
        $courseList->initialize($this, $levelValue, $orAboveValue);
        return $courseList;
    }     
    
    /**
     * 
     * @param type $subjectValue
     * @return type
     */
    public function subjectExists($subjectValue) {
        $course = self::TABLE_COURSE;
        $subject = self::TABLE_COURSE_SUBJECT;
        
        $query = "SELECT * FROM $course WHERE $subject = ? LIMIT 1";
        return (count($this->getQueryResults($query, array($subjectValue))) > 0);          
        
    }


    /**************************************************************************
     * Get Rows by Hierarchy
     **************************************************************************/
    /**
     * Extracts all 'top-level' entries/rows (<em>i.e.</em>, those without a
     * parent) in a table in the database.
     *
     * @param $table            The string table name
     * @param $parentColumn     The string column name for the parent ID
     * @param $orderByColumn    The string column name for ordering
     * @return                  A 2D associative array of rows without a
     *                          parent (<em>i.e.</em>, parent ID is null)
     */
    protected function getTopLevelRows($table, $parentColumn, $orderByColumn) {
        $query = "SELECT * FROM $table WHERE $parentColumn IS NULL ORDER BY $orderByColumn ASC";

        return $this->getQueryResults($query, array());
    }

    /**
     * Extracts all 'child' entries/rows with a specific parent in a table
     * in the database.
     *
     * @param $table            The string table name
     * @param $parentColumn     The string column name for the parent ID
     * @param $parentValue      The value of the parent ID (string or numeric)
     * @param $orderByColumn    The string column name for the ordering
     * @return                  A 2D associative array of rows with a parent
     *                          (<em>i.e.</em>, parent ID is not null)
     */
    protected function getChildRows($table, $parentColumn, $parentValue, $orderByColumn) {
        $query = "SELECT * FROM $table WHERE $parentColumn = ? ORDER BY $orderByColumn ASC";

        return $this->getQueryResults($query, array($parentValue));
    }

    /**
     * Finds all top-level CLLOs.
     *
     * @return      An array of CLLOs without a parent (<em>i.e.</em>, 
     *              parent ID is null)
     */
    public function getTopLevelCLLOs() {
        return self::buildFromDBRows(
            $this->getTopLevelRows(self::TABLE_CLLO, 
                self::TABLE_CLLO_PARENT_ID, self::TABLE_CLLO_NUMBER),
            'CourseLevelLearningOutcome');
    }

    /**
     * Finds all child CLLOs whose parent has the given ID.
     *
     * @param $clloIDValue   The parent's ID (string or numeric)
     * @return               An array of CLLOs with that parent
     */
    public function getChildCLLOs($clloIDValue) {
       return self::buildFromDBRows(
           $this->getChildRows(self::TABLE_CLLO, 
                self::TABLE_CLLO_PARENT_ID, $clloIDValue,
                self::TABLE_CLLO_NUMBER),
           'CourseLevelLearningOutcome');
    }

    /**
     * Finds all top-level PLLOs.
     *
     * @return      An array of PLLOs without a parent (<em>i.e.</em>, 
     *              parent ID is null)
     */
    public function getTopLevelPLLOs() {
        return self::buildFromDBRows(
            $this->getTopLevelRows(self::TABLE_PLLO, 
                self::TABLE_PLLO_PARENT_ID, self::TABLE_PLLO_NUMBER),
            'PlanLevelLearningOutcome');
    }

    /**
     * Finds all child PLLOs whose parent has the given ID.
     *
     * @param $plloIDValue   The parent's ID (string or numeric)
     * @return               An array of PLLOs with that parent
     */
    public function getChildPLLOs($plloIDValue) {
        return self::buildFromDBRows(
            $this->getChildRows(self::TABLE_PLLO, 
                self::TABLE_PLLO_PARENT_ID, $plloIDValue, 
                self::TABLE_PLLO_NUMBER),
            'PlanLevelLearningOutcome');
    }

    /**
     * Finds all top-level ILOs.
     *
     * @return      An array of ILOs without a parent (<em>i.e.</em>, 
     *              parent ID is null)
     */
    public function getTopLevelILOs() {
        return self::buildFromDBRows(
            $this->getTopLevelRows(self::TABLE_ILO, 
                self::TABLE_ILO_PARENT_ID, self::TABLE_ILO_NUMBER),
            'InstitutionLearningOutcome');
    }

    /**
     * Finds all child ILOs whose parent has the given ID.
     *
     * @param $iloIDValue   The parent's ID (string or numeric)
     * @return              An array of ILOs with that parent
     */
    public function getChildILOs($iloIDValue) {
        return self::buildFromDBRows(
            $this->getChildRows(self::TABLE_ILO, 
                self::TABLE_ILO_PARENT_ID, $iloIDValue, self::TABLE_ILO_NUMBER),
            'InstitutionLearningOutcome');
    }

    /**
     * Finds all top-level DLEs.
     *
     * @return      An array of DLEs without a parent (<em>i.e.</em>, 
     *              parent ID is null)
     */
    public function getTopLevelDLEs() {
        return self::buildFromDBRows(
            $this->getTopLevelRows(self::TABLE_DLE, 
                self::TABLE_DLE_PARENT_ID, self::TABLE_DLE_NUMBER),
            'DegreeLevelExpectation');
    }

    /**
     * Finds all child DLEs whose parent has the given ID.
     *
     * @param $dleIDValue   The parent's ID (string or numeric)
     * @return              An array of DLEs with that parent
     */
    public function getChildDLEs($dleIDValue) {
        return self::buildFromDBRows(
            $this->getChildRows(self::TABLE_DLE, 
                self::TABLE_DLE_PARENT_ID, $dleIDValue, self::TABLE_DLE_NUMBER),
            'DegreeLevelExpectation');
    }


    /**************************************************************************
     * Get Courses/CLLOs/DLEs/ILOs from Courses/CLLOs/DLEs/ILOs
     **************************************************************************/
    /**
     * Extracts the CLLOs associated with a particular course.
     *
     * @param $idValue          The id of the course (string or numeric)
     * @return                  An array of the CLLOs set for the course
     */
    public function getCLLOsForCourse($idValue) {
        $cllo = self::TABLE_CLLO;
        $clloID = self::TABLE_CLLO_ID;
        $clloNumber = self::TABLE_CLLO_NUMBER;

        $cac = self::TABLE_CLLO_AND_COURSE;
        $cacCLLOID = self::TABLE_CLLO_AND_COURSE_CLLO_ID;
        $cacCourseID = self::TABLE_CLLO_AND_COURSE_COURSE_ID;

        $query = "SELECT $cllo.* FROM $cllo JOIN (SELECT * FROM $cac WHERE $cac.$cacCourseID = ?) AS $cac ON $cllo.$clloID = $cac.$cacCLLOID ORDER BY $cllo.$clloNumber ASC";
        return self::buildFromDBRows(
            $this->getQueryResults($query, array($idValue)),
            'CourseLevelLearningOutcome');
    }

    /**
     * Extracts the course associated with a particular CLLO.
     *
     * @param $idValue      The id of the CLLO (string or numeric)
     * @return              A Course object
     */
    public function getCourseForCLLO($idValue) {
        $course = self::TABLE_COURSE;
        $courseID = self::TABLE_COURSE_ID;
        $courseSubject = self::TABLE_COURSE_SUBJECT;
        $courseNumber = self::TABLE_COURSE_NUMBER;
        
        $cac = self::TABLE_CLLO_AND_COURSE;
        $cacCLLOID = self::TABLE_CLLO_AND_COURSE_CLLO_ID;
        $cacCourseID = self::TABLE_CLLO_AND_COURSE_COURSE_ID;
        
        $query = "SELECT $course.* FROM $course JOIN (SELECT * FROM $cac WHERE $cac.$cacCLLOID = ?) AS $cac ON $course.$courseID = $cac.$cacCourseID ORDER BY $course.$courseSubject, $course.$courseNumber ASC";
        $courseEntryArray = self::buildFromDBRows(
            $this->getQueryResults($query, array($idValue)), 
            'CourseEntry');
        return new Course($courseEntryArray); 
    }

    /**
     * Determines the PLLOs associated with a set of CLLOs.
     *
     * NOTE: 'Direct' is used in the function name because it looks for a
     * direct connection in the database (<em>i.e.</em>, in the table
     * cllo_and_pllo). Indirect connections (<em>e.g.</em>, from a CLLO - ILO -
     * - PLLO) are not yet supported but may be.
     *
     * @param $clloIDValuesArray     The ids of all of the CLLOs (string or numeric)
     * @return                       An array of all the PLLOs (no duplicates) 
     *                               supported by the CLLOs
     */
    public function getDirectPLLOsForCLLOs($clloIDValuesArray) {
        $pllo = self::TABLE_PLLO;
        $plloID = self::TABLE_PLLO_ID;
        $plloNumber = self::TABLE_PLLO_NUMBER;
        
        $cap = self::TABLE_CLLO_AND_PLLO;
        $capPLLOID = self::TABLE_CLLO_AND_PLLO_PLLO_ID;
        $capCLLOID = self::TABLE_CLLO_AND_PLLO_CLLO_ID;

        $query = "SELECT DISTINCT $pllo.* FROM $pllo JOIN (SELECT * FROM $cap WHERE $cap.$capCLLOID IN ";
        $query .= self::getQuestionMarkString($clloIDValuesArray);
        $query .= ") AS $cap ON $pllo.$plloID = $cap.$capPLLOID ORDER BY $pllo.$plloNumber ASC";

        return self::buildFromDBRows(
            $this->getQueryResults($query, $clloIDValuesArray),
            'PlanLevelLearningOutcome');
    }

    /**
     * Determines the PLLOs associated with an DLE.
     *
     * NOTE: 'Direct' is used in the function name because it looks for a
     * direct connection in the database (<em>i.e.</em>, in the table
     * pllo_and_dle). Indirect connections (<em>e.g.</em>, from a PLLO - CLLO -
     * - DLE) are not yet supported but may be.
     *
     * @param $dleIDValue     The id of the DLE (string or numeric)
     * @return                An array of all the PLLOs supporting the DLE
     */
    public function getDirectPLLOsForDLE($dleIDValue) {
        $pllo = self::TABLE_PLLO;
        $plloID = self::TABLE_PLLO_ID;
        $plloNumber = self::TABLE_PLLO_NUMBER;

        $pad = self::TABLE_PLLO_AND_DLE;
        $padPLLOID = self::TABLE_PLLO_AND_DLE_PLLO_ID;
        $padDLEID = self::TABLE_PLLO_AND_DLE_DLE_ID;

        $query = "SELECT $pllo.* FROM $pllo JOIN (SELECT * FROM $pad WHERE $pad.$padDLEID = ?) AS $pad ON $pllo.$plloID = $pad.$padPLLOID ORDER BY $pllo.$plloNumber ASC";
        return self::buildFromDBRows(
            $this->getQueryResults($query, array($dleIDValue)),
            'PlanLevelLearningOutcome');
    }

    /**
     * Determines the PLLOs associated with an ILO.
     *
     * NOTE: 'Direct' is used in the function name because it looks for a
     * direct connection in the database (<em>i.e.</em>, in the table
     * pllo_and_ilo). Indirect connections (<em>e.g.</em>, from a PLLO - CLLO -
     * - ILO) are not yet supported but may be.
     *
     * @param $iloIDValue     The id of the ILO (string or numeric)
     * @return                An array of all the PLLOs supporting the ILO
     */
    public function getDirectPLLOsForILO($iloIDValue) {
        $pllo = self::TABLE_PLLO;
        $plloID = self::TABLE_PLLO_ID;
        $plloNumber = self::TABLE_PLLO_NUMBER;

        $pai = self::TABLE_PLLO_AND_ILO;
        $paiPLLOID = self::TABLE_PLLO_AND_ILO_PLLO_ID;
        $paiILOID = self::TABLE_PLLO_AND_ILO_ILO_ID;

        $query = "SELECT $pllo.* FROM $pllo JOIN (SELECT * FROM $pai WHERE $pai.$paiILOID = ?) AS $pai ON $pllo.$plloID = $pai.$paiPLLOID ORDER BY $pllo.$plloNumber ASC";
        return self::buildFromDBRows(
            $this->getQueryResults($query, array($iloIDValue)),
            'PlanLevelLearningOutcome');
    }

    /**
     * Determines the CLLOs associated with a PLLO.
     *
     * NOTE: 'Direct' is used in the function name because it looks for a
     * direct connection in the database (<em>i.e.</em>, in the table
     * pllo_and_ilo). Indirect connections (<em>e.g.</em>, from a PLLO - ILO -
     * - CLLO) are not yet supported but may be.
     *
     * @param $plloIDValue     The id of the PLLO (string or numeric)
     * @return                 An array of all the CLLOs supporting the PLLO
     */
    public function getDirectCLLOsForPLLO($plloIDValue) {
        $course = self::TABLE_COURSE;
        $courseID = self::TABLE_COURSE_ID;
        $courseSubject = self::TABLE_COURSE_SUBJECT;
        $courseNumber = self::TABLE_COURSE_NUMBER;

        $cllo = self::TABLE_CLLO;
        $clloID = self::TABLE_CLLO_ID;
        $clloNumber = self::TABLE_CLLO_NUMBER;

        $cap = self::TABLE_CLLO_AND_PLLO;
        $capPLLOID = self::TABLE_CLLO_AND_PLLO_PLLO_ID;
        $capCLLOID = self::TABLE_CLLO_AND_PLLO_CLLO_ID;

        $cac = self::TABLE_CLLO_AND_COURSE;
        $cacCLLOID = self::TABLE_CLLO_AND_COURSE_CLLO_ID;
        $cacCourseID = self::TABLE_CLLO_AND_COURSE_COURSE_ID;

        $query = "SELECT $cllo.* FROM $cllo JOIN (SELECT * FROM $cap WHERE $cap.$capPLLOID = ?) AS $cap ON $cllo.$clloID = $cap.$capCLLOID JOIN $cac ON $cllo.$clloID = $cac.$cacCLLOID JOIN $course ON $course.$courseID = $cac.$cacCourseID ORDER BY $course.$courseSubject, $course.$courseNumber, $cllo.$clloNumber ASC";
        return self::buildFromDBRows(
            $this->getQueryResults($query, array($plloIDValue)),
            'CourseLevelLearningOutcome');
    }
    
    /**
     * Determines the CLLOs associated with a PLLO.
     *
     * NOTE: 'Direct' is used in the function name because it looks for a
     * direct connection in the database (<em>i.e.</em>, in the table
     * pllo_and_ilo). Indirect connections (<em>e.g.</em>, from a PLLO - ILO -
     * - CLLO) are not yet supported but may be.
     *
     * @param $plloIDValue     The id of the PLLO (string or numeric)
     * @param type $subjectValue
     * @return                 An array of all the CLLOs supporting the PLLO
     */
    public function getDirectCLLOsForPLLOAndSubject($plloIDValue, $subjectValue) {
        $course = self::TABLE_COURSE;
        $courseID = self::TABLE_COURSE_ID;
        $courseSubject = self::TABLE_COURSE_SUBJECT;
        $courseNumber = self::TABLE_COURSE_NUMBER;

        $cllo = self::TABLE_CLLO;
        $clloID = self::TABLE_CLLO_ID;
        $clloNumber = self::TABLE_CLLO_NUMBER;

        $cap = self::TABLE_CLLO_AND_PLLO;
        $capPLLOID = self::TABLE_CLLO_AND_PLLO_PLLO_ID;
        $capCLLOID = self::TABLE_CLLO_AND_PLLO_CLLO_ID;

        $cac = self::TABLE_CLLO_AND_COURSE;
        $cacCLLOID = self::TABLE_CLLO_AND_COURSE_CLLO_ID;
        $cacCourseID = self::TABLE_CLLO_AND_COURSE_COURSE_ID;

        $query = "SELECT $cllo.* FROM $cllo JOIN (SELECT * FROM $cap WHERE $cap.$capPLLOID = ?) AS $cap ON $cllo.$clloID = $cap.$capCLLOID JOIN $cac ON $cllo.$clloID = $cac.$cacCLLOID JOIN (SELECT * FROM $course WHERE $course.$courseSubject = ?) AS $course ON $course.$courseID = $cac.$cacCourseID ORDER BY $course.$courseSubject, $course.$courseNumber, $cllo.$clloNumber ASC";
        return self::buildFromDBRows(
            $this->getQueryResults($query, array($plloIDValue, $subjectValue)),
            'CourseLevelLearningOutcome');
    }    

    /**
     * Determines the CLLOs associated with an ILO.
     *
     * NOTE: 'Direct' is used in the function name because it looks for a
     * direct connection in the database (<em>i.e.</em>, in the table
     * cllo_and_ilo). Indirect connections (<em>e.g.</em>, from a ILO - PLLO
     * - CLLO) are not yet supported but may be.
     *
     * @param $iloIDValue     The id of the ILO (string or numeric)
     * @return                An array of all the CLLOs supporting the ILO
     */
    public function getDirectCLLOsForILO($iloIDValue) {
        $course = self::TABLE_COURSE;
        $courseID = self::TABLE_COURSE_ID;
        $courseSubject = self::TABLE_COURSE_SUBJECT;
        $courseNumber = self::TABLE_COURSE_NUMBER;

        $cllo = self::TABLE_CLLO;
        $clloID = self::TABLE_CLLO_ID;
        $clloNumber = self::TABLE_CLLO_NUMBER;

        $cai = self::TABLE_CLLO_AND_ILO;
        $caiILOID = self::TABLE_CLLO_AND_ILO_ILO_ID;
        $caiCLLOID = self::TABLE_CLLO_AND_ILO_CLLO_ID;

        $cac = self::TABLE_CLLO_AND_COURSE;
        $cacCLLOID = self::TABLE_CLLO_AND_COURSE_CLLO_ID;
        $cacCourseID = self::TABLE_CLLO_AND_COURSE_COURSE_ID;

        $query = "SELECT $cllo.* FROM $cllo JOIN (SELECT * FROM $cai WHERE $cai.$caiILOID = ?) AS $cai ON $cllo.$clloID = $cai.$caiCLLOID JOIN $cac ON $cllo.$clloID = $cac.$cacCLLOID JOIN $course ON $course.$courseID = $cac.$cacCourseID ORDER BY $course.$courseSubject, $course.$courseNumber, $cllo.$clloNumber ASC";
        return self::buildFromDBRows(
            $this->getQueryResults($query, array($iloIDValue)),
            'CourseLevelLearningOutcome');
    }

    /**
     * Determines the ILOs associated with a set of CLLOs.
     *
     * NOTE: 'Direct' is used in the function name because it looks for a
     * direct connection in the database (<em>i.e.</em>, in the table
     * cllo_and_ilo). Indirect connections (<em>e.g.</em>, from a CLLO - PLLO -
     * - ILO) are not yet supported but may be.
     *
     * @param $clloIDValuesArray     The ids of all of the CLLOs (string or numeric)
     * @return                       An array of all the ILOs (no duplicates) supported by the CLLOs
     */
    public function getDirectILOsForCLLOs($clloIDValuesArray) {
        $ilo = self::TABLE_ILO;
        $iloID = self::TABLE_ILO_ID;
        $iloNumber = self::TABLE_ILO_NUMBER;

        $cai = self::TABLE_CLLO_AND_ILO;
        $caiILOID = self::TABLE_CLLO_AND_ILO_ILO_ID;
        $caiCLLOID = self::TABLE_CLLO_AND_ILO_CLLO_ID;

        $query = "SELECT DISTINCT $ilo.* FROM $ilo JOIN (SELECT * FROM $cai WHERE $cai.$caiCLLOID IN ";
        $query .= self::getQuestionMarkString($clloIDValuesArray);
        $query .= ") AS $cai ON $ilo.$iloID = $cai.$caiILOID ORDER BY $ilo.$iloNumber ASC";
        return self::buildFromDBRows(
            $this->getQueryResults($query, $clloIDValuesArray),
            'InstitutionLearningOutcome');
    }

    /**
     * Determines the ILOs associated with an PLLO.
     *
     * NOTE: 'Direct' is used in the function name because it looks for a
     * direct connection in the database (<em>i.e.</em>, in the table
     * pllo_and_ilo). Indirect connections (<em>e.g.</em>, from a CLLO - ILO
     * - PLLO) are not yet supported but may be.
     *
     * @param $plloIDValue     The id of the PLLO (string or numeric)
     * @return                 An array of all the ILOs supported by the PLLO
     */
    public function getDirectILOsForPLLO($plloIDValue) {
        $ilo = self::TABLE_ILO;
        $iloID = self::TABLE_ILO_ID;
        $iloNumber = self::TABLE_ILO_NUMBER;

        $pai = self::TABLE_PLLO_AND_ILO;
        $paiILOID = self::TABLE_PLLO_AND_ILO_ILO_ID;
        $paiPLLOID = self::TABLE_PLLO_AND_ILO_PLLO_ID;

        $query = "SELECT $ilo.* FROM $ilo JOIN (SELECT * FROM $pai WHERE $pai.$paiPLLOID = ?) AS $pai ON $ilo.$iloID = $pai.$paiILOID ORDER BY $ilo.$iloNumber ASC";
        return self::buildFromDBRows(
            $this->getQueryResults($query, array($plloIDValue)),
            'InstitutionLearningOutcome');
    }

    /**
     * Extracts the PLLOs associated with an DLE.
     *
     * @param $idValue     The id of the DLE (string or numeric)
     * @return             A array of all the PLLOs (no duplicates) supporting 
     *                     the DLE
     */
    public function getPLLOsForDLE($idValue) {
        $pllo = self::TABLE_PLLO;
        $plloID = self::TABLE_PLLO_ID;
        $plloNumber = self::TABLE_PLLO_NUMBER;

        $pad = self::TABLE_PLLO_AND_DLE;
        $padDLEID = self::TABLE_PLLO_AND_DLE_DLE_ID;
        $padPLLOID = self::TABLE_PLLO_AND_DLE_PLLO_ID;

        $dle = self::TABLE_DLE;
        $dleID = self::TABLE_DLE_ID;

        $query = "SELECT DISTINCT $pllo.* FROM $pllo JOIN (SELECT * FROM $pad WHERE $pad.$padDLEID = ?) AS $pad ON $pllo.$plloID = $pad.$padPLLOID JOIN $dle ON $dle.$dleID = $pad.$padDLEID ORDER BY $pllo.$plloNumber ASC";
        return self::buildFromDBRows(
            $this->getQueryResults($query, array($idValue)),
            'PlanLevelLearningOutcome');
    }
    
    
    /**
     * 
     * @param type $dleIDValue
     * @param type $departmentIDValue
     * @return type
     */
    public function getPLLOsForDLEAndDepartment($dleIDValue, $departmentIDValue) {
        $pllo = self::TABLE_PLLO;
        $plloID = self::TABLE_PLLO_ID;
        $plloNumber = self::TABLE_PLLO_NUMBER;
        
        $department = self::TABLE_DEPARTMENT;
        $departmentID = self::TABLE_DEPARTMENT_ID;
        $departmentName = self::TABLE_DEPARTMENT_NAME;        
                
        $das = self::TABLE_DEPARTMENT_AND_SUBJECT;
        $dasDepartmentID = self::TABLE_DEPARTMENT_AND_SUBJECT_DEPARTMENT_ID;
        $dasSubject = self::TABLE_DEPARTMENT_AND_SUBJECT_SUBJECT;
        
        $course = self::TABLE_COURSE;
        $courseID = self::TABLE_COURSE_ID;
        $courseSubject = self::TABLE_COURSE_SUBJECT;
        
        $cac = self::TABLE_CLLO_AND_COURSE;
        $cacCLLOID = self::TABLE_CLLO_AND_COURSE_CLLO_ID;
        $cacCourseID = self::TABLE_CLLO_AND_COURSE_COURSE_ID;
        
        $cap = self::TABLE_CLLO_AND_PLLO;
        $capPLLOID = self::TABLE_CLLO_AND_PLLO_PLLO_ID;
        $capCLLOID = self::TABLE_CLLO_AND_PLLO_CLLO_ID;
               
        $pad = self::TABLE_PLLO_AND_DLE;
        $padDLEID = self::TABLE_PLLO_AND_DLE_DLE_ID;
        $padPLLOID = self::TABLE_PLLO_AND_DLE_PLLO_ID;
                
        $query = "SELECT $pllo.* FROM (SELECT DISTINCT $pllo.* FROM (SELECT * FROM $department WHERE $department.$departmentID = ?) AS $department JOIN department_and_subject ON $department.$departmentID = $das.$dasDepartmentID JOIN $course ON $das.$dasSubject = $course.$courseSubject JOIN $cac ON $course.$courseID = $cac.$cacCourseID JOIN $cap ON $cac.$cacCLLOID = $cap.$capCLLOID JOIN $pllo ON $cap.$capPLLOID = $pllo.$plloID) AS $pllo JOIN (SELECT * FROM $pad WHERE $pad.$padDLEID = ?) AS $pad ON $pllo.$plloID = $pad.$padPLLOID ORDER BY $pllo.$plloNumber ASC";
        return self::buildFromDBRows(
            $this->getQueryResults($query, array($departmentIDValue, $dleIDValue)),
            'PlanLevelLearningOutcome');
    }
    
    /**
     * 
     * @param type $dleIDValue
     * @param type $subjectValue
     * @return type
     */
    public function getPLLOsForDLEAndSubject($dleIDValue, $subjectValue) {
        $pllo = self::TABLE_PLLO;
        $plloID = self::TABLE_PLLO_ID;
        $plloNumber = self::TABLE_PLLO_NUMBER;
                
        $course = self::TABLE_COURSE;
        $courseID = self::TABLE_COURSE_ID;
        $courseSubject = self::TABLE_COURSE_SUBJECT;
        
        $cac = self::TABLE_CLLO_AND_COURSE;
        $cacCLLOID = self::TABLE_CLLO_AND_COURSE_CLLO_ID;
        $cacCourseID = self::TABLE_CLLO_AND_COURSE_COURSE_ID;
        
        $cap = self::TABLE_CLLO_AND_PLLO;
        $capPLLOID = self::TABLE_CLLO_AND_PLLO_PLLO_ID;
        $capCLLOID = self::TABLE_CLLO_AND_PLLO_CLLO_ID;
               
        $pad = self::TABLE_PLLO_AND_DLE;
        $padDLEID = self::TABLE_PLLO_AND_DLE_DLE_ID;
        $padPLLOID = self::TABLE_PLLO_AND_DLE_PLLO_ID;
              
        $query = "SELECT DISTINCT $pllo.* FROM (SELECT * FROM course WHERE $course.$courseSubject = ?) AS $course JOIN $cac ON $course.$courseID = $cac.$cacCourseID JOIN $cap ON $cac.$cacCLLOID = $cap.$capCLLOID JOIN $pllo ON $cap.$capPLLOID = $pllo.$plloID JOIN (SELECT * FROM $pad WHERE $pad.$padDLEID = ?) AS $pad ON $pllo.$plloID = $pad.$padPLLOID ORDER BY $pllo.$plloNumber ASC";
        return self::buildFromDBRows(
            $this->getQueryResults($query, array($subjectValue, $dleIDValue)),
            'PlanLevelLearningOutcome');
    }    
    

    /**
     * Extracts the DLE associated with a PLLO.
     *
     * @param $idValue     The id of the PLLO (string or numeric)
     * @return             The DLE
     */
    public function getDLEForPLLO($idValue) {
        $pllo = self::TABLE_PLLO;
        $plloID = self::TABLE_PLLO_ID;
        $plloParentID = self::TABLE_PLLO_PARENT_ID;

        $pad = self::TABLE_PLLO_AND_DLE;
        $padDLEID = self::TABLE_PLLO_AND_DLE_DLE_ID;
        $padPLLOID = self::TABLE_PLLO_AND_DLE_PLLO_ID;

        $dle = self::TABLE_DLE;
        $dleID = self::TABLE_DLE_ID;
        $dleNumber = self::TABLE_DLE_NUMBER;

        $query = "SELECT $dle.* FROM $dle JOIN (SELECT $pad.* FROM $pad JOIN (SELECT * FROM $pllo WHERE $pllo.$plloID = ? OR $pllo.$plloParentID = ?) AS $pllo ON $pad.$padPLLOID = $pllo.$plloID) AS $pad ON $dle.$dleID = $pad.$padDLEID ORDER BY $dle.$dleNumber ASC";
        $result = $this->getQueryResult($query, array($idValue, $idValue));        
        return $result ? DLE::buildFromDBRow($result) : null;
    }


    /**************************************************************************
     * Login
     **************************************************************************/
    /**
     * Records information about a user logging into the system.
     *
     * @param $userID             The string ID
     * @param $userLoginTime      The string date/time representation, defined
     *                            in qsc-core/constants.php as QSC_CORE_DATE_AND_TIME_FORMAT
     */
    public function recordUserLogin($userID, $userLoginTime) {
        $query = "INSERT INTO ".self::TABLE_USER_ACCESS." (";
        $query .= self::TABLE_USER_ACCESS_USER_ID.",";
        $query .= self::TABLE_USER_ACCESS_LOGGED_IN;
        $query .= ") VALUES(?,?)";

        return $this->performQuery($query, array($userID, $userLoginTime));
    }


    /**************************************************************************
     * Searching
     **************************************************************************/
    /**
     * Searches in the database for courses whose subject or number contain the
     * given string.
     *
     * @param $searchString    The substring to look for
     * @return                 An array of all the matching courses
     */
    public function findMatchingCourses($searchString) {
        $course = self::TABLE_COURSE;
        $courseID = self::TABLE_COURSE_ID;
        $courseSubject = self::TABLE_COURSE_SUBJECT;
        $courseNumber = self::TABLE_COURSE_NUMBER;
        $searchWords = preg_split('/[\s]/', $searchString, null, PREG_SPLIT_NO_EMPTY);
        $valueArray = array();
        $connector = 'OR';
        
        if (count($searchWords) == 1) {
            $like_term = "%{$searchWords[0]}%";
            $valueArray = array($like_term, $like_term);
        }
        else {
            $like_subject = "%{$searchWords[0]}%";
            $like_number = "%{$searchWords[1]}%";
            $valueArray = array($like_subject, $like_number);
            $connector = 'AND';
        }        

        $query = "SELECT * FROM $course WHERE ($courseSubject LIKE ?) $connector ($courseNumber LIKE ?) ORDER BY {$courseSubject}, $courseNumber ASC";                    
        
        $courseEntryArray = self::buildFromDBRows(
            $this->getQueryResults($query, $valueArray),
            'CourseEntry');
        return Course::createArrayFromCourseEntries($courseEntryArray);
       
    }

    /**
     * Searches in the database for CLLOs whose number, text or notes contain the
     * given string.
     *
     * @param $searchString        The substring to look for
     * @return                     An array of all the matching CLLOs
     */
    public function findMatchingCLLOs($searchString) {
        $cllo = self::TABLE_CLLO;
        $clloNumber = self::TABLE_CLLO_NUMBER;
        $clloText = self::TABLE_CLLO_TEXT;
        $clloNotes = self::TABLE_CLLO_NOTES;
        $likeString = "%{$searchString}%";

        $query = "SELECT * FROM $cllo WHERE ($clloNumber LIKE ?) OR ($clloText LIKE ?) OR ($clloNotes LIKE ?) ORDER BY $clloNumber ASC";
        return self::buildFromDBRows(
            $this->getQueryResults($query, array($likeString, $likeString, $likeString)),
            'CourseLevelLearningOutcome');
    }

    /**
     * Searches in the database for PLLOs whose number, text or notes contain the
     * given string.
     *
     * @param $searchString        The substring to look for
     * @return                     An array of all the matching PLLOs
     */
    public function findMatchingPLLOs($searchString) {
        $pllo = self::TABLE_PLLO;
        $plloNumber = self::TABLE_PLLO_NUMBER;
        $plloText = self::TABLE_PLLO_TEXT;
        $plloNotes = self::TABLE_PLLO_NOTES;
        $likeString = "%{$searchString}%";

        $query = "SELECT * FROM $pllo WHERE (($plloNumber LIKE ?) OR ($plloText LIKE ?) OR ($plloNotes LIKE ?)) ORDER BY $plloNumber ASC";
        return self::buildFromDBRows(
            $this->getQueryResults($query, array($likeString, $likeString, $likeString)),
            'PlanLevelLearningOutcome');
    }

    /**
     * Searches in the database for ILOs whose number, text, notes or
     * description contain the given string.
     *
     * @param $searchString        The substring to look for
     * @return                     An array of all the matching ILOs
     */
    public function findMatchingILOs($searchString) {
        $ilo = self::TABLE_ILO;
        $iloNumber = self::TABLE_ILO_NUMBER;
        $iloText = self::TABLE_ILO_TEXT;
        $iloDescription = self::TABLE_ILO_DESCRIPTION;
        $iloNotes = self::TABLE_ILO_NOTES;
        $likeString = "%{$searchString}%";

        $query = "SELECT * FROM $ilo WHERE (($iloNumber LIKE ?) OR ($iloText LIKE ?) OR ($iloDescription LIKE ?) OR ($iloNotes LIKE ?)) ORDER BY $iloNumber ASC";
        return self::buildFromDBRows(
            $this->getQueryResults($query, array($likeString, $likeString, $likeString, $likeString)),
            'InstitutionLearningOutcome');
    }

    /**
     * Searches in the database for DLEs whose number, text or notes contain the
     * given string.
     *
     * @param $searchString        The substring to look for
     * @return                     An array of all the matching DLEs
     */
    public function findMatchingDLEs($searchString) {
        $dle = self::TABLE_DLE;
        $dleNumber = self::TABLE_DLE_NUMBER;
        $dleText = self::TABLE_DLE_TEXT;
        $dleNotes = self::TABLE_DLE_NOTES;
        $likeString = "%{$searchString}%";

        $query = "SELECT * FROM $dle WHERE";
        $query .= " (($dleNumber LIKE ?) OR ($dleText LIKE ?) OR ($dleNotes LIKE ?)) ORDER BY $dleNumber ASC";

        return self::buildFromDBRows(
            $this->getQueryResults($query, array($likeString, $likeString, $likeString)),
            'DegreeLevelExpectation');
    }

    /**
     * Searches in the database for revisions whose table, column or prior
     * value contain the given string.
     *
     * @param $searchString        The substring to look for
     * @return                     An array of all the matching revisions
     */
    public function findMatchingRevisions($searchString) {
        $revision = self::TABLE_REVISION;
        $revisionPrior = self::TABLE_REVISION_PRIOR_VALUE;
        $revisionDate = self::TABLE_REVISION_DATE_AND_TIME;
        $likeString = "%{$searchString}%";

        $query = "SELECT * FROM $revision WHERE (($revisionPrior LIKE ?) OR ($revisionDate LIKE ?)) ORDER BY $revisionDate DESC";
        return self::buildFromDBRows(
            $this->getQueryResults($query, array($likeString, $likeString)),
            'Revision');
    }
    
    /**
     * Searches in the database for faculties whose name contains the given 
     * string.
     *
     * @param $searchString        The substring to look for
     * @return                     An array of all the matching Faculties
     */
    public function findMatchingFaculties($searchString) {
        $faculty = self::TABLE_FACULTY;
        $facultyName = self::TABLE_FACULTY_NAME;
        $likeString = "%{$searchString}%";

        $query = "SELECT * FROM $faculty WHERE ($facultyName LIKE ?) ORDER BY $facultyName ASC";
        return self::buildFromDBRows(
            $this->getQueryResults($query, array($likeString)),
            'Faculty');
    }

    /**
     * Searches in the database for departments whose name contains the given 
     * string.
     *
     * @param $searchString        The substring to look for
     * @return                     An array of all the matching Departments
     */
    public function findMatchingDepartments($searchString) {
        $department = self::TABLE_DEPARTMENT;
        $departmentName = self::TABLE_DEPARTMENT_NAME;
        $likeString = "%{$searchString}%";

        $query = "SELECT * FROM $department WHERE ($departmentName LIKE ?) ORDER BY $departmentName ASC";
        return self::buildFromDBRows(
            $this->getQueryResults($query, array($likeString)),
            'Department');
    }

    /**
     * Searches in the database for degrees whose name or code contain the
     * given string.
     *
     * @param $searchString        The substring to look for
     * @return                     An array of all the matching Degrees
     */
    public function findMatchingDegrees($searchString) {
        $degree = self::TABLE_DEGREE;
        $degreeName = self::TABLE_DEGREE_NAME;
        $degreeCode = self::TABLE_DEGREE_CODE;
        $likeString = "%{$searchString}%";

        $query = "SELECT * FROM $degree WHERE ($degreeName LIKE ?) OR ($degreeCode LIKE ?) ORDER BY $degreeName ASC";
        return self::buildFromDBRows(
            $this->getQueryResults($query, array($likeString, $likeString)),
            'Degree');
    }

    /**
     * Searches in the database for plans whose name or code contain the
     * given string.
     *
     * @param $searchString        The substring to look for
     * @return                     An array of all the matching Plans
     */
    public function findMatchingPlans($searchString) {
        $plan = self::TABLE_PLAN;
        $planName = self::TABLE_PLAN_NAME;
        $planCode = self::TABLE_PLAN_CODE;
        $planType = self::TABLE_PLAN_TYPE;
        $planText = self::TABLE_PLAN_TEXT;
        $planNotes = self::TABLE_PLAN_NOTES;
        $likeString = "%{$searchString}%";

        $query = "SELECT * FROM $plan WHERE ($planName LIKE ?) OR ($planCode LIKE ?) OR ($planType LIKE ?) OR ($planText LIKE ?) OR ($planNotes LIKE ?) ORDER BY $planName ASC";
        return self::buildFromDBRows(
            $this->getQueryResults($query, array($likeString, $likeString, $likeString, $likeString, $likeString)),
            'Plan');
    }

    /**
     * Searches in the database for programs whose name or code contain the
     * given string.
     *
     * @param $searchString        The substring to look for
     * @return                     An array of all the matching Programs
     */
    public function findMatchingPrograms($searchString) {
        $program = self::TABLE_PROGRAM;
        $programName = self::TABLE_PROGRAM_NAME;
        $programType = self::TABLE_PROGRAM_TYPE;
        $programText = self::TABLE_PROGRAM_TEXT;
        $programNotes = self::TABLE_PROGRAM_NOTES;
        $likeString = "%{$searchString}%";

        $query = "SELECT * FROM $program WHERE ($programName LIKE ?) OR ($programType LIKE ?) OR ($programText LIKE ?) OR ($programNotes LIKE ?) ORDER BY $programName ASC";
        $programArray = self::buildFromDBRows(
            $this->getQueryResults($query, array($likeString, $likeString, $likeString, $likeString)),
            'Program');
        return Program::initializeAndSort($programArray, $this);

    }        


    /**************************************************************************
     * Get All of Something
     **************************************************************************/
    /**
     * Extracts all rows from some table in the database.
     *
     * @param $tableName          The string name of the table
     * @param $idColumn           The string name of the ID column
     * @param $orderColumn        The string name of the column to order by
     * @param $includeIDArray     A list of IDs to include
     * @param $excludeIDArray     A list of IDs to exclude
     * @return                    A 2D associative array of all the resulting
     *                            rows
     */
    protected function getAllRows($tableName, $idColumn, $orderColumn, $includeIDArray = array(), $excludeIDArray = array(), $orderBy = "ASC") {
        $hasInclude = false;
        $query = "SELECT * FROM $tableName";

        if (! empty($includeIDArray)) {
            $query .= " WHERE $idColumn IN ";
            $query .= self::getQuestionMarkString($includeIDArray);
            $hasInclude = true;
        }
        if (! empty($excludeIDArray)) {
            $query .= ($hasInclude) ? " AND" : " WHERE";
            $query .= " $idColumn NOT IN ";
            $query .= self::getQuestionMarkString($excludeIDArray);
        }

        $query .= " ORDER BY $orderColumn $orderBy";
        return $this->getQueryResults($query,
            array_merge($includeIDArray, $excludeIDArray));
    }

    /**
     * Extracts all of the PLLOs in the database.
     *
     * @param $excludeIDArray     A list of PLLO IDs to exclude (default value
     *                            is the empty array)
     * @return                    An array of all the resulting PLLOs
     */
    public function getAllPLLOs($excludeIDArray = array()) {
        return self::buildFromDBRows(
            $this->getAllRows(self::TABLE_PLLO, 
                    self::TABLE_PLLO_ID, self::TABLE_PLLO_NUMBER, 
                    array(), $excludeIDArray),
            'PlanLevelLearningOutcome');
    }

    /**
     * Extracts all of the ILOs in the database.
     *
     * @param $excludeIDArray     A list of ILO IDs to exclude (default value
     *                            is the empty array)
     * @return                    An array of all the resulting ILOs
     */
    public function getAllILOs($excludeIDArray = array()) {
        return self::buildFromDBRows(
            $this->getAllRows(self::TABLE_ILO, 
                self::TABLE_ILO_ID, self::TABLE_ILO_NUMBER, 
                array(), $excludeIDArray),
            'InstitutionLearningOutcome');
    }

    /**
     * Extracts all of the DLEs in the database.
     *
     * @param $excludeIDArray     A list of DLE IDs to exclude (default value
     *                            is the empty array)
     * @return                    An array of all the resulting DLEs
     */
    public function getAllDLEs($excludeIDArray = array()) {
        return self::buildFromDBRows(
            $this->getAllRows(self::TABLE_DLE, 
                self::TABLE_DLE_ID, self::TABLE_DLE_NUMBER, 
                array(), $excludeIDArray),
            'DegreeLevelExpectation');
    }

    /**
     * 
     * @param type $clloID
     * @return type
     */
    public function getCLLOsAndPLLOsForCLLO($clloID) {
        return self::buildFromDBRows(
            $this->getAllRows(self::TABLE_CLLO_AND_PLLO, 
                self::TABLE_CLLO_AND_PLLO_CLLO_ID, 
                self::TABLE_CLLO_AND_PLLO_CLLO_ID, array($clloID)),
            'CLLOAndPLLO');
    }

    /**
     * 
     * @param type $clloID
     * @return type
     */
    public function getCLLOsAndILOsForCLLO($clloID) {
        return self::buildFromDBRows(
            $this->getAllRows(self::TABLE_CLLO_AND_ILO, 
                self::TABLE_CLLO_AND_ILO_CLLO_ID, 
                self::TABLE_CLLO_AND_ILO_CLLO_ID, array($clloID)),
            'CLLOAndILO');
    }

    /**
     * 
     * @param type $plloID
     * @return type
     */
    public function getPLLOsAndILOsForPLLO($plloID) {
        return self::buildFromDBRows(
            $this->getAllRows(self::TABLE_PLLO_AND_ILO, 
                self::TABLE_PLLO_AND_ILO_PLLO_ID, 
                self::TABLE_PLLO_AND_ILO_PLLO_ID, array($plloID)),
            'PLLOAndILO');
    }

    /**
     * Extracts all of the Revisions in the database.
     *
     * @return      An array of all the resulting Revisions
     */
    public function getAllRevisions() {
        return self::buildFromDBRows(
            $this->getAllRows(self::TABLE_REVISION, 
                self::TABLE_REVISION_ID, self::TABLE_REVISION_DATE_AND_TIME, 
                array(), array(), "DESC"),
            'Revision');
    }
        
    /**
     * Extracts all of the faculties in the database.
     *
     * @param $excludeIDArray     A list of faculty IDs to exclude (default value
     *                            is the empty array)
     * @return                    An array of all the resulting Faculties
     */
    public function getAllFaculties($excludeIDArray = array()) {
        return self::buildFromDBRows(
            $this->getAllRows(self::TABLE_FACULTY, 
                self::TABLE_FACULTY_ID, self::TABLE_FACULTY_NAME, 
                array(), $excludeIDArray),
            'Faculty');
    }
    
    /**
     * Extracts all of the degrees in the database.
     *
     * @param $excludeIDArray     A list of degree IDs to exclude (default value
     *                            is the empty array)
     * @return                    An array of all the resulting Degrees
     */
    public function getAllDegrees($excludeIDArray = array()) {
        return self::buildFromDBRows(
            $this->getAllRows(self::TABLE_DEGREE, 
                self::TABLE_DEGREE_ID, self::TABLE_DEGREE_NAME, 
                array(), $excludeIDArray),
            'Degree');
    }    

    /**
     * Extracts all of the programs in the database.
     *
     * @param $excludeIDArray     A list of program IDs to exclude (default value
     *                            is the empty array)
     * @return                    An array of all the resulting Programs
     */
    public function getAllPrograms($excludeIDArray = array()) {
        $programArray = self::buildFromDBRows(
            $this->getAllRows(self::TABLE_PROGRAM, 
                self::TABLE_PROGRAM_ID, self::TABLE_PROGRAM_NAME, 
                array(), $excludeIDArray),
            'Program');
        Program::initializeAndSort($programArray, $this);
        
        return $programArray;
    }    

    /**
     * Extracts all of the plans in the database.
     *
     * @param $excludeIDArray     A list of plan IDs to exclude (default value
     *                            is the empty array)
     * @return                    An array of all the resulting Plans
     */
    public function getAllPlans($excludeIDArray = array()) {
        $programArray = self::buildFromDBRows(
            $this->getAllRows(self::TABLE_PLAN, 
                self::TABLE_PLAN_ID, self::TABLE_PLAN_NAME, 
                array(), $excludeIDArray),
            'Plan');
        Plan::initializeAndSort($programArray, $this);
        
        return $programArray;
    }    
    
    
    /**************************************************************************
     * Get Faculty/Department/Courses for Department/Course/Faculty
     **************************************************************************/
    /**
     * 
     * @param type $facultyIDValue
     * @return type
     */
    function getDepartmentsInFaculty($facultyIDValue) {
        $faculty = self::TABLE_FACULTY;
        $facultyID = self::TABLE_FACULTY_ID;

        $department = self::TABLE_DEPARTMENT;
        $departmentID = self::TABLE_DEPARTMENT_ID;
        $departmentName = self::TABLE_DEPARTMENT_NAME;

        $daf = self::TABLE_DEPARTMENT_AND_FACULTY;
        $dafDepartmentID = self::TABLE_DEPARTMENT_AND_FACULTY_DEPARTMENT_ID;
        $dafFacultyID = self::TABLE_DEPARTMENT_AND_FACULTY_FACULTY_ID;
        
        $query = "SELECT $department.* FROM ((SELECT * FROM $faculty WHERE $faculty.$facultyID = ?) AS $faculty JOIN $daf ON $faculty.$facultyID = $daf.$dafFacultyID JOIN $department ON $daf.$dafDepartmentID = $department.$departmentID) ORDER BY $department.$departmentName ASC";        
        return self::buildFromDBRows(
            $this->getQueryResults($query, array($facultyIDValue)),
            'Department');        
    }
    
    /**
     * 
     * @param type $departmentIDValue
     * @param type $excludeFacultyIDValue
     * @return type
     */
    function getFacultiesFromDepartment($departmentIDValue, $excludeFacultyIDValue = 0) {
        $faculty = self::TABLE_FACULTY;
        $facultyID = self::TABLE_FACULTY_ID;
        $facultyName = self::TABLE_FACULTY_NAME;

        $department = self::TABLE_DEPARTMENT;
        $departmentID = self::TABLE_DEPARTMENT_ID;

        $daf = self::TABLE_DEPARTMENT_AND_FACULTY;
        $dafDepartmentID = self::TABLE_DEPARTMENT_AND_FACULTY_DEPARTMENT_ID;
        $dafFacultyID = self::TABLE_DEPARTMENT_AND_FACULTY_FACULTY_ID;

        $valueArray = array($departmentIDValue);
        
        $query = "SELECT $faculty.* FROM (SELECT * FROM $department WHERE $department.$departmentID = ?) AS $department JOIN $daf ON $department.$departmentID = $daf.$dafDepartmentID JOIN ";
        if ($excludeFacultyIDValue) {
            $query .= "(SELECT * FROM $faculty WHERE $faculty.$facultyID <> ?) AS";
            $valueArray[] = $excludeFacultyIDValue;
        }
        $query .= " $faculty ON $daf.$dafFacultyID = $faculty.$facultyID ORDER BY $faculty.$facultyName ASC";
        
        return self::buildFromDBRows(
            $this->getQueryResults($query, $valueArray),
            'Faculty');                
    }
        
   
    /**************************************************************************
     * Misc
     **************************************************************************/
    /**
     * Extracts all CLLO-PLLO relationships from the database for the given DLE
     * (to which the PLLO is associated) and course (to which the CLLO is
     * associated).
     *
     * @param $dleIDValue       The ID of the DLE (string or numeric)
     * @param $courseIDValue    The ID of the course (string or numeric)
     * @return                  An array of all matching CLLO-and-PLLO IDs
     */
    public function getCLLOsAndPLLOsForDLEAndCourse($dleIDValue, $courseIDValue) {
        $cap = self::TABLE_CLLO_AND_PLLO;
        $capPLLOID = self::TABLE_CLLO_AND_PLLO_PLLO_ID;
        $capCLLOID = self::TABLE_CLLO_AND_PLLO_CLLO_ID;

        $pllo = self::TABLE_PLLO;
        $plloID = self::TABLE_PLLO_ID;
        $plloParentID = self::TABLE_PLLO_PARENT_ID;

        $cac = self::TABLE_CLLO_AND_COURSE;
        $cacCLLOID = self::TABLE_CLLO_AND_COURSE_CLLO_ID;
        $cacCourseID = self::TABLE_CLLO_AND_COURSE_COURSE_ID;

        $cllo = self::TABLE_CLLO;
        $clloID = self::TABLE_CLLO_ID;
        $clloNumber = self::TABLE_CLLO_NUMBER;

        $pad = self::TABLE_PLLO_AND_DLE;
        $padDLEID = self::TABLE_PLLO_AND_DLE_DLE_ID;
        $padPLLOID = self::TABLE_PLLO_AND_DLE_PLLO_ID;
        
        $query = "SELECT $cap.* FROM $cap JOIN (SELECT * FROM $cac WHERE $cac.$cacCourseID = ?) AS $cac ON $cac.$cacCLLOID = $cap.$capCLLOID JOIN $pllo ON $cap.$capPLLOID = $pllo.$plloID JOIN (SELECT * FROM $pad WHERE $pad.$padDLEID = ?) AS $pad ON (($pad.$padPLLOID = $pllo.$plloID) OR ($pad.$padPLLOID = $pllo.$plloParentID)) JOIN $cllo ON $cap.$capCLLOID = $cllo.$clloID ORDER BY $cllo.$clloNumber ASC";
        return self::buildFromDBRows(
            $this->getQueryResults($query, array($courseIDValue, $dleIDValue)),
            'CLLOAndPLLO');
    }


    /**************************************************************************
     * Revisions
     **************************************************************************/
     /**
      * 
      * @param type $userID
      * @param type $editor
      * @param type $limit
      * @return type
      */
     public function getLatestRevisions($userID, $editor = true, $limit = 5) {
         $revision = self::TABLE_REVISION;
         $userID = self::TABLE_REVISION_USER_ID;
         $dateAndTime = self::TABLE_REVISION_DATE_AND_TIME;

         $comparison = $editor ? "=" : "<>";

         $query = "SELECT * FROM $revision WHERE $userID $comparison ? ORDER BY $dateAndTime DESC LIMIT $limit ";
         return self::buildFromDBRows(
             $this->getQueryResults($query, array($userID)), 'Revision');
     }

    /**
     * Performs a removal from the database from a single 'delete' revision.
     *
     * <strong>NOTE:</strong> this function is not yet complete or in use.
     *
     * @param $revision     The Revision containing the deletion information
     */
    public function performDeleteRevision($revision) {
        if (! $revision->isDelete()) {
            qsc_core_log_and_display_error("An attempt was made to remove a row from the database with a revision (ID ".$revision->getDBID().") that is not associated with deletion. No updates have been made.");
            return;
        }

        $table = $revision->getTable();

        $query = "DELETE FROM $table WHERE ".$revision->getPrimaryKeyQueryClause();

        /*
        echo "<p>$query</br>";
        print_r($revision->getPrimaryKeyValues());
        echo "</p>";
        */

        $this->performQuery($query, $revision->getPrimaryKeyValues());
    }

    /**
     * Performs an update to the database from a single 'edit' revision.
     *
     * <strong>NOTE:</strong> this function is not yet complete or in use.
     *
     * @param $revision     The Revision containing the update information
     */
    public function performEditRevision($revision) {
        if (! $revision->isEdit()) {
            qsc_core_log_and_display_error("An attempt was made to update the database with a revision (ID ".$revision->getDBID().") that is not associated with editing. No updates have been made.");
            return;
        }

        $table = $revision->getTable();
        $column = $revision->getColumn();
        $currentValue = $revision->getCurrentValue();

        $query = "UPDATE $table SET $column = ? WHERE ".$revision->getPrimaryKeyQueryClause();

        /*
        echo "<p>$query</br>";
        print_r(array_merge(
            array($currentValue), $revision->getPrimaryKeyValues()));
        */

        $this->performQuery($query, array_merge(
            array($currentValue), $revision->getPrimaryKeyValues()));
    }

    /**
     * Performs several queries, one at a time, to the database from a set of
     * edit and delete revisions.
     *
     * <strong>NOTE:</strong> addition isn't handled by this function
     * because the query for an INSERT requires more than one value, which
     * is a single revision's limit
     *
     * @param $revisionArray    The array of Revisions containing the update
     *                          information
     */
    public function performEditAndDeleteRevisions($revisionArray) {
        foreach ($revisionArray as $revision) {
            if ($revision->isEdit()) {
                $this->performEditRevision($revision);
            }
            else if ($revision->isDelete()) {
                $this->performDeleteRevision($revision);
            }
        }
    }


    /**************************************************************************
     * Adding/Inserting
     **************************************************************************/
     /**
      * 
      * @return type
      */
     public function insertCLLOFromPostData() {
         $userID = SessionManager::getUserID();
         $dateAndTime = date(QSC_CORE_DATE_AND_TIME_FORMAT);

         // Create the new CLLO from the submitted form
         // NOTE: this CLLO does not yet have its ID
         $tempNewCLLO = CLLO::buildFromCLLOPostData();

         // Insert the CLLO into the database and get the final version
         // back with its ID
         $newCLLO = $this->insertCLLO($tempNewCLLO);

         // Insert a revision for adding the new CLLO
         $revision = new Revision(
             $newCLLO->getDBID(), $userID,
             self::TABLE_CLLO, null,
             array(self::TABLE_CLLO_ID => $newCLLO->getDBID()),
             self::TABLE_REVISION_ACTION_ADDED,
             null, $dateAndTime
         );
         $this->insertRevision($revision);

         // Determine the course to which the CLLO is related
         $courseID = qsc_core_extract_form_value(INPUT_POST, QSC_CMP_FORM_CLLO_COURSE_SELECT, FILTER_SANITIZE_NUMBER_INT);
         $course = $this->getCourseFromID($courseID);

         // Add their relationship to the database
         $clloAndCourse = new CLLOAndCourse($newCLLO->getDBID(),
            $course->getDBID());
         $this->insertCLLOAndCourse($clloAndCourse);

         // Insert a revision for adding the new CLLOAndCourse
         $revision = new Revision(
             DatabaseObject::NEW_OBJECT_TEMP_ID, $userID,
             self::TABLE_CLLO_AND_COURSE, null,
             array(
                 self::TABLE_CLLO_AND_COURSE_CLLO_ID => $newCLLO->getDBID(),
                 self::TABLE_CLLO_AND_COURSE_COURSE_ID =>
                 $course->getDBID()),
             self::TABLE_REVISION_ACTION_ADDED,
             null, $dateAndTime
         );
         $this->insertRevision($revision);

         // Create the connections between the new CLLO and any PLLOs
         $plloIDArray = qsc_core_extract_form_array_value(INPUT_POST, QSC_CMP_FORM_CLLO_PLLO_LIST_SUPPORTED, FILTER_SANITIZE_NUMBER_INT);
         foreach ($plloIDArray as $plloID) {
             $clloAndPLLO = new CLLOAndPLLO($newCLLO->getDBID(), $plloID);
             $this->insertCLLOAndPLLO($clloAndPLLO);

             // Insert a revision for adding each CLLOAndPLLO
             $revision = new Revision(
                 DatabaseObject::NEW_OBJECT_TEMP_ID, $userID,
                 self::TABLE_CLLO_AND_PLLO, null,
                 array(self::TABLE_CLLO_AND_PLLO_CLLO_ID => $newCLLO->getDBID(),
                     self::TABLE_CLLO_AND_PLLO_PLLO_ID => $plloID),
                 self::TABLE_REVISION_ACTION_ADDED,
                 null, $dateAndTime
             );
             $this->insertRevision($revision);
         }

         // Create the connections between the new CLLO and any ILOs
         $iloIDArray = qsc_core_extract_form_array_value(INPUT_POST, QSC_CMP_FORM_CLLO_ILO_LIST_SUPPORTED, FILTER_SANITIZE_NUMBER_INT);
         foreach ($iloIDArray as $iloID) {
             $clloAndILO = new CLLOAndILO($newCLLO->getDBID(), $iloID);
             $this->insertCLLOAndILO($clloAndILO);

             // Insert a revision for adding each CLLOAndPLLO
             $revision = new Revision(
                 DatabaseObject::NEW_OBJECT_TEMP_ID, $userID,
                 self::TABLE_CLLO_AND_ILO, null,
                 array(self::TABLE_CLLO_AND_ILO_CLLO_ID => $newCLLO->getDBID(),
                     self::TABLE_CLLO_AND_ILO_ILO_ID => $iloID),
                 self::TABLE_REVISION_ACTION_ADDED,
                 null, $dateAndTime
             );
             $this->insertRevision($revision);
         }

         return $newCLLO;
     }
     
    /**
     * 
     * @return type
     */
    public function insertPLLOFromPostData() {
        $userID = SessionManager::getUserID();
        $dateAndTime = date(QSC_CORE_DATE_AND_TIME_FORMAT);

        // Create the new PLLO from the submitted form
        // NOTE: this PLLO does not yet have its ID
        $tempNewPLLO = PLLO::buildFromPLLOPostData();

        // Insert the PLLO into the database and get the final version
        // back with its ID
        $newPLLO = $this->insertPLLO($tempNewPLLO);

        // Insert a revision for adding the new PLLO
        $revision = new Revision(
                $newPLLO->getDBID(), $userID,
                self::TABLE_PLLO, null,
                array(self::TABLE_PLLO_ID => $newPLLO->getDBID()),
                self::TABLE_REVISION_ACTION_ADDED,
                null, $dateAndTime
        );
        $this->insertRevision($revision);

        // Determine if the new PLLO is associated with a DLE
        $dleID = qsc_core_extract_form_value(INPUT_POST, QSC_CMP_FORM_PLLO_PARENT_DLE_SELECT, FILTER_SANITIZE_NUMBER_INT);
        if (! $dleID) {
            return $newPLLO;
        }

        // Add their relationship to the database
        $plloAndDLE = new PLLOAndDLE($newPLLO->getDBID(), $dleID);
        $this->insertPLLOAndDLE($plloAndDLE);

        // Insert a revision for adding the new CLLOAndCourse
        $revision = new Revision(
                DatabaseObject::NEW_OBJECT_TEMP_ID, $userID,
                self::TABLE_PLLO_AND_DLE, null,
                array(
            self::TABLE_PLLO_AND_DLE_PLLO_ID => $newPLLO->getDBID(),
            self::TABLE_PLLO_AND_DLE_DLE_ID => $dleID),
                self::TABLE_REVISION_ACTION_ADDED,
                null, $dateAndTime
        );
        $this->insertRevision($revision);

        return $newPLLO;
    }

    /**
     * 
     * @param type $cllo
     * @return type
     */
    protected function insertCLLO($cllo) {
        $query = null;

        $tableName = self::TABLE_CLLO;
        $number = self::TABLE_CLLO_NUMBER;
        $text = self::TABLE_CLLO_TEXT;
        $type = self::TABLE_CLLO_TYPE;
        $ioa = self::TABLE_CLLO_IOA;
        $notes = self::TABLE_CLLO_NOTES;
        $parentID = self::TABLE_CLLO_PARENT_ID;

        $query = "INSERT INTO $tableName ($number, $text, $type, $ioa, $notes, $parentID) VALUES (?, ?, ?, ?, ?, ?)";

        $valueArray = array($cllo->getNumber(), $cllo->getText(),
            $cllo->getType(), $cllo->getIOA(), $cllo->getNotes(),
            $cllo->getParentDBID());

         /*
         echo "<p>$query</br>";
         print_r($valueArray);
         echo "</p>";
         */

         $this->performQuery($query, $valueArray);
         $clloID = $this->getLastInsertID();
         return $this->getCLLOFromID($clloID);
    }
    
     /**
      * 
      * @param type $cllo
      * @return type
      */
     protected function insertPLLO($cllo) {
         $query = null;

         $tableName = self::TABLE_PLLO;
         $number = self::TABLE_PLLO_NUMBER;
         $text = self::TABLE_PLLO_TEXT;
         $notes = self::TABLE_PLLO_NOTES;
         $parentID = self::TABLE_PLLO_PARENT_ID;

         $query = "INSERT INTO $tableName ($number, $text, $notes, $parentID) VALUES (?, ?, ?, ?)";

         $valueArray = array($cllo->getNumber(), $cllo->getText(),
             $cllo->getNotes(), $cllo->getParentDBID());

         /*
         echo "<p>$query</br>";
         print_r($valueArray);
         echo "</p>";
         */

         $this->performQuery($query, $valueArray);
         $plloID = $this->getLastInsertID();
         return $this->getPLLOFromID($plloID);
    }    

    /**
     * 
     * @param type $revision
     */
    protected function insertRevision($revision) {
        $query = null;

        $tableName = self::TABLE_REVISION;
        $colUserID = self::TABLE_REVISION_USER_ID;
        $colTable = self::TABLE_REVISION_REV_TABLE;
        $colColumn = self::TABLE_REVISION_REV_COLUMN;
        $colKeyColumns = self::TABLE_REVISION_KEY_COLUMNS;
        $colKeyValues = self::TABLE_REVISION_KEY_VALUES;
        $colAction = self::TABLE_REVISION_ACTION;
        $colPriorValue = self::TABLE_REVISION_PRIOR_VALUE;
        $colDateAndTime = self::TABLE_REVISION_DATE_AND_TIME;

        $query = "INSERT INTO $tableName ($colUserID, $colTable, $colColumn, $colKeyColumns, $colKeyValues, $colAction, $colPriorValue, $colDateAndTime) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $valueArray = array($revision->getUserID(), $revision->getTable(),
            $revision->getColumn(), $revision->getPrimaryKeyColumnString(),
            $revision->getPrimaryKeyValueString(), $revision->getAction(), $revision->getPriorValue(), $revision->getDateAndTime());

        /*
        echo "<p>$query</br>";
        print_r($valueArray);
        echo "</p>";
        */
        $this->performQuery($query, $valueArray);
    }

    /**
     * 
     * @param type $revisionArray
     */
    protected function insertRevisions($revisionArray) {
        foreach ($revisionArray as $revision) {
            $this->insertRevision($revision);
        }
    }

    /**
     * 
     * @param type $clloAndCourse
     */
    protected function insertCLLOAndCourse($clloAndCourse) {
        $tableName = self::TABLE_CLLO_AND_COURSE;
        $colCLLOID = self::TABLE_CLLO_AND_COURSE_CLLO_ID;
        $colCourseID = self::TABLE_CLLO_AND_COURSE_COURSE_ID;

        $query = "INSERT INTO $tableName ($colCLLOID, $colCourseID) VALUES (?, ?)";
        $valueArray = array($clloAndCourse->getCCMDBID(), $clloAndCourse->getCourseDBID());

        /*
        echo "<p>$query</br>";
        print_r($valueArray);
        echo "</p>";
        */

        $this->performQuery($query, $valueArray);
    }

    /**
     * 
     * @param type $clloAndPLLO
     */
    protected function insertCLLOAndPLLO($clloAndPLLO) {
        $tableName = self::TABLE_CLLO_AND_PLLO;
        $colCLLOID = self::TABLE_CLLO_AND_PLLO_CLLO_ID;
        $colPLLOID = self::TABLE_CLLO_AND_PLLO_PLLO_ID;

        $query = "INSERT INTO $tableName ($colCLLOID, $colPLLOID) VALUES (?, ?)";
        $valueArray = array($clloAndPLLO->getCCMDBID(), $clloAndPLLO->getPCMDBID());

        /*
        echo "<p>$query</br>";
        print_r($valueArray);
        echo "</p>";
        */

        $this->performQuery($query, $valueArray);
    }    

    /**
     * 
     * @param type $clloAndILO
     */
    protected function insertCLLOAndILO($clloAndILO) {
        $tableName = self::TABLE_CLLO_AND_ILO;
        $colCLLOID = self::TABLE_CLLO_AND_ILO_CLLO_ID;
        $colILOID = self::TABLE_CLLO_AND_ILO_ILO_ID;

        $query = "INSERT INTO $tableName ($colCLLOID, $colILOID) VALUES (?, ?)";
        $valueArray = array($clloAndILO->getCCMDBID(), $clloAndILO->getICMDBID());

        /*
        echo "<p>$query</br>";
        print_r($valueArray);
        echo "</p>";
        */

        $this->performQuery($query, $valueArray);
    }
      
    /**
     * 
     * @param type $clloAndDLE
     */
    protected function insertPLLOAndDLE($clloAndDLE) {
        $tableName = self::TABLE_PLLO_AND_DLE;
        $colPLLOID = self::TABLE_PLLO_AND_DLE_PLLO_ID;
        $colDLEID = self::TABLE_PLLO_AND_DLE_DLE_ID;

        $query = "INSERT INTO $tableName ($colPLLOID, $colDLEID) VALUES (?, ?)";
        $valueArray = array($clloAndDLE->getPCMDBID(), $clloAndDLE->getDLEDBID());

        /*
        echo "<p>$query</br>";
        print_r($valueArray);
        echo "</p>";
        */

        $this->performQuery($query, $valueArray);
    }

    /**
     * 
     * @param type $plloAndILO
     */
    protected function insertPLLOAndILO($plloAndILO) {
        $tableName = self::TABLE_PLLO_AND_ILO;
        $colPLLOID = self::TABLE_PLLO_AND_ILO_PLLO_ID;
        $colILOID = self::TABLE_PLLO_AND_ILO_ILO_ID;

        $query = "INSERT INTO $tableName ($colPLLOID, $colILOID) VALUES (?, ?)";
        $valueArray = array($plloAndILO->getPCMDBID(), $plloAndILO->getICMDBID());

        /*
        echo "<p>$query</br>";
        print_r($valueArray);
        echo "</p>";
        */

        $this->performQuery($query, $valueArray);
    }
    

    /**************************************************************************
     * Editing
     **************************************************************************/
    /**
     * Compares the current version of a CLLO with its edited counterpart and
     * determines what revisions have been made.
     *
     * @param type $originalCLLO    The newer version of this CLLO
     * @return type
     */
    public function updateCLLOFromPostData($originalCLLO) {
        $userID = SessionManager::getUserID();
        $dateAndTime = date(QSC_CORE_DATE_AND_TIME_FORMAT);

        // Create the updated version of the CLLO from the submitted form
        $updatedCLLO = CLLO::buildFromCLLOPostData();

        // Start with the revisions to the CLLO object/row
        $revisionArray = $originalCLLO->getRevisions($updatedCLLO, $userID, $dateAndTime);

        // Perform the changes/revisions and add each revision to the
        // database
        $this->performEditAndDeleteRevisions($revisionArray);
        $this->insertRevisions($revisionArray);

        // Now move onto the CLLO's relationships
        $this->updateCLLOCourseFromPostData($originalCLLO, $userID, $dateAndTime);
        $this->updateCLLOsAndPLLOsFromPostData($originalCLLO, $userID, $dateAndTime);
        $this->updateCLLOsAndILOsFromPostData($originalCLLO, $userID, $dateAndTime);

        return $updatedCLLO;
    }

    /**
     * Compares the prior course for a CLLO with its newer counterpart and
     * determines what revisions have been made.
     *
     * <strong>NOTE:</strong> At the moment, a CLLO is only associated with
     * one course, but the database supports the option to support more than
     * one.
     *
     * @param $originalCLLO
     * @param $userID
     * @param $dateAndTime
     */
    protected function updateCLLOCourseFromPostData($originalCLLO, $userID, $dateAndTime) {
        // Get the course ID from the form data
        $updatedCourseID = qsc_core_extract_form_value(INPUT_POST, QSC_CMP_FORM_CLLO_COURSE_SELECT, FILTER_SANITIZE_NUMBER_INT);
        if (! $updatedCourseID) {
            return;
        }

        // Get the courses and determine if there's been a change
        $originalCourse = $this->getCourseForCLLO($originalCLLO->getDBID());
        $updatedCourse = $this->getCourseFromID($updatedCourseID);
        if ($originalCourse->getDBID() == $updatedCourse->getDBID()) {
            return;
        }

        // Delete the old entry and add a revison
        $revision = new Revision(
            DatabaseObject::NEW_OBJECT_TEMP_ID, $userID,
            self::TABLE_CLLO_AND_COURSE, null,
            array(
                self::TABLE_CLLO_AND_COURSE_CLLO_ID => $originalCLLO->getDBID(),
                self::TABLE_CLLO_AND_COURSE_COURSE_ID =>
                $originalCourse->getDBID()),
            self::TABLE_REVISION_ACTION_DELETED,
            null, $dateAndTime
        );

        $this->performDeleteRevision($revision);
        $this->insertRevision($revision);

        // Add a new entry and a revision
        $revision = new Revision(
            DatabaseObject::NEW_OBJECT_TEMP_ID, $userID,
            self::TABLE_CLLO_AND_COURSE, null,
            array(
                self::TABLE_CLLO_AND_COURSE_CLLO_ID => $originalCLLO->getDBID(),
                self::TABLE_CLLO_AND_COURSE_COURSE_ID =>
                $updatedCourse->getDBID()),
            self::TABLE_REVISION_ACTION_ADDED,
            null, $dateAndTime
        );

        $updatedCLLOAndCourse = new CLLOAndCourse($originalCLLO->getDBID(), $updatedCourse->getDBID());

        $this->insertCLLOAndCourse($updatedCLLOAndCourse);
        $this->insertRevision($revision);
    }

    /**
     * Compares a set of older CLLOAndPLLOs with a set of newer counterparts
     * and determines what revisions have been made.
     *
     * @param $originalCLLO
     * @param $userID
     * @param $dateAndTime
     */
    protected function updateCLLOsAndPLLOsFromPostData($originalCLLO, $userID, $dateAndTime) {
        // Get the PLLO IDs from the form data
        $updatedPLLOIDArray = qsc_core_extract_form_array_value(INPUT_POST, 
            QSC_CMP_FORM_CLLO_PLLO_LIST_SUPPORTED, 
            FILTER_SANITIZE_NUMBER_INT);
        
        // Create an updated set of CLLOAndPLLOs
        $updatedCLLOAndPLLOArray = array();
        foreach ($updatedPLLOIDArray as $updatedPLLOID) {
             $updatedCLLOAndPLLOArray[] = new CLLOAndPLLO($originalCLLO->getDBID(), $updatedPLLOID);
        }

        // Get the original CLLO and PLLO information in the database
        $originalCLLOAndPLLOArray = $this->getCLLOsAndPLLOsForCLLO($originalCLLO->getDBID());

        // Remove the identical CLLOAndPLLOs in both arrays
        qsc_core_remove_identical_values($updatedCLLOAndPLLOArray, $originalCLLOAndPLLOArray);

        // Everything left in the prior set has been deleted
        $revisionArray = array();
        foreach($originalCLLOAndPLLOArray as $deletedCLLOAndPLLO) {
            $revisionArray[] = new Revision(
                DatabaseObject::NEW_OBJECT_TEMP_ID, $userID,
                self::TABLE_CLLO_AND_PLLO, null,
                array(self::TABLE_CLLO_AND_PLLO_CLLO_ID =>
                    $deletedCLLOAndPLLO->getCCMDBID(),
                    self::TABLE_CLLO_AND_PLLO_PLLO_ID =>
                    $deletedCLLOAndPLLO->getPCMDBID()),
                self::TABLE_REVISION_ACTION_DELETED,
                null, $dateAndTime
            );
        }

        // Everything left in the new set has been added
        foreach($updatedCLLOAndPLLOArray as $addedCLLOAndPLLO) {
            $this->insertCLLOAndPLLO($addedCLLOAndPLLO);
            $revisionArray[] = new Revision(
                DatabaseObject::NEW_OBJECT_TEMP_ID, $userID,
                self::TABLE_CLLO_AND_PLLO, null,
                array(self::TABLE_CLLO_AND_PLLO_CLLO_ID =>
                    $addedCLLOAndPLLO->getCCMDBID(),
                    self::TABLE_CLLO_AND_PLLO_PLLO_ID =>
                    $addedCLLOAndPLLO->getPCMDBID()),
                self::TABLE_REVISION_ACTION_ADDED,
                null, $dateAndTime
            );
        }

        // Perform the changes/revisions and add each revision to the
        // database
        $this->performEditAndDeleteRevisions($revisionArray);
        $this->insertRevisions($revisionArray);
    }

    /**
     * Compares a set of older CLLOAndILOs with a set of newer counterparts
     * and determines what revisions have been made.
     *
     * @param $originalCLLO
     * @param $userID
     * @param $dateAndTime
     */
    protected function updateCLLOsAndILOsFromPostData($originalCLLO, $userID, $dateAndTime) {
        // Get the ILO IDs from the form data
        $updatedILOIDArray = qsc_core_extract_form_array_value(INPUT_POST, QSC_CMP_FORM_CLLO_ILO_LIST_SUPPORTED, FILTER_SANITIZE_NUMBER_INT);
        if (! $updatedILOIDArray) {
            $updatedILOIDArray = array();
        }

        // Create an updated set of CLLOAndILOs
        $updatedCLLOAndILOArray = array();
        foreach ($updatedILOIDArray as $updatedILOID) {
             $updatedCLLOAndILOArray[] = new CLLOAndILO($originalCLLO->getDBID(), $updatedILOID);
        }

        // Get the original CLLO and ILO information in the database
        $originalCLLOAndILOArray = $this->getCLLOsAndILOsForCLLO($originalCLLO->getDBID());

        // Remove the identical CLLOAndILOs in both arrays
        qsc_core_remove_identical_values($updatedCLLOAndILOArray, $originalCLLOAndILOArray);

        // Everything left in the prior set has been deleted
        $revisionArray = array();
        foreach($originalCLLOAndILOArray as $deletedCLLOAndILO) {
            $revisionArray[] = new Revision(
                DatabaseObject::NEW_OBJECT_TEMP_ID, $userID,
                self::TABLE_CLLO_AND_ILO, null,
                array(self::TABLE_CLLO_AND_ILO_CLLO_ID =>
                    $deletedCLLOAndILO->getCCMDBID(),
                    self::TABLE_CLLO_AND_ILO_ILO_ID =>
                    $deletedCLLOAndILO->getICMDBID()),
                self::TABLE_REVISION_ACTION_DELETED,
                null, $dateAndTime
            );
        }

        // Everything left in the new set has been added
        foreach($updatedCLLOAndILOArray as $addedCLLOAndILO) {
            $this->insertCLLOAndILO($addedCLLOAndILO);
            // $pdo->lastInsertId
            $revisionArray[] = new Revision(
                DatabaseObject::NEW_OBJECT_TEMP_ID, $userID,
                self::TABLE_CLLO_AND_ILO, null,
                array(self::TABLE_CLLO_AND_ILO_CLLO_ID =>
                    $addedCLLOAndILO->getCCMDBID(),
                    self::TABLE_CLLO_AND_ILO_ILO_ID =>
                    $addedCLLOAndILO->getICMDBID()),
                self::TABLE_REVISION_ACTION_ADDED,
                null, $dateAndTime
            );
        }

        // Perform the changes/revisions and add each revision to the
        // database
        $this->performEditAndDeleteRevisions($revisionArray);
        $this->insertRevisions($revisionArray);
    }
        
    /**
     * Compares the current version of a PLLO with its edited counterpart and
     * determines what revisions have been made.
     *
     * @param $originalPLLO       The newer version of this PLLO
     */
    public function updatePLLOFromPostData($originalPLLO) {
        $userID = SessionManager::getUserID();
        $dateAndTime = date(QSC_CORE_DATE_AND_TIME_FORMAT);

        // Create the updated version of the PLLO from the submitted form
        $updatedPLLO = PLLO::buildFromPLLOPostData();

        // Start with the revisions to the PLLO object/row
        $revisionArray = $originalPLLO->getRevisions($updatedPLLO, $userID, $dateAndTime);

        // Perform the changes/revisions and add each revision to the
        // database
        $this->performEditAndDeleteRevisions($revisionArray);
        $this->insertRevisions($revisionArray);

        // Now move onto the PLLO's relationships
        $this->updatePLLOAndDLEFromPostData($originalPLLO, $userID, $dateAndTime);
        $this->updatePLLOsAndILOsFromPostData($originalPLLO, $userID, $dateAndTime);
        
        return $updatedPLLO;
    }

    /**
     * Compares the prior course for a PLLO with its newer counterpart and
     * determines what revisions have been made.
     *
     * @param $originalPLLO
     * @param $userID
     * @param $dateAndTime
     */
    protected function updatePLLOAndDLEFromPostData($originalPLLO, $userID, $dateAndTime) {
        // Get the old DLE from the form data
        $originalDLE = $this->getDLEForPLLO($originalPLLO->getDBID());
        $originalDLEID = ($originalDLE) ? $originalDLE->getDBID() : null;

        // Get the new DLE ID from the form data
        $updatedDLEID = qsc_core_extract_form_value(INPUT_POST, QSC_CMP_FORM_PLLO_PARENT_DLE_SELECT, FILTER_SANITIZE_NUMBER_INT);

        if ($originalDLEID == $updatedDLEID) {
            // If there's no change then there's no revision
            return;
        } 
        else {
            // A change has been made            
            if ($originalDLEID) {
                // If there was a prior entry, delete it and add a revison
                $revision = new Revision(
                        DatabaseObject::NEW_OBJECT_TEMP_ID, $userID,
                        self::TABLE_PLLO_AND_DLE, null,
                        array(
                    self::TABLE_PLLO_AND_DLE_PLLO_ID => $originalPLLO->getDBID(),
                    self::TABLE_PLLO_AND_DLE_DLE_ID =>
                    $originalDLEID),
                        self::TABLE_REVISION_ACTION_DELETED,
                        null, $dateAndTime
                );

                $this->performDeleteRevision($revision);
                $this->insertRevision($revision);
            }

            if ($updatedDLEID) {
                // Add a new entry and a revision
                $revision = new Revision(
                        DatabaseObject::NEW_OBJECT_TEMP_ID, $userID,
                        self::TABLE_PLLO_AND_DLE, null,
                        array(
                    self::TABLE_PLLO_AND_DLE_PLLO_ID => $originalPLLO->getDBID(),
                    self::TABLE_PLLO_AND_DLE_DLE_ID =>
                    $updatedDLEID),
                        self::TABLE_REVISION_ACTION_ADDED,
                        null, $dateAndTime
                );
                
                $updatedPLLOAndDLE = new PLLOAndDLE($originalPLLO->getDBID(), $updatedDLEID);
                $this->insertPLLOAndDLE($updatedPLLOAndDLE);
                $this->insertRevision($revision);                
            }
        }
    }
    
    /**
     * Compares a set of older PLLOAndILOs with a set of newer counterparts
     * and determines what revisions have been made.
     *
     * @param $originalPLLO
     * @param $userID
     * @param $dateAndTime
     */
    protected function updatePLLOsAndILOsFromPostData($originalPLLO, $userID, $dateAndTime) {
        // Get the ILO IDs from the form data
        $updatedILOIDArray = qsc_core_extract_form_array_value(INPUT_POST, QSC_CMP_FORM_PLLO_ILO_LIST_SUPPORTED, FILTER_SANITIZE_NUMBER_INT);
        if (! $updatedILOIDArray) {
            $updatedILOIDArray = array();
        }

        // Create an updated set of PLLOAndILOs
        $updatedPLLOAndILOArray = array();
        foreach ($updatedILOIDArray as $updatedILOID) {
             $updatedPLLOAndILOArray[] = new PLLOAndILO($originalPLLO->getDBID(), $updatedILOID);
        }

        // Get the original PLLO and ILO information in the database
        $originalPLLOAndILOArray = $this->getPLLOsAndILOsForPLLO($originalPLLO->getDBID());

        // Remove the identical PLLOAndILOs in both arrays
        qsc_core_remove_identical_values($updatedPLLOAndILOArray, $originalPLLOAndILOArray);

        // Everything left in the prior set has been deleted
        $revisionArray = array();
        foreach($originalPLLOAndILOArray as $deletedPLLOAndILO) {
            $revisionArray[] = new Revision(
                DatabaseObject::NEW_OBJECT_TEMP_ID, $userID,
                self::TABLE_PLLO_AND_ILO, null,
                array(self::TABLE_PLLO_AND_ILO_PLLO_ID =>
                    $deletedPLLOAndILO->getPCMDBID(),
                    self::TABLE_PLLO_AND_ILO_ILO_ID =>
                    $deletedPLLOAndILO->getICMDBID()),
                self::TABLE_REVISION_ACTION_DELETED,
                null, $dateAndTime
            );
        }

        // Everything left in the new set has been added
        foreach($updatedPLLOAndILOArray as $addedPLLOAndILO) {
            $this->insertPLLOAndILO($addedPLLOAndILO);
            // $pdo->lastInsertId
            $revisionArray[] = new Revision(
                DatabaseObject::NEW_OBJECT_TEMP_ID, $userID,
                self::TABLE_PLLO_AND_ILO, null,
                array(self::TABLE_PLLO_AND_ILO_PLLO_ID =>
                    $addedPLLOAndILO->getPCMDBID(),
                    self::TABLE_PLLO_AND_ILO_ILO_ID =>
                    $addedPLLOAndILO->getICMDBID()),
                self::TABLE_REVISION_ACTION_ADDED,
                null, $dateAndTime
            );
        }

        // Perform the changes/revisions and add each revision to the
        // database
        $this->performEditAndDeleteRevisions($revisionArray);
        $this->insertRevisions($revisionArray);
    }
    
    
    /**************************************************************************
     * Deleting
     **************************************************************************/
     /**
      * 
      * @param type $clloID
      * @return type
      */
    public function deleteCLLOFromID($clloID) {
        $userID = SessionManager::getUserID();
        $dateAndTime = date(QSC_CORE_DATE_AND_TIME_FORMAT);
        $revisionArray = array();

        $cllo = $this->getCLLOFromID($clloID);
        if (! $cllo) {
            qsc_core_log_and_display_error("The CLLO with ID $clloID could not be located
                and as such was not deleted.");
            return;
        }

        // Create a revision to remove the CLLO
        $revisionArray[] = new Revision(
            DatabaseObject::NEW_OBJECT_TEMP_ID, $userID,
            self::TABLE_CLLO, null,
            array(self::TABLE_CLLO_ID => $clloID),
            self::TABLE_REVISION_ACTION_DELETED,
            $cllo->getDeletionRevisionPriorValue(),
            $dateAndTime
        );

        // Create a revision to remove the connection between the
        // CLLO and its course
        $course = $this->getCourseForCLLO($clloID);
        $revisionArray[] = new Revision(
            DatabaseObject::NEW_OBJECT_TEMP_ID, $userID,
            self::TABLE_CLLO_AND_COURSE, null,
            array(self::TABLE_CLLO_AND_COURSE_CLLO_ID => $clloID,
                self::TABLE_CLLO_AND_COURSE_COURSE_ID => $course->getDBID()),
            self::TABLE_REVISION_ACTION_DELETED,
            null, $dateAndTime
        );

        // Get the CLLO and PLLO information in the database
        $clloAndPLLOArray = $this->getCLLOsAndPLLOsForCLLO($clloID);
        foreach($clloAndPLLOArray as $clloAndPLLO) {
            // Create a revision to remove each CLLOAndPLLO
            $revisionArray[] = new Revision(
                DatabaseObject::NEW_OBJECT_TEMP_ID, $userID,
                self::TABLE_CLLO_AND_PLLO, null,
                array(self::TABLE_CLLO_AND_PLLO_CLLO_ID =>
                    $clloAndPLLO->getCCMDBID(),
                    self::TABLE_CLLO_AND_PLLO_PLLO_ID =>
                    $clloAndPLLO->getPCMDBID()),
                self::TABLE_REVISION_ACTION_DELETED,
                null, $dateAndTime
            );
        }

        // Get the CLLO and ILO information in the database
        $clloAndILOArray = $this->getCLLOsAndILOsForCLLO($clloID);
        foreach($clloAndILOArray as $clloAndILO) {
            // Create a revision to remove each CLLOAndILO
            $revisionArray[] = new Revision(
                DatabaseObject::NEW_OBJECT_TEMP_ID, $userID,
                self::TABLE_CLLO_AND_ILO, null,
                array(self::TABLE_CLLO_AND_ILO_CLLO_ID =>
                    $clloAndILO->getCCMDBID(),
                    self::TABLE_CLLO_AND_ILO_ILO_ID =>
                    $clloAndILO->getICMDBID()),
                self::TABLE_REVISION_ACTION_DELETED,
                null, $dateAndTime
            );
        }

        // Perform the deletions and add each revision to the database
        /*
        foreach ($revisionArray as $revision) {
            echo "<p>";
            print_r($revision);
            echo "</p>";
        }
        */
        $this->performEditAndDeleteRevisions($revisionArray);
        $this->insertRevisions($revisionArray);
    }
    
    /**
     * 
     * @param type $plloID
     * @return type
     */
    public function deletePLLOFromID($plloID) {
        $userID = SessionManager::getUserID();
        $dateAndTime = date(QSC_CORE_DATE_AND_TIME_FORMAT);
        $revisionArray = array();

        $pllo = $this->getPLLOFromID($plloID);
        if (! $pllo) {
            qsc_core_log_and_display_error("The PLLO with ID $plloID could not be located
                and as such was not deleted.");
            return;
        }

        // Create a revision to remove the PLLO
        $revisionArray[] = new Revision(
            DatabaseObject::NEW_OBJECT_TEMP_ID, $userID,
            self::TABLE_PLLO, null,
            array(self::TABLE_PLLO_ID => $plloID),
            self::TABLE_REVISION_ACTION_DELETED,
            $pllo->getDeletionRevisionPriorValue(),
            $dateAndTime
        );

        // Get the PLLO and DLE information in the database (if any)        
        $dle = $this->getDLEForPLLO($plloID);
        if ($dle) {
            // Create a revision to remove the PLLOAndDLE
            $revisionArray[] = new Revision(
                DatabaseObject::NEW_OBJECT_TEMP_ID, $userID,
                self::TABLE_PLLO_AND_DLE, null,
                array(self::TABLE_PLLO_AND_DLE_PLLO_ID =>
                    $plloID,
                    self::TABLE_PLLO_AND_DLE_DLE_ID =>
                    $dle->getDBID()),
                self::TABLE_REVISION_ACTION_DELETED,
                null, $dateAndTime
            );
        }

        // Perform the deletions and add each revision to the database
        foreach ($revisionArray as $revision) {
            // error_log($revision->getPrimaryKey());
            /*
            echo "<p>";
            print_r($revision);
            echo "</p>";
             *
             */
        }
        $this->performEditAndDeleteRevisions($revisionArray);
        $this->insertRevisions($revisionArray);
    }    

}