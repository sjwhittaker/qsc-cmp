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


/****************************************************************************
 * You can `define` any constant here in `config.php` if you want to create 
 * a custom versions of something (e.g., page).
 ***************************************************************************/


/******************************************************************************/
/* Basic and Header Information */
/******************************************************************************/
qsc_core_define_constant("QSC_CMP_INSTITUTION_NAME_TEXT", "Queen's University");
qsc_core_define_constant("QSC_CMP_INSTITUTION_NAME_HTML", '<span class="queens-text">Queen\'s</span> University');
qsc_core_define_constant("QSC_CMP_INSTITUTION_URL", 'https://www.queensu.ca/');
qsc_core_define_constant("QSC_CMP_INSTITUTION_ADDRESS_HTML", '99 University Avenue<br/>Kingston, Ontario<br/>Canada<br/>K7L 3N6');
qsc_core_define_constant("QSC_CMP_INSTITUTION_LOGO_SRC", QSC_CMP_IMG_DIRECTORY_LINK."/queens_logo_white.png");

qsc_core_define_constant("QSC_CMP_SOFTWARE_NAME_TEXT", "Curriculum Mapping Program");
qsc_core_define_constant("QSC_CMP_SOFTWARE_NAME_HTML", QSC_CMP_SOFTWARE_NAME_TEXT);


/****************************************************************************
 * Database password information
 ***************************************************************************/
qsc_core_define_constant("QSC_CMP_CMD_INI_DATA_PROPERTY_KEY_KEY", "key");
qsc_core_define_constant("QSC_CMP_CMD_INI_DATA_PROPERTY_KEY_OUTCOMES", "outcomes");
qsc_core_define_constant("QSC_CMP_CMD_INI_DATA_PROPERTY_KEY_CALENDAR", "calendar");


/****************************************************************************
 * Page Links 
 ***************************************************************************/
// No sub-directory
qsc_core_define_constant("QSC_CMP_DASHBOARD_PAGE_LINK", QSC_CMP_DIRECTORY_LINK."/index.php");
qsc_core_define_constant("QSC_CMP_DLES_PAGE_LINK", QSC_CMP_DIRECTORY_LINK."/dles.php");
qsc_core_define_constant("QSC_CMP_ERROR_PAGE_LINK", QSC_CMP_DIRECTORY_LINK."/error.php");
qsc_core_define_constant("QSC_CMP_FACULTIES_AND_DEPARTMENTS_PAGE_LINK", QSC_CMP_DIRECTORY_LINK."/faculties-and-departments.php");
qsc_core_define_constant("QSC_CMP_ILOS_PAGE_LINK", QSC_CMP_DIRECTORY_LINK."/ilos.php");
qsc_core_define_constant("QSC_CMP_PROGRAMS_PAGE_LINK", QSC_CMP_DIRECTORY_LINK."/programs.php");
qsc_core_define_constant("QSC_CMP_PLANS_PAGE_LINK", QSC_CMP_DIRECTORY_LINK."/plans.php");
qsc_core_define_constant("QSC_CMP_REVISIONS_PAGE_LINK", QSC_CMP_DIRECTORY_LINK."/revisions.php");
qsc_core_define_constant("QSC_CMP_SEARCH_PAGE_LINK", QSC_CMP_DIRECTORY_LINK."/search.php");

// In sub-directory
qsc_core_define_constant("QSC_CMP_CLLO_ADD_PAGE_LINK", QSC_CMP_CLLO_DIRECTORY_LINK."/add.php");
qsc_core_define_constant("QSC_CMP_CLLO_VIEW_PAGE_LINK", QSC_CMP_CLLO_DIRECTORY_LINK."/view.php");
qsc_core_define_constant("QSC_CMP_CLLO_EDIT_PAGE_LINK", QSC_CMP_CLLO_DIRECTORY_LINK."/edit.php");

