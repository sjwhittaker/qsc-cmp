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
 * The class Program is intended to store the information from a row in the
 * database about a program.
 */
class Program extends CalendarComponent {
    /*************************************************************************
     * Static Functions
     *************************************************************************/
    /**
     * This function uses the values in a database row to create a new 
     * Program object and set the values of the member variables.
     *
     * @param $argArray        The program row from the database
     */
    public static function buildFromDBRow($argArray) {
        $id = $argArray[CMD::TABLE_PROGRAM_ID];
        $name = $argArray[CMD::TABLE_PROGRAM_NAME];
        $type = $argArray[CMD::TABLE_PROGRAM_TYPE];
        $code = $argArray[CMD::TABLE_PROGRAM_CODE];
        $text = $argArray[CMD::TABLE_PROGRAM_TEXT];
        $notes = $argArray[CMD::TABLE_PROGRAM_NOTES];

        return new Program($id, $name, $type, $code, $text, $notes);
    }
       

    /*************************************************************************
     * Member Variables
     *************************************************************************/
    protected $type = null;
    protected $code = null;
    protected $text = null;
    protected $notes = null;
    
    protected $degree = null;
    protected $plan = null;
       

    /*************************************************************************
     * Constructor
     *************************************************************************/
    /**
     * This constructor sets all of the member variables using the arguments.
     *
     * @param $argDBID         The program's database integer ID
     * @param $argName       The program's string name
     * @param type $argType
     * @param type $argCode
     * @param type $argText
     * @param type $argNotes
     */
    public function __construct($argDBID, $argName, $argType, $argCode, $argText, $argNotes) {
        parent::__construct($argDBID, $argName);
        
        $this->type = $argType;
        $this->code = $argCode;
        $this->text = $argText;
        $this->notes = $argNotes;
    }
    

    /*************************************************************************
     * Initialize
     *************************************************************************/
    /**
     * Initializes the degree and plan collections using the database.
     * 
     * @param type $dbCurriculum
     */
    public function initialize($dbCurriculum, $argArray = array()) {
        $this->degree = $dbCurriculum->getDegreeForProgram($this->getDBID());
        $this->plan = $dbCurriculum->getPlanForProgram($this->getDBID());
        
        $planName = '';
        $planType = '';
        $planFullCode = '';
        $planIsMedial = false;
        $planHasInternship = false;
        $planInternshipCode = '';

        $degreeName = '';
        $degreeType = '';
        $degreeCode = '';
        
        $delimeter = QSC_CMP_PROGRAM_AND_PLAN_NAME_DELIMETER;

        
        if ($this->plan) {
            $planName = $this->plan->getName();
            $planType = $this->plan->getType();
            $planFullCode = $this->plan->getFullCode();
            $planIsMedial = $this->plan->isMedial();
            $planHasInternship = $this->plan->hasInternship();
            $planInternshipCode = $this->plan->getInternshipCode();
        }

        if ($this->degree) {
            $degreeName = $this->degree->getName();
            $degreeType = $this->degree->getType();
            $degreeCode = $this->degree->getCode();
        }
        
        // Use the plan and degree information to put together the name,
        // type and code information if it doesn't already exist
        if (! $this->type) {
            $this->type = "$planType ($degreeType)";
        }
        if (! $this->name) {
            $this->name = "$planName $delimeter ".$this->type." $delimeter $degreeName";
        }
        if (! $this->code) {
            $this->code = ($planIsMedial) ?
                $planFullCode :
                "$planFullCode$delimeter$degreeCode" ;
            
            if ($planHasInternship){
                $this->code .= " ($planName)<br/>";
                $this->code .= "$planInternshipCode$delimeter$degreeCode";
                $this->code .= " ($planName with Professional Internship)<br/>";
            }            
        }        
    }
    
    
    /*************************************************************************
     * Get and Set Methods
     *************************************************************************/
    /**
     * 
     * @param type $noneOption
     * @return type
     */
    public function getName($noneOption = null) {
       return qsc_core_get_none_if_empty($this->name, $noneOption);   
    }

    /**
     * 
     * @param type $noneOption
     * @return type
     */
    public function getType($noneOption = null) {
       return qsc_core_get_none_if_empty($this->type, $noneOption);   
    }

    /**
     * 
     * @param type $noneOption
     * @return type
     */
    public function getCode($noneOption = null) {
       return qsc_core_get_none_if_empty($this->code, $noneOption);   
    }
    
    /** 
     * The get method for the program's text.
     *
     * @param type $noneOption
     * @return  The string text
     */ 
    public function getText($noneOption = null) {
       return qsc_core_get_none_if_empty($this->text, $noneOption);   
    } 

    /** 
     * The get method for the program's notes.
     *
     * @param type $noneOption
     * @return  The string notes
     */ 
    public function getNotes($noneOption = null) {
       return qsc_core_get_none_if_empty($this->notes, $noneOption);   
    }    
    
    /**
     * 
     * @return type
     */
    public function getDegree() {
        return $this->degree;
    }
    
    /**
     * 
     * @return type
     */
    public function getPlan() {
        return $this->plan;
    }    
    
    
    /*************************************************************************
     * Member Functions
     *************************************************************************/    
    /**
     * Creates a link to view this program using its ID.
     *
     * @return      A string containing the link
     */
    public function getLinkToView() {
        return self::getLinkWithID(QSC_CMP_PROGRAM_VIEW_PAGE_LINK);
    }

}
