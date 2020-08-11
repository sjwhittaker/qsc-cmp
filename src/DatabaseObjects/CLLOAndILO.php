<?php
namespace DatabaseObjects;

use Managers\CurriculumMappingDatabase as CMD;


/** 
 * The class CLLOAndILO represents a relationship between a CLLO and ILO as 
 * stored in the database.
 */
class CLLOAndILO extends BiRelationship {
    /**************************************************************************
     * Static Functions
     **************************************************************************/
    /** 
     * This function uses the values in $_POST following an 'Add' or 'Edit'
     * form submission to create a new CLLOAndILO object and set the
     * values of the member variables.
     *
     * @param $clloID        The ID of the CLLO
     */ 
     public static function buildFromCLLOPostData($clloID) {
         $clloAndPLLOArray = array();       

         $iloIDArray = qsc_core_extract_form_value(INPUT_POST, QSC_CMP_FORM_CLLO_ILO_LIST_SUPPORTED, FILTER_SANITIZE_NUMBER_INT);
         if (! $iloIDArray) {
            return null;    
         }
         
         foreach ($iloIDArray as $iloID) {
              $clloAndPLLOArray[] = new CLLOAndILO($clloID, $iloID); 
         }
         
         return $clloAndPLLOArray;
     }           
     
    /** 
     * This function uses the values in a database row to create a new 
     * CLLOAndILO object and set the values of the member variables.
     *
     * @param $argArray        The row from the database
     */ 
     public static function buildFromDBRow($argArray) {
         $cllo_id = $argArray[CMD::TABLE_CLLO_AND_ILO_CLLO_ID];
         $ilo_id = $argArray[CMD::TABLE_CLLO_AND_ILO_ILO_ID];         
         
         return new CLLOAndILO($cllo_id, $ilo_id);
     }
          
 
    /**************************************************************************
     * Constructor
     **************************************************************************/
    /** 
     * This constructor sets all of the member variables using the arguments.
     * 
     * @param type $argCLLODBID
     * @param type $argILODBID
     */     
    public function __construct($argCLLODBID, $argILODBID) {
        parent::__construct($argCLLODBID, $argILODBID);
    }

     
    /**************************************************************************
     * Get and Set Methods
     **************************************************************************/
    /** 
     * The get method for the CLLO's database ID.
     *
     * @return      The string ID
     */ 
    public function getCLLODBID() {
        return $this->firstDBID;   
    } 

    /** 
     * The get method for the ILO's database ID.
     *
     * @return      The string ID
     */ 
    public function getICMDBID() {
        return $this->secondDBID;   
    } 

}