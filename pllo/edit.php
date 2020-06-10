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

qsc_cmp_start_page_load();

$db_curriculum = new CMD();

$pllo_id = qsc_core_get_id_from_post(QSC_CMP_FORM_PLLO_ID);

if ($pllo_id === false) :
    qsc_cmp_start_html(array(QSC_CMP_START_HTML_TITLE => "Error Displaying PLLO"));
    ?>

    <h1>Error Displaying Plan Level Learning Outcome</h1>
    <?php
    qsc_core_log_and_display_error("The PLLO ID could not be extracted.");
else:
    $pllo = $db_curriculum->getPLLOFromID($pllo_id);
    if (!$pllo) :
        qsc_cmp_start_html(array(QSC_CMP_START_HTML_TITLE => "Error Finding PLLO"));
        ?>

        <h1>Error Finding Plan Level Learning Outcome</h1>
        <?php
        qsc_core_log_and_display_error("A PLLO with that ID could not be retrieved from the database.");
    else :
        qsc_cmp_start_html(array(
            QSC_CMP_START_HTML_TITLE => "Edit " . $pllo->getName(),
            QSC_CMP_START_HTML_SCRIPTS => array(QSC_CMP_SCRIPT_PLLO_FORMS_LINK)));
       ?>

<h1>Edit <?= $pllo->getName(); ?></h1>

    <?php
        qsc_cmp_display_pllo_form(QSC_CMP_ACTION_EDIT_LINK, QSC_CMP_FORM_PLLO_EDIT, 
            QSC_CMP_FORM_TYPE_EDIT_PLLO, "Save Changes", $pllo);   
    
    endif;
endif;

qsc_cmp_end_html();

qsc_cmp_end_page_load();
?>
