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


/**
 * The class RelationshipCourseList represents a course list with a 
 * relationship (e.g., and, or) as stored in the database.
 */
class RelationshipCourseList extends CourseList {        
    /**************************************************************************
     * Static Functions
     **************************************************************************/    
    /**
     * 
     * @return boolean
     */
    public static function sortAfterInitialization() {
        return true;
    }

    
    /**************************************************************************
     * Member Variables
     **************************************************************************/
    protected $relationship = null;  
    
           
    /*************************************************************************
     * Initialize
     *************************************************************************/
    /**
     * Initializes the member variables that can't be set by a single DB row.
     * NOTE: Relationship lists don't have levels; the additional arguments
     * are ignored.
     * 
     * @param type $dbCurriculum
     */
    public function initialize($dbCurriculum, $argArray = array()) {
        $listName = $this->name;
        $listHTML = $this->html;

        // Determine the relationship for this course list
        $this->relationship = $dbCurriculum->getCourseListRelationship($this->getDBID());   
                
        // Get the courses that are directly associated with this list
        $this->childCourseArray = $dbCurriculum->getCoursesInCourseList($this->dbID);
        
        // Initialize these courses using the calendar database
        $this->initializeChildCoursesWithCalendar();
        
        // Fetch and initialize the child course lists
        $this->childCourseListArray = $dbCurriculum->getChildCourseLists($this->dbID);
        
        // Determine the name and link/HTML for this course list
        // Is there already a name specified?
        if ($listName) {
            // If so, use it in the HTML representation
            $listHTML = '<a href="'.$this->getLinkToView().'">'.$listName.'</a>';            
        }
        else {
            // Build the name from the course information
            $listName = $this->createCourseString();
            $listHTML = $this->createCourseString(true);            
        }
        
        $this->name = $listName;
        $this->html = $listHTML;        
    }
    
    /**
     * 
     * @param type $getHTML
     * @return type
     */
    protected function createCourseString($getHTML = false) {
        // Start with courses that are directly ties to this list
        $courseString = implode($this->getRelationshipSeparator(), 
            qsc_core_map_member_function($this->childCourseArray, 
                ($getHTML) ? 'getAnchorToView' : 'getName'));
        
        // Add child course lists
        if (!empty($this->childCourseListArray)) {
            $childListNameArray = array();
            
            foreach ($this->childCourseListArray as $childCourseList) {
                $childListName = ($getHTML) ?
                    $childCourseList->getHTML() :
                    $childCourseList->getName();
                $childListNameArray[] = 
                    ($childCourseList instanceof RelationshipCourseList) ?
                        "($childListName)" : $childListName;
            }

            $courseString .= ($courseString) ? $this->getRelationshipSeparator() : '';
            $courseString .= implode($this->getRelationshipSeparator(), $childListNameArray);
        }
        
        return $courseString;
    }


    /**************************************************************************
     * Get and Set Methods
     **************************************************************************/
    /** 
     * The get method for the course list's relationship.
     *
     * @return  The string relationship
     */ 
    public function getRelationship() {
        return $this->relationship;           
    }
    
    /**
     * 
     * @return type
     */
    public function isAnd() {
        return ($this->relationship == CMD::TABLE_COURSELIST_AND_RELATIONSHIP_RELATIONSHIP_AND);
    }

    /**
     * 
     * @return type
     */
    public function isOr() {
        return ($this->relationship == CMD::TABLE_COURSELIST_AND_RELATIONSHIP_RELATIONSHIP_OR);
    }    

    /**
     * 
     * @return type
     */
    public function isAny() {
        return ($this->relationship == CMD::TABLE_COURSELIST_AND_RELATIONSHIP_RELATIONSHIP_ANY);
    }    


    /**************************************************************************
     * Member Functions
     **************************************************************************/
    /** 
     * 
     * @return string
     */
    public function getRelationshipSeparator() {
        switch ($this->relationship) {
            case CMD::TABLE_COURSELIST_AND_RELATIONSHIP_RELATIONSHIP_AND:
                return QSC_CMP_COURSELIST_RELATIONSHIP_SEPARATOR_AND;
            case CMD::TABLE_COURSELIST_AND_RELATIONSHIP_RELATIONSHIP_OR:
                return QSC_CMP_COURSELIST_RELATIONSHIP_SEPARATOR_OR;
            case CMD::TABLE_COURSELIST_AND_RELATIONSHIP_RELATIONSHIP_ANY:
                return QSC_CMP_COURSELIST_RELATIONSHIP_SEPARATOR_ANY;
        }
        
        return ' ';
    }   
    
