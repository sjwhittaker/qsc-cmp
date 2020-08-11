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
use Managers\CourseCalendarDatabase as CCD;

use DatabaseObjects\CourseEntry;
use DatabaseObjects\CourseLevelLearningOutcome as CLLO;
use DatabaseObjects\PlanLevelLearningOutcome as PLLO;
use DatabaseObjects\DegreeLevelExpectation as DLE;
use DatabaseObjects\InstitutionLearningOutcome as ILO;

qsc_cmp_start_page_load();

$db_curriculum = new CMD();
$db_calendar = new CCD();

$pllo_id = false;
$edit_occurred = false;

// Get the ID from the URL
$pllo_id = qsc_core_get_id_from_get();

if ($pllo_id === false) :
    qsc_cmp_start_html(array(QSC_CMP_START_HTML_TITLE => "Error Displaying PLLO"));
?>

<h1>Error Displaying Plan Level Learning Outcome</h1>
    <?php qsc_core_log_and_display_error("The PLLO ID could not be extracted as an integer from the URL.");
else:
    $pllo = $db_curriculum->getPLLOFromID($pllo_id);
    if (! $pllo) :
        qsc_cmp_start_html(array(QSC_CMP_START_HTML_TITLE => "Error Finding PLLO"));  
?>
    
<h1>Error Finding Plan Level Learning Outcome</h1>
    <?php qsc_core_log_and_display_error("A PLLO with that ID could not be retrieved from the database.");
    else :       
        qsc_cmp_start_html(array(
            QSC_CMP_START_HTML_TITLE => "View ".$pllo->getName(),
            QSC_CMP_START_HTML_SCRIPTS => array(QSC_CMP_SCRIPT_PLLO_FORMS_LINK)
            )
        );
        
        // Has an 'Add' or 'Edit' form submission taken place?
        // Check to see if a form was submitted to add or edit a PLLO
        $form_result = qsc_cmp_get_form_result();
        if (($form_result == QSC_CMP_FORM_RESULT_ADD_PLLO_SUCCESSFUL) || ($form_result ==  QSC_CMP_FORM_RESULT_EDIT_PLLO_SUCCESSFUL)) {
            qsc_core_display_success_message("The PLLO was ".(
                ($form_result == QSC_CMP_FORM_RESULT_ADD_PLLO_SUCCESSFUL) ? "added" : "edited")." successfully."
            );
        }       
                                                                                                
        // Is there a DLE? If so, get it's information
        $dle = $db_curriculum->getDLEForPLLO($pllo->getDBID());
        
        // Is there parent CLLO? If so, get it's information
        $parent_pllo = null;
        if ($pllo->hasParent()) {
            $parent_pllo = $db_curriculum->getPLLOFromID($pllo->getParentDBID());
        }
                
        // Get all of the direct CLLOs for this PLLO
        $cllo_array = $db_curriculum->getDirectCLLOsForPLLO($pllo_id);

        // Get all of the direct ILOs for this CLLO
        $ilo_array = $db_curriculum->getDirectILOsForPLLO($pllo_id);
        
        $child_pllo_array = $db_curriculum->getChildPLLOs($pllo_id);        
        
        // Get all of the plans associated with this PLLO
        $plan_array = $db_curriculum->getPlansFromPLLO($pllo_id);
        
        $dle_relationship = ($parent_pllo) ? "Grandparent" : "Parent";
        
        qsc_core_form_display_single_button_form(
            QSC_CMP_PLLO_EDIT_PAGE_LINK, 
            "Edit This PLLO", 
            array(
                QSC_CORE_FORM_HIDDEN_CONTROLS => array(
                    QSC_CMP_FORM_PLLO_ID => $pllo->getDBID()
                )
            )
        );
?>

<h1><?= $pllo->getName();?></h1>

        <?php qsc_cmp_display_property_columns(array(
            "Text" => $pllo->getText(),
            "$dle_relationship DLE" => (($dle) ? $dle->getAnchorToView(true) : QSC_CMP_TEXT_NONE_SPECIFIED),
            "Parent PLLO" => (($parent_pllo) ? $parent_pllo->getAnchorToView(true) : QSC_CMP_TEXT_NONE_SPECIFIED),
            "Notes" => $pllo->getNotes(QSC_CMP_TEXT_NONE_SPECIFIED)
            )
        ); ?>

<h2>Plans</h2>
        <?php if (empty($plan_array)) : ?>
    <p>This PLLO is not associated with any plans.</p>
        <?php
        else :
            qsc_cmp_display_plan_table($plan_array);
        endif; ?>

        <?php if (! empty($child_pllo_array)) : ?>    
<h2>Child PLLOs</h2>
        <?php qsc_cmp_display_pllo_table($child_pllo_array, $db_curriculum, false); ?>
        <?php endif; ?>

<h2>Course Level Learning Outcomes</h2>
        <?php if (empty($cllo_array)) : ?>
<p>There are no CLLOs set for this PLLO.</p>
        <?php
        else :
            qsc_cmp_display_cllo_table($cllo_array, $db_curriculum, $db_calendar, true, false, false);
        endif; ?>

<h2>Institution Learning Outcomes</h2>
        <?php if (empty($ilo_array)) : ?>
<p>There are no ILOs set for this PLLO.</p>
        <?php
        else :
            qsc_cmp_display_ilo_table($ilo_array);            
        endif; 
        
        qsc_core_form_display_single_button_form(
            QSC_CMP_ACTION_DELETE_LINK,
            "Delete This PLLO",
            array(
                QSC_CORE_FORM_FORM_ID => QSC_CMP_FORM_PLLO_DELETE,
                QSC_CORE_FORM_HIDDEN_CONTROLS => array(
                QSC_CMP_FORM_PLLO_ID => $pllo->getDBID(),
                QSC_CMP_FORM_TYPE => QSC_CMP_FORM_TYPE_DELETE_PLLO
                )
            )
        );
        ?>
    
<?php
    endif;
endif;

qsc_cmp_end_html();

qsc_cmp_end_page_load();
?>