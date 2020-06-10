<?php
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

include_once('../config/config.php');

use Managers\CurriculumMappingDatabase as CMD;

use DatabaseObjects\Program;


// Get the action or type of query requested
$ajax_action = qsc_core_extract_form_value(INPUT_POST, QSC_CMP_AJAX_ACTION, FILTER_SANITIZE_STRING);
if (! $ajax_action) {
    error_log("Couldn't determine the action or type of request in getPrograms.php");
    echo "";
    exit;
}

$query_value = null;
$program_array = null;
$db_curriculum = new CMD();
$output_array = array();

if ($ajax_action == QSC_CMP_AJAX_ACTION_SEARCH_PROGRAMS) {
    // Extract the search string from the input
    $query_value = qsc_core_extract_form_value(INPUT_POST, QSC_CMP_AJAX_INPUT_SEARCH, FILTER_SANITIZE_STRING);    
    if (! $query_value) {
        echo "";
        exit;        
    }
    
    // Perform the search
    $program_array = $db_curriculum->findMatchingPrograms($query_value);
}
else if ($ajax_action == QSC_CMP_AJAX_ACTION_GET_PROGRAM_FROM_ID) {
    // Extract the program ID from the input
    $query_value = qsc_core_extract_form_value(INPUT_POST, QSC_CMP_AJAX_INPUT_ID, FILTER_SANITIZE_NUMBER_INT);
    if (! $query_value) {
        error_log("Couldn't determine the program's ID in getPrograms.php");
        echo "";
        exit;        
    }
    
    // Get the program
    $program_array = array($db_curriculum->getProgramByID($query_value));
}

// Go through all the results and create the output with just the IDs
// and names of the programs
if (empty($output_array)) {
    foreach ($program_array as $program) {
        $output_array[] = array(QSC_CMP_AJAX_OUTPUT_ID => $program->getDBID(),
            QSC_CMP_AJAX_OUTPUT_NAME => $program->getName(),
            QSC_CMP_AJAX_OUTPUT_LINK => $program->getLinkToView());
    }    
} 

echo json_encode($output_array);