qsc_core_define_constant("QSC_CMP_COURSE_VIEW_PAGE_LINK", QSC_CMP_COURSE_DIRECTORY_LINK."/view.php");
qsc_core_define_constant("QSC_CMP_COURSELIST_VIEW_PAGE_LINK", QSC_CMP_COURSELIST_DIRECTORY_LINK."/view.php");
qsc_core_define_constant("QSC_CMP_CPR_VIEW_PAGE_LINK", QSC_CMP_CPR_DIRECTORY_LINK."/view.php");
qsc_core_define_constant("QSC_CMP_DEGREE_VIEW_PAGE_LINK", QSC_CMP_DEGREE_DIRECTORY_LINK."/view.php");
qsc_core_define_constant("QSC_CMP_DEPARTMENT_VIEW_PAGE_LINK", QSC_CMP_DEPARTMENT_DIRECTORY_LINK."/view.php");

qsc_core_define_constant("QSC_CMP_DLE_ADD_PAGE_LINK", QSC_CMP_DLE_DIRECTORY_LINK."/add.php");
qsc_core_define_constant("QSC_CMP_DLE_VIEW_PAGE_LINK", QSC_CMP_DLE_DIRECTORY_LINK."/view.php");
qsc_core_define_constant("QSC_CMP_DLE_EDIT_PAGE_LINK", QSC_CMP_DLE_DIRECTORY_LINK."/edit.php");

qsc_core_define_constant("QSC_CMP_FACULTY_VIEW_PAGE_LINK", QSC_CMP_FACULTY_DIRECTORY_LINK."/view.php");

qsc_core_define_constant("QSC_CMP_ILO_ADD_PAGE_LINK", QSC_CMP_ILO_DIRECTORY_LINK."/add.php");
qsc_core_define_constant("QSC_CMP_ILO_VIEW_PAGE_LINK", QSC_CMP_ILO_DIRECTORY_LINK."/view.php");
qsc_core_define_constant("QSC_CMP_ILO_EDIT_PAGE_LINK", QSC_CMP_ILO_DIRECTORY_LINK."/edit.php");

qsc_core_define_constant("QSC_CMP_PLAN_VIEW_PAGE_LINK", QSC_CMP_PLAN_DIRECTORY_LINK."/view.php");

qsc_core_define_constant("QSC_CMP_PLLO_ADD_PAGE_LINK", QSC_CMP_PLLO_DIRECTORY_LINK."/add.php");
qsc_core_define_constant("QSC_CMP_PLLO_VIEW_PAGE_LINK", QSC_CMP_PLLO_DIRECTORY_LINK."/view.php");
qsc_core_define_constant("QSC_CMP_PLLO_EDIT_PAGE_LINK", QSC_CMP_PLLO_DIRECTORY_LINK."/edit.php");

qsc_core_define_constant("QSC_CMP_PROGRAM_VIEW_PAGE_LINK", QSC_CMP_PROGRAM_DIRECTORY_LINK."/view.php");
qsc_core_define_constant("QSC_CMP_REPORTS_ALIGNMENT_LINK", QSC_CMP_REPORTS_DIRECTORY_LINK."/alignment.php");
qsc_core_define_constant("QSC_CMP_REPORTS_COURSE_MATRIX_LINK", QSC_CMP_REPORTS_DIRECTORY_LINK."/course-matrix.php");
qsc_core_define_constant("QSC_CMP_SUBJECT_VIEW_PAGE_LINK", QSC_CMP_SUBJECT_DIRECTORY_LINK."/view.php");
qsc_core_define_constant("QSC_CMP_TPR_VIEW_PAGE_LINK", QSC_CMP_TPR_DIRECTORY_LINK."/view.php");


/******************************************************************************/
/* Form Actions */
/******************************************************************************/
qsc_core_define_constant("QSC_CMP_ACTION_ADD_LINK", QSC_CMP_ACTION_DIRECTORY_LINK."/add.php");
qsc_core_define_constant("QSC_CMP_ACTION_EDIT_LINK", QSC_CMP_ACTION_DIRECTORY_LINK."/edit.php");
qsc_core_define_constant("QSC_CMP_ACTION_DELETE_LINK", QSC_CMP_ACTION_DIRECTORY_LINK."/delete.php");