    /**
     * The value is calculate using the same approach as that in 
     * getAllCourseSubsets(...)
     * 
     * @return type
     */
    public function getNumberOfCourseSubsets() {
        $totalNumberOfSubsets = 0;
        
        $numberOfChildCourseListSubsets = 0;
        
        // Start by gathering all individual courses and all subsets
        
        // Start with the number of child courses
        $numberOfDescendantCourses = count($this->childCourseArray);
        
        // Go through the child lists and look at the type
        foreach ($this->childCourseListArray as $childCourseList) {
            if ($this->isAny() && (! ($childCourseList instanceof RelationshipCourseList))) {
                // This relationship is about any possibility and the child 
                // list is an OptionCourseList or a SubjectCourseList.
                // Append all of the courses from the list.
                $numberOfDescendantCourses += 
                    $childCourseList->getNumberOfCourses();               
            }
            else {
                // Store all of the child list's subsets.
                $numberOfChildCourseListSubsets += 
                    $childCourseList->getNumberOfCourseSubsets();
                
            }             
        }
        
        // Now check the relationship
        if ($this->isOr()) {
            // For 'or', it's individual courses merged with the subsets from
            // any child lists
            
            // Add the sizes of the two sets of subsets
            $totalNumberOfSubsets = $numberOfDescendantCourses + 
                $numberOfChildCourseListSubsets;            
        }
        else if ($this->isAny()) {
            // For 'any', it's any and all combinations of individual courses
            // and subsets from child lists
            
            // Determine the number of subsets for just the descendant courses
            $numberOfDescendantCourses = 2**$numberOfDescendantCourses - 1;
            
            // Merge both existing arrays of subsets into the result
            $totalNumberOfSubsets = $numberOfDescendantCourses + 
                $numberOfChildCourseListSubsets +
                ($numberOfDescendantCourses * $numberOfChildCourseListSubsets);             
        }        
        else {
            // For 'and', individual courses are grouped together as one
            // subset. All subsets are then merged via Cartesian product.
            $totalNumberOfSubsets = max($numberOfDescendantCourses ? 1 : 0,
                $numberOfChildCourseListSubsets);          
        }

        return $totalNumberOfSubsets;
    }    
    
    /**
     * Getting all possible subsets in a RelationshipCourseList is trickier: 
     * you have to merge the child courses and those from the child lists
     * according to the relationship. Here's the approach:
     * 
     * 1) Collect all individual courses together: this list's child courses
     * and any courses from child OptionCourseLists and SubjectCourseLists.
     * 2) Collect all subsets from child RelationshipCourseLists.
     * 3) Consider the relationship:
     *      3.1) OR
     *          3.1.1) Create subsets from the individual courses
     *          3.1.2) Return all individual course subsets and child 
     *                 RelationshipCourseList subsets
     *      3.2) ANY
     *          3.2.1) Create subsets from the individual courses
     *          3.2.2) Get all child RelationshipCourseList subsets 
     *          3.2.3) Return all subsets *and* the Cartesian product of both
     *      3.3) AND
     *          3.3.1) Merge all individual courses with each child
     *                 RelationshipCourseList subset via Cartesian product
     * 
     * NOTE: units are a problem here
     * 
     * @param type $units
     * @param type $optionArray
     * @return array
     */
    public function getAllCourseSubsets($units = false, $optionArray = array()) {
        // Set up the managers to ignore the minimum units (for now)
        $subsetManagerOptionArray = empty($optionArray) ? 
            array(SubsetManager::SUBSET_MINIMUM_VALUE => false) :
            $optionArray;
        
        $childCourseListSubsetManager = self::getSubsetManagerForCourses(array(), 
            $units, $subsetManagerOptionArray);
        
        $resultSubsetManager = self::getSubsetManagerForCourses(array(), 
            $units, $subsetManagerOptionArray);
        
        // Start by gathering all individual courses and all subsets
        
        // Copy all directly related courses into the descendant course array
        $descendantCourseArray = qsc_core_clone_array($this->childCourseArray);
        
        // Go through the child lists and look at the type
        foreach ($this->childCourseListArray as $childCourseList) {
            if ($this->isAny() && (! ($childCourseList instanceof RelationshipCourseList))) {
                // This relationship is about any possibility and the child 
                // list is an OptionCourseList or a SubjectCourseList.
                // Append all of the courses from the list.
                $descendantCourseArray = array_merge($descendantCourseArray,
                    $childCourseList->getAllCourses());                
            }
            else {
                // Store all of the child list's subsets
                $childCourseListSubsetManager->addSubsetsFromSubsetManager(
                    $childCourseList->getAllCourseSubsets($units,
                        $subsetManagerOptionArray));
                
            }            
        }
        
        // Now check the relationship and figure out what to do with the 
        // courses and subsets.
        if ($this->isOr()) {
            // 'or' means one choice for all the collected options.
            
            // Create single-element 'subsets' from all descendant courses
            // and add them to the result
            $resultSubsetManager->addSubsetsFromArray(
                array_map(function($element) { return array($element); },
                    $descendantCourseArray)
            );
            
            // Add the subsets from the child lists
            $resultSubsetManager->addSubsetsFromSubsetManager($childCourseListSubsetManager);            
        }
        else if ($this->isAny()) {
            // 'any' means any and all combinations of individual courses
            // and subsets from child lists.
            
            // Start with the subsets for just the descendant courses and add
            // to the result.
            $resultSubsetManager = CourseList::getSubsetManagerForCourses(
                $descendantCourseArray, $units);            
            
            // Add the subsets from the child lists and use Cartesian product
            // *but* include all existing subsets as well
            $resultSubsetManager->addSubsetsFromSubsetManager(
                $childCourseListSubsetManager,
                SubsetManager::ADD_SUBSETS_TYPE_CARTESIAN_PRODUCT_PLUS_SUBSETS);            
        }
        else {
            // 'and' means grouping all individual courses as one subset 
            // with each child list subset.
            
            // Add the descendant courses as a single subset.
            $resultSubsetManager->addSubsetsFromArray(
                array($descendantCourseArray));

            // Add the subsets from the child lists and use Cartesian product
            $resultSubsetManager->addSubsetsFromSubsetManager(
                $childCourseListSubsetManager,
                SubsetManager::ADD_SUBSETS_TYPE_CARTESIAN_PRODUCT);
        }

        $resultSubsetManager->applyMinimumValue();
        return $resultSubsetManager;        
    }    
    
}
