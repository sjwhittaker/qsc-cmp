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


use Managers\CurriculumMappingDatabase as CMD;


/** 
 * The class CourseEntry is intended to store the information from a row in the
 * database about a single course.
 * 
 * NOTE: a CourseEntry can represent one offering of a Course, which may be 
 * cross-referenced.
 */
class CourseEntry extends DatabaseObject {
    /**************************************************************************
     * Static Functions
     **************************************************************************/
    /** 
     * This function uses the values in a database row to create a new 
     * Course object and set the values of the member variables.
     *
     * @param $argArray        The row from the database
     */ 
     public static function buildFromDBRow($argArray) {
        $id = $argArray[CMD::TABLE_COURSE_ID];
        $subject = $argArray[CMD::TABLE_COURSE_SUBJECT];
        $number = $argArray[CMD::TABLE_COURSE_NUMBER];
        $notes = $argArray[CMD::TABLE_COURSE_NOTES];
         
        return new CourseEntry($id, $subject, $number, $notes);
    }
    
    /**
     * 
     * @return type
     */
    public static function getSortFunction() {
        return function($a, $b) { 
                $sortValue = strcmp($a->getSubject(), $b->getSubject());
                return ($sortValue === 0) ? 
                    strcmp($a->getNumber(), $b->getNumber()) : $sortValue;
            };        
    }    
         
     
    /**************************************************************************
     * Member Variables
     **************************************************************************/
    protected $subject = null;
    protected $number = null;
    protected $notes = null;
     
 
    /**************************************************************************
     * Constructor
     **************************************************************************/
    /** 
     * This constructor sets all of the member variables using the arguments.
     *
     * @param $argDBID         The course's database integer ID
     * @param $argSubject      The course's string subject
     * @param $argNumber       The course's string 'number'
     * @param $argNotes        The course's string notes
     */ 
    public function __construct($argDBID, $argSubject, $argNumber, $argNotes) {
        parent::__construct($argDBID);
         
        $this->subject = $argSubject;
        $this->number = $argNumber;
        $this->notes = $argNotes;
    }

     
    /**************************************************************************
     * Get and Set Methods
     **************************************************************************/
    /** 
     * The get method for the course's subject(s).
     *
     * @return  The string subject(s)
     */ 
    public function getSubject() {
        return $this->subject;
    }
    
    /** 
     * The get method for the course's notes.
     *
     * @return  The string notes
     */ 
    public function getNotes($noneOption = null) {
        return qsc_core_get_none_if_empty($this->notes, $noneOption);
    }        
    
    /** 
     * The get method for the course's number.
     *
     * @return  The string 'number'
     */ 
    public function getNumber() {
        return $this->number;   
    } 
     
    /** 
     * The get method for the course's name, which includes the subject followed
     * by the number.
     *
     * @return  The string name
     */ 
    public function getName() {
        return $this->subject.QSC_CMP_COURSE_CODE_DELIMETER.$this->number;
    }
    
    /**
     * 
     * @return type
     */
    public function getCode() {
        return $this->getName();
    }
    

    /**************************************************************************
     * Member Functions
     **************************************************************************/
    /**
     * Creates a link to view this CourseEntry via its Course using its ID.
     *
     * @return      A string containing the link
     */
    public function getLinkToView() {
        return self::getLinkWithID(QSC_CMP_COURSE_VIEW_PAGE_LINK);
    }
        
    /**
     * Creates the key for the corresponding course in the course calendar 
     * database, which is the subject followed by the number.
     *
     * @return      The string course calendar DB ID
     */
    public function getCalendarCourseDBID() {
        return $this->getName();
    }   
    
}