/******************************************************************************/
/* Scripts */
/******************************************************************************/
qsc_core_define_constant("QSC_CMP_SCRIPT_CLLO_FORMS_LINK", QSC_CMP_JS_DIRECTORY_LINK."/cllo-forms.js");
qsc_core_define_constant("QSC_CMP_SCRIPT_PLLO_FORMS_LINK", QSC_CMP_JS_DIRECTORY_LINK."/pllo-forms.js");
qsc_core_define_constant("QSC_CMP_SCRIPT_SEARCH_LINK", QSC_CMP_JS_DIRECTORY_LINK."/search.js");


/******************************************************************************/
/* Forms: General */
/******************************************************************************/
qsc_core_define_constant("QSC_CMP_FORM_JS_COMPLETED_ON_SUBMISSION", 'js-completed-on-submission');

qsc_core_define_constant("QSC_CMP_FORM_TYPE", 'form-type');
qsc_core_define_constant("QSC_CMP_FORM_RESULT", 'form-result');


/******************************************************************************/
/* Forms: CLLO Add and Edit */
/******************************************************************************/
qsc_core_define_constant("QSC_CMP_FORM_CLLO_ADD", "form-cllo-add");
qsc_core_define_constant("QSC_CMP_FORM_CLLO_EDIT", "form-cllo-edit");
qsc_core_define_constant("QSC_CMP_FORM_CLLO_DELETE", "form-cllo-delete");

qsc_core_define_constant("QSC_CMP_FORM_CLLO_ID", "cllo-id");

qsc_core_define_constant("QSC_CMP_FORM_CLLO_COURSE_INPUT", "cllo-course-input");
qsc_core_define_constant("QSC_CMP_FORM_CLLO_COURSE_INPUT_HELP", "cllo-course-input-help");
qsc_core_define_constant("QSC_CMP_FORM_CLLO_COURSE_SELECT", "cllo-course-select");
qsc_core_define_constant("QSC_CMP_FORM_CLLO_COURSE_SELECT_HELP", "cllo-course-select-help");

qsc_core_define_constant("QSC_CMP_FORM_CLLO_NUMBER", "cllo-number");
qsc_core_define_constant("QSC_CMP_FORM_CLLO_NUMBER_HELP", "cllo-number-help");
qsc_core_define_constant("QSC_CMP_FORM_CLLO_NUMBER_MAX_LENGTH", 15);

qsc_core_define_constant("QSC_CMP_FORM_CLLO_PARENT_SELECT", "cllo-parent-select");
qsc_core_define_constant("QSC_CMP_FORM_CLLO_PARENT_UNSELECT", "cllo-parent-unselect");
qsc_core_define_constant("QSC_CMP_FORM_CLLO_PARENT_SELECT_HELP", "cllo-parent-select-help");

qsc_core_define_constant("QSC_CMP_FORM_CLLO_TEXT", "cllo-text");
qsc_core_define_constant("QSC_CMP_FORM_CLLO_TEXT_HELP", "cllo-text-help");
qsc_core_define_constant("QSC_CMP_FORM_CLLO_TEXT_MAX_LENGTH", 500);

qsc_core_define_constant("QSC_CMP_FORM_CLLO_TYPE", "cllo-type");
qsc_core_define_constant("QSC_CMP_FORM_CLLO_TYPE_HELP", "cllo-type-help");
qsc_core_define_constant("QSC_CMP_FORM_CLLO_TYPE_OPTION_NONE", "None");
qsc_core_define_constant("QSC_CMP_FORM_CLLO_TYPE_OPTION_CORE", "Core");
qsc_core_define_constant("QSC_CMP_FORM_CLLO_TYPE_OPTION_DETAIL", "Detail");

qsc_core_define_constant("QSC_CMP_FORM_CLLO_IOA", "cllo-ioa");
qsc_core_define_constant("QSC_CMP_FORM_CLLO_IOA_HELP", "cllo-ioa-help");
qsc_core_define_constant("QSC_CMP_FORM_CLLO_IOA_MAX_LENGTH", 250);

