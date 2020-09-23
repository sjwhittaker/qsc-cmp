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

include_once('../../../config/config.php');

use Managers\CurriculumMappingDatabase as CMD;

use DatabaseObjects\TPR;

qsc_cmp_start_page_load();

$db_curriculum = new CMD();

$tpr_id = qsc_core_get_id_from_get();

if ($tpr_id === false) :
    qsc_cmp_start_html(array(QSC_CMP_START_HTML_TITLE => "Error Displaying TPR"));
?>

<h1>Error Displaying TPR</h1>
    <?php qsc_core_log_and_display_error("The text plan requirement ID could not be extracted as an integer from the URL.");
else:
    $tpr = $db_curriculum->getTPRFromID($tpr_id);
    if (! $tpr) :
        qsc_cmp_start_html(array(QSC_CMP_START_HTML_TITLE => "Error Finding TPR"));
?>

<h1>Error Finding TPR</h1>
    <?php qsc_core_log_and_display_error("A text plan requirement with that ID could not be retrieved from the database.");
    else :
        $plan = $db_curriculum->getPlanForTPR($tpr_id);
        $planName = ($plan) ? ' for '.$plan->getName() : '';
        $planAnchor = ($plan) ? ' for '.$plan->getAnchorToView() : '';
        
        $tpr_type = $db_curriculum->getTypeForTPR($tpr_id);
        
        qsc_cmp_start_html(array(QSC_CMP_START_HTML_TITLE => "View $tpr_type Requirement ".$tpr->getNumber().$planName));
    
        ?>

<h1><?= $tpr_type ?> Requirement <?= $tpr->getNumber();?><?= $planAnchor; ?></h1>

        <?php qsc_cmp_display_property_columns(array(
            "Text" => $tpr->getText(QSC_CMP_TEXT_NONE_SPECIFIED),
            "Notes" => $tpr->getNotes(QSC_CMP_TEXT_NONE_SPECIFIED)            
            )
        ); ?>

    <?php endif;
endif;

qsc_cmp_end_html();

qsc_cmp_end_page_load();
?>
