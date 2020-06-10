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
use DatabaseObjects\DegreeLevelExpectation as DLE;
use DatabaseObjects\InstitutionLearningOutcome as ILO;


qsc_cmp_start_page_load();

$db_curriculum = new CMD();

$dle_id = qsc_core_get_id_from_get();

if ($dle_id === false) :
    qsc_cmp_start_html(array(QSC_CMP_START_HTML_TITLE => "Error Displaying DLE"));
?>

<h1>Error Displaying Degree Level Expectation</h1>
    <?php qsc_core_log_and_display_error("The DLE ID could not be extracted as an integer from the URL.");
else:
    $dle = $db_curriculum->getDLEFromID($dle_id);
    if (! $dle) :
        qsc_cmp_start_html(array(QSC_CMP_START_HTML_TITLE => "Error Finding DLE"));
?>

<h1>Error Finding Degree Level Expectation</h1>
    <?php qsc_core_log_and_display_error("A DLE with that ID could not be retrieved from the database.");
    else :
        qsc_cmp_start_html(array(QSC_CMP_START_HTML_TITLE => "View ".$dle->getName()));

        // Get all of the direct PLLOs for this DLE
        $pllo_array = $db_curriculum->getDirectPLLOsForDLE($dle_id);
?>

<h1><?= $dle->getName();?></h1>

        <?php qsc_cmp_display_property_columns(array(
            "Text" => $dle->getText(),
            "Notes" => $dle->getNotes(QSC_CMP_TEXT_NONE_SPECIFIED)
            )
        ); ?>

<h2>Plan Level Learning Outcomes</h2>
        <?php if (empty($pllo_array)) : ?>
<p>There are no PLLOs set for this DLE.</p>
    <?php
        else :
            qsc_cmp_display_pllo_table($pllo_array, $db_curriculum);
        endif;
    endif;
endif;

qsc_cmp_end_html();

qsc_cmp_end_page_load();
?>
