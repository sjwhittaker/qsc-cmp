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
 * The class Plan is intended to store the information from a row in the
 * database about a plan.
 */
class Plan extends CalendarComponent {
    /*************************************************************************
     * Static Functions
     *************************************************************************/
    /**
     * This function uses the values in a database row to create a new 
     * Plan object and set the values of the member variables.
     *
     * @param $argArray        The plan row from the database
     */
    public static function buildFromDBRow($argArray) {
        $id = $argArray[CMD::TABLE_PLAN_ID];
        $name = $argArray[CMD::TABLE_PLAN_NAME];
        $code = $argArray[CMD::TABLE_PLAN_CODE];
        $type = $argArray[CMD::TABLE_PLAN_TYPE];
        $internship = $argArray[CMD::TABLE_PLAN_INTERNSHIP];
        $prior_to = $argArray[CMD::TABLE_PLAN_PRIOR_TO];
        $text = $argArray[CMD::TABLE_PLAN_TEXT];
        $notes = $argArray[CMD::TABLE_PLAN_NOTES];

        return new Plan($id, $name, $code, $type, $internship, $prior_to, $text, $notes);
    }
        
    /**
     * 
     * @param type $plan
     * @param type $dbCurriculum
     * @return type
     */
    public static function getSubjectHTML($plan, $dbCurriculum) {        
        $adminDepartmentArray = array();
        $partnerDepartmentArray = array();

        $departmentArray = $dbCurriculum->getDepartmentsForPlan($plan->getDBID());
        foreach ($departmentArray as $department) {
            $role = $dbCurriculum->getRoleForDepartmentAndPlan($department->getDBID(), $plan->getDBID());

            if ($role == CMD::TABLE_DEPARTMENT_AND_PLAN_ROLE_ADMINISTRATOR) {
                $adminDepartmentArray[] = $department;
            } else {
                $partnerDepartmentArray[] = $department;
            }
        }

        $adminHTML = '';
        $admin_anchor_array = qsc_core_map_member_function($adminDepartmentArray, 'getAnchorToView');
        if (!empty($adminDepartmentArray)) {
            $adminHTML = "Administered by the ";
            $adminHTML .= qsc_core_connect_strings_for_sentence($admin_anchor_array);
        }

        $partnerHTML = '';
        $partner_anchor_array = qsc_core_map_member_function($partnerDepartmentArray, 'getAnchorToView');
        if (!empty($partnerDepartmentArray)) {
            $partnerHTML = " in cooperation with the ";
            $partnerHTML .= qsc_core_connect_strings_for_sentence($partner_anchor_array);
        }

        return "$adminHTML$partnerHTML";
    }
    
    /**
     * 
     * @param type $planArray
     * @param type $dbCurriculum
     */
    public static function initializeAndSort(&$planArray, $dbCurriculum) {
        foreach ($planArray as $plan) {
            $plan->initialize($dbCurriculum);
        }
        
        usort($planArray, 
            function($a, $b) { 
                return strcmp($a->getName(), $b->getName());            
            }
        );
    }    
    

    /*************************************************************************
     * Member Variables
     *************************************************************************/
    protected $code = null;
    protected $type = null;
    protected $internship = null;
    protected $prior_to = null;
    protected $text = null;
    protected $notes = null;
    protected $subPlanArray = array();
       

    /*************************************************************************
     * Constructor
     *************************************************************************/
    /**
     * This constructor sets all of the member variables using the arguments.
     *
     * @param $argDBID         The plan's database integer ID
     * @param $argName       The plan's string name
     * @param type $argCode
     * @param type $argType
     * @param type $argInternship
     * @param type $argPriorTo
     * @param type $argText
     * @param type $argNotes
     */
    public function __construct($argDBID, $argName, $argCode, $argType, 
        $argInternship, $argPriorTo, $argText, $argNotes) {
        parent::__construct($argDBID, $argName);
        
        $this->code = $argCode;
        $this->type = $argType;
        $this->internship = $argInternship;
        $this->prior_to = $argPriorTo;
        $this->text = $argText;
        $this->notes = $argNotes;
    }
    
    
    /*************************************************************************
     * Initialize
     *************************************************************************/
    /**
     * 
     * @param type $dbCurriculum
     */
    public function initialize($dbCurriculum, $argArray = array()) {
        // Get child plans (if any)
        $this->subPlanArray = $dbCurriculum->getSubPlans($this->getDBID());        
    }
    

