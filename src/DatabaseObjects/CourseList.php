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

use Managers\SubsetManager;
use Managers\CurriculumMappingDatabase as CMD;
use Managers\CourseCalendarDatabase as CCD;


abstract class CourseList extends DatabaseObject {
    /*************************************************************************
     * Static Functions
     ************************************************************************/
    /**
     * This function checks the class in the database row and calls the
     * corresponding function in the correct child class to create the 
     * CourseList object.
     * 
     * NOTE: this only handles a row from the 'courselist' table; it doesn't
     * initialize the particular class of CourseList.
     *
     * @param $argArray        The course row from the database
     * @return                 A new CourseList object with those values
     */
    public static function buildFromDBRow($argArray) {
        $courseList = null;
        
        $id = $argArray[CMD::TABLE_COURSELIST_ID];
        $name = $argArray[CMD::TABLE_COURSELIST_NAME];
        $notes = $argArray[CMD::TABLE_COURSELIST_NOTES];

        $listClass = $argArray[CMD::TABLE_COURSELIST_CLASS];
        
        switch($listClass) {
            case CMD::TABLE_COURSELIST_CLASS_RELATIONSHIP :
                $courseList = new RelationshipCourseList($id, $name, $notes);
                break;
            case CMD::TABLE_COURSELIST_CLASS_OPTION :
                $courseList = new OptionCourseList($id, $name, $notes);
                break;
            case CMD::TABLE_COURSELIST_CLASS_SUBJECT :
                $courseList = new SubjectCourseList($id, $name, $notes);
                break;
        }
        
        return $courseList;
    }
    
    /**
     * 
     * @return type
     */
    public static function getSortFunction() {
        return function($a, $b) { 
                return strcmp($a->getName(), $b->getName());
            };        
    }     
    
    /**
     * Returns all course subsets with units in the range 
     * [$units, $units + QSC_CMP_COURSELIST_MAXIMUM_ADDITIONAL_UNITS]
     * to a limit of QSC_CMP_COURSELIST_RECURSIVE_LIMIT recursive calls.
     */
    public static function getSubsetManagerForCourses($courseArray, $units = false, $optionArray = array()) {        
        // Prepare the parameters
        $parameterArray = array(
            SubsetManager::MAXIMUM_RECURSIVE_CALLS => QSC_CMP_COURSELIST_SUBSETS_RECURSIVE_LIMIT);
            
        if ($units !== false) {
            $parameterArray[SubsetManager::ELEMENT_VALUE_FUNCTION] = 
                function($course) { return $course->getUnitsAsFloat(); };
            $parameterArray[SubsetManager::SUBSET_MINIMUM_VALUE] = $units;
            $parameterArray[SubsetManager::SUBSET_MAXIMUM_VALUE] = 
                $units + QSC_CMP_COURSELIST_SUBSETS_MAXIMUM_ADDITIONAL_UNITS;
            $parameterArray[SubsetManager::SUBSET_STOP_VALUE] = $units;
            
        }
        
        return new SubsetManager($courseArray, 
            qsc_core_merge_arrays($optionArray, $parameterArray));
    }     
        

    /**************************************************************************
     * Member Variables
     **************************************************************************/
    // Stored in the courselist table
    protected $name = null;
    protected $notes = null;
    
    // Generated from information (or lack thereof) in the courselist table    
    protected $html = null; 
    
    // Stored in the courselist_and_course table *or* derived from the 
    // courselist_and_subject table
    protected $childCourseArray = array();
    
    // Stored in the courselist_and_courselist table
    protected $childCourseListArray = array();
    
    // Stored in the courselist_and_courselist_level table
    protected $level = CMD::TABLE_COURSELIST_TO_COURSELIST_LEVEL_NONE;
    protected $orAbove = false;
       
    

    /**************************************************************************
     * Constructor
     **************************************************************************/
    /**
     * This constructor sets all of the member variables except for the parent
     * ID.
     *
     * @param type $argDBID
     * @param type $argName
     * @param type $argNotes
     */
    protected function __construct($argDBID, $argName = null, $argNotes = null) {
        parent::__construct($argDBID);
        
        $this->name = empty($argName) ? '' : $argName;
        $this->notes = $argNotes;
    }


    /*************************************************************************
     * Initialize
     *************************************************************************/
    /**
     * 
     */
    protected function initializeChildCoursesWithCalendar() {
        $dbCalendar = new CCD();
        
        // Go through each course
        foreach ($this->childCourseArray as $childCourse) {
            // Get the units for each course
            $childCourse->initializeUnits($dbCalendar);
        }
    }
       

    /**************************************************************************
     * Get and Set Methods
     **************************************************************************/
    /** 
     * The get method for the course list's class.
     *
     * @return  The string class
     */ 
    public function getListClass() {
        return $this->listClass;           
    }
    
    /** 
     * The get method for the course list's name.
     *
     * @param type $noneOption
     * @return  The string name
     */ 
    public function getName($noneOption = null) {
        return qsc_core_get_none_if_empty($this->name, $noneOption);           
    }
        
