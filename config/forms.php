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

use Managers\CurriculumMappingDatabase as CMD;

use DatabaseObjects\CourseEntry;
use DatabaseObjects\CourseLevelLearningOutcome as CLLO;
use DatabaseObjects\PlanLevelLearningOutcome as PLLO;
use DatabaseObjects\InstitutionLearningOutcome as ILO;


/**
 * 
 * @param type $form_action
 * @param type $form_id
 * @param type $form_type
 * @param type $submit_button_text
 * @param type $pllo
 */
function qsc_cmp_display_pllo_form($form_action, $form_id, $form_type, $submit_button_text, $pllo = null) {
    $db_curriculum = new CMD();

    $pllo_id = null;
    $pllo_number = null;
    $pllo_prefix = null;
    $pllo_text = null;
    $pllo_notes = null;
    $parent_dle_id = null;
    $parent_pllo_id = null;

    $pllo_array = $db_curriculum->getAllPLLOs();
    $dle_array = $db_curriculum->getAllDLEs();
    $ilo_possible_array = array();
    $ilo_chosen_array = array();
    $hidden_controls = array();

    if ($pllo) {
        // Get the basics from the PLLO
        $pllo_id = $pllo->getDBID();
        $pllo_number = $pllo->getNumber();
        $pllo_prefix = $pllo->getPrefix();
        $pllo_text = $pllo->getText();
        $pllo_notes = $pllo->getNotes();

        $hidden_controls[QSC_CMP_FORM_PLLO_ID] = $pllo_id;

        // Is there parent PLLO? If so, get it
        if ($pllo->hasParent()) {
            $parent_pllo = $db_curriculum->getCLLOFromID($pllo->getParentDBID());
            $parent_pllo_id = $parent_pllo->getDBID();
        } else {
            // Is there a parent DLE? If so, get it's information
            $parent_dle = $db_curriculum->getDLEForPLLO($pllo_id);
            $parent_dle_id = ($parent_dle) ? $parent_dle->getDBID() : null;
        }

        // Get all of the direct ILOs for this PLLO
        $ilo_chosen_array = $db_curriculum->getDirectILOsForPLLO($pllo_id);
        $ilo_possible_array = $db_curriculum->getAllILOs(
                qsc_core_get_db_id_array($ilo_chosen_array));
    } 
    else {
        $ilo_possible_array = $db_curriculum->getAllILOs();
    }
    ?>
<form action="<?= $form_action; ?>" method="POST" id="<?= $form_id; ?>">
    <div class="form-section">
        <?php qsc_core_form_display_input_text(
            "Number",
            QSC_CMP_FORM_PLLO_NUMBER, 
            array(
                QSC_CORE_FORM_INPUT_HELP_ID => QSC_CMP_FORM_PLLO_NUMBER_HELP,
                QSC_CORE_FORM_INPUT_HELP_TEXT => "enter a value (numeric or not) with a maximum length of ".QSC_CMP_FORM_PLLO_NUMBER_MAX_LENGTH." characters.",
                QSC_CORE_FORM_INPUT_MAX_LENGTH => QSC_CMP_FORM_PLLO_NUMBER_MAX_LENGTH,
                QSC_CORE_FORM_INPUT_VALUE => $pllo_number,
                QSC_CORE_FORM_REQUIRED => true
            )
        );
        ?>               
    </div>
    <div class="form-section">
        <?php qsc_core_form_display_input_and_select_group(
            "Plan",
            QSC_CMP_FORM_PLLO_PLAN_INPUT, 
            QSC_CMP_FORM_PLLO_PLAN_SELECT, 
            array(
                QSC_CORE_FORM_INPUT_HELP_ID => QSC_CMP_FORM_PLLO_PLAN_INPUT_HELP,
                QSC_CORE_FORM_INPUT_HELP_TEXT => "Type the plan code <strong>or</strong> name to filter the options in the list.",
                QSC_CORE_FORM_SELECT_HELP_ID => QSC_CMP_FORM_PLLO_PLAN_SELECT_HELP,
                QSC_CORE_FORM_SELECT_HELP_TEXT => "Select the matching plan in this list; its associated PLLOs will be loaded below.",
                QSC_CORE_FORM_SIDE_BY_SIDE => true,
                QSC_CORE_FORM_REQUIRED => true
            )
        );
        ?>                 
    </div>
    <div class="form-section">                
        <?php qsc_core_form_display_label(QSC_CMP_FORM_PLLO_PARENT_DLE_SELECT, "Parent DLE", true); ?>    
        <?php qsc_core_form_display_label(QSC_CMP_FORM_PLLO_PARENT_PLLO_SELECT_HELP, " <em>or</em> PLLO"); ?>        
        <div class="form-row">                    
            <div class="col-lg-6">
                <?php qsc_core_form_display_help_text(QSC_CMP_FORM_PLLO_PARENT_DLE_SELECT_HELP, "Select the parent DLE from this list (if any)."); ?>               
                <select class="form-control" name="<?= QSC_CMP_FORM_PLLO_PARENT_DLE_SELECT; ?>" id="<?= QSC_CMP_FORM_PLLO_PARENT_DLE_SELECT; ?>" aria-describedby="<?= QSC_CMP_FORM_PLLO_PARENT_PLLO_OR_DLE_HELP; ?>" size="6">
                <?php foreach ($dle_array as $dle) : ?>
                    <option value="<?= $dle->getDBID(); ?>"<?= ($dle->getDBID() == $parent_dle_id) ? " selected" : ""; ?>><?= $dle->getShortSnippet(); ?></option>
                <?php endforeach; ?>
                </select>
                <input type="button" id="<?= QSC_CMP_FORM_PLLO_PARENT_DLE_UNSELECT; ?>" name="<?= QSC_CMP_FORM_PLLO_PARENT_DLE_UNSELECT; ?>" value="Unselect"/>
            </div>
            <div class="col-lg-6">
                <?php qsc_core_form_display_help_text(QSC_CMP_FORM_PLLO_PARENT_PLLO_SELECT_HELP, "Select the parent PLLO from this list (if any). You <strong>must</strong> select a plan first to populate this list."); ?>
                <select class="custom-select" name="<?= QSC_CMP_FORM_PLLO_PARENT_PLLO_SELECT; ?>" id="<?= QSC_CMP_FORM_PLLO_PARENT_PLLO_SELECT; ?>" aria-describedby="<?= QSC_CMP_FORM_PLLO_PARENT_PLLO_OR_DLE_HELP; ?>" size="6">
                </select>
                <input type="button" id="<?= QSC_CMP_FORM_PLLO_PARENT_PLLO_UNSELECT; ?>" name="<?= QSC_CMP_FORM_PLLO_PARENT_PLLO_UNSELECT; ?>" value="Unselect"/>
            </div>
            <div class="col-12">
                <?php qsc_core_form_display_help_text(QSC_CMP_FORM_PLLO_PARENT_PLLO_OR_DLE_HELP, "a PLLO must be associated with a single parent DLE <strong>or</strong> a parent PLLO, <strong>not both</strong>.", true); ?>               
            </div>
        </div>        
    </div>
    <div class="form-section">
        <?php qsc_core_form_display_textarea(
            "Text",
            QSC_CMP_FORM_PLLO_TEXT, 
            array(
                QSC_CORE_FORM_TEXTAREA_HELP_ID => QSC_CMP_FORM_PLLO_TEXT_HELP,
                QSC_CORE_FORM_TEXTAREA_HELP_TEXT => "enter the PLLO's description with a maxiumum length of ".QSC_CMP_FORM_PLLO_TEXT_MAX_LENGTH." characters.",
                QSC_CORE_FORM_TEXTAREA_ROWS => 5,
                QSC_CORE_FORM_TEXTAREA_MAX_LENGTH => QSC_CMP_FORM_PLLO_TEXT_MAX_LENGTH,
                QSC_CORE_FORM_TEXTAREA_VALUE => $pllo_text,
                QSC_CORE_FORM_REQUIRED => true
            )
        );
        ?>               
    </div>
    <div class="form-section">
        <?php qsc_core_form_display_input_text(
            "Prefix",
            QSC_CMP_FORM_PLLO_PREFIX, 
            array(
                QSC_CORE_FORM_INPUT_HELP_ID => QSC_CMP_FORM_PLLO_PREFIX_HELP,
                QSC_CORE_FORM_INPUT_HELP_TEXT => "Enter a custom display prefix (<em>e.g.</em>, COMP) with a maximum length of ".QSC_CMP_FORM_PLLO_PREFIX_MAX_LENGTH." characters. If none is specified, the prefix is derived from the plan code(s).",
                QSC_CORE_FORM_INPUT_MAX_LENGTH => QSC_CMP_FORM_PLLO_PREFIX_MAX_LENGTH,
                QSC_CORE_FORM_INPUT_VALUE => $pllo_prefix
            )
        );
        ?>               
    </div>                   
    <div class="form-section">
        <?php qsc_core_display_select_transfer_group(
            "Supports ILOs",
            QSC_CMP_FORM_PLLO_ILO_LIST_POSSIBLE,
            QSC_CMP_FORM_PLLO_ILO_LIST_SUPPORTED,
            QSC_CMP_FORM_PLLO_ILO_ADD,
            QSC_CMP_FORM_PLLO_ILO_REMOVE,
            array(
                QSC_CORE_FORM_TRANSFER_POSSIBLE_HELP_ID => QSC_CMP_FORM_PLLO_ILO_LIST_POSSIBLE_HELP,
                QSC_CORE_FORM_TRANSFER_POSSIBLE_HELP_TEXT => "This list contains ILOs that are <strong>not</strong> supported; click the buttons to transfer them to the supported list.",
                QSC_CORE_FORM_TRANSFER_CHOSEN_HELP_ID => QSC_CMP_FORM_PLLO_ILO_LIST_SUPPORTED_HELP,
                QSC_CORE_FORM_TRANSFER_CHOSEN_HELP_TEXT => "This list contains ILOs that <strong>are</strong> supported.",
                QSC_CORE_FORM_TRANSFER_POSSIBLE_OPTIONS => qsc_cmp_extract_form_option_data($ilo_possible_array),
                QSC_CORE_FORM_TRANSFER_CHOSEN_OPTIONS => qsc_cmp_extract_form_option_data($ilo_chosen_array)
            )
        );
        ?>             
    </div>
    <div class="form-section">
        <?php qsc_core_form_display_textarea(
            "Notes",
            QSC_CMP_FORM_PLLO_NOTES, 
            array(
                QSC_CORE_FORM_TEXTAREA_HELP_ID => QSC_CMP_FORM_PLLO_NOTES_HELP,
                QSC_CORE_FORM_TEXTAREA_HELP_TEXT => "Enter any related notes with a maxiumum length of ".QSC_CMP_FORM_PLLO_TEXT_MAX_LENGTH." characters.",
                QSC_CORE_FORM_TEXTAREA_ROWS => 5,
                QSC_CORE_FORM_TEXTAREA_MAX_LENGTH => QSC_CMP_FORM_PLLO_NOTES_MAX_LENGTH,
                QSC_CORE_FORM_TEXTAREA_VALUE => $pllo_notes
            )
        );
        ?>               
    </div>
    <div class="form-section">
        <?php qsc_cmp_display_submit_group($form_type, $submit_button_text, $hidden_controls); ?>
    </div>
</form>
<?php
}