qsc_core_define_constant("QSC_CMP_FORM_CLLO_NOTES", "cllo-notes");
qsc_core_define_constant("QSC_CMP_FORM_CLLO_NOTES_HELP", "cllo-notes-help");
qsc_core_define_constant("QSC_CMP_FORM_CLLO_NOTES_MAX_LENGTH", 500);

qsc_core_define_constant("QSC_CMP_FORM_CLLO_PLLO_LIST_POSSIBLE", "cllo-pllo-list-possible");
qsc_core_define_constant("QSC_CMP_FORM_CLLO_PLLO_LIST_POSSIBLE_HELP", "cllo-pllo-list-possible-help");
qsc_core_define_constant("QSC_CMP_FORM_CLLO_PLLO_LIST_SUPPORTED", "cllo-pllo-list-supported");
qsc_core_define_constant("QSC_CMP_FORM_CLLO_PLLO_LIST_SUPPORTED_HELP", "cllo-pllo-list-supported-help");
qsc_core_define_constant("QSC_CMP_FORM_CLLO_PLLO_ADD", "cllo-pllo-add");
qsc_core_define_constant("QSC_CMP_FORM_CLLO_PLLO_REMOVE", "cllo-pllo-remove");

qsc_core_define_constant("QSC_CMP_FORM_CLLO_ILO_LIST_POSSIBLE", "cllo-ilo-list-possible");
qsc_core_define_constant("QSC_CMP_FORM_CLLO_ILO_LIST_POSSIBLE_HELP", "cllo-ilo-list-possible-help");
qsc_core_define_constant("QSC_CMP_FORM_CLLO_ILO_LIST_SUPPORTED", "cllo-ilo-list-supported");
qsc_core_define_constant("QSC_CMP_FORM_CLLO_ILO_LIST_SUPPORTED_HELP", "cllo-ilo-list-supported-help");
qsc_core_define_constant("QSC_CMP_FORM_CLLO_ILO_ADD", "cllo-ilo-add");
qsc_core_define_constant("QSC_CMP_FORM_CLLO_ILO_REMOVE", "cllo-ilo-remove");

qsc_core_define_constant("QSC_CMP_FORM_TYPE_ADD_CLLO", 'add-cllo');
qsc_core_define_constant("QSC_CMP_FORM_TYPE_EDIT_CLLO", 'edit-cllo');
qsc_core_define_constant("QSC_CMP_FORM_TYPE_DELETE_CLLO", 'delete-cllo');

qsc_core_define_constant("QSC_CMP_FORM_RESULT_ADD_CLLO_SUCCESSFUL", 'add-cllo-successful');
qsc_core_define_constant("QSC_CMP_FORM_RESULT_EDIT_CLLO_SUCCESSFUL", 'edit-cllo-successful');
qsc_core_define_constant("QSC_CMP_FORM_RESULT_DELETE_CLLO_SUCCESSFUL", 'delete-cllo-successful');


/******************************************************************************/
/* Forms: PLLO Add and Edit */
/******************************************************************************/
qsc_core_define_constant("QSC_CMP_FORM_PLLO_ADD", "form-pllo-add");
qsc_core_define_constant("QSC_CMP_FORM_PLLO_EDIT", "form-pllo-edit");
qsc_core_define_constant("QSC_CMP_FORM_PLLO_DELETE", "form-pllo-delete");

qsc_core_define_constant("QSC_CMP_FORM_PLLO_ID", "pllo-id");

qsc_core_define_constant("QSC_CMP_FORM_PLLO_NUMBER", "pllo-number");
qsc_core_define_constant("QSC_CMP_FORM_PLLO_NUMBER_HELP", "pllo-number-help");
qsc_core_define_constant("QSC_CMP_FORM_PLLO_NUMBER_MAX_LENGTH", 15);

