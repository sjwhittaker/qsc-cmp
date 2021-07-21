<?php
namespace DatabaseObjects;

use Managers\CurriculumMappingDatabase as CMD;


/** 
 * The class CourseAndCLLOLevel is a container for a Course object and a 
 * CLLOLevel object. 
 */
class CourseAndCLLOLevel {
    /**************************************************************************
     * Member Variables
     **************************************************************************/
    protected $course = null;
    protected $clloLevel = null;


    /**************************************************************************
     * Constructor
     **************************************************************************/
    /** 
     * This constructor sets all of the member variables using the arguments.
     * 
     * @param type $argCourse
     * @param type $argCLLOLevel
     */     
    public function __construct($argCourse, $argCLLOLevel) {
        $this->course = $argCourse;
        $this->clloLevel = $argCLLOLevel;
    }

     
    /**************************************************************************
     * Get and Set Methods
     **************************************************************************/
    /** 
     * The get method for the Course object.
     *
     * @return      The Course object
     */ 
    public function getCourse() {
        return $this->course;   
    } 

    /** 
     * The get method for the CLLOLevel object.
     *
     * @return      The CLLOLevel object
     */ 
    public function getCLLOLevel() {
        return $this->clloLevel;   
    } 

}