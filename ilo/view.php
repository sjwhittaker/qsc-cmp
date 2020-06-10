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
use DatabaseObjects\PlanLevelLearningOutcome as PLLO;
use DatabaseObjects\InstitutionLearningOutcome as ILO;


qsc_cmp_start_page_load();

$db_curriculum = new CMD();

$ilo_id = qsc_core_get_id_from_get();

if ($ilo_id === false) :
    qsc_cmp_start_html(array(QSC_CMP_START_HTML_TITLE => "Error Displaying ILO"));
?>

<h1>Error Displaying Institution Learning Outcome</h1>
    <?php qsc_core_log_and_display_error("The ILO ID could not be extracted as an integer from the URL.");
else:
    $ilo = $db_curriculum->getILOFromID($ilo_id);
    if (! $ilo) :
        qsc_cmp_start_html(array(QSC_CMP_START_HTML_TITLE => "Error Finding Course"));  
?>
    
<h1>Error Finding Institution Learning Outcome</h1>
    <?php qsc_core_log_and_display_error("A ILO with that ID could not be retrieved from the database.");
    else :        
        qsc_cmp_start_html(array(QSC_CMP_START_HTML_TITLE => "View ".$ilo->getName()));
                                                                                        
        // Is there parent ILO? If so, get it's information
        $parent_ilo = null;
        if ($ilo->hasParent()) {
            $parent_ilo = $db_curriculum->getILOFromID($ilo->getParentDBID());
        }
                      
        // Get all of the direct PLLOs for this ILO
        $pllo_array = $db_curriculum->getDirectPLLOsForILO($ilo_id);

        // Get all of the direct CLLOs for this ILO
        $cllo_array = $db_curriculum->getDirectCLLOsForILO($ilo_id);
?>

<h1><?= $ilo->getName();?></h1>

        <?php qsc_cmp_display_property_columns(array(
            "Text" => $ilo->getText(),
            "Description" => $ilo->getDescription(QSC_CMP_TEXT_NONE_SPECIFIED),
            "Parent ILO" => (($parent_ilo) ? $parent_ilo->getAnchorToView(true) : QSC_CMP_TEXT_NONE_SPECIFIED),
            "Notes" => $ilo->getNotes(QSC_CMP_TEXT_NONE_SPECIFIED)
            )
        ); ?>

    <h2>Plan Level Learning Outcomes</h2>
        <?php if (empty($pllo_array)) : ?>
    <p>There are no PLLOs set for this ILO.</p>
        <?php
        else :
            qsc_cmp_display_pllo_table($pllo_array, $db_curriculum);
        endif; ?>

    <h2>Course Level Learning Outcomes</h2>
        <?php if (empty($cllo_array)) : ?>
    <p>There are no CLLOs set for this ILO.</p>
        <?php
        else :
            qsc_cmp_display_cllo_table($cllo_array, $db_curriculum, $db_calendar, true, false);
        endif; 
        
        if ($ilo->isTopLevel()) : 
            $child_ilo_array = $db_curriculum->getChildILOs($ilo_id);
            if (! empty($child_ilo_array)) : ?>
    <h2>Child ILOs</h2>
                <?php qsc_cmp_display_ilo_table($child_ilo_array); 
            endif; 
        endif;
    endif; 
endif;

qsc_cmp_end_html();

qsc_cmp_end_page_load();
?>