qsc_core_define_constant("QSC_CMP_FORM_PLLO_PLAN_INPUT", "pllo-plan-input");
qsc_core_define_constant("QSC_CMP_FORM_PLLO_PLAN_INPUT_HELP", "pllo-plan-input-help");
qsc_core_define_constant("QSC_CMP_FORM_PLLO_PLAN_LIST_POSSIBLE", "pllo-plan-list-possible");
qsc_core_define_constant("QSC_CMP_FORM_PLLO_PLAN_LIST_POSSIBLE_HELP", "pllo-plan-list-possible-help");
qsc_core_define_constant("QSC_CMP_FORM_PLLO_PLAN_LIST_SUPPORTED", "pllo-plan-list-supported");
qsc_core_define_constant("QSC_CMP_FORM_PLLO_PLAN_LIST_SUPPORTED_HELP", "pllo-plan-list-supported-help");
qsc_core_define_constant("QSC_CMP_FORM_PLLO_PLAN_ADD", "pllo-plan-add");
qsc_core_define_constant("QSC_CMP_FORM_PLLO_PLAN_REMOVE", "pllo-plan-remove");
qsc_core_define_constant("QSC_CMP_FORM_PLLO_PLAN_HELP", "pllo-plan-help");

qsc_core_define_constant("QSC_CMP_FORM_PLLO_PARENT_DLE_SELECT", "pllo-parent-dle-select");
qsc_core_define_constant("QSC_CMP_FORM_PLLO_PARENT_DLE_UNSELECT", "pllo-parent-dle-unselect");
qsc_core_define_constant("QSC_CMP_FORM_PLLO_PARENT_DLE_SELECT_HELP", "pllo-parent-dle-select-help");

qsc_core_define_constant("QSC_CMP_FORM_PLLO_PARENT_PLLO_OR_DLE_HELP", "pllo-parent-pllo-or-dle-help");

qsc_core_define_constant("QSC_CMP_FORM_PLLO_PARENT_PLLO_SELECT", "pllo-parent-pllo-select");
qsc_core_define_constant("QSC_CMP_FORM_PLLO_PARENT_PLLO_UNSELECT", "pllo-parent-pllo-unselect");
qsc_core_define_constant("QSC_CMP_FORM_PLLO_PARENT_PLLO_SELECT_HELP", "pllo-parent-pllo-select-help");

qsc_core_define_constant("QSC_CMP_FORM_PLLO_TEXT", "pllo-text");
qsc_core_define_constant("QSC_CMP_FORM_PLLO_TEXT_HELP", "pllo-text-help");
qsc_core_define_constant("QSC_CMP_FORM_PLLO_TEXT_MAX_LENGTH", 500);

qsc_core_define_constant("QSC_CMP_FORM_PLLO_PREFIX", "pllo-prefix");
qsc_core_define_constant("QSC_CMP_FORM_PLLO_PREFIX_HELP", "pllo-prefix-help");
qsc_core_define_constant("QSC_CMP_FORM_PLLO_PREFIX_MAX_LENGTH", 10);

qsc_core_define_constant("QSC_CMP_FORM_PLLO_ILO_LIST_POSSIBLE", "pllo-ilo-list-possible");
qsc_core_define_constant("QSC_CMP_FORM_PLLO_ILO_LIST_POSSIBLE_HELP", "pllo-ilo-list-possible-help");
qsc_core_define_constant("QSC_CMP_FORM_PLLO_ILO_LIST_SUPPORTED", "pllo-ilo-list-supported");
qsc_core_define_constant("QSC_CMP_FORM_PLLO_ILO_LIST_SUPPORTED_HELP", "pllo-ilo-list-supported-help");
qsc_core_define_constant("QSC_CMP_FORM_PLLO_ILO_ADD", "pllo-ilo-add");
qsc_core_define_constant("QSC_CMP_FORM_PLLO_ILO_REMOVE", "pllo-ilo-remove");

qsc_core_define_constant("QSC_CMP_FORM_PLLO_NOTES", "pllo-notes");
qsc_core_define_constant("QSC_CMP_FORM_PLLO_NOTES_HELP", "pllo-notes-help");
qsc_core_define_constant("QSC_CMP_FORM_PLLO_NOTES_MAX_LENGTH", 500);

