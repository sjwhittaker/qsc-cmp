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

use DatabaseObjects\PlanLevelLearningOutcome as PLLO;


// Get the action or type of query requested
$ajax_action = qsc_core_extract_form_value(INPUT_POST, QSC_CMP_AJAX_ACTION, FILTER_SANITIZE_STRING);
if (! $ajax_action) {
    error_log("Couldn't determine the action or type of request in getPLLOs.php");
    echo "";
    exit;
}

$query_value = null;
$pllos_array = null;
$db_curriculum = new CMD();
$output_array = array();

if ($ajax_action == QSC_CMP_AJAX_ACTION_SEARCH_PLLOS) {
    // Extract the search string from the input
    $query_value = qsc_core_extract_form_value(INPUT_POST, QSC_CMP_AJAX_INPUT_SEARCH, FILTER_SANITIZE_STRING);
    if (! $query_value) {
        echo "";
        exit;        
    }

    // Perform the search
    $pllos_array = $db_curriculum->findMatchingPLLOs($query_value);
}
else if ($ajax_action == QSC_CMP_AJAX_ACTION_GET_PLLO_FROM_ID) {
    // Extract the PLLO ID from the input
    $query_value = qsc_core_extract_form_value(INPUT_POST, QSC_CMP_AJAX_INPUT_ID, FILTER_SANITIZE_NUMBER_INT);    
    if (! $query_value) {
        error_log("Couldn't determine the PLLO's ID in getPLLOs.php");
        echo "";
        exit;        
    }
    
    // Get the PLLO
    $pllos_array = array($db_curriculum->getPLLOFromID($query_value));
}
else if ($ajax_action == QSC_CMP_AJAX_ACTION_GET_PLLOS_FOR_PLANS) {
    // Extract the PLLO ID array from the input
    $query_id_array = qsc_core_extract_form_array_value(INPUT_POST, QSC_CMP_AJAX_INPUT_ID, FILTER_SANITIZE_NUMBER_INT);
    $query_exclude_array = qsc_core_extract_form_array_value(INPUT_POST, QSC_CMP_AJAX_INPUT_EXCLUDE, FILTER_SANITIZE_NUMBER_INT);
    
    if (! $query_id_array) {
        error_log("No plan ID array was received by getPLLOs.php");
        echo "";
        exit;        
    }
    
    // Prepare the value and get the PLLOs
    $pllos_array = $db_curriculum->getPLLOsForPlans($query_id_array, $query_exclude_array);
}
else if ($ajax_action == QSC_CMP_AJAX_ACTION_GET_PLLOS_FOR_COURSE) {
    // Extract the course ID from the input
    $query_value = qsc_core_extract_form_value(INPUT_POST, QSC_CMP_AJAX_INPUT_ID, FILTER_SANITIZE_NUMBER_INT);    
    if (! $query_value) {
        error_log("Couldn't determine the course's ID in getPLLOs.php");
        echo "";
        exit;        
    }
    
    // Prepare the value and get the PLLOs
    $pllos_array = $db_curriculum->getPLLOsForCourse($query_value);
}
else if ($ajax_action == QSC_CMP_AJAX_ACTION_GET_PLLOS_FOR_SEVERAL_COURSES) {
    // Extract the course ID from the input
    $query_id_array = qsc_core_extract_form_array_value(INPUT_POST, QSC_CMP_AJAX_INPUT_ID_ARRAY, FILTER_SANITIZE_NUMBER_INT);
    $exclude_id_array = qsc_core_extract_form_array_value(INPUT_POST, QSC_CMP_AJAX_INPUT_EXCLUDE, FILTER_SANITIZE_NUMBER_INT);
    if (! $query_id_array) {
        error_log("Couldn't determine the courses' IDs in getPLLOs.php");
        echo "";
        exit;        
    }
    
    // Prepare the value and get the PLLOs
    $pllos_array = $db_curriculum->getPLLOsForSeveralCourses($query_id_array, $exclude_id_array);
}


// Go through all the results and create the output with just the IDs
// and names of the PLLOs
foreach ($pllos_array as $pllo) {
    $output_array[] = array(QSC_CMP_AJAX_OUTPUT_ID => $pllo->getDBID(),
        QSC_CMP_AJAX_OUTPUT_NAME => $pllo->getShortSnippet(),
        QSC_CMP_AJAX_OUTPUT_LINK => $pllo->getLinkToView());    
}

echo json_encode($output_array);