/**
 * 
 * @param type $form_action
 * @param type $form_id
 * @param type $form_type
 * @param type $submit_button_text
 * @param type $cllo
 */
function qsc_cmp_display_cllo_form($form_action, $form_id, $form_type, $submit_button_text, $cllo = null) {    
    $db_curriculum = new CMD();
    
    $cllo_id = null;
    $cllo_number = null;
    $cllo_text = null;
    $cllo_type = null;
    $cllo_ioa = null;
    $cllo_notes = null;    
    $course_id = null;
    $course_name = null;
    $course_CLLO_array = array();
    $parent_cllo_id = null;
    $pllo_possible_array = array();
    $pllo_chosen_array = array();
    $ilo_possible_array = array();
    $ilo_chosen_array = array();    
    $hidden_controls = array();
    
    if ($cllo) {
        // Get the basics from the CLLO
        $cllo_id = $cllo->getDBID();
        $cllo_number = $cllo->getNumber();
        $cllo_text = $cllo->getText();
        $cllo_type = $cllo->getType();
        $cllo_ioa = $cllo->getIOA();
        $cllo_notes = $cllo->getNotes();
        
        $hidden_controls[QSC_CMP_FORM_CLLO_ID] = $cllo_id;
        
        // Get the course associated with the CLLO
        $course = $db_curriculum->getCourseForCLLO($cllo_id);
        $course_id = $course->getDBID();
        $course_name = $course->getName();

        // Get the CLLOs associated with the course
        $course_CLLO_array = $db_curriculum->getCLLOsForCourse($course->getDBID());

        // Is there parent CLLO? If so, get it
        if ($cllo->hasParent()) {
            $parent_cllo = $db_curriculum->getCLLOFromID($cllo->getParentDBID());
            $parent_cllo_id = $parent_cllo->getDBID();
        }

        // Get all of the direct PLLOs and ILOs for this CLLO
        $pllo_chosen_array = $db_curriculum->getDirectPLLOsForCLLOs(array($cllo_id));
        $pllo_possible_array = $db_curriculum->getAllPLLOs(
            qsc_core_get_db_id_array($pllo_chosen_array));
                
        $ilo_chosen_array = $db_curriculum->getDirectILOsForCLLOs(array($cllo_id));
        $ilo_possible_array = $db_curriculum->getAllILOs(
            qsc_core_get_db_id_array($ilo_chosen_array));        
    }
    else {
        $pllo_possible_array = $db_curriculum->getAllPLLOs();                
        $ilo_possible_array = $db_curriculum->getAllILOs();                
    }
    ?>
<form action="<?= $form_action; ?>" method="POST" id="<?= $form_id; ?>">
    <div class="form-section">
        <?php qsc_core_form_display_input_and_select_group(
            "Course",
            QSC_CMP_FORM_CLLO_COURSE_INPUT, 
            QSC_CMP_FORM_CLLO_COURSE_SELECT, 
            array(
                QSC_CORE_FORM_INPUT_HELP_ID => QSC_CMP_FORM_CLLO_COURSE_INPUT_HELP,
                QSC_CORE_FORM_INPUT_HELP_TEXT => "Type the course code <strong>or</strong> number here to filter the options in the list.",
                QSC_CORE_FORM_SELECT_HELP_ID => QSC_CMP_FORM_CLLO_COURSE_SELECT_HELP,
                QSC_CORE_FORM_SELECT_HELP_TEXT => "select the matching course in this list.",
                QSC_CORE_FORM_SELECT_SELECTED_VALUE => $course_id,
                QSC_CORE_FORM_SELECT_SELECTED_TEXT => $course_name,
                QSC_CORE_FORM_SIDE_BY_SIDE => true,
                QSC_CORE_FORM_REQUIRED => true
            )
        );
        ?>               
    </div>
    <div class="form-section">
        <?php qsc_core_form_display_input_text(
            "Number",
            QSC_CMP_FORM_CLLO_NUMBER, 
            array(
                QSC_CORE_FORM_INPUT_HELP_ID => QSC_CMP_FORM_CLLO_NUMBER_HELP,
                QSC_CORE_FORM_INPUT_HELP_TEXT => "enter a value (numeric or not) with a maximum length of ".QSC_CMP_FORM_CLLO_NUMBER_MAX_LENGTH." characters.",
                QSC_CORE_FORM_INPUT_MAX_LENGTH => QSC_CMP_FORM_CLLO_NUMBER_MAX_LENGTH,
                QSC_CORE_FORM_INPUT_VALUE => $cllo_number,
                QSC_CORE_FORM_REQUIRED => true
            )
        );
        ?>               
    </div>
    <div class="form-section">
        <?php qsc_core_form_display_select(
            "Parent CLLO",
            QSC_CMP_FORM_CLLO_PARENT_SELECT, 
            array(
                QSC_CORE_FORM_SELECT_HELP_ID => QSC_CMP_FORM_CLLO_PARENT_SELECT_HELP,
                QSC_CORE_FORM_SELECT_HELP_TEXT => "Select the parent CLLO from this list (if any); it is automatically updated with the CLLOs from the selected course.",
                QSC_CORE_FORM_SELECT_UNSELECT_ID => QSC_CMP_FORM_CLLO_PARENT_UNSELECT,
                QSC_CORE_FORM_SELECT_OPTIONS => qsc_cmp_extract_form_option_data($course_CLLO_array),
                QSC_CORE_FORM_SELECT_SELECTED_VALUE => $parent_cllo_id,
                QSC_CORE_FORM_SELECT_SIZE => 5
            )
        );
        ?>               
    </div>
    <div class="form-section">
        <?php qsc_core_form_display_textarea(
            "Text",
            QSC_CMP_FORM_CLLO_TEXT, 
            array(
                QSC_CORE_FORM_TEXTAREA_HELP_ID => QSC_CMP_FORM_CLLO_TEXT_HELP,
                QSC_CORE_FORM_TEXTAREA_HELP_TEXT => "enter the CLLO's description with a maxiumum length of ".QSC_CMP_FORM_CLLO_TEXT_MAX_LENGTH." characters.",
                QSC_CORE_FORM_TEXTAREA_ROWS => 5,
                QSC_CORE_FORM_TEXTAREA_MAX_LENGTH => QSC_CMP_FORM_CLLO_TEXT_MAX_LENGTH,
                QSC_CORE_FORM_TEXTAREA_VALUE => $cllo_text,
                QSC_CORE_FORM_REQUIRED => true
            )
        );
        ?>               
    </div>
    <div class="form-section">
        <?php qsc_core_form_display_select(
            "Type",
            QSC_CMP_FORM_CLLO_TYPE, 
            array(
                QSC_CORE_FORM_SELECT_HELP_ID => QSC_CMP_FORM_CLLO_TYPE_HELP,
                QSC_CORE_FORM_SELECT_HELP_TEXT => "Select the type of CLLO from the list (if any).",
                QSC_CORE_FORM_SELECT_SELECTED_VALUE => $cllo_type,
                QSC_CORE_FORM_SELECT_OPTIONS => array(
                    QSC_CMP_FORM_CLLO_TYPE_OPTION_NONE => QSC_CMP_FORM_CLLO_TYPE_OPTION_NONE,
                    QSC_CMP_FORM_CLLO_TYPE_OPTION_CORE => QSC_CMP_FORM_CLLO_TYPE_OPTION_CORE,
                    QSC_CMP_FORM_CLLO_TYPE_OPTION_DETAIL => QSC_CMP_FORM_CLLO_TYPE_OPTION_DETAIL
                )
            )
        );
        ?>             
    </div>
    <div class="form-section">
        <?php qsc_core_display_select_transfer_group(
            "Supports PLLOs",
            QSC_CMP_FORM_CLLO_PLLO_LIST_POSSIBLE,
            QSC_CMP_FORM_CLLO_PLLO_LIST_SUPPORTED,
            QSC_CMP_FORM_CLLO_PLLO_ADD,
            QSC_CMP_FORM_CLLO_PLLO_REMOVE,
            array(
                QSC_CORE_FORM_TRANSFER_POSSIBLE_HELP_ID => QSC_CMP_FORM_CLLO_PLLO_LIST_POSSIBLE_HELP,
                QSC_CORE_FORM_TRANSFER_POSSIBLE_HELP_TEXT => "This list contains PLLOs that are <strong>not</strong> supported; click the buttons to transfer them to the supported list.",
                QSC_CORE_FORM_TRANSFER_CHOSEN_HELP_ID => QSC_CMP_FORM_CLLO_PLLO_LIST_SUPPORTED_HELP,
                QSC_CORE_FORM_TRANSFER_CHOSEN_HELP_TEXT => "This list contains PLLOs that <strong>are</strong> supported.",
                QSC_CORE_FORM_TRANSFER_POSSIBLE_OPTIONS => qsc_cmp_extract_form_option_data($pllo_possible_array),
                QSC_CORE_FORM_TRANSFER_CHOSEN_OPTIONS => qsc_cmp_extract_form_option_data($pllo_chosen_array)
            )
        );
        ?>             
    </div>        
    <div class="form-section">
        <?php qsc_core_display_select_transfer_group(
            "Supports ILOs",
            QSC_CMP_FORM_CLLO_ILO_LIST_POSSIBLE,
            QSC_CMP_FORM_CLLO_ILO_LIST_SUPPORTED,
            QSC_CMP_FORM_CLLO_ILO_ADD,
            QSC_CMP_FORM_CLLO_ILO_REMOVE,
            array(
                QSC_CORE_FORM_TRANSFER_POSSIBLE_HELP_ID => QSC_CMP_FORM_CLLO_ILO_LIST_POSSIBLE_HELP,
                QSC_CORE_FORM_TRANSFER_POSSIBLE_HELP_TEXT => "This list contains ILOs that are <strong>not</strong> supported; click the buttons to transfer them to the supported list.",
                QSC_CORE_FORM_TRANSFER_CHOSEN_HELP_ID => QSC_CMP_FORM_CLLO_ILO_LIST_SUPPORTED_HELP,
                QSC_CORE_FORM_TRANSFER_CHOSEN_HELP_TEXT => "This list contains ILOs that <strong>are</strong> supported.",
                QSC_CORE_FORM_TRANSFER_POSSIBLE_OPTIONS => qsc_cmp_extract_form_option_data($ilo_possible_array),
                QSC_CORE_FORM_TRANSFER_CHOSEN_OPTIONS => qsc_cmp_extract_form_option_data($ilo_chosen_array)
            )
        );
        ?>             
    </div>   
    <div class="form-section">
        <?php qsc_core_form_display_input_text(
            "Indicator of Achievement",
            QSC_CMP_FORM_CLLO_IOA, 
            array(
                QSC_CORE_FORM_INPUT_HELP_ID => QSC_CMP_FORM_CLLO_IOA_HELP,
                QSC_CORE_FORM_INPUT_HELP_TEXT => "Enter an indicator of achievement/completion (if any) with a maxiumum length of ".QSC_CMP_FORM_CLLO_IOA_MAX_LENGTH." characters.",
                QSC_CORE_FORM_INPUT_MAX_LENGTH => QSC_CMP_FORM_CLLO_IOA_MAX_LENGTH,
                QSC_CORE_FORM_INPUT_VALUE => $cllo_ioa
            )
        );
        ?>               
    </div>
    <div class="form-section">
        <?php qsc_core_form_display_textarea(
            "Notes",
            QSC_CMP_FORM_CLLO_NOTES, 
            array(
                QSC_CORE_FORM_TEXTAREA_HELP_ID => QSC_CMP_FORM_CLLO_NOTES_HELP,
                QSC_CORE_FORM_TEXTAREA_HELP_TEXT => "Enter any related notes with a maxiumum length of ".QSC_CMP_FORM_CLLO_TEXT_MAX_LENGTH." characters.",
                QSC_CORE_FORM_TEXTAREA_ROWS => 5,
                QSC_CORE_FORM_TEXTAREA_MAX_LENGTH => QSC_CMP_FORM_CLLO_NOTES_MAX_LENGTH,
                QSC_CORE_FORM_TEXTAREA_VALUE => $cllo_notes
            )
        );
        ?>               
    </div>    
    <div class="form-section">
        <?php qsc_cmp_display_submit_group($form_type, $submit_button_text, $hidden_controls); ?>
    </div>
</form>
<?php
}