qsc_core_define_constant("QSC_CMP_FORM_TYPE_ADD_PLLO", 'add-pllo');
qsc_core_define_constant("QSC_CMP_FORM_TYPE_EDIT_PLLO", 'edit-pllo');
qsc_core_define_constant("QSC_CMP_FORM_TYPE_DELETE_PLLO", 'delete-pllo');

qsc_core_define_constant("QSC_CMP_FORM_RESULT_ADD_PLLO_SUCCESSFUL", 'add-pllo-successful');
qsc_core_define_constant("QSC_CMP_FORM_RESULT_EDIT_PLLO_SUCCESSFUL", 'edit-pllo-successful');
qsc_core_define_constant("QSC_CMP_FORM_RESULT_DELETE_PLLO_SUCCESSFUL", 'delete-pllo-successful');


/******************************************************************************/
/* Forms: Search */
/******************************************************************************/
qsc_core_define_constant("QSC_CMP_FORM_SEARCH", "search-form");
qsc_core_define_constant("QSC_CMP_FORM_SEARCH_TEXT", "search-text");
qsc_core_define_constant("QSC_CMP_FORM_SEARCH_TEXT_HELP", "search-text-help");

qsc_core_define_constant("QSC_CMP_FORM_SEARCH_OPTION_CLLOS", "search-option-cllos");
qsc_core_define_constant("QSC_CMP_FORM_SEARCH_OPTION_COURSES", "search-option-courses");
qsc_core_define_constant("QSC_CMP_FORM_SEARCH_OPTION_DEGREES", "search-option-degrees");
qsc_core_define_constant("QSC_CMP_FORM_SEARCH_OPTION_DEPARTMENTS", "search-option-departments");
qsc_core_define_constant("QSC_CMP_FORM_SEARCH_OPTION_DLES", "search-option-dles");
qsc_core_define_constant("QSC_CMP_FORM_SEARCH_OPTION_FACULTIES", "search-option-faculties");
qsc_core_define_constant("QSC_CMP_FORM_SEARCH_OPTION_ILOS", "search-option-ilos");
qsc_core_define_constant("QSC_CMP_FORM_SEARCH_OPTION_PLANS", "search-option-plans");
qsc_core_define_constant("QSC_CMP_FORM_SEARCH_OPTION_PLLOS", "search-option-pllos");
qsc_core_define_constant("QSC_CMP_FORM_SEARCH_OPTION_PROGRAMS", "search-option-programs");
qsc_core_define_constant("QSC_CMP_FORM_SEARCH_OPTION_REVISIONS", "search-option-revisions");

qsc_core_define_constant("QSC_CMP_FORM_SEARCH_RESULTS_CLLOS", "search-results-cllos");
qsc_core_define_constant("QSC_CMP_FORM_SEARCH_RESULTS_COURSES", "search-results-courses");
qsc_core_define_constant("QSC_CMP_FORM_SEARCH_RESULTS_DEGREES", "search-results-degrees");
qsc_core_define_constant("QSC_CMP_FORM_SEARCH_RESULTS_DEPARTMENTS", "search-results-departments");
qsc_core_define_constant("QSC_CMP_FORM_SEARCH_RESULTS_DLES", "search-results-dles");
qsc_core_define_constant("QSC_CMP_FORM_SEARCH_RESULTS_FACULTIES", "search-results-faculties");
qsc_core_define_constant("QSC_CMP_FORM_SEARCH_RESULTS_ILOS", "search-results-ilos");
qsc_core_define_constant("QSC_CMP_FORM_SEARCH_RESULTS_PLANS", "search-results-plans");
qsc_core_define_constant("QSC_CMP_FORM_SEARCH_RESULTS_PLLOS", "search-results-pllos");
qsc_core_define_constant("QSC_CMP_FORM_SEARCH_RESULTS_PROGRAMS", "search-results-programs");
qsc_core_define_constant("QSC_CMP_FORM_SEARCH_RESULTS_REVISIONS", "search-results-revisions");