    /** 
     * The get method for the course list's notes.
     *
     * @param type $noneOption
     * @return  The string notes
     */ 
    public function getNotes($noneOption = null) {
        return qsc_core_get_none_if_empty($this->notes, $noneOption);   
    }
    
    /** 
     * The get method for the course list formatted as HTML.
     *
     * @param type $noneOption
     * @return  The string containing HTML
     */ 
    public function getHTML($noneOption = null) {
        return qsc_core_get_none_if_empty($this->html, $noneOption);           
    }        
    
    /**
     * 
     * @return type
     */
    public function getChildCourseArray() {
        return $this->childCourseArray;
    }
    
    /**
     * 
     * @return type
     */
    public function getChildCourseListArray() {
        return $this->childCourseListArray;
    }
    
    /** 
     * The get method for the course list's level.
     *
     * @return  The string level
     */ 
    public function getLevel($noneOption = null) {
        return qsc_core_get_none_if_empty($this->level, $noneOption);           
    }
    
    /** 
     * The get method for the course list's 'or above' boolean.
     *
     * @return  The boolean 
     */ 
    public function orAbove() {
        return $this->orAbove;           
    }    
    
    
    /**************************************************************************
     * Member Functions
     **************************************************************************/   
    /**
     * Creates a link to view this course list using its ID.
     *
     * @return      A string containing the link
     */
    public function getLinkToView() {
        $queryStringArray = array(QSC_CORE_QUERY_STRING_NAME_ID => $this->dbID);
        if ($this->hasLevel()) {
            $queryStringArray[QSC_CORE_QUERY_STRING_NAME_LEVEL] = $this->level;
            if ($this->orAbove()) {
                $queryStringArray[QSC_CORE_QUERY_STRING_NAME_OR_ABOVE] = $this->orAbove;                
            }
        }
        
        return qsc_core_create_link_with_query_string(
            QSC_CMP_COURSELIST_VIEW_PAGE_LINK, 
            $queryStringArray);
    }
    
    /**
     * 
     * @return type
     */
    public function getAnchorToView() {
        return '<a href="'.$this->getLinkToView().'">'.$this->getName().'</a>';
    }    
        
    /**
     * 
     */
    public function getNameSnippet() {
        return qsc_core_get_snippet($this->name, QSC_CORE_STRING_SNIPPET_LENGTH_HEADING);
    }
    
    /**
     * 
     * @return type
     */
    public function hasLevel() {
        return ($this->level != CMD::TABLE_COURSELIST_TO_COURSELIST_LEVEL_NONE);
    }
    
    /**
     * 
     */
    protected function getLevelAndOrAbovePostfix() {
        $postfix = '';
        
        if ($this->hasLevel()) {
            $postfix = QSC_CMP_COURSELIST_LEVEL_PREFIX.$this->level.QSC_CMP_COURSELIST_LEVEL_POSTFIX;            
        }
        if ($this->orAbove()) {
            $postfix .= QSC_CMP_COURSELIST_OR_ABOVE_POSTFIX;
        }
        
        return $postfix;
    }
    
    /**
     * This function returns the total number of Courses associated with this
     * CourseList.
     * 
     * @return type
     */
    public function getNumberOfCourses() {
        // Start with any directly related courses
        $numberOfCourses = count($this->childCourseArray);
        
        // Now add those in any child course lists
        foreach ($this->childCourseListArray as $childCourseList) {
            $numberOfCourses += $childCourseList->getNumberOfCourses();
        }
        
        return $numberOfCourses;
    }     
    
    /**
     * This function returns all of the Courses associated with this
     * CourseList.
     */
    public function getAllCourses() {
        // Start with any directly related courses
        $resultCourseArray = qsc_core_clone_array($this->childCourseArray);
        
        // Now add those in any child course lists
        foreach ($this->childCourseListArray as $childCourseList) {
            $resultCourseArray = array_merge($resultCourseArray, 
                $childCourseList->getAllCourses());
        }
        
        return $resultCourseArray;
    }   
    
    /**
     * Returns the total number of course subsets.
     * 
     * <strong>NOTE:</strong> the return value is based on the total number 
     * of courses and no limit on the number of units.
     */
    public function getNumberOfCourseSubsets() {        
        // The number of subsets is 2^n - 1 where n is the number of courses
        return 2**($this->getNumberOfCourses()) - 1;
    }    
        
    /**
     * Returns all course subsets with units in the range 
     * [$units, $units + QSC_CMP_COURSELIST_MAXIMUM_ADDITIONAL_UNITS]
     * to a limit of QSC_CMP_COURSELIST_RECURSIVE_LIMIT recursive calls.
     */
    public function getAllCourseSubsets($units = false, $optionArray = array()) {
        return self::getSubsetManagerForCourses($this->getAllCourses(), $units, $optionArray);
    }

}
