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

use DatabaseObjects\CourseEntry;
use DatabaseObjects\CourseLevelLearningOutcome as CLLO;


// Get the action or type of query requested
$ajax_action = qsc_core_extract_form_value(INPUT_POST, QSC_CMP_AJAX_ACTION, FILTER_SANITIZE_STRING);
if (! $ajax_action) {
    error_log("Couldn't determine the action or type of request in getCLLOs.php");
    echo "";
    exit;
}

$query_value = null;
$cllos_array = null;
$db_curriculum = new CMD();
$output_array = array();

if ($ajax_action == QSC_CMP_AJAX_ACTION_SEARCH_CLLOS) {
    // Extract the search string from the input
    $query_value = qsc_core_extract_form_value(INPUT_POST, QSC_CMP_AJAX_INPUT_SEARCH, FILTER_SANITIZE_STRING);    
    if (! $query_value) {
        echo "";
        exit;        
    }
    
    // Perform the search
    $cllos_array = $db_curriculum->findMatchingCLLOs($query_value);
    
    foreach ($cllos_array as $cllo) {
        $course = $db_curriculum->getCourseForCLLO($cllo->getDBID());
        
        $output_array[] = array(QSC_CMP_AJAX_OUTPUT_ID => $cllo->getDBID(),
            QSC_CMP_AJAX_OUTPUT_NAME => $cllo->getName()." for ".$course->getName().": ".qsc_core_get_snippet($cllo->getText(), QSC_CORE_STRING_SNIPPET_LENGTH_SHORT),
            QSC_CMP_AJAX_OUTPUT_LINK => $cllo->getLinkToView());
    }    
}
else if ($ajax_action == QSC_CMP_AJAX_ACTION_GET_CLLO_FROM_ID) {
    // Extract the CLLO ID from the input
    $query_value = qsc_core_extract_form_value(INPUT_POST, QSC_CMP_AJAX_INPUT_ID, FILTER_SANITIZE_NUMBER_INT);
    if (! $query_value) {
        error_log("Couldn't determine the CSLO's ID in getCLLOs.php");
        echo "";
        exit;        
    }
    
    // Get the CLLO
    $cllos_array = array($db_curriculum->getCLLOByID($query_value));
}
else if ($ajax_action == QSC_CMP_AJAX_ACTION_GET_CLLOS_FOR_COURSE) {
    // Extract the course ID from the input
    $query_value = qsc_core_extract_form_value(INPUT_POST, QSC_CMP_AJAX_INPUT_ID, FILTER_SANITIZE_NUMBER_INT);
    if (! $query_value) {
        error_log("No course ID received by getCLLOs.php");
        echo "";
        exit;        
    }
    
    // Prepare the value and get the CLLO
    $cllos_array = $db_curriculum->getCLLOsForCourse($query_value);
}

// Go through all the results and create the output with just the IDs
// and names of the CLLOs
if (empty($output_array)) {
    foreach ($cllos_array as $cllo) {
        $course = $db_curriculum->getCourseForCLLO($cllo->getDBID());
        
        $output_array[] = array(QSC_CMP_AJAX_OUTPUT_ID => $cllo->getDBID(),
            QSC_CMP_AJAX_OUTPUT_NAME => $cllo->getShortSnippet(),
            QSC_CMP_AJAX_OUTPUT_LINK => $cllo->getLinkToView());
    }
} 

echo json_encode($output_array);