    /*************************************************************************
     * Get and Set Methods
     *************************************************************************/
    /**
     * 
     * @return type
     */
    public function getCode() {
        return $this->code;
    }

    /**
     * 
     * @return type
     */
    public function getType() {
        return $this->type;
    }
    
    /**
     * 
     * @return type
     */
    public function hasInternship() {
        return $this->internship;
    }
        
    /**
     * 
     * @param type $noneOption
     * @return type
     */ 
    public function getPriorTo($noneOption = null) {
       return qsc_core_get_none_if_empty($this->prior_to, $noneOption);   
    }    
    
    /** 
     * 
     * @param type $noneOption
     * @return type
     */
    public function getText($noneOption = null) {
       return qsc_core_get_none_if_empty($this->text, $noneOption);   
    } 

    /** 
     * 
     * @param type $noneOption
     * @return type
     */
    public function getNotes($noneOption = null) {
        return qsc_core_get_none_if_empty($this->notes, $noneOption);   
    }
    
    /**
     * 
     * @return type
     */
    public function isMedial() {
        return ($this->type == CMD::TABLE_PLAN_TYPE_MEDIAL);
    }
    
    /**
     * 
     * @return type
     */
    public function getSubPlanArray() {
       return $this->subPlanArray; 
    }
    

    /*************************************************************************
     * Member Functions
     *************************************************************************/
    /**
     * 
     * @return type
     */
    public function hasSubPlans() {
        return (! empty($this->subPlanArray));
    }
    
    /**
     * 
     * @return type
     */
    public function isSubPlan() {
        return ($this->type == CMD::TABLE_PLAN_TYPE_SUB_PLAN);
    }
    
    /**
     * 
     * @param type $includeExplanation
     * @return string
     */
    public function getTypeCode($includeExplanation = false) {
        switch ($this->type) {
            case CMD::TABLE_PLAN_TYPE_MAJOR :
                return 'M';
            case CMD::TABLE_PLAN_TYPE_MINOR :
                return '';
            case CMD::TABLE_PLAN_TYPE_SPECIALIZATION :
                return 'P';
            case CMD::TABLE_PLAN_TYPE_MEDIAL :
                $code = $this->code;
                $typeCode = $includeExplanation ?
                    "$code where [‐‐‐‐] is a second subject of study" :
                    $code;
                return $typeCode;
            case CMD::TABLE_PLAN_TYPE_GENERAL :
                return 'G';
            default:
                return '';
        }
    }
    
    /**
     * 
     * @return type
     */
    public function getFullCode() {
        $code = $this->code;
        $typeCode = $this->getTypeCode();
        
        return ($this->isMedial()) ?
            $typeCode : 
            $code.QSC_CMP_PROGRAM_AND_PLAN_CODE_DELIMETER.$typeCode;
    }
    
    /**
     * 
     * @return type
     */
    public function getInternshipCode() {
        return ($this->internship) ?
            $this->code.QSC_CMP_PROGRAM_AND_PLAN_CODE_DELIMETER."I" :
            '' ;
    }   
    
    /**
     * Creates a link to view this plan using its ID.
     *
     * @return      A string containing the link
     */
    public function getLinkToView() {
        return self::getLinkWithID(QSC_CMP_PLAN_VIEW_PAGE_LINK);
    }
    
    /**
     * 
     * @return type
     */
    public function getAnchorToView() {
        return '<a href="'.$this->getLinkToView().'">'.$this->getName().' ('.$this->getCode().')'.'</a>';
    }
    
    /**
     * 
     * @param type $db_curriculum
     * @return type
     */
    public function getRequiredCourses($db_curriculum) {
        $requiredCourseArray = array();
        
        // Get the CPRs
        $cprArray = $db_curriculum->getCPRsForPlan($this->getDBID());
        if (! $cprArray) {
            return array();
        }
        
        // Go through each CPR and get the required courses
        foreach ($cprArray as $cpr) {
            $cprCourseArray = $cpr->getRequiredCourses($db_curriculum);
            $requiredCourseArray = array_merge($requiredCourseArray, $cprCourseArray);
        }
                
        // Sort the final list
        usort($requiredCourseArray, 
            function($a, $b) { 
                return strcmp($a->getName(), $b->getName());            
            }
        );        
                                
        return $requiredCourseArray;
    }    

}
