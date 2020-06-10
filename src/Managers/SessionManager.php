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


/** 
 * This class is essentially a wrapper for constants and (static) functions
 * that access and modify session variables.
 */
class SessionManager {
    /**************************************************************************
     * Constants
     **************************************************************************/
    private const SESSION_USER_ID = "qsc_cmp_user_id";
    private const SESSION_USER_FIRST_NAME = "qsc_cmp_user_first_name";
    private const SESSION_USER_LAST_NAME = "qsc_cmp_user_last_name";
    private const SESSION_USER_LOGIN_TIME = "qsc_cmp_user_login_time";
    private const SESSION_ERROR_MESSAGE = "qsc_cmp_error_message"; 
     
     
    /**************************************************************************
     * Static Functions
     **************************************************************************/     
    /** 
     * Manages the session values associated with a user logging in.
     *
     * @param $user_id          The string user's ID
     * @param $user_first_name  The string user's first name
     * @param $user_last_name   The string user's last name
     * @param $user_login_time  The string time the user logged in
     */
    public static function userLogin($user_id, $user_first_name, $user_last_name, $user_login_time) {
        $_SESSION[self::SESSION_USER_ID] = $user_id;
        $_SESSION[self::SESSION_USER_FIRST_NAME] = $user_first_name;
        $_SESSION[self::SESSION_USER_LAST_NAME] = $user_last_name;
        $_SESSION[self::SESSION_USER_LOGIN_TIME] = $user_login_time;
    } 
    
    /** 
     * Initializes the session for use on a page or in a script.
     */
    public static function initialize() {
        session_start();
    }    
       
    /** 
     * Removes the session values and CLLOses the session when a user logs out.
     */
    public static function userLogout() {
        session_unset();  
        session_destroy(); 
    }
    
    /** 
     * Returns whether the user is presently logged in.
     *
     * @return  A boolean with the user's login status
     */
    public static function isUserLoggedIn() {
        return (! empty(self::getUserID()));
    }    

    /** 
     * Returns the user's ID.
     *
     * @return  The string user's ID
     */
    public static function getUserID() {
        return array_key_exists(self::SESSION_USER_ID, $_SESSION) ?
            $_SESSION[self::SESSION_USER_ID] : "";
    }

    /** 
     * Returns the user's login time.
     *
     * @return  The string user's login time
     */
    public static function getUserLoginTime() {
        return array_key_exists(self::SESSION_USER_LOGIN_TIME, $_SESSION) ?
            $_SESSION[self::SESSION_USER_LOGIN_TIME] : "";
    }
    
    /** 
     * Returns the user's first name.
     *
     * @return  The string user's name
     */
    public static function getUserFirstName() {
        return array_key_exists(self::SESSION_USER_FIRST_NAME, $_SESSION) ?
            $_SESSION[self::SESSION_USER_FIRST_NAME] : "";
    }
    
    /** 
     * Returns the last 'saved' error message.
     *
     * @return  A string which may contain HTML
     */
    public static function getErrorMessage() {
        return array_key_exists(self::SESSION_ERROR_MESSAGE, $_SESSION) ?
            $_SESSION[self::SESSION_ERROR_MESSAGE] : "";
    }
    
    /** 
     * Records/'saves' an error message.
     *
     * @param $message      A string which may contain HTML
     */
    public static function setErrorMessage($message) {
        $_SESSION[self::SESSION_ERROR_MESSAGE] = $message;
    }    
}