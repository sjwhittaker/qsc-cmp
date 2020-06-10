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

use Managers\CourseCalendarDatabase as CCD;


/** 
 * The class CalendarCourse is intended to store the information from a row in
 * the course calendar database about a course.
 */ 
class CalendarCourse extends DatabaseObject {
    /**************************************************************************
     * Static Functions
     **************************************************************************/
    /**
     * This function uses the values in a database row to create a new 
     * CalendarCourse object and set the values of the member variables.
     *
     * @param $argArray        The course row from the database
     */
    public static function buildFromDBRow($argArray) {
        $code = $argArray[CCD::TABLE_COURSES_CODE];
        $name = $argArray[CCD::TABLE_COURSES_NAME];
        $units = $argArray[CCD::TABLE_COURSES_UNITS];
        $description = $argArray[CCD::TABLE_COURSES_DESCRIPTION];
        $prereq = $argArray[CCD::TABLE_COURSES_PREREQ];
        $coreq = $argArray[CCD::TABLE_COURSES_COREQ];
        $note = $argArray[CCD::TABLE_COURSES_NOTE];
        $exclusion = $argArray[CCD::TABLE_COURSES_EXCLUSION];
        $reccomend = $argArray[CCD::TABLE_COURSES_RECCOMEND];
        $oneWay = $argArray[CCD::TABLE_COURSES_ONEWAY];
        $learnHours = $argArray[CCD::TABLE_COURSES_LEARNING_HOURS];
        $equivalency = $argArray[CCD::TABLE_COURSES_EQUIVALENCY];
        $website = $argArray[CCD::TABLE_COURSES_WEBSITE];

        return new CalendarCourse($code, $name, $units, $description, $prereq, $coreq, $note, $exclusion, $reccomend, $oneWay, $learnHours, $equivalency, $website);
    }
        
    /**
     * This function uses the values in a 2D associative array to create an
     * array of new CalendarCourse objects and set the values of the member variables.
     *
     * @param $argArray        The array of course row from the database
     */    
    public static function buildFromDBRows($argArray) {
        return qsc_core_map_member_function($argArray, 
            __NAMESPACE__ .'\CalendarCourse::buildFromDBRow');        
    }


    /**************************************************************************
     * Member Variables
     **************************************************************************/
    protected $code = null;
    protected $name = null;
    protected $units = null;
    protected $description = null;
    protected $prereq = null;
    protected $coreq = null;
    protected $note = null;
    protected $exclusion = null;
    protected $reccomend = null;
    protected $oneWay = null;
    protected $learnHours = null;
    protected $equivalency = null;
    protected $website = null;

    /**************************************************************************
     * Constructor
     **************************************************************************/
    /** 
     * This constructor sets all of the member variables using the arguments.
     *
     * @param $argCode         The course's code (subject and number) and 
     *                         primary key
     * @param $argName         The course's name
     * @param $argUnits        The course's unit's
     * @param $argDescription  The course's description
     * @param $argPrereq       The course's prerequisites (default is null)
     * @param $argCoreq        The course's corequisites (default is null)
     * @param $argNote         The course's notes (default is null)
     * @param $argExclusion    The course's exclusion(s) (default is null)
     * @param $argReccomend    The course's reccomendation (default is null)
     * @param $argOneWay       The course's one-way exclusion(s) (default is
     *                         null)
     * @param $argLearnHours   The course's learning hours (default is null)
     * @param $argEquivalency  The course's equivalency (default is null)
     * @param $argWebsite      The course's website (default is null)
     */ 
     public function __construct($argCode, $argName, $argUnits, $argDescription, $argPrereq = null, $argCoreq = null, $argNote = null, $argExclusion = null, $argReccomend = null, $argOneWay = null, $argLearnHours = null, $argEquivalency = null, $argWebsite = null) {
        parent::__construct($argCode);

        $this->name = $argName;
        $this->units = $argUnits;
        $this->description = $argDescription;
        $this->prereq = $argPrereq;
        $this->coreq = $argCoreq;
        $this->note = $argNote;
        $this->exclusion = $argExclusion;
        $this->reccomend = $argReccomend;
        $this->oneWay = $argOneWay;
        $this->learnHours = $argLearnHours;
        $this->equivalency = $argEquivalency;
        $this->website = $argWebsite;
        
        if (! is_float($this->units)) {
            $this->units = floatval($this->units);
        }
    }

    /**************************************************************************
     * Get and Set Methods
     **************************************************************************/
    /** 
     * The get method for the course's units.
     *
     * @return  The float units
     */ 
    public function getUnits() {
        return $this->units;   
    } 
     
    /** 
     * The get method for the course's name.
     *
     * @return  The string name
     */ 
    public function getName() {
        return $this->name;   
    }      
         
    /** 
     * The get method for the course's description.
     *
     * @return  The string description 
     */ 
    public function getDescription() {
        return $this->description;   
    }      
        
    /** 
     * The get method for the course's prerequisites.
     *
     * @param $noneOption   What to return if the value is null
     * @return              The string prerequisites
     */ 
    public function getPrerequisites($noneOption = null) {
        return self::getNoneIfEmpty($this->prereq, $noneOption);   
    }      

    /** 
     * The get method for the course's corequisites.
     *
     * @param $noneOption   What to return if the value is null
     * @return              The string corequisites
     */ 
    public function getCorequisites($noneOption = null) {
        return self::getNoneIfEmpty($this->coreq, $noneOption);   
    } 
     
    /** 
     * The get method for the course's notes.
     *
     * @param $noneOption   What to return if the value is null
     * @return              The string notes
     */ 
    public function getNote($noneOption = null) {
        return self::getNoneIfEmpty($this->note, $noneOption);   
    }
     
    /** 
     * The get method for the course's exclusions.
     *
     * @param $noneOption   What to return if the value is null
     * @return              The string exclusions
     */ 
    public function getExclusions($noneOption = null) {
        return self::getNoneIfEmpty($this->exclusion, $noneOption);   
    }       
     
    /** 
     * The get method for the course's recommendation.
     *
     * @param $noneOption   What to return if the value is null
     * @return              The string recommendation
     */ 
    public function getRecommendation($noneOption = null) {
        return self::getNoneIfEmpty($this->reccomend, $noneOption);   
    }       

    /** 
     * The get method for the course's one-way exclusions.
     *
     * @param $noneOption   What to return if the value is null
     * @return              The string one-way exclusions
     */ 
    public function getOneWayExclusion($noneOption = null) {
        return self::getNoneIfEmpty($this->oneWay, $noneOption);   
    }       

    /** 
     * The get method for the course's learning hours.
     *
     * @param $noneOption   What to return if the value is null
     * @return              The string learning hours
     */ 
    public function getLearningHours() {
        return $this->learnHours;   
    }       
     
    /** 
     * The get method for the course's equivalency.
     *
     * @param $noneOption   What to return if the value is null
     * @return              The string equivalency
     */ 
    public function getEquivalency($noneOption = null) {
        return self::getNoneIfEmpty($this->equivalency, $noneOption);   
    }    
     
    /** 
     * The get method for the course's website.
     *
     * @param $noneOption   What to return if the value is null
     * @return              The string website
     */ 
    public function getWebsite($noneOption = null) {
        return self::getNoneIfEmpty($this->website, $noneOption);   
    }      
     
}