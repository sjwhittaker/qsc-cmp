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
use DatabaseObjects\CPRList;
use DatabaseObjects\DatabaseObject;
use DatabaseObjects\Degree;
use DatabaseObjects\DegreeLevelExpectation as DLE;
use DatabaseObjects\Department;
use DatabaseObjects\Faculty;
use DatabaseObjects\InstitutionLearningOutcome as ILO;
use DatabaseObjects\OptionCourseList;
use DatabaseObjects\Plan;
use DatabaseObjects\PlanAndPLLO;
use DatabaseObjects\PlanLevelLearningOutcome as PLLO;
use DatabaseObjects\PLLOAndDLE;
use DatabaseObjects\PLLOAndILO;
use DatabaseObjects\Program;
use DatabaseObjects\RelationshipCourseList;
use DatabaseObjects\Revision;
use DatabaseObjects\SubjectCourseList;
use DatabaseObjects\TextPlanRequirement as TPR;
use DatabaseObjects\TPRList;
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
    public const TABLE_COURSE_LEGACY = "legacy";
    public const TABLE_COURSE_NOTES = "notes";
    public const TABLE_COURSE_NOTES_MAX_LENGTH = 500;
    
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

    public const TABLE_COURSELIST_TO_COURSE = "courselist_to_course";
    public const TABLE_COURSELIST_TO_COURSE_PARENT_COURSELIST_ID = "parent_courselist_id";
    public const TABLE_COURSELIST_TO_COURSE_CHILD_COURSE_ID = "child_course_id";

    public const TABLE_COURSELIST_TO_COURSELIST = "courselist_to_courselist";
    public const TABLE_COURSELIST_TO_COURSELIST_PARENT_ID = "parent_id";
    public const TABLE_COURSELIST_TO_COURSELIST_CHILD_ID = "child_id";
    public const TABLE_COURSELIST_TO_COURSELIST_LEVEL = "level";
    public const TABLE_COURSELIST_TO_COURSELIST_LEVEL_NONE = "None";
    public const TABLE_COURSELIST_TO_COURSELIST_LEVEL_P = "P";
    public const TABLE_COURSELIST_TO_COURSELIST_LEVEL_100 = "100";
    public const TABLE_COURSELIST_TO_COURSELIST_LEVEL_200 = "200";
    public const TABLE_COURSELIST_TO_COURSELIST_LEVEL_300 = "300";
    public const TABLE_COURSELIST_TO_COURSELIST_LEVEL_400 = "400";
    public const TABLE_COURSELIST_TO_COURSELIST_LEVEL_500 = "500";
    public const TABLE_COURSELIST_TO_COURSELIST_OR_ABOVE = "or_above";
        
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
    public const TABLE_CPR_NUMBER = "number";
    public const TABLE_CPR_NUMBER_MAX_LENGTH = 10;
    public const TABLE_CPR_UNITS = "units";
    public const TABLE_CPR_CONNECTOR = "connector";
    public const TABLE_CPR_CONNECTOR_FROM = "from";
    public const TABLE_CPR_CONNECTOR_IN = "in";
    public const TABLE_CPR_TEXT = "text";
    public const TABLE_CPR_TEXT_MAX_LENGTH = 100;
    public const TABLE_CPR_NOTES = "notes";
    public const TABLE_CPR_NOTES_MAX_LENGTH = 500;
    public const TABLE_CPR_CLASS = "class";
    public const TABLE_CPR_CLASS_MAX_LENGTH = 25;

    public const TABLE_CPR_AND_COURSELIST = "cpr_and_courselist";
    public const TABLE_CPR_AND_COURSELIST_CPR_ID = "cpr_id";
    public const TABLE_CPR_AND_COURSELIST_COURSELIST_ID = "courselist_id";
    public const TABLE_CPR_AND_COURSELIST_LEVEL = self::TABLE_COURSELIST_TO_COURSELIST_LEVEL;
    public const TABLE_CPR_AND_COURSELIST_LEVEL_NONE = self::TABLE_COURSELIST_TO_COURSELIST_LEVEL_NONE;
    public const TABLE_CPR_AND_COURSELIST_LEVEL_P = self::TABLE_COURSELIST_TO_COURSELIST_LEVEL_P;
    public const TABLE_CPR_AND_COURSELIST_LEVEL_100 = self::TABLE_COURSELIST_TO_COURSELIST_LEVEL_100;
    public const TABLE_CPR_AND_COURSELIST_LEVEL_200 = self::TABLE_COURSELIST_TO_COURSELIST_LEVEL_200;
    public const TABLE_CPR_AND_COURSELIST_LEVEL_300 = self::TABLE_COURSELIST_TO_COURSELIST_LEVEL_300;
    public const TABLE_CPR_AND_COURSELIST_LEVEL_400 = self::TABLE_COURSELIST_TO_COURSELIST_LEVEL_400;
    public const TABLE_CPR_AND_COURSELIST_LEVEL_500 = self::TABLE_COURSELIST_TO_COURSELIST_LEVEL_500;
    public const TABLE_CPR_AND_COURSELIST_OR_ABOVE = self::TABLE_COURSELIST_TO_COURSELIST_OR_ABOVE;
    
    public const TABLE_CPR_TO_CPRLIST = "cpr_to_cprlist";
    public const TABLE_CPR_TO_CPRLIST_PARENT_CPR_ID = "parent_cpr_id";
    public const TABLE_CPR_TO_CPRLIST_CHILD_CPRLIST_ID = "child_cprlist_id";    
    
    public const TABLE_CPRLIST = "cprlist";
    public const TABLE_CPRLIST_ID = "id";
    public const TABLE_CPRLIST_NUMBER = "number";
    public const TABLE_CPRLIST_NUMBER_MAX_LENGTH = 10;
    public const TABLE_CPRLIST_TYPE = "type";
    public const TABLE_CPRLIST_TYPE_MAX_LENGTH = 100;
    public const TABLE_CPRLIST_NOTES = "notes";
    public const TABLE_CPRLIST_NOTES_MAX_LENGTH = 500;

    public const TABLE_CPRLIST_TO_CPR = "cprlist_to_cpr";
    public const TABLE_CPRLIST_TO_CPR_PARENT_CPRLIST_ID = "parent_cprlist_id";
    public const TABLE_CPRLIST_TO_CPR_CHILD_CPR_ID = "child_cpr_id";

    public const TABLE_CPRLIST_TO_CPRLIST = "cprlist_to_cprlist";
    public const TABLE_CPRLIST_TO_CPRLIST_PARENT_ID = "parent_id";
    public const TABLE_CPRLIST_TO_CPRLIST_CHILD_ID = "child_id";

    public const TABLE_CPRLIST_TO_PLAN = "cprlist_to_plan";
    public const TABLE_CPRLIST_TO_PLAN_PARENT_CPRLIST_ID = "parent_cprlist_id";
    public const TABLE_CPRLIST_TO_PLAN_CHILD_PLAN_ID = "child_plan_id";    

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
    public const TABLE_DEPARTMENT_CODE = "code";
    public const TABLE_DEPARTMENT_CODE_MAX_LENGTH = 10;    

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

    public const TABLE_LEGACY_COURSE_TO_COURSE = "legacy_course_to_course";
    public const TABLE_LEGACY_COURSE_TO_COURSE_LEGACY_COURSE_ID = "legacy_course_id";
    public const TABLE_LEGACY_COURSE_TO_COURSE_COURSE_ID = "course_id";
    
    public const TABLE_PLAN = "plan";
    public const TABLE_PLAN_ID = "id";
    public const TABLE_PLAN_NAME = "name";
    public const TABLE_PLAN_NAME_MAX_LENGTH = 75;    
    public const TABLE_PLAN_CODE = "code";
    public const TABLE_PLAN_CODE_MAX_LENGTH = 10;    
    public const TABLE_PLAN_INTERNSHIP = "internship";
    public const TABLE_PLAN_DESCRIPTIVE_NAME = "descriptive_name";
    public const TABLE_PLAN_DESCRIPTIVE_NAME_MAX_LENGTH = 75;    
    public const TABLE_PLAN_TEXT = "text";
    public const TABLE_PLAN_TEXT_MAX_LENGTH = 500;    
    public const TABLE_PLAN_PRIOR_TO = "prior_to";
    public const TABLE_PLAN_NUMBER = "number";
    public const TABLE_PLAN_NUMBER_MAX_LENGTH = 10;    
    public const TABLE_PLAN_NOTES = "notes";
    public const TABLE_PLAN_NOTES_MAX_LENGTH = 500;    
    
    public const TABLE_PLAN_AND_PLLO = "plan_and_pllo";
    public const TABLE_PLAN_AND_PLLO_PLAN_ID = "plan_id";
    public const TABLE_PLAN_AND_PLLO_PLLO_ID = "pllo_id";
    
    public const TABLE_PLAN_TO_CPRLIST = "plan_to_cprlist";
    public const TABLE_PLAN_TO_CPRLIST_PARENT_PLAN_ID = "parent_plan_id";
    public const TABLE_PLAN_TO_CPRLIST_CHILD_CPRLIST_ID = "child_cprlist_id";

    public const TABLE_PLAN_TO_TPRLIST = "plan_to_tprlist";
    public const TABLE_PLAN_TO_TPRLIST_PARENT_PLAN_ID = "parent_plan_id"; 
    public const TABLE_PLAN_TO_TPRLIST_CHILD_TPRLIST_ID = "child_tprlist_id";
    
    public const TABLE_PLLO = "pllo";
    public const TABLE_PLLO_ID = "id";
    public const TABLE_PLLO_NUMBER = "number";
    public const TABLE_PLLO_NUMBER_MAX_LENGTH = 15;
    public const TABLE_PLLO_TEXT = "text";
    public const TABLE_PLLO_TEXT_MAX_LENGTH = 100;
    public const TABLE_PLLO_NOTES = "notes";
    public const TABLE_PLLO_NOTES_MAX_LENGTH = 500;
    public const TABLE_PLLO_PREFIX = "prefix";
    public const TABLE_PLLO_PREFIX_MAX_LENGTH = 10;
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
    public const TABLE_PROGRAM_AND_PLAN_TYPE = "type";
    public const TABLE_PROGRAM_AND_PLAN_TYPE_MAJOR = "Major";
    public const TABLE_PROGRAM_AND_PLAN_TYPE_MINOR = "Minor";
    public const TABLE_PROGRAM_AND_PLAN_TYPE_SPECIALIZATION = "Specialization";
    public const TABLE_PROGRAM_AND_PLAN_TYPE_MEDIAL = "Medial";
    public const TABLE_PROGRAM_AND_PLAN_TYPE_GENERAL = "General";
    public const TABLE_PROGRAM_AND_PLAN_TYPE_SUB_PLAN = "Sub-Plan";
    
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
    public const TABLE_TPR_NUMBER = "number";
    public const TABLE_TPR_NUMBER_MAX_LENGTH = 10;
    public const TABLE_TPR_TEXT = "text";
    public const TABLE_TPR_TEXT_MAX_LENGTH = 750;
    public const TABLE_TPR_NOTES = "notes";
    public const TABLE_TPR_NOTES_MAX_LENGTH = 500;
    
    public const TABLE_TPRLIST = "tprlist";
    public const TABLE_TPRLIST_ID = "id";
    public const TABLE_TPRLIST_NUMBER = "number";
    public const TABLE_TPRLIST_NUMBER_MAX_LENGTH = 10;
    public const TABLE_TPRLIST_TYPE = "type";
    public const TABLE_TPRLIST_TYPE_MAX_LENGTH = 100;
    public const TABLE_TPRLIST_NOTES = "notes";
    public const TABLE_TPRLIST_NOTES_MAX_LENGTH = 500;

    public const TABLE_TPRLIST_TO_TPR = "tprlist_to_tpr";
    public const TABLE_TPRLIST_TO_TPR_PARENT_TPRLIST_ID = "parent_tprlist_id";
    public const TABLE_TPRLIST_TO_TPR_CHILD_TPR_ID = "child_tpr_id";

    public const TABLE_TPRLIST_TO_TPRLIST = "tprlist_to_tprlist";
    public const TABLE_TPRLIST_TO_TPRLIST_PARENT_ID = "parent_id";
    public const TABLE_TPRLIST_TO_TPRLIST_CHILD_ID = "child_id";
    
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
        return array(self::TABLE_COURSELIST_TO_COURSELIST_LEVEL_P,
            self::TABLE_COURSELIST_TO_COURSELIST_LEVEL_100,
            self::TABLE_COURSELIST_TO_COURSELIST_LEVEL_200,
            self::TABLE_COURSELIST_TO_COURSELIST_LEVEL_300,
            self::TABLE_COURSELIST_TO_COURSELIST_LEVEL_400,
            self::TABLE_COURSELIST_TO_COURSELIST_LEVEL_500);        
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
    public static function getDegreeTypes() {
        return array(self::TABLE_DEGREE_TYPE_ARTS,
            self::TABLE_DEGREE_TYPE_SCIENCE,
            self::TABLE_DEGREE_TYPE_COMPUTING,
            self::TABLE_DEGREE_TYPE_MUSIC_THEATRE,
            self::TABLE_DEGREE_TYPE_PHYSICAL_EDUCATION);        
    }
    
    /**
     * 
     * @param type $level
     * @param type $orAbove
     * @return string
     */
    public static function getCourseLevelCondition(
        $level = self::TABLE_COURSELIST_TO_COURSELIST_LEVEL_NONE, 
        $orAbove = false) {
        
        $course = self::TABLE_COURSE;
        $courseNumber = self::TABLE_COURSE_NUMBER;
        $courseLevelP = self::TABLE_COURSELIST_TO_COURSELIST_LEVEL_P;
        $courseLevelNone = self::TABLE_COURSELIST_TO_COURSELIST_LEVEL_NONE;
        
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
        $level = self::TABLE_COURSELIST_TO_COURSELIST_LEVEL_NONE, 
        $orAbove = false) {
        
        $cacLevel = self::TABLE_COURSELIST_TO_COURSELIST_LEVEL;
        $cacOrAbove = self::TABLE_COURSELIST_TO_COURSELIST_OR_ABOVE;
        
        if ($level == self::TABLE_COURSELIST_TO_COURSELIST_LEVEL_NONE) {
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
        $level = self::TABLE_COURSELIST_TO_COURSELIST_LEVEL_NONE, 
        $orAbove = false) {
        $argumentArray = array();
        
        // Create a level condition based on the parameters
        if ($level != self::TABLE_COURSELIST_TO_COURSELIST_LEVEL_NONE) {
            $argumentArray[] = $level;
            if ($orAbove) {
                $argumentArray[] = $orAbove;
            }
        }
        
        return $argumentArray;
    }
    
    /**
     * 
     * @param type $includeExplanation
     * @return string
     */
    public static function getPlanTypeCode($planType, $degreeType) {
        switch ($planType) {
            case self::TABLE_PROGRAM_AND_PLAN_TYPE_MAJOR :
                return 'M';
            case self::TABLE_PROGRAM_AND_PLAN_TYPE_MINOR :
                return ($degreeType == self::TABLE_DEGREE_TYPE_ARTS) ? 'Y' : 'Z';
            case self::TABLE_PROGRAM_AND_PLAN_TYPE_SPECIALIZATION :
                return 'P';
            case self::TABLE_PROGRAM_AND_PLAN_TYPE_GENERAL :
                return 'G';
            default:
                return '';
        }
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
     * Creating Objects
     **************************************************************************/    
    /**    
     * 
     * @param type $dbRow
     * @param type $objectType
     * @return type
     */
    public function createObjectFromDatabaseRow($dbRow, $objectType,
        $initializeArgArray = array()) {
        // Check to make sure the row isn't null or empty before trying to 
        // create an object
        if (! $dbRow) {
            return null;
        }
        
        // Create the 'core' of the object from the row
        $dbObject = call_user_func('\\DatabaseObjects\\'.$objectType."::buildFromDBRow", $dbRow);
        if (! $dbObject) {
            return null;
        }
        
        // Check the arguments to pass into the initialization array; is there
        // anything in the database row that would override it?
        $initializeFinalArgArray = $initializeArgArray;
        foreach ($initializeFinalArgArray as $key => $value) {
            if (array_key_exists($key, $dbRow)) {
                $initializeFinalArgArray[$key] = $dbRow[$key];
            }
        }
                
        // Initialize the object; if no arguments are given, allow the
        // specific implementation to handle them
        if (empty($initializeFinalArgArray)) {
            $dbObject->initialize($this);
        }
        else {
            $dbObject->initialize($this, $initializeFinalArgArray);            
        }
        return $dbObject;
    }
    
    /**
     * 
     * @param type $rowArray
     * @param type $objectType
     * @return type
     */
    public function createObjectsFromDatabaseRows($dbRowArray, $objectType,
        $initializeDefaultArgArray = array()) {       
        // Check to make sure the row isn't null or empty before trying to 
        // create an object
        if (! $dbRowArray) {
            return array();
        }        

        $dbObjectArray = array();
        
        foreach ($dbRowArray as $dbRow) {
            $dbObjectArray[] = $this->createObjectFromDatabaseRow($dbRow, 
                $objectType, $initializeDefaultArgArray);
        }
        
        $sortAfterInitialization = call_user_func('\\DatabaseObjects\\'.$objectType."::sortAfterInitialization");
        
        if ((! empty($dbObjectArray)) && $sortAfterInitialization) {
            usort($dbObjectArray, 
                call_user_func('\\DatabaseObjects\\'.$objectType."::getSortFunction"));
        }

        return $dbObjectArray;
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
        return $this->createObjectsFromDatabaseRows(
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
        $levelValue = self::TABLE_COURSELIST_TO_COURSELIST_LEVEL_NONE, 
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

        $courseEntryArray = $this->createObjectsFromDatabaseRows(
            $this->getQueryResults($query, array($subjectValue)), 
            'CourseEntry');
        return Course::buildCoursesFromCourseEntries($courseEntryArray);
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
        $courseEntryArray = $this->createObjectsFromDatabaseRows(
            $this->getQueryResults($query, array($departmentIDValue)), 
            'CourseEntry');
        return Course::buildCoursesFromCourseEntries($courseEntryArray);     
    }

    /**
     * 
     * @param type $legacyCourseIDValue
     * @return type
     */
    public function getCoursesRelatedToLegacyCourse($legacyCourseIDValue) {
        $course = self::TABLE_COURSE;
        $courseID = self::TABLE_COURSE_ID;
        $courseSubject = self::TABLE_COURSE_SUBJECT;
        $courseNumber = self::TABLE_COURSE_NUMBER;   

        $lcac = self::TABLE_LEGACY_COURSE_TO_COURSE;
        $lcacLegacyCourseID = self::TABLE_LEGACY_COURSE_TO_COURSE_LEGACY_COURSE_ID;
        $lcacCourseID = self::TABLE_LEGACY_COURSE_TO_COURSE_COURSE_ID;
        
        $query = "SELECT $course.* FROM $course JOIN (SELECT * FROM $lcac WHERE $lcac.$lcacLegacyCourseID = ?) AS $lcac ON $course.$courseID = $lcac.$lcacCourseID ORDER BY $course.$courseSubject, $course.$courseNumber ASC";
        $courseEntryArray = $this->createObjectsFromDatabaseRows(
            $this->getQueryResults($query, array($legacyCourseIDValue)), 
            'CourseEntry');
        return Course::buildCoursesFromCourseEntries($courseEntryArray);         
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
        return $this->createObjectsFromDatabaseRows(
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
        return $this->createObjectsFromDatabaseRows(
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
        return $this->createObjectsFromDatabaseRows(
            $this->getQueryResults($query, array($facultyIDValue)), 
            'Program');
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
        $planDescriptiveName = self::TABLE_PLAN_DESCRIPTIVE_NAME;        
                
        $pap = self::TABLE_PROGRAM_AND_PLAN;
        $papPlanID = self::TABLE_PROGRAM_AND_PLAN_PLAN_ID;
        $papProgramID = self::TABLE_PROGRAM_AND_PLAN_PROGRAM_ID;
        
        $query = "SELECT $plan.* FROM $plan JOIN (SELECT * FROM $pap WHERE $pap.$papProgramID = ?) AS $pap ON $pap.$papPlanID = $plan.$planID ORDER BY $plan.$planName, $plan.$planDescriptiveName ASC";
        $resultRow = $this->getQueryResult($query, array($programIDValue));
        
        return $this->createObjectFromDatabaseRow($resultRow, 'Plan');
    }   
    
    /**
     * 
     * @param type $planIDValue
     * @param type $programIDValue
     * @return type
     */
    public function getPlanTypeForProgram($planIDValue, $programIDValue) {                
        $pap = self::TABLE_PROGRAM_AND_PLAN;
        $papPlanID = self::TABLE_PROGRAM_AND_PLAN_PLAN_ID;
        $papProgramID = self::TABLE_PROGRAM_AND_PLAN_PROGRAM_ID;
        $papType = self::TABLE_PROGRAM_AND_PLAN_TYPE;
        
        $query = "SELECT $papType FROM $pap WHERE $pap.$papPlanID = ? AND $pap.$papProgramID = ?";
        $resultRow = $this->getQueryResult($query, array($planIDValue, $programIDValue));
        
        return $resultRow[$papType];
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
        $planDescriptiveName = self::TABLE_PLAN_DESCRIPTIVE_NAME;
                
        $pap = self::TABLE_PLAN_AND_PLLO;
        $papPlanID = self::TABLE_PLAN_AND_PLLO_PLAN_ID;
        $papPLLOID = self::TABLE_PLAN_AND_PLLO_PLLO_ID;
        
        $query = "SELECT $plan.* FROM $plan JOIN (SELECT * FROM $pap WHERE $pap.$papPLLOID = ?) AS $pap ON $pap.$papPlanID = $plan.$planID ORDER BY $plan.$planName, $plan.$planDescriptiveName ASC";
        $resultPlans = $this->createObjectsFromDatabaseRows(
            $this->getQueryResults($query, array($plloIDValue)), 
            'Plan');
        
        return $resultPlans;        
    }
    
    /**
     * 
     * @param type $plloIDValue
     * @return type
     */
    public function getPlansFromDLE($dleIDValue) {
        $plan = self::TABLE_PLAN;
        $planID = self::TABLE_PLAN_ID;
        $planName = self::TABLE_PLAN_NAME;
                
        $pad = self::TABLE_PLLO_AND_DLE;
        $padPLLOID = self::TABLE_PLLO_AND_DLE_PLLO_ID;
        $padDLEID = self::TABLE_PLLO_AND_DLE_DLE_ID;
        
        $pllo = self::TABLE_PLLO;
        $plloID = self::TABLE_PLLO_ID;

        $pap = self::TABLE_PLAN_AND_PLLO;
        $papPlanID = self::TABLE_PLAN_AND_PLLO_PLAN_ID;
        $papPLLOID = self::TABLE_PLAN_AND_PLLO_PLLO_ID;

        
        $query = "SELECT DISTINCT $plan.* FROM (SELECT * FROM $pad WHERE "
                . "$pad.$padDLEID = ?) AS $pad JOIN $pllo ON $pad.$padPLLOID = $pllo.$plloID JOIN "
                . "$pap ON $pllo.$plloID = $pap.$papPLLOID JOIN $plan on $pap.$papPlanID = "
                . "$plan.$planID ORDER BY $plan.$planName ASC";
        $resultPlans = $this->createObjectsFromDatabaseRows(
            $this->getQueryResults($query, array($dleIDValue)), 
            'Plan');
        
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
        $planDescriptiveName = self::TABLE_PLAN_DESCRIPTIVE_NAME;
                
        $dap = self::TABLE_DEPARTMENT_AND_PLAN;
        $dapPlanID = self::TABLE_DEPARTMENT_AND_PLAN_PLAN_ID;
        $dapDepartmentID = self::TABLE_DEPARTMENT_AND_PLAN_DEPARTMENT_ID;
        $dapRole = self::TABLE_DEPARTMENT_AND_PLAN_ROLE;
        
        $cap = self::TABLE_CPRLIST_TO_PLAN;
        $capChildPlanID = self::TABLE_CPRLIST_TO_PLAN_CHILD_PLAN_ID;
        
        $query = "SELECT $plan.* FROM (SELECT * FROM $plan WHERE $plan.$planID NOT IN (SELECT DISTINCT $plan.$planID FROM $plan JOIN $cap on $plan.$planID = $cap.$capChildPlanID)) AS $plan JOIN (SELECT * FROM $dap WHERE $dap.$dapDepartmentID = ?) AS $dap ON $dap.$dapPlanID = $plan.$planID ORDER BY $dap.$dapRole, $plan.$planName, $plan.$planDescriptiveName ASC";
        $resultPlans = $this->createObjectsFromDatabaseRows(
            $this->getQueryResults($query, array($departmentIDValue)), 
            'Plan');
        
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
        return $this->createObjectFromDatabaseRow(
            $this->getQueryResult($query, array($programIDValue)),
            'Degree');
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
        return $this->createObjectsFromDatabaseRows(
            $this->getQueryResults($query, array($degreeIDValue)), 
            'Program');
    }
    
    /**
     * 
     * @param type $planIDValue
     * @return type
     */
    public function getProgramsFromPlan($planIDValue) {
        $program = self::TABLE_PROGRAM;
        $programID = self::TABLE_PROGRAM_ID;
        $programName = self::TABLE_PROGRAM_NAME;                
        
        $pap = self::TABLE_PROGRAM_AND_PLAN;
        $papPlanID = self::TABLE_PROGRAM_AND_PLAN_PLAN_ID;
        $papProgramID = self::TABLE_PROGRAM_AND_PLAN_PROGRAM_ID;
        
        $programArray = array();
        $parentPlan = $this->getAncestorPlanForPlan($planIDValue);
        while ($parentPlan != null) {
            $programArray = array_merge($programArray,
                $this->getProgramsFromPlan($parentPlan->getDBID()));
            $parentPlan = $this->getAncestorPlanForPlan($parentPlan->getDBID());
        }        
        
        $query = "SELECT $program.* FROM $program JOIN (SELECT * FROM $pap WHERE $pap.$papPlanID = ?) AS $pap ON $program.$programID = $pap.$papProgramID ORDER BY $program.$programName ASC";
        $queryResults = $this->createObjectsFromDatabaseRows(
            $this->getQueryResults($query, array($planIDValue)), 
            'Program');
        
        return array_merge($programArray, $queryResults);
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
        return $this->createObjectsFromDatabaseRows(
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
        return $this->createObjectsFromDatabaseRows(
            $this->getQueryResults($query, array($planIDValue)), 
            'Department');
    }
    
    /**
     * 
     * @param type $planIDValue
     * @return type
     */
    public function getAdministrativeDepartmentCodesForPLLO($plloIDValue) {
        $pap = self::TABLE_PLAN_AND_PLLO;
        $papPlanID = self::TABLE_PLAN_AND_PLLO_PLAN_ID;
        $papPLLOID = self::TABLE_PLAN_AND_PLLO_PLLO_ID;
        
        $dap = self::TABLE_DEPARTMENT_AND_PLAN;
        $dapDepartmentID = self::TABLE_DEPARTMENT_AND_PLAN_DEPARTMENT_ID;
        $dapPlanID = self::TABLE_DEPARTMENT_AND_PLAN_PLAN_ID;
        $dapRole = self::TABLE_DEPARTMENT_AND_PLAN_ROLE;
        $dapRoleAdministrator = self::TABLE_DEPARTMENT_AND_PLAN_ROLE_ADMINISTRATOR;

        $department = self::TABLE_DEPARTMENT;
        $departmentID = self::TABLE_DEPARTMENT_ID;
        $departmentCode = self::TABLE_DEPARTMENT_CODE;
        
        $query = "SELECT DISTINCT $department.$departmentCode FROM (SELECT * FROM $pap WHERE $pap.$papPLLOID = ?) AS $pap JOIN (SELECT * FROM $dap WHERE $dap.role = 'Administrator') AS $dap ON $pap.$papPlanID = $dap.$dapPlanID JOIN (SELECT * FROM $department WHERE $department.$departmentCode IS NOT NULL) AS $department ON $dap.$dapDepartmentID = $department.$departmentID ORDER BY $department.$departmentCode ASC";
        $queryResult = $this->getQueryResults($query, array($plloIDValue));
        return self::extractValueFromDBRows($queryResult, $departmentCode);
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
        return $this->createObjectsFromDatabaseRows(
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
        return $this->createObjectsFromDatabaseRows(
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
        $planID = self::TABLE_PLAN_ID;
        $planName = self::TABLE_PLAN_NAME;
        $planDescriptiveName = self::TABLE_PLAN_DESCRIPTIVE_NAME;
        
        $ctp = self::TABLE_CPRLIST_TO_PLAN;
        $ctpChildID = self::TABLE_CPRLIST_TO_PLAN_CHILD_PLAN_ID;
        
        $query = "SELECT $plan.* FROM $plan LEFT JOIN cprlist_to_plan ON $plan.$planID = $ctp.$ctpChildID WHERE $ctp.$ctpChildID IS NULL ORDER BY $plan.$planName, $plan.$planDescriptiveName";
        return $this->createObjectsFromDatabaseRows(
            $this->getQueryResults($query, array()),
            'Plan');
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
    public function getChildCPRListsForPlan($planIDValue, $cprlTypeValue = null) {
        $cprl = self::TABLE_CPRLIST;
        $cprlID = self::TABLE_CPRLIST_ID;
        $cprlNumber = self::TABLE_CPRLIST_NUMBER;
        $cprlType = self::TABLE_CPRLIST_TYPE;
        
        $pac = self::TABLE_PLAN_TO_CPRLIST;
        $pacPlanID = self::TABLE_PLAN_TO_CPRLIST_PARENT_PLAN_ID;
        $pacCPRListID = self::TABLE_PLAN_TO_CPRLIST_CHILD_CPRLIST_ID;
        
        $cprlTable = $cprl;
        $valueArray = array($planIDValue);
        if ($cprlTypeValue) {
            $cprlTable = "(SELECT * FROM $cprl WHERE $cprl.$cprlType = ?) AS $cprl";
            $valueArray = array($cprlTypeValue, $planIDValue);
        }

        $query = "SELECT $cprl.* FROM $cprlTable JOIN (SELECT * FROM $pac WHERE $pac.$pacPlanID = ?) AS $pac ON $cprl.$cprlID = $pac.$pacCPRListID ORDER BY $cprl.$cprlNumber ASC";
        return $this->createObjectsFromDatabaseRows(
            $this->getQueryResults($query, $valueArray), 
            'CPRList');        
    }
    
    /**
     * 
     * @param type $cprIDValue
     * @return type
     */
    public function getParentCPRListForCPR($cprIDValue) {
        $cpr = self::TABLE_CPR;
        $cprID = self::TABLE_CPR_ID;
        $cprNumber = self::TABLE_CPR_NUMBER;        
        
        $cac = self::TABLE_CPRLIST_TO_CPR;
        $cacCPRListID = self::TABLE_CPRLIST_TO_CPR_PARENT_CPRLIST_ID;
        $cacCPRID = self::TABLE_CPRLIST_TO_CPR_CHILD_CPR_ID;

        $cprl = self::TABLE_CPRLIST;
        $cprlID = self::TABLE_CPRLIST_ID;
        $cprlNumber = self::TABLE_CPRLIST_NUMBER;
        $cprlType = self::TABLE_CPRLIST_TYPE;
        
        $query = "SELECT $cprl.* FROM $cprl JOIN (SELECT * FROM $cac WHERE $cac.$cacCPRID = ?) AS $cac ON $cprl.$cprlID = $cac.$cacCPRListID";
        return $this->createObjectFromDatabaseRow(
            $this->getQueryResult($query, array($cprIDValue)), 
            'CPRList');        
    }
    
    /**
     * 
     * @param type $cprListIDValue
     * @return type
     */
    public function getParentCPRListForCPRList($cprListIDValue) {
        $cprl = self::TABLE_CPRLIST;
        $cprlID = self::TABLE_CPRLIST_ID;
        $cprlNumber = self::TABLE_CPRLIST_NUMBER;
        $cprlType = self::TABLE_CPRLIST_TYPE;        
        
        $cac = self::TABLE_CPRLIST_TO_CPRLIST;
        $cacCPRLParentID = self::TABLE_CPRLIST_TO_CPRLIST_PARENT_ID;
        $cacCPRLChildID = self::TABLE_CPRLIST_TO_CPRLIST_CHILD_ID;
        
        $query = "SELECT $cprl.* FROM $cprl JOIN (SELECT * FROM $cac WHERE $cac.$cacCPRLChildID = ?) AS $cac ON $cprl.$cprlID = $cac.$cacCPRLParentID";
        return $this->createObjectFromDatabaseRow(
            $this->getQueryResult($query, array($cprListIDValue)), 
            'CPRList');        
    }    
    
    /**
     * 
     * @return type
     */
    public function getTypeForCPR($cprIDValue) {        
        $cac = self::TABLE_CPRLIST_TO_CPR;
        $cacCPRListID = self::TABLE_CPRLIST_TO_CPR_PARENT_CPRLIST_ID;
        $cacCPRID = self::TABLE_CPRLIST_TO_CPR_CHILD_CPR_ID;

        $cprl = self::TABLE_CPRLIST;
        $cprlID = self::TABLE_CPRLIST_ID;
        $cprlType = self::TABLE_CPRLIST_TYPE;
        
        $query = "SELECT $cprl.$cprlType FROM $cprl JOIN (SELECT * FROM $cac WHERE $cac.$cacCPRID = ?) AS $cac ON $cprl.$cprlID = $cac.$cacCPRListID";
        $result = $this->getQueryResult($query, array($cprIDValue));
        
        return (! empty($result)) ? $result[$cprlType] : '';
    }
    
    /**
     * 
     * @return type
     */
    public function getTypeForTPR($tprIDValue) {        
        $tat = self::TABLE_TPRLIST_TO_TPR;
        $tatTPRListID = self::TABLE_TPRLIST_TO_TPR_PARENT_TPRLIST_ID;
        $tatTPRID = self::TABLE_TPRLIST_TO_TPR_CHILD_TPR_ID;

        $tprl = self::TABLE_TPRLIST;
        $tprlID = self::TABLE_TPRLIST_ID;
        $tprlType = self::TABLE_TPRLIST_TYPE;
        
        $query = "SELECT $tprl.$tprlType FROM $tprl JOIN (SELECT * FROM $tat WHERE $tat.$tatTPRID = ?) AS $tat ON $tprl.$tprlID = $tat.$tatTPRListID";
        $result = $this->getQueryResult($query, array($tprIDValue));
        
        return (! empty($result)) ? $result[$tprlType] : '';
    }     
    
    /**
     * 
     * @param type $cprListIDValue
     * @return type
     */
    public function getChildCPRsForCPRList($cprListIDValue) {
        $cpr = self::TABLE_CPR;
        $cprID = self::TABLE_CPR_ID;
        $cprNumber = self::TABLE_CPR_NUMBER;        
        
        $cac = self::TABLE_CPRLIST_TO_CPR;
        $cacCPRListID = self::TABLE_CPRLIST_TO_CPR_PARENT_CPRLIST_ID;
        $cacCPRID = self::TABLE_CPRLIST_TO_CPR_CHILD_CPR_ID;
        
        $query = "SELECT $cpr.* FROM $cpr JOIN (SELECT * FROM $cac WHERE $cac.$cacCPRListID = ?) AS $cac ON $cpr.$cprID = $cac.$cacCPRID ORDER BY $cpr.$cprNumber ASC";        
        return $this->createObjectsFromDatabaseRows(
            $this->getQueryResults($query, array($cprListIDValue)), 
            'CoursePlanRequirement');
    }
    
    /**
     * 
     * @param type $cprListIDValue
     * @return type
     */
    public function getChildPlansInCPRList($cprListIDValue) {
        $plan = self::TABLE_PLAN;
        $planID = self::TABLE_PLAN_ID;
        $planNumber = self::TABLE_PLAN_NUMBER;        
        $planCode = self::TABLE_PLAN_CODE;        
        
        $cap = self::TABLE_CPRLIST_TO_PLAN;
        $capCPRListID = self::TABLE_CPRLIST_TO_PLAN_PARENT_CPRLIST_ID;
        $capPlanID = self::TABLE_CPRLIST_TO_PLAN_CHILD_PLAN_ID;
        
        $query = "SELECT $plan.* FROM $plan JOIN (SELECT * FROM $cap WHERE $cap.$capCPRListID = ?) AS $cap ON $plan.$planID = $cap.$capPlanID ORDER BY $plan.$planNumber, $plan.$planCode ASC";        
        return $this->createObjectsFromDatabaseRows(
            $this->getQueryResults($query, array($cprListIDValue)), 
            'Plan');
    }    

    /**
     * 
     * @param type $cprListIDValue
     * @return type
     */
    public function getChildCPRListsForCPRList($cprListIDValue) {
        $cprl = self::TABLE_CPRLIST;
        $cprlID = self::TABLE_CPRLIST_ID;
        $cprlNumber = self::TABLE_CPRLIST_NUMBER;

        $cac = self::TABLE_CPRLIST_TO_CPRLIST;
        $cacParentID = self::TABLE_CPRLIST_TO_CPRLIST_PARENT_ID;
        $cacChildID = self::TABLE_CPRLIST_TO_CPRLIST_CHILD_ID;

        $query = "SELECT $cprl.* FROM $cprl JOIN (SELECT * FROM $cac WHERE $cac.$cacParentID = ?) AS $cac ON $cprl.$cprlID = $cac.$cacChildID ORDER BY $cprl.$cprlNumber ASC";
        return $this->createObjectsFromDatabaseRows(
            $this->getQueryResults($query, array($cprListIDValue)),
            'CPRList');
    }
    
    /**
     * 
     * @param type $cprIDValue
     * @return type
     */
    public function getChildCPRListsForCPR($cprIDValue) {
        $cprl = self::TABLE_CPRLIST;
        $cprlID = self::TABLE_CPRLIST_ID;
        $cprlNumber = self::TABLE_CPRLIST_NUMBER;

        $cac = self::TABLE_CPR_TO_CPRLIST;
        $cacParentCPRID = self::TABLE_CPR_TO_CPRLIST_PARENT_CPR_ID;
        $cacChildCPRListID = self::TABLE_CPR_TO_CPRLIST_CHILD_CPRLIST_ID;

        $query = "SELECT $cprl.* FROM $cprl JOIN (SELECT * FROM $cac WHERE $cac.$cacParentCPRID = ?) AS $cac ON $cprl.$cprlID = $cac.$cacChildCPRListID ORDER BY $cprl.$cprlNumber ASC";
        return $this->createObjectsFromDatabaseRows(
            $this->getQueryResults($query, array($cprIDValue)),
            'CPRList');
    }    
    
    /**
     * This function might return a CPR, a CPRList or a Plan
     * 
     * @param type $cprListIDValue
     * @return type
     */
    public function getAncestorPlanForCPRList($cprListIDValue) {
        $cprl = self::TABLE_CPRLIST;
        $cprlID = self::TABLE_CPRLIST_ID;
        $cprlNumber = self::TABLE_CPRLIST_NUMBER;
                
        $pac = self::TABLE_PLAN_TO_CPRLIST;
        $pacPlanID = self::TABLE_PLAN_TO_CPRLIST_PARENT_PLAN_ID;
        $pacCPRListID = self::TABLE_PLAN_TO_CPRLIST_CHILD_CPRLIST_ID;

        $clacl = self::TABLE_CPRLIST_TO_CPRLIST;
        $claclParentID = self::TABLE_CPRLIST_TO_CPRLIST_PARENT_ID;
        $claclChildID = self::TABLE_CPRLIST_TO_CPRLIST_CHILD_ID;

        $cacl = self::TABLE_CPR_TO_CPRLIST;
        $caclParentCPRID = self::TABLE_CPR_TO_CPRLIST_PARENT_CPR_ID;
        $caclChildCPLID = self::TABLE_CPR_TO_CPRLIST_CHILD_CPRLIST_ID;
        
        // The most likely option is that the parent is a Plan
        $query = "SELECT $pacPlanID FROM $pac WHERE $pacCPRListID = ?";                
        $result = $this->getQueryResult($query, array($cprListIDValue));
        if (! empty($result)) {
            return $this->getPlanFromID($result[$pacPlanID]);
        }
        
        // The second most likely option is that the parent is another CPRList
        // and we'll need need the Plan from that one
        $query = "SELECT $claclParentID FROM $clacl WHERE $claclChildID = ?";                
        $result = $this->getQueryResult($query, array($cprListIDValue));
        if (! empty($result)) {
            return $this->getAncestorPlanForCPRList($result[$claclParentID]);
        }
        
        // The last option is that the parent is a CPR and we'll need its Plan
        $query = "SELECT $caclParentCPRID FROM $cacl WHERE $caclChildCPLID = ?";                
        $result = $this->getQueryResult($query, array($cprListIDValue));
        return (! empty($result)) ?
            $this->getAncestorPlanForCPR($result[$caclParentCPRID]) :
            null;        
    }
            
    /**
     * 
     * @param type $planIDValue
     * @param type $tprTypeValue
     * @return type
     */
    public function getChildTPRListsForPlan($planIDValue, $tprlTypeValue = null) {
        $tprl = self::TABLE_TPRLIST;
        $tprlID = self::TABLE_TPRLIST_ID;
        $tprlNumber = self::TABLE_TPRLIST_NUMBER;
        $tprlType = self::TABLE_TPRLIST_TYPE;
        
        $pac = self::TABLE_PLAN_TO_TPRLIST;
        $pacPlanID = self::TABLE_PLAN_TO_TPRLIST_PARENT_PLAN_ID;
        $pacTPRListID = self::TABLE_PLAN_TO_TPRLIST_CHILD_TPRLIST_ID;
        
        $tprlTable = $tprl;
        $valueArray = array($planIDValue);
        if ($tprlTypeValue) {
            $tprlTable = "(SELECT * FROM $tprl WHERE $tprl.$tprlType = ?) AS $tprl";
            $valueArray = array($tprlTypeValue, $planIDValue);
        }

        $query = "SELECT $tprl.* FROM $tprlTable JOIN (SELECT * FROM $pac WHERE $pac.$pacPlanID = ?) AS $pac ON $tprl.$tprlID = $pac.$pacTPRListID ORDER BY $tprl.$tprlNumber ASC";
        return $this->createObjectsFromDatabaseRows(
            $this->getQueryResults($query, $valueArray), 
            'TPRList');        
    }
    
    /**
     * 
     * @param type $tprListIDValue
     * @param type $levelValue
     * @param type $orAboveValue
     * @return type
     */
    public function getChildTPRsForTPRList($tprListIDValue) {
        $tpr = self::TABLE_TPR;
        $tprID = self::TABLE_TPR_ID;
        $tprNumber = self::TABLE_TPR_NUMBER;        
        
        $tat = self::TABLE_TPRLIST_TO_TPR;
        $tatTPRListID = self::TABLE_TPRLIST_TO_TPR_PARENT_TPRLIST_ID;
        $tatTPRID = self::TABLE_TPRLIST_TO_TPR_CHILD_TPR_ID;
        
        $query = "SELECT $tpr.* FROM $tpr JOIN (SELECT * FROM $tat WHERE $tat.$tatTPRListID = ?) AS $tat ON $tpr.$tprID = $tat.$tatTPRID ORDER BY $tpr.$tprNumber ASC";
        
        return $this->createObjectsFromDatabaseRows(
            $this->getQueryResults($query, array($tprListIDValue)), 
            'TextPlanRequirement');
    }

    /**
     * 
     * @param type $tprListIDValue
     * @return type
     */
    public function getChildTPRListsForTPRList($tprListIDValue) {
        $tprl = self::TABLE_TPRLIST;
        $tprlID = self::TABLE_TPRLIST_ID;
        $tprlNumber = self::TABLE_TPRLIST_NUMBER;

        $tat = self::TABLE_TPRLIST_TO_TPRLIST;
        $tatParentID = self::TABLE_TPRLIST_TO_TPRLIST_PARENT_ID;
        $tatChildID = self::TABLE_TPRLIST_TO_TPRLIST_CHILD_ID;

        $query = "SELECT $tprl.* FROM $tprl JOIN (SELECT * FROM $tat WHERE $tat.$tatParentID = ?) AS $tat ON $tprl.$tprlID = $tat.$tatChildID ORDER BY $tprl.$tprlNumber ASC";
        return $this->createObjectsFromDatabaseRows(
            $this->getQueryResults($query, array($tprListIDValue)),
            'TPRList');
    }
    
    /**
     * This function might return a TPRList or a Plan
     * 
     * @param type $tprListIDValue
     * @return type
     */
    public function getParentForTPRList($tprListIDValue) {
        $tprl = self::TABLE_TPRLIST;
        $tprlID = self::TABLE_TPRLIST_ID;
        $tprlNumber = self::TABLE_TPRLIST_NUMBER;
                
        $pat = self::TABLE_PLAN_TO_TPRLIST;
        $patPlanID = self::TABLE_PLAN_TO_TPRLIST_PARENT_PLAN_ID;
        $patTPRListID = self::TABLE_PLAN_TO_TPRLIST_CHILD_TPRLIST_ID;

        $tlatl = self::TABLE_TPRLIST_TO_TPRLIST;
        $tlatlParentID = self::TABLE_TPRLIST_TO_TPRLIST_PARENT_ID;
        $tlatlChildID = self::TABLE_TPRLIST_TO_TPRLIST_CHILD_ID;
        
        // The most likely option is that the parent is a Plan
        $query = "SELECT $patPlanID FROM $pat WHERE $patTPRListID = ?";                
        $result = $this->getQueryResults($query, array($tprListIDValue));
        if (! empty($result)) {
            return $this->getPlanFromID($result[self::TABLE_PLAN_ID]);
        }
        
        // The other option is that the parent is another TPRList
        $query = "SELECT $tlatlParentID FROM $tlatl WHERE $tlatlChildID = ?";                
        $result = $this->getQueryResults($query, array($tprListIDValue));
        return (! empty($result)) ?
            $this->getTPRFromID($result[self::TABLE_TPR_ID]) :
            null;        
    }         
    
    /**
     * 
     * @param type $cprIDValue
     * @return type
     */
    public function getAncestorPlanForCPR($cprIDValue) {        
        $cac = self::TABLE_CPRLIST_TO_CPR;
        $cacCPRListID = self::TABLE_CPRLIST_TO_CPR_PARENT_CPRLIST_ID;
        $cacCPRID = self::TABLE_CPRLIST_TO_CPR_CHILD_CPR_ID;
                        
        $query = "SELECT $cacCPRListID FROM $cac WHERE $cacCPRID = ?";
        $result = $this->getQueryResult($query, array($cprIDValue));
        return (! empty($result)) ?
            $this->getAncestorPlanForCPRList($result[$cacCPRListID]) :
            null;  
    }
    
    /**
     * 
     * @param type $cprIDValue
     * @return type
     */
    public function getAncestorPlanForPlan($planIDValue) {
        $plan = self::TABLE_PLAN;
        $planID = self::TABLE_PLAN_ID;
        
        $ctp = self::TABLE_CPRLIST_TO_PLAN;
        $ctpCPRListID = self::TABLE_CPRLIST_TO_PLAN_PARENT_CPRLIST_ID;
        $ctpPlanID = self::TABLE_CPRLIST_TO_PLAN_CHILD_PLAN_ID;

        $ptc = self::TABLE_PLAN_TO_CPRLIST;
        $ptcPlanID = self::TABLE_PLAN_TO_CPRLIST_PARENT_PLAN_ID;
        $ptcCPRListID = self::TABLE_PLAN_TO_CPRLIST_CHILD_CPRLIST_ID;
        
        $query = "SELECT $plan.* FROM (SELECT * FROM $ctp WHERE $ctp.$ctpPlanID = ?) AS $ctp JOIN $ptc ON $ctp.$ctpCPRListID = $ptc.$ptcCPRListID JOIN $plan ON $ptc.$ptcPlanID = $plan.$planID";
        $result = $this->getQueryResult($query, array($planIDValue));
        return $this->createObjectFromDatabaseRow(
            $this->getQueryResult($query, array($planIDValue)),
            'Plan');  
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
        
        $pat = self::TABLE_PLAN_TO_TPRLIST;
        $patPlanID = self::TABLE_PLAN_TO_TPRLIST_PARENT_PLAN_ID;
        $patTPRID = self::TABLE_PLAN_TO_TPRLIST_CHILD_TPRLIST_ID;
        
        $query = "SELECT $plan.* FROM $plan JOIN (SELECT * FROM $pat WHERE $pat.$patTPRID = ?) AS $pat ON $plan.$planID = $pat.$patPlanID ORDER BY $plan.$planName ASC";
        return $this->createObjectFromDatabaseRow(
            $this->getQueryResult($query, array($tprIDValue)),
            'Plan');
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
        return $this->createObjectFromDatabaseRow(
            $this->getQueryResult($query, array($cprIDValue)),
            'CourseList',
            array(self::TABLE_CPR_AND_COURSELIST_LEVEL => self::TABLE_CPR_AND_COURSELIST_LEVEL_NONE, 
                self::TABLE_CPR_AND_COURSELIST_OR_ABOVE => false)
            );
    }    
    
    /**
     * 
     * @param type $courseListIDValue
     * @param type $levelValue
     * @param type $orAboveValue
     * @return type
     */
    public function getCoursesInCourseList($courseListIDValue, 
        $levelValue = self::TABLE_COURSELIST_TO_COURSELIST_LEVEL_NONE, 
        $orAboveValue = false) {
        $course = self::TABLE_COURSE;
        $courseID = self::TABLE_COURSE_ID;
        $courseSubject = self::TABLE_COURSE_SUBJECT;        
        $courseNumber = self::TABLE_COURSE_NUMBER;        
        
        $cac = self::TABLE_COURSELIST_TO_COURSE;
        $cacCourseListID = self::TABLE_COURSELIST_TO_COURSE_PARENT_COURSELIST_ID;
        $cacCourseID = self::TABLE_COURSELIST_TO_COURSE_CHILD_COURSE_ID;
        
        $levelCondition = self::getCourseLevelCondition($levelValue, $orAboveValue);
        
        $query = "SELECT $course.* FROM $course JOIN (SELECT * FROM $cac WHERE $cac.$cacCourseListID = ?) AS $cac ON $course.$courseID = $cac.$cacCourseID"; 
        if ($levelCondition) {
            $query .= " WHERE $levelCondition";
        }        
        $query .= " ORDER BY $course.$courseSubject, $course.$courseNumber ASC";
        
        $courseEntryArray = $this->createObjectsFromDatabaseRows(
            $this->getQueryResults($query, array($courseListIDValue)), 
            'CourseEntry');
        return Course::buildCoursesFromCourseEntries($courseEntryArray);     
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

        $cac = self::TABLE_COURSELIST_TO_COURSELIST;
        $cacParentID = self::TABLE_COURSELIST_TO_COURSELIST_PARENT_ID;
        $cacChildID = self::TABLE_COURSELIST_TO_COURSELIST_CHILD_ID;
        $cacLevel = self::TABLE_COURSELIST_TO_COURSELIST_LEVEL;
        $cacOrAbove = self::TABLE_COURSELIST_TO_COURSELIST_OR_ABOVE;

        $query = "SELECT $cl.*, $cac.$cacLevel, $cac.$cacOrAbove FROM $cl JOIN (SELECT * FROM $cac WHERE $cac.$cacParentID = ?) AS $cac ON $cl.$clID = $cac.$cacChildID ORDER BY $cl.$clName ASC";
        return $this->createObjectsFromDatabaseRows(
            $this->getQueryResults($query, array($courseListIDValue)),
                'CourseList',
                array(self::TABLE_COURSELIST_TO_COURSELIST_LEVEL => self::TABLE_COURSELIST_TO_COURSELIST_LEVEL_NONE, 
                    self::TABLE_COURSELIST_TO_COURSELIST_OR_ABOVE => false)
            );
    }
    
    /**
     * 
     * @param type $courseListIDValue
     * @param type $levelValue
     * @param type $orAboveValue
     * @return type
     */
    public function getParentCourseLists($courseListIDValue, 
        $levelValue = self::TABLE_COURSELIST_TO_COURSELIST_LEVEL_NONE, 
        $orAboveValue = false) {
        $cl = self::TABLE_COURSELIST;
        $clID = self::TABLE_COURSELIST_ID;
        $clName = self::TABLE_COURSELIST_NAME;

        $cac = self::TABLE_COURSELIST_TO_COURSELIST;
        $cacParentID = self::TABLE_COURSELIST_TO_COURSELIST_PARENT_ID;
        $cacChildID = self::TABLE_COURSELIST_TO_COURSELIST_CHILD_ID;
        
        // Create a level condition based on the parameters
        $levelCondition = self::getCourseListLevelCondition($cac, $levelValue, $orAboveValue);
        $queryArguments = array_merge(array($courseListIDValue),
            self::getCourseListLevelArguments($levelValue, $orAboveValue));
       
        $query = "SELECT $cl.* FROM ($cl JOIN (SELECT * FROM $cac WHERE $cac.$cacChildID = ? $levelCondition) AS $cac ON $cl.$clID = $cac.$cacParentID) ORDER BY $cl.$clName ASC";                
        return $this->createObjectsFromDatabaseRows(
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
        $cal = self::TABLE_COURSELIST_TO_COURSELIST_LEVEL;
        $calParentID = self::TABLE_COURSELIST_TO_COURSELIST_LEVEL_PARENT_ID;
        $calChildID = self::TABLE_COURSELIST_TO_COURSELIST_LEVEL_CHILD_ID;
        $calLevel = self::TABLE_COURSELIST_TO_COURSELIST_LEVEL_LEVEL;
        $calOrAbove = self::TABLE_COURSELIST_TO_COURSELIST_LEVEL_OR_ABOVE;

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
        $levelValue = self::TABLE_COURSELIST_TO_COURSELIST_LEVEL_NONE, 
        $orAboveValue = false) {
        $cprResultArray = array();
        
        $cpr = self::TABLE_CPR;
        $cprID = self::TABLE_CPR_ID;
        $cprNumber = self::TABLE_CPR_NUMBER;        
        
        $cac = self::TABLE_CPR_AND_COURSELIST;
        $cacCourseListID = self::TABLE_CPR_AND_COURSELIST_COURSELIST_ID;
        $cacCPRID = self::TABLE_CPR_AND_COURSELIST_CPR_ID;
        
        // Create a level condition based on the parameters
        $levelCondition = self::getCourseListLevelCondition($cac, $levelValue, $orAboveValue);
        $queryArguments = array_merge(array($clIDValue),
            self::getCourseListLevelArguments($levelValue, $orAboveValue));
        
        // Start by finding all CPRs that have this list as their direct child                
        $query = "SELECT $cpr.* FROM ($cpr JOIN (SELECT * FROM $cac WHERE $cac.$cacCourseListID = ? $levelCondition) AS $cac ON $cpr.$cprID = $cac.$cacCPRID) ORDER BY $cpr.$cprNumber ASC";
        $parentCPRArray = $this->createObjectsFromDatabaseRows(
            $this->getQueryResults($query, $queryArguments),
            'CoursePlanRequirement');
                
        // Add these CPRs to the result; index by the ID to prevent duplicates
        foreach ($parentCPRArray as $parentCPR) {
            $cprResultArray[$parentCPR->getDBID()] = $parentCPR;
        }
                
        // Now find all CourseLists that have this list as a direct child
        $parentCourseListArray = $this->getParentCourseLists($clIDValue, $levelValue, $orAboveValue);
        
        // Go through each of these and find their CPR ancestors
        foreach ($parentCourseListArray as $parentCourseList) {
            $recursiveCPRArray = $this->getCPRsForCourseList($parentCourseList->getDBID());

            foreach ($recursiveCPRArray as $recursiveCPR) {
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
        $courseEntryArray = $this->createObjectsFromDatabaseRows(
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
        return $this->createObjectFromDatabaseRow(
            $this->getRowFromID(self::TABLE_CLLO, self::TABLE_CLLO_ID, $idValue),
            'CourseLevelLearningOutcome');
    }

    /**
     * Queries the database for the single PLLO with the given ID.
     *
     * @param $idValue   The PLLO's ID (string or numeric)
     * @return 
     */
    public function getPLLOFromID($idValue) {
        return $this->createObjectFromDatabaseRow(
            $this->getRowFromID(self::TABLE_PLLO, self::TABLE_PLLO_ID, $idValue),
            'PlanLevelLearningOutcome');
    }

    /**
     * Queries the database for the single ILO with the given ID.
     *
     * @param $idValue   The ILO's ID (string or numeric)
     * @return  
     */
    public function getILOFromID($idValue) {
        return $this->createObjectFromDatabaseRow(
            $this->getRowFromID(self::TABLE_ILO, self::TABLE_ILO_ID, $idValue),
            'InstitutionLearningOutcome');
    }

    /**
     * Queries the database for the single DLE with the given ID.
     *
     * @param $idValue   The DLE's ID (string or numeric)
     * @return      
     */
    public function getDLEFromID($idValue) {
        return $this->createObjectFromDatabaseRow(
            $this->getRowFromID(self::TABLE_DLE, self::TABLE_DLE_ID, $idValue),
            'DegreeLevelExpectation');
    }

    /**
     * Queries the database for the single user with the given ID.
     *
     * @param $idValue    The user's ID (string)
     * @return
     */
    public function getUserFromID($idValue) {
        return $this->createObjectFromDatabaseRow(
            $this->getRowFromID(self::TABLE_USER, self::TABLE_USER_ID, $idValue),
            'User');
    }

    /**
     * Queries the database for the single revision with the given ID.
     *
     * @param $idValue   The revision's ID (string or numeric)
     * @return      
     */
    public function getRevisionFromID($idValue) {
        return $this->createObjectFromDatabaseRow(
            $this->getRowFromID(self::TABLE_REVISION, self::TABLE_REVISION_ID, $idValue),
            'Revision');
    }
    
    /**
     * Queries the database for the single department with the given ID.
     *
     * @param $idValue   The department's ID (string or numeric)
     * @return    
     */
    public function getDepartmentFromID($idValue) {
        return $this->createObjectFromDatabaseRow(
            $this->getRowFromID(self::TABLE_DEPARTMENT, self::TABLE_DEPARTMENT_ID, $idValue),
            'Department');
    }   
    
    /**
     * Queries the database for the single faculty with the given ID.
     *
     * @param $idValue   The faculty's ID (string or numeric)
     * @return      
     */
    public function getFacultyFromID($idValue) {
        return $this->createObjectFromDatabaseRow(
            $this->getRowFromID(self::TABLE_FACULTY, self::TABLE_FACULTY_ID, $idValue),
            'Faculty');
    }
    
    /**
     * Queries the database for the single degree with the given ID.
     *
     * @param $idValue   The degree's ID (string or numeric)
     * @return      
     */
    public function getDegreeFromID($idValue) {
        return $this->createObjectFromDatabaseRow(
            $this->getRowFromID(self::TABLE_DEGREE, self::TABLE_DEGREE_ID, $idValue),
            'Degree');
    }
    
    /**
     * Queries the database for the single plan with the given ID.
     *
     * @param $idValue   The plan's ID (string or numeric)
     * @return      
     */
    public function getPlanFromID($idValue) {
        return $this->createObjectFromDatabaseRow(
            $this->getRowFromID(self::TABLE_PLAN, self::TABLE_PLAN_ID, $idValue),
            'Plan');
    }

    /**
     * Queries the database for the single program with the given ID.
     *
     * @param $idValue   The program's ID (string or numeric)
     * @return      
     */
    public function getProgramFromID($idValue) {
        return $this->createObjectFromDatabaseRow(
            $this->getRowFromID(self::TABLE_PROGRAM, self::TABLE_PROGRAM_ID, $idValue),
            'Program');
    } 
    
    /**
     * Queries the database for the single CPR with the given ID.
     *
     * @param $idValue   The CPR's ID (string or numeric)
     * @return      
     */
    public function getCPRFromID($idValue) {
        return $this->createObjectFromDatabaseRow(
            $this->getRowFromID(self::TABLE_CPR, self::TABLE_CPR_ID, $idValue),
            'CoursePlanRequirement');
    } 

    /**
     * Queries the database for the single TPR with the given ID.
     *
     * @param $idValue   The TPR's ID (string or numeric)
     * @return      
     */
    public function getTPRFromID($idValue) {
        return $this->createObjectFromDatabaseRow(
            $this->getRowFromID(self::TABLE_TPR, self::TABLE_TPR_ID, $idValue),
            'TextPlanRequirement');
    }     

    /**
     * Queries the database for the single CourseList with the given ID.
     *
     * @param $idValue   The CourseList's ID (string or numeric)
     * @return      
     */
    public function getCourseListFromID($idValue, 
        $levelValue = self::TABLE_COURSELIST_TO_COURSELIST_LEVEL_NONE, 
        $orAboveValue = false) {
        return $this->createObjectFromDatabaseRow(
            $this->getRowFromID(self::TABLE_COURSELIST, self::TABLE_COURSELIST_ID, $idValue),
            'CourseList',
            array(self::TABLE_COURSELIST_TO_COURSELIST_LEVEL => $levelValue, 
                self::TABLE_COURSELIST_TO_COURSELIST_OR_ABOVE => $orAboveValue)
            );
    }     
    
    /**
     * Queries the database for the single CPRList with the given ID.
     *
     * @param $idValue   The CPRList's ID (string or numeric)
     * @return      
     */
    public function getCPRListFromID($idValue) {
        return $this->createObjectFromDatabaseRow(
            $this->getRowFromID(self::TABLE_CPRLIST, self::TABLE_CPRLIST_ID, $idValue),
            'CPRList');
    }     

    /**
     * Queries the database for the single TPRList with the given ID.
     *
     * @param $idValue   The TPRList's ID (string or numeric)
     * @return      
     */
    public function getTPRListFromID($idValue) {
        return $this->createObjectFromDatabaseRow(
            $this->getRowFromID(self::TABLE_TPRLIST, self::TABLE_TPRLIST_ID, $idValue),
            'TPRList');
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
        return $this->createObjectsFromDatabaseRows(
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
       return $this->createObjectsFromDatabaseRows(
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
        return $this->createObjectsFromDatabaseRows(
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
        return $this->createObjectsFromDatabaseRows(
            $this->getChildRows(self::TABLE_PLLO, 
                self::TABLE_PLLO_PARENT_ID, $plloIDValue, 
                self::TABLE_PLLO_NUMBER),
            'PlanLevelLearningOutcome');
    }
    
    /**
     * 
     * @param type $plloIDValue
     * @return int
     */
    public function getPLLOHeight($plloIDValue) {
        $childPLLORows = $this->getChildRows(self::TABLE_PLLO, 
            self::TABLE_PLLO_PARENT_ID, $plloIDValue, self::TABLE_PLLO_NUMBER);
        
        if (empty($childPLLORows)) {
            return 1;
        }
        
        $maxHeight = 1;
        foreach ($childPLLORows as $childPLLORow) {
            $childHeight = $this->getPLLOHeight(
                $childPLLORow[self::TABLE_PLLO_ID]);
            if ($childHeight > $maxHeight) {
                $maxHeight = $childHeight;
            }
        }
        
        return $maxHeight + 1;        
    }   
        
    /**
     * 
     * @param type $plloIDValue
     * @return int
     */
    public function getPLLODepth($plloIDValue) {
        $pllo = $this->getPLLOFromID($plloIDValue);
        
        if (! $pllo) {
            return 0;
        }
        
        $parentPLLOID = $pllo->getParentDBID();
        if (! $parentPLLOID) {
            return 1;
        }
        
        return 1 + $this->getPLLODepth($parentPLLOID);        
    }        

    /**
     * Finds all top-level ILOs.
     *
     * @return      An array of ILOs without a parent (<em>i.e.</em>, 
     *              parent ID is null)
     */
    public function getTopLevelILOs() {
        return $this->createObjectsFromDatabaseRows(
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
        return $this->createObjectsFromDatabaseRows(
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
        return $this->createObjectsFromDatabaseRows(
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
        return $this->createObjectsFromDatabaseRows(
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
     * @param $excludeIDArray
     * @return                  An array of the CLLOs set for the course
     */
    public function getCLLOsForCourse($idValue, $excludeIDArray = array()) {
        $cllo = self::TABLE_CLLO;
        $clloID = self::TABLE_CLLO_ID;
        $clloNumber = self::TABLE_CLLO_NUMBER;

        $cac = self::TABLE_CLLO_AND_COURSE;
        $cacCLLOID = self::TABLE_CLLO_AND_COURSE_CLLO_ID;
        $cacCourseID = self::TABLE_CLLO_AND_COURSE_COURSE_ID;
        
        $excludeIDQuestionMarkString = self::getQuestionMarkString($excludeIDArray);
        $queryIDArray = $excludeIDArray;
        $queryIDArray[] = $idValue;

        $query = "SELECT $cllo.* FROM ";
        if (empty($excludeIDArray)) {
            $query .= $cllo;
        }
        else {
            $query .= "(SELECT * FROM $cllo WHERE $cllo.$clloID NOT IN $excludeIDQuestionMarkString) as $cllo";            
        }
        $query .= " JOIN (SELECT * FROM $cac WHERE $cac.$cacCourseID = ?) AS $cac ON $cllo.$clloID = $cac.$cacCLLOID ORDER BY $cllo.$clloNumber ASC";
        return $this->createObjectsFromDatabaseRows(
            $this->getQueryResults($query, $queryIDArray),
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
        $courseEntryArray = $this->createObjectsFromDatabaseRows(
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

        return $this->createObjectsFromDatabaseRows(
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
        return $this->createObjectsFromDatabaseRows(
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
        return $this->createObjectsFromDatabaseRows(
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
        return $this->createObjectsFromDatabaseRows(
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
        return $this->createObjectsFromDatabaseRows(
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
        return $this->createObjectsFromDatabaseRows(
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
        return $this->createObjectsFromDatabaseRows(
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
        return $this->createObjectsFromDatabaseRows(
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

        $query = "SELECT DISTINCT $pllo.* FROM $pllo JOIN (SELECT * FROM $pad WHERE $pad.$padDLEID = ?) AS $pad ON $pllo.$plloID = $pad.$padPLLOID ORDER BY $pllo.$plloNumber ASC";
        return $this->createObjectsFromDatabaseRows(
            $this->getQueryResults($query, array($idValue)),
            'PlanLevelLearningOutcome');
    }
    
    /**
     * Extracts the PLLOs associated with an Plan.
     *
     * @param $idValue     The id of the Plan (string or numeric)
     * @return             A array of all the PLLOs associated with the Plan
     */
    public function getPLLOsForPlan($idValue) {
        $pllo = self::TABLE_PLLO;
        $plloID = self::TABLE_PLLO_ID;
        $plloNumber = self::TABLE_PLLO_NUMBER;

        $pap = self::TABLE_PLAN_AND_PLLO;
        $papPlanID = self::TABLE_PLAN_AND_PLLO_PLAN_ID;
        $papPLLOID = self::TABLE_PLAN_AND_PLLO_PLLO_ID;

        $plan = self::TABLE_PLAN;
        $planID = self::TABLE_PLAN_ID;

        $query = "SELECT $pllo.* FROM $pllo JOIN (SELECT * FROM $pap WHERE $pap.$papPlanID = ?) AS $pap ON $pllo.$plloID = $pap.$papPLLOID JOIN $plan ON $plan.$planID = $pap.$papPlanID ORDER BY $pllo.$plloNumber ASC";
        return $this->createObjectsFromDatabaseRows(
            $this->getQueryResults($query, array($idValue)),
            'PlanLevelLearningOutcome');
    }
    
    /**
     * Extracts the PLLOs associated with multiple Plans.
     *
     * @param $idValueArray     The ids of the Plan (string or numeric)
     * @return                  A array of all the PLLOs associated with the 
     *                          Plans
     */
    public function getPLLOsForPlans($idValueArray, $excludePLLOIDValueArray = array()) {
        $pllo = self::TABLE_PLLO;
        $plloID = self::TABLE_PLLO_ID;
        $plloNumber = self::TABLE_PLLO_NUMBER;

        $pap = self::TABLE_PLAN_AND_PLLO;
        $papPlanID = self::TABLE_PLAN_AND_PLLO_PLAN_ID;
        $papPLLOID = self::TABLE_PLAN_AND_PLLO_PLLO_ID;

        $plan = self::TABLE_PLAN;
        $planID = self::TABLE_PLAN_ID;
        
        $planIDString = self::getQuestionMarkString($idValueArray);
        $excludePLLOIDString = self::getQuestionMarkString($excludePLLOIDValueArray);

        $query = "SELECT DISTINCT $pllo.* FROM ";
        if (empty($excludePLLOIDValueArray)) {
            $query .= "$pllo";            
        }
        else {
            $query .= "(SELECT * FROM $pllo WHERE $pllo.$plloID NOT IN $excludePLLOIDString) AS $pllo";                        
        }
        $query .= " JOIN (SELECT * FROM $pap WHERE $pap.$papPlanID IN $planIDString) AS $pap ON $pllo.$plloID = $pap.$papPLLOID JOIN $plan ON $plan.$planID = $pap.$papPlanID ORDER BY $pllo.$plloNumber ASC";
                
        return $this->createObjectsFromDatabaseRows(
            $this->getQueryResults($query, array_merge($excludePLLOIDValueArray, $idValueArray)),
            'PlanLevelLearningOutcome'
        );
    }
    
    /**
     * Extracts the PLLOs associated with an administering department's plans.
     *
     * @param $departmentIDValue     
     */
    public function getTopLevelPLLOsForAdministeringDepartmentsPlans($departmentIDValue) {
        $dap = self::TABLE_DEPARTMENT_AND_PLAN;
        $dapPlanID = self::TABLE_DEPARTMENT_AND_PLAN_PLAN_ID;
        $dapDepartmentID = self::TABLE_DEPARTMENT_AND_PLAN_DEPARTMENT_ID;
        $dapRole = self::TABLE_DEPARTMENT_AND_PLAN_ROLE;
        $dapRoleAdministrator = self::TABLE_DEPARTMENT_AND_PLAN_ROLE_ADMINISTRATOR;

        $pap = self::TABLE_PLAN_AND_PLLO;
        $papPlanID = self::TABLE_PLAN_AND_PLLO_PLAN_ID;
        $papPLLOID = self::TABLE_PLAN_AND_PLLO_PLLO_ID;

        $pllo = self::TABLE_PLLO;
        $plloID = self::TABLE_PLLO_ID;
        $plloNumber = self::TABLE_PLLO_NUMBER;
        $plloParentID = self::TABLE_PLLO_PARENT_ID;
        
        $query = "SELECT DISTINCT $pllo.* FROM (SELECT * FROM $dap WHERE $dap.$dapDepartmentID = ? AND $dap.$dapRole = '$dapRoleAdministrator') AS $dap JOIN $pap ON $dap.$dapPlanID = $pap.$papPlanID JOIN (SELECT * FROM $pllo WHERE $pllo.$plloParentID IS NULL) AS $pllo ON $pap.$papPLLOID = $pllo.$plloID ORDER BY $pllo.$plloNumber";                
        return $this->createObjectsFromDatabaseRows(
            $this->getQueryResults($query, array($departmentIDValue)),
            'PlanLevelLearningOutcome'
        );
    }    

    /**
     * Extracts the PLLOs associated with an Plan and DLE.
     *
     * @param $dleIDValue     The id of the DLE (string or numeric)
     * @param $planIDValue    The id of the Plan (string or numeric)
     * @return                A array of all the PLLOs associated with the DLE
     *                        and Plan
     */
    public function getPLLOsForDLEAndPlan($dleIDValue, $planIDValue) {
        $pllo = self::TABLE_PLLO;
        $plloID = self::TABLE_PLLO_ID;
        $plloNumber = self::TABLE_PLLO_NUMBER;

        $pap = self::TABLE_PLAN_AND_PLLO;
        $papPlanID = self::TABLE_PLAN_AND_PLLO_PLAN_ID;
        $papPLLOID = self::TABLE_PLAN_AND_PLLO_PLLO_ID;

        $pad = self::TABLE_PLLO_AND_DLE;
        $padDLEID = self::TABLE_PLLO_AND_DLE_DLE_ID;
        $padPLLOID = self::TABLE_PLLO_AND_DLE_PLLO_ID;
        
        $plan = self::TABLE_PLAN;
        $planID = self::TABLE_PLAN_ID;

        $query = "SELECT $pllo.* FROM $pllo JOIN (SELECT * FROM $pad WHERE $pad.$padDLEID = ?) AS $pad ON $pllo.$plloID = $pad.$padPLLOID JOIN (SELECT * FROM $pap WHERE $pap.$papPlanID = ?) AS $pap ON $pllo.$plloID = $pap.$papPLLOID JOIN $plan ON $plan.$planID = $pap.$papPlanID ORDER BY $pllo.$plloNumber ASC";
        return $this->createObjectsFromDatabaseRows(
            $this->getQueryResults($query, array($dleIDValue, $planIDValue)),
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
        
        $dap = self::TABLE_DEPARTMENT_AND_PLAN;
        $dapDepartmentID = self::TABLE_DEPARTMENT_AND_PLAN_DEPARTMENT_ID;
        $dapPlanID = self::TABLE_DEPARTMENT_AND_PLAN_PLAN_ID;
        
        $plan = self::TABLE_PLAN;
        $planID = self::TABLE_PLAN_ID;
        
        $pap = self::TABLE_PLAN_AND_PLLO;
        $papPlanID = self::TABLE_PLAN_AND_PLLO_PLAN_ID;
        $papPLLOID = self::TABLE_PLAN_AND_PLLO_PLLO_ID;
                               
        $pad = self::TABLE_PLLO_AND_DLE;
        $padDLEID = self::TABLE_PLLO_AND_DLE_DLE_ID;
        $padPLLOID = self::TABLE_PLLO_AND_DLE_PLLO_ID;
                
        $query = "SELECT DISTINCT $pllo.* FROM (SELECT * FROM $dap WHERE $dap.$dapDepartmentID = ?) AS $dap JOIN $plan ON $dap.$dapPlanID = $plan.$planID JOIN $pap ON $plan.$planID = $pap.$papPlanID JOIN (SELECT * FROM $pad WHERE $pad.$padDLEID = ?) AS $pad ON $pap.$papPLLOID = $pad.$padPLLOID JOIN $pllo on $pad.$padPLLOID = $pllo.$plloID ORDER BY $pllo.$plloNumber ASC";
        return $this->createObjectsFromDatabaseRows(
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
              
        // Get all the PLLO IDs for courses with this subject
        $query = "SELECT DISTINCT $pllo.* FROM (SELECT * FROM $course WHERE $course.$courseSubject = ?) AS $course JOIN $cac ON $course.$courseID = $cac.$cacCourseID JOIN $cap ON $cac.$cacCLLOID = $cap.$capCLLOID JOIN $pllo ON $cap.$capPLLOID = $pllo.$plloID ORDER BY $pllo.$plloNumber ASC";
        $possible_pllo_array = $this->createObjectsFromDatabaseRows(
            $this->getQueryResults($query, array($subjectValue)),
            'PlanLevelLearningOutcome');
        
        // Go through each PLLO
        $confirmed_pllo_array = array();
        $confirmed_pllo_id_array = array();
        while (! empty($possible_pllo_array)) {
            $possible_pllo = array_pop($possible_pllo_array);
            
            // Check if this PLLO is directly linked to the DLE
            $query = "SELECT 1 FROM $pad WHERE $pad.$padPLLOID = ? AND $pad.$padDLEID = ?";
            $result = $this->getQueryResult($query, array(
                $possible_pllo->getDBID(), $dleIDValue));
            
            if ($result) {
                // Was there a match? If so, keep this PLLO.
                $confirmed_pllo_array[] = $possible_pllo;
                $confirmed_pllo_id_array[] = $possible_pllo->getDBID();
            }
            elseif ($possible_pllo->hasParent() && 
                    (! in_array($possible_pllo->getParentDBID(), $confirmed_pllo_id_array))) {
                // If this PLLO has a parent, check the parent *if* it hasn't 
                // already been found
                $possible_pllo_array[] = $this->getPLLOFromID($possible_pllo->getParentDBID());
            }
        }
        
        // Now it's just a matter of sorting the results and returning them
        usort($confirmed_pllo_array, PLLO::getSortFunction());
        return $confirmed_pllo_array;
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
        return $this->createObjectFromDatabaseRow(
            $this->getQueryResult($query, array($idValue, $idValue)),
            'DegreeLevelExpectation');        
    }
    
    /**
     * 
     * @param type $courseIDValue
     * @return type
     */
    public function getPLLOsForCourse($courseIDValue) {
        $pllo = self::TABLE_PLLO;
        $plloID = self::TABLE_PLLO_ID;
        $plloNumber = self::TABLE_PLLO_NUMBER;
        
        $pap = self::TABLE_PLAN_AND_PLLO;
        $papPlanID = self::TABLE_PLAN_AND_PLLO_PLAN_ID;
        $papPLLOID = self::TABLE_PLAN_AND_PLLO_PLLO_ID;
        
        $dap = self::TABLE_DEPARTMENT_AND_PLAN;
        $dapPlanID = self::TABLE_DEPARTMENT_AND_PLAN_PLAN_ID;
        $dapDepartmentID = self::TABLE_DEPARTMENT_AND_PLAN_DEPARTMENT_ID;
                
        $das = self::TABLE_DEPARTMENT_AND_SUBJECT;
        $dasDepartmentID = self::TABLE_DEPARTMENT_AND_SUBJECT_DEPARTMENT_ID;
        $dasSubject = self::TABLE_DEPARTMENT_AND_SUBJECT_SUBJECT;
        
        $course = self::TABLE_COURSE;
        $courseID = self::TABLE_COURSE_ID;
        $courseSubject = self::TABLE_COURSE_SUBJECT;
                        
        $query = "SELECT DISTINCT $pllo.* FROM $pllo JOIN $pap ON $pllo.$plloID = $pap.$papPLLOID JOIN $dap ON $pap.$papPlanID = $dap.$dapPlanID JOIN (SELECT $das.* FROM $das JOIN (SELECT * FROM $course WHERE $course.$courseID = ?) AS $course ON $das.$dasSubject = $course.$courseSubject) AS $das ON $dap.$dapDepartmentID = $das.$dasDepartmentID ORDER BY $pllo.$plloNumber ASC";
        return $this->createObjectsFromDatabaseRows(
            $this->getQueryResults($query, array($courseIDValue)),
            'PlanLevelLearningOutcome');
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
        
        $courseEntryArray = $this->createObjectsFromDatabaseRows(
            $this->getQueryResults($query, $valueArray),
            'CourseEntry');
        return Course::buildCoursesFromCourseEntries($courseEntryArray);
       
    }
    
    /**
     * 
     * @param type $objectName
     * @param type $searchString
     * @param type $tableName
     * @param type $searchColumnArray
     * @param type $orderByColumnArray
     * @return type
     */
    protected function findMatchingObjects($objectName, $searchString, 
            $tableName, $searchColumnArray, $orderByColumnArray, 
            $idColumn = null, $excludeIDArray = array()) {
        
        // Check that there are search columns before proceeding
        if (empty($searchColumnArray)) {
            return array();            
        }
        
        // Format the string for the query and create the individual LIKE parts
        $likeString = "%{$searchString}%";
        $likeStringArray = array_fill(0, count($searchColumnArray), $likeString);
        $searchClauseArray = array_map(function($searchColumn) {
                return "($searchColumn LIKE ?)";
            }, $searchColumnArray);
            
        // Create the main part of the query to search each column
        $query = "SELECT * FROM $tableName WHERE (";
        $query .= join(' OR ', $searchClauseArray).")";

        // Check whether any ids should be excluded
        if (($idColumn !== null) && (! empty($excludeIDArray))) {
            $query .= " AND $idColumn NOT IN ";
            $query .= self::getQuestionMarkString($excludeIDArray);
        }        
        
        $query .= " ORDER BY ".join(', ', $orderByColumnArray)." ASC";
        //error_log($query);        
        return $this->createObjectsFromDatabaseRows(
            $this->getQueryResults($query, array_merge($likeStringArray, $excludeIDArray)),
            $objectName);        
    }
    
    /**
     * Searches in the database for CLLOs whose number, text or notes contain the
     * given string.
     *
     * @param $searchString        The substring to look for
     * @param type $searchColumnArray
     * @param type $excludeIDArray
     * @return                     An array of all the matching CLLOs
     */
    public function findMatchingCLLOs($searchString, $searchColumnArray = array(), 
            $excludeIDArray = array()) {
        
        $completeSearchColumnArray = empty($searchColumnArray) ?
            array(self::TABLE_CLLO_NUMBER, self::TABLE_CLLO_TEXT, self::TABLE_CLLO_NOTES) :
            $searchColumnArray;
        
        return $this->findMatchingObjects('CourseLevelLearningOutcome', 
            $searchString, self::TABLE_CLLO, $completeSearchColumnArray, 
            array(self::TABLE_CLLO_NUMBER), self::TABLE_CLLO_ID, $excludeIDArray);
        
        /*
        $query = "SELECT * FROM $cllo WHERE ($clloNumber LIKE ?) OR ($clloText LIKE ?) OR ($clloNotes LIKE ?) ORDER BY $clloNumber ASC";
         */
    }

    /**
     * Searches in the database for PLLOs whose number, text or notes contain the
     * given string.
     *
     * @param $searchString        The substring to look for
     * @param type $searchColumnArray
     * @param type $excludeIDArray
     * @return                     An array of all the matching PLLOs
     */
    public function findMatchingPLLOs($searchString, $searchColumnArray = array(), 
            $excludeIDArray = array()) {
        
        $completeSearchColumnArray = empty($searchColumnArray) ?
            array(self::TABLE_PLLO_NUMBER, self::TABLE_PLLO_PREFIX, self::TABLE_PLLO_TEXT, self::TABLE_PLLO_NOTES) :
            $searchColumnArray;
        
        return $this->findMatchingObjects('PlanLevelLearningOutcome', 
            $searchString, self::TABLE_PLLO, $completeSearchColumnArray, 
            array(self::TABLE_PLLO_NUMBER), self::TABLE_PLLO_ID, $excludeIDArray);
    }

    /**
     * Searches in the database for ILOs whose number, text, notes or
     * description contain the given string.
     *
     * @param $searchString        The substring to look for
     * @param type $searchColumnArray
     * @param type $excludeIDArray
     * @return                     An array of all the matching ILOs
     */
    public function findMatchingILOs($searchString, $searchColumnArray = array(), 
            $excludeIDArray = array()) {
        
        $completeSearchColumnArray = empty($searchColumnArray) ?
            array(self::TABLE_ILO_NUMBER, self::TABLE_ILO_TEXT, self::TABLE_ILO_DESCRIPTION, self::TABLE_ILO_NOTES) :
            $searchColumnArray;
        
        return $this->findMatchingObjects('InstitutionLearningOutcome', 
            $searchString, self::TABLE_ILO, $completeSearchColumnArray, 
            array(self::TABLE_ILO_NUMBER), self::TABLE_ILO_ID, $excludeIDArray);
        
        /*
        $query = "SELECT * FROM $ilo WHERE (($iloNumber LIKE ?) OR ($iloText LIKE ?) OR ($iloDescription LIKE ?) OR ($iloNotes LIKE ?)) ORDER BY $iloNumber ASC";
         */
    }

    /**
     * Searches in the database for DLEs whose number, text or notes contain the
     * given string.
     *
     * @param $searchString        The substring to look for
     * @param type $searchColumnArray
     * @param type $excludeIDArray
     * @return                     An array of all the matching DLEs
     */
    public function findMatchingDLEs($searchString, $searchColumnArray = array(), 
            $excludeIDArray = array()) {

        $completeSearchColumnArray = empty($searchColumnArray) ?
            array(self::TABLE_DLE_NUMBER, self::TABLE_DLE_TEXT, self::TABLE_DLE_NOTES) :
            $searchColumnArray;
        
        return $this->findMatchingObjects('DegreeLevelExpectation', 
            $searchString, self::TABLE_DLE, $completeSearchColumnArray, 
            array(self::TABLE_DLE_NUMBER), self::TABLE_DLE_ID, $excludeIDArray);

        /*
        $query = "SELECT * FROM $dle WHERE (($dleNumber LIKE ?) OR ($dleText LIKE ?) OR ($dleNotes LIKE ?)) ORDER BY $dleNumber ASC";
        */
    }

    /**
     * Searches in the database for revisions whose table, column or prior
     * value contain the given string.
     *
     * @param $searchString        The substring to look for
     * @param type $searchColumnArray
     * @param type $excludeIDArray
     * @return                     An array of all the matching revisions
     */
    public function findMatchingRevisions($searchString, $searchColumnArray = array(), 
            $excludeIDArray = array()) {

        $completeSearchColumnArray = empty($searchColumnArray) ?
            array(self::TABLE_REVISION_PRIOR_VALUE, self::TABLE_REVISION_DATE_AND_TIME) :
            $searchColumnArray;
        
        return $this->findMatchingObjects('Revision', 
            $searchString, self::TABLE_REVISION, $completeSearchColumnArray, 
            array(self::TABLE_REVISION_DATE_AND_TIME), self::TABLE_REVISION_ID, $excludeIDArray);
        
        /*
        $query = "SELECT * FROM $revision WHERE (($revisionPrior LIKE ?) OR ($revisionDate LIKE ?)) ORDER BY $revisionDate DESC";
         */
    }
    
    /**
     * Searches in the database for faculties whose name contains the given 
     * string.
     *
     * @param $searchString        The substring to look for
     * @param type $searchColumnArray
     * @param type $excludeIDArray
     * @return                     An array of all the matching Faculties
     */
    public function findMatchingFaculties($searchString, $searchColumnArray = array(), 
            $excludeIDArray = array()) {

        $completeSearchColumnArray = empty($searchColumnArray) ?
            array(self::TABLE_FACULTY_NAME) :
            $searchColumnArray;
        
        return $this->findMatchingObjects('Faculty', 
            $searchString, self::TABLE_FACULTY, $completeSearchColumnArray, 
            array(self::TABLE_FACULTY_NAME), self::TABLE_FACULTY_ID, $excludeIDArray);
        
        /*
        $query = "SELECT * FROM $faculty WHERE ($facultyName LIKE ?) ORDER BY $facultyName ASC";
         */
    }

    /**
     * Searches in the database for departments whose name contains the given 
     * string.
     *
     * @param $searchString        The substring to look for
     * @param type $searchColumnArray
     * @param type $excludeIDArray
     * @return                     An array of all the matching Departments
     */
    public function findMatchingDepartments($searchString, $searchColumnArray = array(), 
            $excludeIDArray = array()) {

        $completeSearchColumnArray = empty($searchColumnArray) ?
            array(self::TABLE_DEPARTMENT_NAME) :
            $searchColumnArray;
        
        return $this->findMatchingObjects('Department', 
            $searchString, self::TABLE_DEPARTMENT, $completeSearchColumnArray, 
            array(self::TABLE_DEPARTMENT_NAME), self::TABLE_DEPARTMENT_ID, $excludeIDArray);
        
        /*
        $query = "SELECT * FROM $department WHERE ($departmentName LIKE ?) ORDER BY $departmentName ASC";
         */
    }

    /**
     * Searches in the database for degrees whose name or code contain the
     * given string.
     *
     * @param $searchString        The substring to look for
     * @param type $searchColumnArray
     * @param type $excludeIDArray
     * @return                     An array of all the matching Degrees
     */
    public function findMatchingDegrees($searchString, $searchColumnArray = array(), 
            $excludeIDArray = array()) {

        $completeSearchColumnArray = empty($searchColumnArray) ?
            array(self::TABLE_DEGREE_NAME, self::TABLE_DEGREE_CODE) :
            $searchColumnArray;
        
        return $this->findMatchingObjects('Degree', 
            $searchString, self::TABLE_DEGREE, $completeSearchColumnArray, 
            array(self::TABLE_DEGREE_NAME), self::TABLE_DEGREE_ID, $excludeIDArray);

        /*
        $query = "SELECT * FROM $degree WHERE ($degreeName LIKE ?) OR ($degreeCode LIKE ?) ORDER BY $degreeName ASC";
         */
    }

    /**
     * Searches in the database for plans whose name or code contain the
     * given string.
     *
     * @param $searchString        The substring to look for
     * @param type $searchColumnArray
     * @param type $excludeIDArray
     * @return                     An array of all the matching Plans
     */
    public function findMatchingPlans($searchString, $searchColumnArray = array(), 
            $excludeIDArray = array()) {

        $completeSearchColumnArray = empty($searchColumnArray) ?
            array(self::TABLE_PLAN_NAME, self::TABLE_PLAN_CODE, self::TABLE_PLAN_TEXT, self::TABLE_PLAN_NOTES) :
            $searchColumnArray;
        
        return $this->findMatchingObjects('Plan', 
            $searchString, self::TABLE_PLAN, $completeSearchColumnArray, 
            array(self::TABLE_PLAN_NAME), self::TABLE_PLAN_ID, $excludeIDArray);
        
        /*
        $query = "SELECT * FROM $plan WHERE ($planName LIKE ?) OR ($planCode LIKE ?) OR ($planType LIKE ?) OR ($planText LIKE ?) OR ($planNotes LIKE ?) ORDER BY $planName ASC";
         */
    }

    /**
     * Searches in the database for programs whose name or code contain the
     * given string.
     *
     * @param $searchString        The substring to look for
     * @param type $searchColumnArray
     * @param type $excludeIDArray
     * @return                     An array of all the matching Programs
     */
    public function findMatchingPrograms($searchString, $searchColumnArray = array(), 
            $excludeIDArray = array()) {

        $completeSearchColumnArray = empty($searchColumnArray) ?
            array(self::TABLE_PROGRAM_NAME, self::TABLE_PROGRAM_TYPE, self::TABLE_PROGRAM_TEXT, self::TABLE_PROGRAM_NOTES) :
            $searchColumnArray;
        
        return $this->findMatchingObjects('Program', 
            $searchString, self::TABLE_PROGRAM, $completeSearchColumnArray, 
            array(self::TABLE_PROGRAM_NAME), self::TABLE_PROGRAM_ID, $excludeIDArray);
        
        /*
        $query = "SELECT * FROM $program WHERE ($programName LIKE ?) OR ($programType LIKE ?) OR ($programText LIKE ?) OR ($programNotes LIKE ?) ORDER BY $programName ASC";
         */
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
        return $this->createObjectsFromDatabaseRows(
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
        return $this->createObjectsFromDatabaseRows(
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
        return $this->createObjectsFromDatabaseRows(
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
        return $this->createObjectsFromDatabaseRows(
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
        return $this->createObjectsFromDatabaseRows(
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
        return $this->createObjectsFromDatabaseRows(
            $this->getAllRows(self::TABLE_PLLO_AND_ILO, 
                self::TABLE_PLLO_AND_ILO_PLLO_ID, 
                self::TABLE_PLLO_AND_ILO_PLLO_ID, array($plloID)),
            'PLLOAndILO');
    }
    
    /**
     * 
     * @param type $plloID
     * @return type
     */
    public function getPlansAndPLLOsForPLLO($plloID) {
        return $this->createObjectsFromDatabaseRows(
            $this->getAllRows(self::TABLE_PLAN_AND_PLLO, 
                self::TABLE_PLAN_AND_PLLO_PLLO_ID, 
                self::TABLE_PLAN_AND_PLLO_PLLO_ID, array($plloID)),
            'PlanAndPLLO');
    }    

    /**
     * Extracts all of the Revisions in the database.
     *
     * @return      An array of all the resulting Revisions
     */
    public function getAllRevisions() {
        return $this->createObjectsFromDatabaseRows(
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
        return $this->createObjectsFromDatabaseRows(
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
        return $this->createObjectsFromDatabaseRows(
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
        return $this->createObjectsFromDatabaseRows(
            $this->getAllRows(self::TABLE_PROGRAM, 
                self::TABLE_PROGRAM_ID, self::TABLE_PROGRAM_NAME, 
                array(), $excludeIDArray),
            'Program');
    }    

    /**
     * Extracts all of the plans in the database.
     *
     * @param $excludeIDArray     A list of plan IDs to exclude (default value
     *                            is the empty array)
     * @return                    An array of all the resulting Plans
     */
    public function getAllPlans($excludeIDArray = array()) {
        return $this->createObjectsFromDatabaseRows(
            $this->getAllRows(self::TABLE_PLAN, 
                self::TABLE_PLAN_ID, self::TABLE_PLAN_NAME, 
                array(), $excludeIDArray),
            'Plan');
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
        return $this->createObjectsFromDatabaseRows(
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
        
        return $this->createObjectsFromDatabaseRows(
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
        return $this->createObjectsFromDatabaseRows(
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
     public function getLatestRevisions($argUserID, $argEditor = true, $argLimit = 5) {
         $revision = self::TABLE_REVISION;
         $userID = self::TABLE_REVISION_USER_ID;
         $dateAndTime = self::TABLE_REVISION_DATE_AND_TIME;

         $comparison = $argEditor ? "=" : "<>";

         $query = "SELECT * FROM $revision WHERE $userID $comparison ? ORDER BY $dateAndTime DESC LIMIT $argLimit ";
         return $this->createObjectsFromDatabaseRows(
             $this->getQueryResults($query, array($argUserID)), 'Revision');
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
        
        // Get the plan(s) associated with the PLLO
        $planIDArray = qsc_core_extract_form_array_value(INPUT_POST, QSC_CMP_FORM_PLLO_PLAN_LIST_SUPPORTED, FILTER_SANITIZE_NUMBER_INT);

        // Go through each plan ID
        foreach ($planIDArray as $planID) {
            // Insert a revision for adding the new PlanAndPLLO
            $revision = new Revision(
                    DatabaseObject::NEW_OBJECT_TEMP_ID, $userID,
                    self::TABLE_PLAN_AND_PLLO, null,
                    array(self::TABLE_PLAN_AND_PLLO_PLAN_ID => $planID,
                        self::TABLE_PLAN_AND_PLLO_PLLO_ID => $newPLLO->getDBID()),
                    self::TABLE_REVISION_ACTION_ADDED,
                    null, $dateAndTime
            );
            $this->insertRevision($revision);
            
           // Add the new PlanAndPLLO
            $planAndPLLO = new PlanAndPLLO($planID, $newPLLO->getDBID());
            $this->insertPlanAndPLLO($planAndPLLO);
        }
        
        // Determine if the new PLLO is associated with a DLE
        $dleID = qsc_core_extract_form_value(INPUT_POST, QSC_CMP_FORM_PLLO_PARENT_DLE_SELECT, FILTER_SANITIZE_NUMBER_INT);
        if (! $dleID) {
            return $newPLLO;
        }

        // Add their relationship to the database
        $plloAndDLE = new PLLOAndDLE($newPLLO->getDBID(), $dleID);
        $this->insertPLLOAndDLE($plloAndDLE);

        // Insert a revision for adding the new PLLOAndDLE
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
        $valueArray = array($clloAndCourse->getCLLODBID(), $clloAndCourse->getCourseDBID());

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
        $valueArray = array($clloAndPLLO->getCLLODBID(), $clloAndPLLO->getPLLODBID());

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
        $valueArray = array($clloAndILO->getCLLODBID(), $clloAndILO->getILODBID());

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
        $valueArray = array($clloAndDLE->getPLLODBID(), $clloAndDLE->getDLEDBID());

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
        $valueArray = array($plloAndILO->getPLLODBID(), $plloAndILO->getILODBID());

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
    protected function insertPlanAndPLLO($planAndPLLO) {
        $tableName = self::TABLE_PLAN_AND_PLLO;
        $colPlanID = self::TABLE_PLAN_AND_PLLO_PLAN_ID;
        $colPLLOID = self::TABLE_PLAN_AND_PLLO_PLLO_ID;

        $query = "INSERT INTO $tableName ($colPlanID, $colPLLOID) VALUES (?, ?)";
        $valueArray = array($planAndPLLO->getPlanDBID(), $planAndPLLO->getPLLODBID());

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
                    $deletedCLLOAndPLLO->getCLLODBID(),
                    self::TABLE_CLLO_AND_PLLO_PLLO_ID =>
                    $deletedCLLOAndPLLO->getPLLODBID()),
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
                    $addedCLLOAndPLLO->getCLLODBID(),
                    self::TABLE_CLLO_AND_PLLO_PLLO_ID =>
                    $addedCLLOAndPLLO->getPLLODBID()),
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
                    $deletedCLLOAndILO->getCLLODBID(),
                    self::TABLE_CLLO_AND_ILO_ILO_ID =>
                    $deletedCLLOAndILO->getILODBID()),
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
                    $addedCLLOAndILO->getCLLODBID(),
                    self::TABLE_CLLO_AND_ILO_ILO_ID =>
                    $addedCLLOAndILO->getILODBID()),
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
        $updatedPLLO->initialize($this);
        
        // Start with the revisions to the PLLO object/row
        $revisionArray = $originalPLLO->getRevisions($updatedPLLO, $userID, $dateAndTime);

        // Perform the changes/revisions and add each revision to the
        // database
        $this->performEditAndDeleteRevisions($revisionArray);
        $this->insertRevisions($revisionArray);

        // Now move onto the PLLO's relationships
        $this->updatePlanAndPLLOsFromPostData($originalPLLO, $userID, $dateAndTime);
        $this->updatePLLOAndDLEFromPostData($originalPLLO, $userID, $dateAndTime);
        $this->updatePLLOAndILOsFromPostData($originalPLLO, $userID, $dateAndTime);
        
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
    protected function updatePLLOAndILOsFromPostData($originalPLLO, $userID, $dateAndTime) {
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
                    $deletedPLLOAndILO->getPLLODBID(),
                    self::TABLE_PLLO_AND_ILO_ILO_ID =>
                    $deletedPLLOAndILO->getILODBID()),
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
                    $addedPLLOAndILO->getPLLODBID(),
                    self::TABLE_PLLO_AND_ILO_ILO_ID =>
                    $addedPLLOAndILO->getILODBID()),
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
     * Compares a set of older PlanAndPLLOs with a set of newer counterparts
     * and determines what revisions have been made.
     *
     * @param $originalPLLO
     * @param $userID
     * @param $dateAndTime
     */
    protected function updatePlanAndPLLOsFromPostData($originalPLLO, $userID, $dateAndTime) {
        // Get the Plan IDs from the form data
        $updatedPlanIDArray = qsc_core_extract_form_array_value(INPUT_POST, QSC_CMP_FORM_PLLO_PLAN_LIST_SUPPORTED, FILTER_SANITIZE_NUMBER_INT);
        if (! $updatedPlanIDArray) {
            $updatedPlanIDArray = array();
        }

        // Create an updated set of PlanAndPLLOs
        $updatedPlanAndPLLOArray = array();
        foreach ($updatedPlanIDArray as $updatedPlanID) {
             $updatedPlanAndPLLOArray[] = new PlanAndPLLO($updatedPlanID, $originalPLLO->getDBID());
        }
        

        // Get the original PLLO and Plan information in the database
        $originalPlanAndPLLOArray = $this->getPlansAndPLLOsForPLLO($originalPLLO->getDBID());

        // Remove the identical PlanAndPLLOs in both arrays
        qsc_core_remove_identical_values($updatedPlanAndPLLOArray, $originalPlanAndPLLOArray);

        // Everything left in the prior set has been deleted
        $revisionArray = array();
        foreach($originalPlanAndPLLOArray as $deletedPlanAndPLLO) {
            $revisionArray[] = new Revision(
                DatabaseObject::NEW_OBJECT_TEMP_ID, $userID,
                self::TABLE_PLAN_AND_PLLO, null,
                array(self::TABLE_PLAN_AND_PLLO_PLAN_ID =>
                    $deletedPlanAndPLLO->getPlanDBID(),
                    self::TABLE_PLAN_AND_PLLO_PLLO_ID =>
                    $deletedPlanAndPLLO->getPLLODBID()),
                self::TABLE_REVISION_ACTION_DELETED,
                null, $dateAndTime
            );
        }

        // Everything left in the new set has been added
        foreach($updatedPlanAndPLLOArray as $addedPlanAndPLLO) {
            $this->insertPlanAndPLLO($addedPlanAndPLLO);
            // $pdo->lastInsertId
            $revisionArray[] = new Revision(
                DatabaseObject::NEW_OBJECT_TEMP_ID, $userID,
                self::TABLE_PLAN_AND_PLLO, null,
                array(self::TABLE_PLAN_AND_PLLO_PLAN_ID =>
                    $addedPlanAndPLLO->getPlanDBID(),
                    self::TABLE_PLAN_AND_PLLO_PLLO_ID =>
                    $addedPlanAndPLLO->getPLLODBID()),
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
                    $clloAndPLLO->getCLLODBID(),
                    self::TABLE_CLLO_AND_PLLO_PLLO_ID =>
                    $clloAndPLLO->getPLLODBID()),
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
                    $clloAndILO->getCLLODBID(),
                    self::TABLE_CLLO_AND_ILO_ILO_ID =>
                    $clloAndILO->getILODBID()),
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
