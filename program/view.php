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

use DatabaseObjects\Plan;
use DatabaseObjects\Program;

qsc_cmp_start_page_load();

$db_curriculum = new CMD();

$program_id = qsc_core_get_id_from_get();

if ($program_id === false) :
    qsc_cmp_start_html(array(QSC_CMP_START_HTML_TITLE => "Error Displaying Program"));
?>

<h1>Error Displaying Program</h1>
    <?php qsc_core_log_and_display_error("The program ID could not be extracted as an integer from the URL.");
else:
    $program = $db_curriculum->getProgramFromID($program_id);
    if (! $program) :
        qsc_cmp_start_html(array(QSC_CMP_START_HTML_TITLE => "Error Finding Program"));
?>

<h1>Error Finding Program</h1>
    <?php qsc_core_log_and_display_error("A program with that ID could not be retrieved from the database.");
    else :
        qsc_cmp_start_html(array(QSC_CMP_START_HTML_TITLE => "View ".$program->getName()));
    
        $plan = $db_curriculum->getPlanForProgram($program_id);
        
        $degree = $db_curriculum->getDegreeForProgram($program_id);    
        ?>

<h1><?= $program->getName();?></h1>

        <?php qsc_cmp_display_property_columns(array(
            "Code" => $program->getCode(),
            "Subject" => Plan::getSubjectHTML($plan, $db_curriculum),
            "Degree" => ($degree) ? $degree->getAnchorToView() : null,
            "Program" => $program->getText()
            )
        ); ?>

        <?php if (! $plan) : ?>
<h2>Plan</h2>
<p>There is no plan associated with this program.</p>
        <?php else : ?>
<h2>Plan: <?= $plan->getAnchorToView(); ?></h2>
<p><?= $plan->getText(); ?></p>
            <?php qsc_cmp_display_plan_requirements($plan, $db_curriculum); 
        endif;
    endif;
endif;

qsc_cmp_end_html();

qsc_cmp_end_page_load();
?>
