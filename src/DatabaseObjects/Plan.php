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
        $internship = $argArray[CMD::TABLE_PLAN_INTERNSHIP];
        $descriptive_name = $argArray[CMD::TABLE_PLAN_DESCRIPTIVE_NAME];
        $text = $argArray[CMD::TABLE_PLAN_TEXT];
        $prior_to = $argArray[CMD::TABLE_PLAN_PRIOR_TO];
        $number = $argArray[CMD::TABLE_PLAN_NUMBER];
        $notes = $argArray[CMD::TABLE_PLAN_NOTES];

        return new Plan($id, $name, $code, $internship, $descriptive_name, $text, $prior_to, $number, $notes);
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
    
    
    /*************************************************************************
     * Member Variables
     *************************************************************************/
    protected $code = null;
    protected $internship = null;
    protected $text = null;
    protected $descriptiveName = null;
    protected $prior_to = null;
    protected $number = null;
    protected $notes = null;
    protected $cprListArray = array();
    protected $tprListArray = array();
       

    /*************************************************************************
     * Constructor
     *************************************************************************/
    /**
     * This constructor sets all of the member variables using the arguments.
     *
     * @param $argDBID         The plan's database integer ID
     * @param $argName       The plan's string name
     * @param type $argCode
     * @param type $argInternship
     * @param type $argText
     * @param type $argPriorTo
     * @param type $argNumber
     * @param type $argNotes
     */
    public function __construct($argDBID, $argName, $argCode, $argInternship, 
            $argDescriptiveName = null, $argText = null, $argPriorTo = null, $argNumber = null, $argNotes = null) {
        parent::__construct($argDBID, $argName);
        
        $this->code = $argCode;
        $this->internship = $argInternship;
        $this->descriptiveName = $argDescriptiveName ? $argDescriptiveName : $argName;
        $this->text = $argText;
        $this->prior_to = $argPriorTo;
        $this->number = $argNumber;
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
        // Get all direct CPRLists and TPRLists
        $this->cprListArray = $dbCurriculum->getChildCPRListsForPlan($this->getDBID());
        $this->tprListArray = $dbCurriculum->getChildTPRListsForPlan($this->getDBID());                
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
    public function hasInternship() {
        return $this->internship;
    }
    
    /** 
     * 
     * @return type
     */
    public function getDescriptiveName() {
       return $this->descriptiveName;   
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
    public function getPriorTo($noneOption = null) {
       return qsc_core_get_none_if_empty($this->prior_to, $noneOption);   
    }    
        
    /** 
     * The get method for the plan's number.
     *
     * @return  The string 'number'
     */ 
    public function getNumber($noneOption = null) {
        return qsc_core_get_none_if_empty($this->number, $noneOption);
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
    public function getCPRListArray() {
       return $this->cprListArray; 
    }

    /**
     * 
     * @return type
     */
    public function getTPRListArray() {
       return $this->tprListArray; 
    }     
    

    /*************************************************************************
     * Member Functions
     *************************************************************************/
    /**
     * 
     * @return boolean
     */
    public function hasSubPlans() {
        // Check for a CPRList with sub-plans
        foreach ($this->cprListArray as $cprList) {
            if ($cprList->hasSubPlans()) {
                return true;
            }
        }
        
        return false;
    }    
    
    /**
     * 
     * @return type
     */
    public function getSubPlanArray() {
        // Check for a CPRList with sub-plans
        foreach ($this->cprListArray as $cprList) {
            if ($cprList->hasSubPlans()) {
                return $cprList->getSubPlanArray();
            }
        }
        
        return array();
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
        return '<a href="'.$this->getLinkToView().'">'.$this->getDescriptiveName().' ('.$this->getCode().')'.'</a>';
    }
    
    /**
     * 
     * @return array
     */
    public function getDescendantCPRs() {
        $cprArray = array();
        
        foreach ($this->cprListArray as $cprList) {
            $cprArray = array_merge($cprArray, $cprList->getAllCPRsRecursive());            
        }
        
        return $cprArray;
    }
    
    /**
     * 
     * @param type $db_curriculum
     * @return type
     */
    public function getRequiredCourses($db_curriculum) {
        $requiredCourseArray = array();
        
        // Get the CPRs
        $cprArray = $this->getDescendantCPRs();
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
    
    /**
     * 
     * @param type $cprlType
     * @return type
     */
    public function getCPRListFromType($cprlType) {
        return self::getPRListFromType($this->cprListArray, $cprlType);
    }
    
    /**
     * 
     * @param type $tprlType
     * @return type
     */
    public function getTPRListFromType($tprlType) {
        return self::getPRListFromType($this->tprListArray, $tprlType);
    }    
    
    /**
     * 
     * @param type $prlArray
     * @param type $prlType
     * @return type
     */
    protected static function getPRListFromType($prlArray, $prlType) {
        foreach ($prlArray as $prList) {
            if ($prList->getType() == $prlType) {
                return $prList;
            }
        }
        
        return null;
    }
    
    /**
     * 
     * @return type
     */
    public function getTotalUnits($cprlType = null) {
        if ($cprlType) {
            $cprList = $this->getCPRListFromType($cprlType);
            return $cprList ? $cprList->getTotalUnits() : 0;
        }

        return array_sum(
            qsc_core_map_member_function($this->cprListArray, 'getTotalUnits'));
    }

    /**
     * 
     * @return type
     */
    public function getTotalUnitsToDisplay($cprlType = null) {
        return number_format($this->getTotalUnits($cprlType), 1);
    }    

}
