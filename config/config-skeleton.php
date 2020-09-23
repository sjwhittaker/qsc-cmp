<?php
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

use Managers\SessionManager;


/****************************************************************************
 * File System Constants - Most Likely to Change
 * You can edit these directly. 
 ***************************************************************************/
define("QSC_CMP_DOCUMENT_ROOT", filter_input(INPUT_SERVER, "DOCUMENT_ROOT", FILTER_SANITIZE_URL));

define("QSC_CMP_DIRECTORY_LINK", "/qsc-cmp");
define("QSC_CMP_DIRECTORY_PATH", QSC_CMP_DOCUMENT_ROOT.QSC_CMP_DIRECTORY_LINK);

define("QSC_CORE_DIRECTORY_LINK", "/qsc-core");
define("QSC_CORE_DIRECTORY_PATH", QSC_CMP_DOCUMENT_ROOT.QSC_CORE_DIRECTORY_LINK);


/****************************************************************************
 * File System Constants - Least Likely to Change
 * You can edit these directly.
 ***************************************************************************/
define("QSC_CMP_CONFIG_DIRECTORY_PATH", QSC_CMP_DIRECTORY_PATH."/config");
define("QSC_CMP_SRC_DIRECTORY_PATH", QSC_CMP_DIRECTORY_PATH."/src");
define("QSC_CMP_SRC_MANAGERS_DIRECTORY_PATH", QSC_CMP_SRC_DIRECTORY_PATH."/Managers");
define("QSC_CMP_SRC_DATABASE_OBJECTS_DIRECTORY_PATH", QSC_CMP_SRC_DIRECTORY_PATH."/DatabaseObjects");

define("QSC_CMP_ACTION_DIRECTORY_LINK", QSC_CMP_DIRECTORY_LINK."/action");
define("QSC_CMP_AJAX_DIRECTORY_LINK", QSC_CMP_DIRECTORY_LINK."/ajax");
define("QSC_CMP_CLLO_DIRECTORY_LINK", QSC_CMP_DIRECTORY_LINK."/cllo");
define("QSC_CMP_COURSE_DIRECTORY_LINK", QSC_CMP_DIRECTORY_LINK."/course");
define("QSC_CMP_COURSELIST_DIRECTORY_LINK", QSC_CMP_DIRECTORY_LINK."/courselist");
define("QSC_CMP_CSS_DIRECTORY_LINK", QSC_CMP_DIRECTORY_LINK."/css");
define("QSC_CMP_DEGREE_DIRECTORY_LINK", QSC_CMP_DIRECTORY_LINK."/degree");
define("QSC_CMP_DEPARTMENT_DIRECTORY_LINK", QSC_CMP_DIRECTORY_LINK."/department");
define("QSC_CMP_DLE_DIRECTORY_LINK", QSC_CMP_DIRECTORY_LINK."/dle");
define("QSC_CMP_FACULTY_DIRECTORY_LINK", QSC_CMP_DIRECTORY_LINK."/faculty");
define("QSC_CMP_ILO_DIRECTORY_LINK", QSC_CMP_DIRECTORY_LINK."/ilo");
define("QSC_CMP_IMG_DIRECTORY_LINK", QSC_CMP_DIRECTORY_LINK."/img");
define("QSC_CMP_JS_DIRECTORY_LINK", QSC_CMP_DIRECTORY_LINK."/js");
define("QSC_CMP_PLAN_DIRECTORY_LINK", QSC_CMP_DIRECTORY_LINK."/plan");
define("QSC_CMP_PLAN_REQUIREMENT_DIRECTORY_LINK", QSC_CMP_PLAN_DIRECTORY_LINK."/requirement");
define("QSC_CMP_CPR_DIRECTORY_LINK", QSC_CMP_PLAN_REQUIREMENT_DIRECTORY_LINK."/course");
define("QSC_CMP_TPR_DIRECTORY_LINK", QSC_CMP_PLAN_REQUIREMENT_DIRECTORY_LINK."/text");
define("QSC_CMP_PLLO_DIRECTORY_LINK", QSC_CMP_DIRECTORY_LINK."/pllo");
define("QSC_CMP_PROGRAM_DIRECTORY_LINK", QSC_CMP_DIRECTORY_LINK."/program");
define("QSC_CMP_SUBJECT_DIRECTORY_LINK", QSC_CMP_DIRECTORY_LINK."/subject");
define("QSC_CMP_REPORTS_DIRECTORY_LINK", QSC_CMP_DIRECTORY_LINK."/reports");


/****************************************************************************
 * Include QSC-CORE
 * This is required by QSC-CMP and really shouldn't be removed.
 ***************************************************************************/
include_once(QSC_CORE_DIRECTORY_PATH.'/config.php');


/****************************************************************************
 * Customizations
 * You can define anything in constants.php that you need to here with 
 * `define` to override the default; this version will be used.
 * You can also define custom versions of any functions in functions.php or
 * html.php here or in another file and `include` it. 
 ***************************************************************************/


/****************************************************************************
 * Include Standard Constants and Functions
 * These are required by QSC-CMP and really shouldn't be removed.
 ***************************************************************************/
include_once(QSC_CMP_CONFIG_DIRECTORY_PATH.'/constants.php');
include_once(QSC_CMP_CONFIG_DIRECTORY_PATH.'/functions.php');

include_once(QSC_CMP_CONFIG_DIRECTORY_PATH.'/html.php');
include_once(QSC_CMP_CONFIG_DIRECTORY_PATH.'/forms.php');


