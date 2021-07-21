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
 * The class CLLOAndCourseAndLevel represents a relationship between a CLLO 
 * and a Course at a CLLOLevel as stored in the database.
 */
class CLLOAndCourseAndLevel extends TriRelationship {
    /**************************************************************************
     * Static Functions
     **************************************************************************/
    /**
     * This function uses the values in $_POST following an 'Add' or 'Edit'
     * form submission to create a new CLLOAndCourseAndLevel object and set
     * the values of the member variables.
     *
     * @param $clloID        The ID of the CLLO
     */
    public static function buildFromCLLOPostData($clloID) {
        $clloAndCourseAndLevelArray = array();       
        
        $courseIDArray = qsc_core_extract_form_array_value(INPUT_POST, QSC_CMP_FORM_CLLO_COURSE_LIST_SELECTED, FILTER_SANITIZE_NUMBER_INT);
        $levelIDArray = qsc_core_extract_form_array_value(INPUT_POST, QSC_CMP_FORM_CLLO_LEVEL_LIST_SELECTED, FILTER_SANITIZE_NUMBER_INT);
        
        foreach ($courseIDArray as $index => $courseID) {
            $clloAndCourseAndLevelArray[] = new CLLOAndCourseAndLevel($clloID, $courseID, $levelIDArray[$index]);
        }
        
        return $clloAndCourseAndLevelArray;
    }

    /**
     * This function uses the values in a database row to create a new
     * CLLOAndCourseAndLevel object and set the values of the member variables.
     *
     * @param $argArray        The course row from the database
     * @return                 A CLLOAndCourseAndLevel with the corresponding
     *                         information
     */ 
     public static function buildFromDBRow($argArray) {
         $clloID = $argArray[CMD::TABLE_CLLO_AND_COURSE_AND_LEVEL_CLLO_ID];
         $courseID = $argArray[CMD::TABLE_CLLO_AND_COURSE_AND_LEVEL_COURSE_ID];
         $levelID = $argArray[CMD::TABLE_CLLO_AND_COURSE_AND_LEVEL_LEVEL_ID];

         return new CLLOAndCourseAndLevel($clloID, $courseID, $levelID);
     }


    /**************************************************************************
     * Constructor
     **************************************************************************/
    /**
     * This constructor sets all of the member variables using the arguments.
     *
     * @param type $argCLLODBID
     * @param type $argCourseDBID
     */
    public function __construct($argCLLODBID, $argCourseDBID, $argLevelDBID) {
        parent::__construct($argCLLODBID, $argCourseDBID, $argLevelDBID);
    }


    /**************************************************************************
     * Get and Set Methods
     **************************************************************************/
    /**
     * The get method for the CLLO's database ID.
     *
     * @return      The int ID
     */
    public function getCLLODBID() {
        return $this->firstDBID;
    }

    /**
     * The get method for the Course's database ID.
     *
     * @return      The int ID
     */
    public function getCourseDBID() {
        return $this->secondDBID;
    }
    
    /**
     * The get method for the CLLOLevel's database ID.
     *
     * @return      The int ID
     */
    public function getLevelDBID() {
        return $this->thirdDBID;
    }
    
}
