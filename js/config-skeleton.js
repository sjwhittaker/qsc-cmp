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


/******************************************************************************
 * Constants
 *****************************************************************************/
const QSC_CMP_AJAX_SCRIPTS_DIRECTORY = "/qsc-cmp/ajax";

const QSC_CMP_AJAX_SCRIPT_GET_COURSES = QSC_CMP_AJAX_SCRIPTS_DIRECTORY + "/getCourses.php";
const QSC_CMP_AJAX_SCRIPT_GET_CLLOS = QSC_CMP_AJAX_SCRIPTS_DIRECTORY + "/getCLLOs.php";
const QSC_CMP_AJAX_SCRIPT_GET_PLLOS = QSC_CMP_AJAX_SCRIPTS_DIRECTORY + "/getPLLOs.php";
const QSC_CMP_AJAX_SCRIPT_GET_ILOS = QSC_CMP_AJAX_SCRIPTS_DIRECTORY + "/getILOs.php";
const QSC_CMP_AJAX_SCRIPT_GET_DLES = QSC_CMP_AJAX_SCRIPTS_DIRECTORY + "/getDLEs.php";
const QSC_CMP_AJAX_SCRIPT_GET_DEGREES = QSC_CMP_AJAX_SCRIPTS_DIRECTORY + "/getDegrees.php";
const QSC_CMP_AJAX_SCRIPT_GET_DEPARTMENTS = QSC_CMP_AJAX_SCRIPTS_DIRECTORY + "/getDepartments.php";
const QSC_CMP_AJAX_SCRIPT_GET_FACULTIES = QSC_CMP_AJAX_SCRIPTS_DIRECTORY + "/getFaculties.php";
const QSC_CMP_AJAX_SCRIPT_GET_PLANS = QSC_CMP_AJAX_SCRIPTS_DIRECTORY + "/getPlans.php";
const QSC_CMP_AJAX_SCRIPT_GET_PROGRAMS = QSC_CMP_AJAX_SCRIPTS_DIRECTORY + "/getPrograms.php";
const QSC_CMP_AJAX_SCRIPT_GET_REVISIONS = QSC_CMP_AJAX_SCRIPTS_DIRECTORY + "/getRevisions.php";

const QSC_CMP_AJAX_ACTION_SEARCH_COURSES = "searchCourses";
const QSC_CMP_AJAX_ACTION_SEARCH_CLLOS = "searchCLLOs";
const QSC_CMP_AJAX_ACTION_SEARCH_PLLOS = "searchPLLOs";
const QSC_CMP_AJAX_ACTION_SEARCH_ILOS = "searchILOs";
const QSC_CMP_AJAX_ACTION_SEARCH_DLES = "searchDLEs";
const QSC_CMP_AJAX_ACTION_SEARCH_DEGREES = "searchDegrees";
const QSC_CMP_AJAX_ACTION_SEARCH_DEPARTMENTS = "searchDepartments";
const QSC_CMP_AJAX_ACTION_SEARCH_FACULTIES = "searchFaculties";
const QSC_CMP_AJAX_ACTION_SEARCH_PLANS = "searchPlans";
const QSC_CMP_AJAX_ACTION_SEARCH_PROGRAMS = "searchPrograms";
const QSC_CMP_AJAX_ACTION_SEARCH_REVISIONS = "searchRevisions";

const QSC_CMP_AJAX_ACTION_GET_COURSE_FROM_ID = "getCourseFromID";
const QSC_CMP_AJAX_ACTION_GET_CLLO_FROM_ID = "getCLLOFromID";
const QSC_CMP_AJAX_ACTION_GET_CLLOS_FOR_COURSE = "getCLLOsForCourse";
const QSC_CMP_AJAX_ACTION_GET_PLLO_FROM_ID = "getPLLOFromID";
const QSC_CMP_AJAX_ACTION_GET_PLLOS_FOR_PLAN = "getPLLOsForPlan";
const QSC_CMP_AJAX_ACTION_GET_ILO_FROM_ID = "getILOFromID";
const QSC_CMP_AJAX_ACTION_GET_DEPARTMENT_FROM_ID = "getDepartmentFromID";
const QSC_CMP_AJAX_ACTION_GET_FACULTY_FROM_ID = "getFacultyFromID";
const QSC_CMP_AJAX_ACTION_GET_DEGREE_FROM_ID = "getDegreeFromID";
const QSC_CMP_AJAX_ACTION_GET_PLAN_FROM_ID = "getPlanFromID";
const QSC_CMP_AJAX_ACTION_GET_PROGRAM_FROM_ID = "getProgramFromID";