/****************************************************************************
 * Include All Database-Related Classes
 * You should only alter these if you're overriding one or more classes 
 * (e.g., `CourseCalendarDatabase`).
 ***************************************************************************/
// Used or inherited by other classes
include_once(QSC_CMP_SRC_DATABASE_OBJECTS_DIRECTORY_PATH.'/CalendarComponent.php');
include_once(QSC_CMP_SRC_DATABASE_OBJECTS_DIRECTORY_PATH.'/CourseEntry.php');
include_once(QSC_CMP_SRC_DATABASE_OBJECTS_DIRECTORY_PATH.'/LearningOutcome.php');
include_once(QSC_CMP_SRC_DATABASE_OBJECTS_DIRECTORY_PATH.'/PlanRequirement.php');
include_once(QSC_CMP_SRC_DATABASE_OBJECTS_DIRECTORY_PATH.'/CourseList.php');

include_once(QSC_CMP_SRC_DATABASE_OBJECTS_DIRECTORY_PATH.'/CalendarCourse.php');
include_once(QSC_CMP_SRC_DATABASE_OBJECTS_DIRECTORY_PATH.'/CLLOAndCourse.php');
include_once(QSC_CMP_SRC_DATABASE_OBJECTS_DIRECTORY_PATH.'/CLLOAndILO.php');
include_once(QSC_CMP_SRC_DATABASE_OBJECTS_DIRECTORY_PATH.'/CLLOAndPLLO.php');
include_once(QSC_CMP_SRC_DATABASE_OBJECTS_DIRECTORY_PATH.'/Course.php');
include_once(QSC_CMP_SRC_DATABASE_OBJECTS_DIRECTORY_PATH.'/CourseLevelLearningOutcome.php');
include_once(QSC_CMP_SRC_DATABASE_OBJECTS_DIRECTORY_PATH.'/CoursePlanRequirement.php');
include_once(QSC_CMP_SRC_DATABASE_OBJECTS_DIRECTORY_PATH.'/CPRList.php');
include_once(QSC_CMP_SRC_DATABASE_OBJECTS_DIRECTORY_PATH.'/Degree.php');
include_once(QSC_CMP_SRC_DATABASE_OBJECTS_DIRECTORY_PATH.'/DegreeLevelExpectation.php');
include_once(QSC_CMP_SRC_DATABASE_OBJECTS_DIRECTORY_PATH.'/Department.php');
include_once(QSC_CMP_SRC_DATABASE_OBJECTS_DIRECTORY_PATH.'/Faculty.php');
include_once(QSC_CMP_SRC_DATABASE_OBJECTS_DIRECTORY_PATH.'/InstitutionLearningOutcome.php');
include_once(QSC_CMP_SRC_DATABASE_OBJECTS_DIRECTORY_PATH.'/OptionCourseList.php');
include_once(QSC_CMP_SRC_DATABASE_OBJECTS_DIRECTORY_PATH.'/Plan.php');
include_once(QSC_CMP_SRC_DATABASE_OBJECTS_DIRECTORY_PATH.'/PlanAndPLLO.php');
include_once(QSC_CMP_SRC_DATABASE_OBJECTS_DIRECTORY_PATH.'/PlanLevelLearningOutcome.php');
include_once(QSC_CMP_SRC_DATABASE_OBJECTS_DIRECTORY_PATH.'/PLLOAndDLE.php');
include_once(QSC_CMP_SRC_DATABASE_OBJECTS_DIRECTORY_PATH.'/PLLOAndILO.php');
include_once(QSC_CMP_SRC_DATABASE_OBJECTS_DIRECTORY_PATH.'/Program.php');
include_once(QSC_CMP_SRC_DATABASE_OBJECTS_DIRECTORY_PATH.'/RelationshipCourseList.php');
include_once(QSC_CMP_SRC_DATABASE_OBJECTS_DIRECTORY_PATH.'/Revision.php');
include_once(QSC_CMP_SRC_DATABASE_OBJECTS_DIRECTORY_PATH.'/SubjectCourseList.php');
include_once(QSC_CMP_SRC_DATABASE_OBJECTS_DIRECTORY_PATH.'/TextPlanRequirement.php');
include_once(QSC_CMP_SRC_DATABASE_OBJECTS_DIRECTORY_PATH.'/TPRList.php');
include_once(QSC_CMP_SRC_DATABASE_OBJECTS_DIRECTORY_PATH.'/User.php');

include_once(QSC_CMP_SRC_MANAGERS_DIRECTORY_PATH.'/CourseCalendarDatabase.php');
include_once(QSC_CMP_SRC_MANAGERS_DIRECTORY_PATH.'/CurriculumMappingDatabase.php');
include_once(QSC_CMP_SRC_MANAGERS_DIRECTORY_PATH.'/SessionManager.php');


/****************************************************************************
 * Database Access Functions
 ***************************************************************************/
/**
 * 
 * @return string
 */
function qsc_cmp_get_cmd_host_name() {
    return "";
}

/**
 * 
 * @return string
 */
function qsc_cmp_get_cmd_user_name() {
    return "";
}

/**
 * 
 * @return string
 */
function qsc_cmp_get_cmd_database_name() {
    return "";
}

/**
 * 
 * @return type
 */
function qsc_cmp_get_ccd_host_name() {
    return qsc_cmp_get_cmd_host_name();
}

/**
 * 
 * @return type
 */
function qsc_cmp_get_ccd_user_name() {
    return qsc_cmp_get_cmd_user_name();
}

/**
 * 
 * @return string
 */
function qsc_cmp_get_ccd_database_name() {
    return "";
}

