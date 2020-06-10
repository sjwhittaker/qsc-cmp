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
use Managers\CurriculumMappingDatabase as CMD;


/**
 * Handles all aspects of a user logging in, including initializing the
 * database connections and recording the login in the database.
 *
 * @return      A boolean representing whether login was successful (true)
 *              or not (false).
 */
if (! function_exists("qsc_cmp_log_user_in")) {
    function qsc_cmp_log_user_in() {
        // Connect to the curriculum mapping database
        $db_curriculum = new CMD();

        // Attempt #1: check for local development and log in with the constants
        if (QSC_CMP_LOCAL_DEVELOPMENT) {
            $login_time = date(QSC_CORE_DATE_AND_TIME_FORMAT);
            
            SessionManager::userLogin(QSC_CMP_LOCAL_DEVELOPMENT_USER_ID, 
                QSC_CMP_LOCAL_DEVELOPMENT_USER_FIRST_NAME, 
                QSC_CMP_LOCAL_DEVELOPMENT_USER_LAST_NAME, 
                $login_time);
            
            // Record the login in the database
            $db_curriculum->recordUserLogin(QSC_CMP_LOCAL_DEVELOPMENT_USER_ID, $login_time);
            
            return true;
        }

        // Attempt #2: check for an institution-level sign-in system
        if (function_exists("qsc_cmp_log_user_in_institution")) {
            if (qsc_cmp_log_user_in_institution($db_curriculum)) {
                return true;
            }
        }

        // TBD
        // Attempt #3: attempt login via CANARIE

        // All attempts have failed
        return false;
    }
}

/**
 * Handles all aspects of logging the user out, including recording it in
 * the database and destroying all session variables (including the
 * database connection).
 *
 * @return      A boolean representing whether logout was successful (true)
 *              or not (false).
 */
if (! function_exists("qsc_cmp_log_user_out")) {
    function qsc_cmp_log_user_out() {
        if (! SessionManager::isUserLoggedIn()) {
            return false;
        }

        // (Un)set the session variables and close the session
        SessionManager::userLogout();

        return true;
    }
}

/**
 * Checks whether the current user has permission to load a page. At present,
 * all logged-in users have the same permissions: they can view everything.
 * This is more of a placeholder for possible future expansion.
 *
 * @return      A boolean representing whether the user has permission (true)
 *              or not (false).
 */
if (! function_exists("qsc_cmp_check_permissions")) {
    function qsc_cmp_check_permissions() {
        return SessionManager::isUserLoggedIn();
    }
}

/**
 * Starts/initializes everything a user will need to use the system,
 * including login.
 *
 * This function and its counterpart qsc_cmp_end_page_load(...) should not be used on
 * pages where the user won't be logged in, such as those for errors and
 * logout.
 *
 * @return      A boolean representing whether page load was successful (true)
 *              or not (false).
 */
if (! function_exists("qsc_cmp_start_page_load")) {
    function qsc_cmp_start_page_load() {
        // Initialize the session
        SessionManager::initialize();

        // Check if the user isn't logged into this system yet. (The user must be
        // logged in via single sign-on or they wouldn't see the page.)
        // If this fails, the user will be redirected to an error page.
        if (! SessionManager::isUserLoggedIn()) {
            qsc_cmp_log_user_in();
        }

        // Check permissions for this page
        if (! qsc_cmp_check_permissions()) {
            SessionManager::setErrorMessage("You don't have permission to view this page");
            header("Location: ".QSC_CMP_ERROR_PAGE_LINK);
            return false;
        }


        return true;
    }
}

/**
 * This function doesn't presently do anything; it's a placeholder for future
 * functionality.
 */
if (! function_exists("qsc_cmp_end_page_load")) {
    function qsc_cmp_end_page_load() {

    }
}

/**
 * 
 * @return type
 */
function qsc_cmp_get_form_js_completed_on_submission() {
    return qsc_core_extract_form_value(INPUT_POST, QSC_CMP_FORM_JS_COMPLETED_ON_SUBMISSION, FILTER_SANITIZE_STRING);
}

/**
 * 
 * @return type
 */
function qsc_cmp_get_form_type() {
    return qsc_core_extract_form_value(INPUT_POST, QSC_CMP_FORM_TYPE, FILTER_SANITIZE_STRING);
}

/**
 * 
 * @return type
 */
function qsc_cmp_get_form_result() {
    return qsc_core_extract_form_value(INPUT_POST, QSC_CMP_FORM_RESULT, FILTER_SANITIZE_STRING);
}

/**
 * 
 * @param type $db_object_array
 * @return type
 */
function qsc_cmp_extract_form_option_data($db_object_array) {
    $option_data_array = array();
    
    foreach ($db_object_array as $db_object) {
        $id = $db_object->getDBID();
        $text = "";
        if (method_exists($db_object, "getShortSnippet")) {
            $text = $db_object->getShortSnippet();
        }
        
        $option_data_array[$id] = $text;
    }
    
    return $option_data_array;
}

/**
 * 
 * @param type $subject
 * @return type
 */
function qsc_cmp_get_link_to_view_subject($subject) {
    return qsc_core_create_link_with_id(QSC_CMP_SUBJECT_VIEW_PAGE_LINK, 
        $subject, FILTER_SANITIZE_STRING);
}

/**
 * 
 * @param type $subject
 * @return type
 */
function qsc_cmp_get_anchor_to_view_subject($subject) {
    return '<a href="'.qsc_cmp_get_link_to_view_subject($subject).'">'.$subject.'</a>';
}

/**
 * 
 * @param type $name
 * @param type $value
 * @return type
 */
function qsc_cmp_get_link_to_alignment_report($name, $value) {
    return qsc_core_create_link_with_query_string(
        QSC_CMP_REPORTS_ALIGNMENT_LINK,
        array($name => $value));
}

/**
 * 
 * @param type $name
 * @param type $value
 * @return type
 */
function qsc_cmp_get_link_to_course_matrix($name, $value) {
    return qsc_core_create_link_with_query_string(
        QSC_CMP_REPORTS_COURSE_MATRIX_LINK,
        array($name => $value));
}
