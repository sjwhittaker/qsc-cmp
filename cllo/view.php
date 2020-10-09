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
use Managers\SessionManager;

use DatabaseObjects\CourseEntry;
use DatabaseObjects\CalendarCourse;
use DatabaseObjects\CourseLevelLearningOutcome as CLLO;
use DatabaseObjects\PlanLevelLearningOutcome as PLLO;
use DatabaseObjects\InstitutionLearningOutcome as ILO;
use DatabaseObjects\CLLOAndPLLO;

// Begin with the page load
qsc_cmp_start_page_load();

// Connect to the various databases
$db_curriculum = new CMD();
$db_calendar = new CCD();

$cllo_id = qsc_core_get_id_from_get();
if ($cllo_id === false) :
    qsc_cmp_start_html(array(QSC_CMP_START_HTML_TITLE => "Error Displaying CLLO"));
?>
<h1>Error Displaying Course Level Learning Outcome</h1>
    <?php qsc_core_log_and_display_error("The CLLO ID could not be determined.");
else:
    $cllo = $db_curriculum->getCLLOFromID($cllo_id);
    if (! $cllo) :
        qsc_cmp_start_html(array(QSC_CMP_START_HTML_TITLE => "Error Finding CLLO"));
    ?>
<h1>Error Finding Course Level Learning Outcome</h1>
    <?php qsc_core_log_and_display_error("A CLLO with that ID could not be retrieved from the database.");
    else :
        // Get the course associated with the CLLO
        $course = $db_curriculum->getCourseForCLLO($cllo_id);

        qsc_cmp_start_html(array(
            QSC_CMP_START_HTML_TITLE => "View ".$cllo->getName()." for ".$course->getName(),
            QSC_CMP_START_HTML_SCRIPTS => array(QSC_CMP_SCRIPT_CLLO_FORMS_LINK)));
        
        // Check to see if a form was submitted to add or edit a CLLO
        $form_result = qsc_cmp_get_form_result();
        if (($form_result == QSC_CMP_FORM_RESULT_ADD_CLLO_SUCCESSFUL) || ($form_result ==  QSC_CMP_FORM_RESULT_EDIT_CLLO_SUCCESSFUL)) {
            qsc_core_display_success_message("The CLLO was ".(
                ($form_result == QSC_CMP_FORM_RESULT_ADD_CLLO_SUCCESSFUL) ? "added" : "edited")." successfully."
            );
        }

        // Is there parent CLLO? If so, get it's information
        $parent_cllo = null;
        if ($cllo->hasParent()) {
            $parent_cllo = $db_curriculum->getCLLOFromID($cllo->getParentDBID());
        }

        // Get all of the direct PLLOs for this CLLO
        $pllo_array = $db_curriculum->getDirectPLLOsForCLLOs(array($cllo_id));

        // Get all of the direct ILOs for this CLLO
        $ilo_array = $db_curriculum->getDirectILOsForCLLOs(array($cllo_id));
                
        qsc_core_form_display_single_button_form(
            QSC_CMP_CLLO_EDIT_PAGE_LINK, 
            "Edit This CLLO", 
            array(
                QSC_CORE_FORM_HIDDEN_CONTROLS => array(
                    QSC_CMP_FORM_CLLO_ID => $cllo->getDBID()
                )
            )
        );
        ?>

<h1><?= $cllo->getName();?> for <?= $course->getAnchorToView(); ?></h1>

        <?php qsc_cmp_display_property_columns(array(
            "Course" => $course->getAnchorToView($db_calendar),
            "Text" => $cllo->getText(),
            "Type" => $cllo->getType(),
            "Parent" => (($parent_cllo) ? $parent_cllo->getAnchorToView(true) : QSC_CMP_TEXT_NONE_SPECIFIED),
            "Indicator of Achievement" => $cllo->getIOA(),
            "Notes" => $cllo->getNotes()
            )
        ); ?>

    <h2>Plan Level Learning Outcomes</h2>
        <?php if (empty($pllo_array)) : ?>
    <p>There are no PLLOs set for this CLLO.</p>
        <?php
        else :
            qsc_cmp_display_pllo_table($pllo_array, $db_curriculum, false);
        endif; ?>

    <h2>Institution Learning Outcomes</h2>
        <?php if (empty($ilo_array)) : ?>
    <p>There are no ILOs set for this CLLO.</p>
        <?php else : 
            qsc_cmp_display_ilo_table($ilo_array); 
        endif; ?>

        <?php if ($cllo->isTopLevel()) :
            $child_CLLO_array = $db_curriculum->getChildCLLOs($cllo->getDBID());
            if (! empty($child_CLLO_array)) : ?>
    <h2>Child CLLOs</h2>
                <?php qsc_cmp_display_cllo_table($child_CLLO_array);
            endif;
        endif; 
                
        qsc_core_form_display_single_button_form(
            QSC_CMP_ACTION_DELETE_LINK, 
            "Delete This CLLO", 
            array(
                QSC_CORE_FORM_FORM_ID => QSC_CMP_FORM_CLLO_DELETE,
                QSC_CORE_FORM_HIDDEN_CONTROLS => array(
                    QSC_CMP_FORM_CLLO_ID => $cllo->getDBID(),
                    QSC_CMP_FORM_TYPE => QSC_CMP_FORM_TYPE_DELETE_CLLO                    
                )
            )
        );        

    endif;
endif;

qsc_cmp_end_html();

qsc_cmp_end_page_load();
?>
