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

$plan = null;
$page_title = "PLLOs";
$content_name = '';
$content_anchor = '';

$plan_id = qsc_core_get_id_from_get();

if ($plan_id) {
    $page_title .= " for ";
    $plan = $db_curriculum->getPlanFromID($plan_id);
    $content_name = $plan->getDescriptiveName();
    $content_anchor = $plan->getAnchorToView();
}

qsc_cmp_start_html(array(QSC_CMP_START_HTML_TITLE => $page_title.$content_name));
?>

<?php if (! $plan) : ?>

<h1>Plan PLLOs</h1>

<p>No plan has been selected.</p>


<?php else: ?>

<h1><?= $page_title; ?> <?= $content_anchor; ?></h1>


<?php 
    $pllo_array = $db_curriculum->getPLLOsForPlan($plan_id);    
    if (empty($pllo_array)) : ?>

<p>The <?= $content_name ?> does not have any plan-level learning outcomes associated with its courses</p>

    <?php else : 
        qsc_cmp_display_pllo_table($pllo_array, $db_curriculum, false); 
    endif;
endif;

qsc_cmp_end_html();

qsc_cmp_end_page_load();
?>