/******************************************************************************/
/* AJAX */
/******************************************************************************/
qsc_core_define_constant("QSC_CMP_AJAX_ACTION", "action");
qsc_core_define_constant("QSC_CMP_AJAX_ACTION_SEARCH_CLLOS", "searchCLLOs");
qsc_core_define_constant("QSC_CMP_AJAX_ACTION_SEARCH_COURSES", "searchCourses");
qsc_core_define_constant("QSC_CMP_AJAX_ACTION_SEARCH_DEGREES", "searchDegrees");
qsc_core_define_constant("QSC_CMP_AJAX_ACTION_SEARCH_DEPARTMENTS", "searchDepartments");
qsc_core_define_constant("QSC_CMP_AJAX_ACTION_SEARCH_DLES", "searchDLEs");
qsc_core_define_constant("QSC_CMP_AJAX_ACTION_SEARCH_FACULTIES", "searchFaculties");
qsc_core_define_constant("QSC_CMP_AJAX_ACTION_SEARCH_ILOS", "searchILOs");
qsc_core_define_constant("QSC_CMP_AJAX_ACTION_SEARCH_PLANS", "searchPlans");
qsc_core_define_constant("QSC_CMP_AJAX_ACTION_SEARCH_PLANS_FOR_PLLOS", "searchPlansForPLLOs");
qsc_core_define_constant("QSC_CMP_AJAX_ACTION_SEARCH_PLLOS", "searchPLLOs");
qsc_core_define_constant("QSC_CMP_AJAX_ACTION_SEARCH_PROGRAMS", "searchPrograms");
qsc_core_define_constant("QSC_CMP_AJAX_ACTION_SEARCH_REVISIONS", "searchRevisions");

qsc_core_define_constant("QSC_CMP_AJAX_ACTION_GET_CLLO_FROM_ID", "getCLLOFromID");
qsc_core_define_constant("QSC_CMP_AJAX_ACTION_GET_CLLOS_FOR_COURSE", "getCLLOsForCourse");
qsc_core_define_constant("QSC_CMP_AJAX_ACTION_GET_COURSE_FROM_ID", "getCourseFromID");
qsc_core_define_constant("QSC_CMP_AJAX_ACTION_GET_DEGREE_FROM_ID", "getDegreeFromID");
qsc_core_define_constant("QSC_CMP_AJAX_ACTION_GET_DEPARTMENT_FROM_ID", "getDepartmentFromID");
qsc_core_define_constant("QSC_CMP_AJAX_ACTION_GET_DLE_FROM_ID", "getDLEFromID");
qsc_core_define_constant("QSC_CMP_AJAX_ACTION_GET_FACULTY_FROM_ID", "getFacultyFromID");
qsc_core_define_constant("QSC_CMP_AJAX_ACTION_GET_ILO_FROM_ID", "getILOFromID");
qsc_core_define_constant("QSC_CMP_AJAX_ACTION_GET_PLAN_FROM_ID", "getPlanFromID");
qsc_core_define_constant("QSC_CMP_AJAX_ACTION_GET_PLLO_FROM_ID", "getPLLOFromID");
qsc_core_define_constant("QSC_CMP_AJAX_ACTION_GET_PLLOS_FOR_PLAN", "getPLLOsForPlan");
qsc_core_define_constant("QSC_CMP_AJAX_ACTION_GET_PLLOS_FOR_PLANS", "getPLLOsForPlans");
qsc_core_define_constant("QSC_CMP_AJAX_ACTION_GET_PROGRAM_FROM_ID", "getProgramFromID");
qsc_core_define_constant("QSC_CMP_AJAX_ACTION_GET_REVISION_FROM_ID", "getRevisionFromID");

qsc_core_define_constant("QSC_CMP_AJAX_INPUT_ID", "id");
qsc_core_define_constant("QSC_CMP_AJAX_INPUT_SEARCH", "search");
qsc_core_define_constant("QSC_CMP_AJAX_INPUT_EXCLUDE", "exclude");

