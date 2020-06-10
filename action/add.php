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

use Managers\SessionManager;
use Managers\CurriculumMappingDatabase as CMD;

use DatabaseObjects\CourseLevelLearningOutcome as CLLO;

qsc_cmp_start_page_load();

$db_curriculum = new CMD();

// Check that an 'Add' form was submitted to get to this page.
$form_type = qsc_cmp_get_form_type();
if (! $form_type) {
    qsc_core_log_and_display_error("There was an attempt to incorrectly access an internal page.");
    header("Location: ".QSC_CMP_ERROR_PAGE_LINK);
    exit();
}

// Check that JS did everything it needed to prior to submission
$js_completed = qsc_cmp_get_form_js_completed_on_submission();
if (! $js_completed) {
    qsc_core_log_and_display_error("JavaScript was not working properly upon submission of an 'Add' form. No updates have been made.");    
    header("Location: ".QSC_CMP_ERROR_PAGE_LINK);
    exit();
}

// Determine what type of 'Add' form was submitted
$redirect_link = null;
$form_result = null;
if ($form_type == QSC_CMP_FORM_TYPE_ADD_CLLO) {
    // It was an 'Add CLLO' form
    $cllo = $db_curriculum->insertCLLOFromPostData();
    $redirect_link = $cllo->getLinkToView();    
    $form_result = QSC_CMP_FORM_RESULT_ADD_CLLO_SUCCESSFUL;
}
elseif ($form_type == QSC_CMP_FORM_TYPE_ADD_PLLO) {
    // It was an 'Add CLLO' form
    $pllo = $db_curriculum->insertPLLOFromPostData();
    $redirect_link = $pllo->getLinkToView();    
    $form_result = QSC_CMP_FORM_RESULT_ADD_PLLO_SUCCESSFUL;
}

if (! $redirect_link) {
    qsc_core_log_and_display_error("An 'Add' submission was detected but could not be processed.");
    header("Location: ".QSC_CMP_ERROR_PAGE_LINK);
    exit();
}
?>

<!DOCTYPE html>
<html>

<head>
    <script src="<?= QSC_CORE_JQUERY_LINK; ?>"></script>
</head>

<body>
    <form action="<?= $redirect_link; ?>" method="POST" id="<?= $form_type; ?>">
        <input type="hidden" name="<?= QSC_CMP_FORM_RESULT; ?>" id="<?= QSC_CMP_FORM_RESULT; ?>" value="<?= $form_result; ?>"/>
        <input type="submit" value="">
    </form>

    <script>
        $(document).ready(function() {
            $('#<?= $form_type; ?>').submit();
        });
    </script>
</body>

</html>

<?php qsc_cmp_end_page_load(); ?>