qsc_core_define_constant("QSC_CMP_AJAX_OUTPUT_ID", "id");
qsc_core_define_constant("QSC_CMP_AJAX_OUTPUT_NAME", "name");
qsc_core_define_constant("QSC_CMP_AJAX_OUTPUT_EXCLUDE", "exclude");
qsc_core_define_constant("QSC_CMP_AJAX_OUTPUT_LINK", "link");


/******************************************************************************/
/* Query Strings */
/******************************************************************************/
define("QSC_CORE_QUERY_STRING_NAME_DEPARTMENT_ID", "dept_id");
define("QSC_CORE_QUERY_STRING_NAME_SUBJECT", "subject");
define("QSC_CORE_QUERY_STRING_NAME_PLAN_ID", "plan_id");
define("QSC_CORE_QUERY_STRING_NAME_LEVEL", "level");
define("QSC_CORE_QUERY_STRING_NAME_OR_ABOVE", "or_above");


/******************************************************************************/
/* Misc */
/******************************************************************************/
qsc_core_define_constant("QSC_CMP_LOCAL_DEVELOPMENT", false);
qsc_core_define_constant("QSC_CMP_LOCAL_DEVELOPMENT_USER_ID", '');
qsc_core_define_constant("QSC_CMP_LOCAL_DEVELOPMENT_USER_FIRST_NAME", '');
qsc_core_define_constant("QSC_CMP_LOCAL_DEVELOPMENT_USER_LAST_NAME", '');

qsc_core_define_constant("QSC_CMP_TEXT_NONE", "<em>None</em>");
qsc_core_define_constant("QSC_CMP_TEXT_NONE_SPECIFIED", "<em>None specified</em>");

qsc_core_define_constant("QSC_CMP_HTML_REVISION_NONE", "<span class=\"revision-none\">None</span>");

qsc_core_define_constant("QSC_CMP_COURSE_CODE_DELIMETER", " ");
qsc_core_define_constant("QSC_CMP_COURSE_CROSS_REFERENCE_DELIMETER", "/");
qsc_core_define_constant("QSC_CMP_COURSE_UNITS_DEFAULT", 3.0);

qsc_core_define_constant("QSC_CMP_COURSELIST_LEVEL_PREFIX", " at the ");
qsc_core_define_constant("QSC_CMP_COURSELIST_LEVEL_POSTFIX", " level");
qsc_core_define_constant("QSC_CMP_COURSELIST_OR_ABOVE_POSTFIX", " or above");

qsc_core_define_constant("QSC_CMP_COURSELIST_RELATIONSHIP_SEPARATOR_AND", " and ");
qsc_core_define_constant("QSC_CMP_COURSELIST_RELATIONSHIP_SEPARATOR_OR", " or ");
qsc_core_define_constant("QSC_CMP_COURSELIST_RELATIONSHIP_SEPARATOR_ANY", "; ");

qsc_core_define_constant("QSC_CMP_COURSELIST_OPTION_COURSE_SEPARATOR", " ; ");

qsc_core_define_constant("QSC_CMP_COURSELIST_SUBSETS_RECURSIVE_LIMIT", 1000);
qsc_core_define_constant("QSC_CMP_COURSELIST_SUBSETS_DISPLAY_LIMIT", 50);
qsc_core_define_constant("QSC_CMP_COURSELIST_SUBSETS_MAXIMUM_ADDITIONAL_UNITS", 3.0);

qsc_core_define_constant("QSC_CMP_PROGRAM_AND_PLAN_NAME_DELIMETER", "-");
qsc_core_define_constant("QSC_CMP_PROGRAM_AND_PLAN_CODE_DELIMETER", "-");

qsc_core_define_constant("QSC_CMP_PLLO_PREFIX_DELIMETER", "-");

qsc_core_define_constant("QSC_CMP_PLAN_REQUIREMENT_START_NUMBER", 1);
qsc_core_define_constant("QSC_CMP_SUB_PLAN_START_LETTER", 'A');
qsc_core_define_constant("QSC_CMP_SUB_PLAN_REQUIREMENT_START_NUMBER", 1);


