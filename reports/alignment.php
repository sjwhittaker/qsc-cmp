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
use DatabaseObjects\CalendarCourse;
use DatabaseObjects\CourseLevelLearningOutcome as CLLO;
use DatabaseObjects\PlanLevelLearningOutcome as PLLO;
use DatabaseObjects\DegreeLevelExpectation as DLE;
use DatabaseObjects\Department;

qsc_cmp_start_page_load();

$db_curriculum = new CMD();
$db_calendar = new CCD();

$department = null;
$course_array = array();
$page_title = "Alignment Report for the ";
$content_name = null;
$content_anchor = null;

$department_id = qsc_core_get_id_from_get(QSC_CORE_QUERY_STRING_NAME_DEPARTMENT_ID);
$subject = qsc_core_get_id_from_get(QSC_CORE_QUERY_STRING_NAME_SUBJECT, FILTER_SANITIZE_STRING);

if ($department_id) {
    $department = $db_curriculum->getDepartmentFromID($department_id);
    $content_name = $department->getName();
    $content_anchor = $department->getAnchorToView();
}
elseif ($subject) {
    $page_title .= 'Subject ';    
    $content_name = $subject;
    $content_anchor = qsc_cmp_get_anchor_to_view_subject($subject);
}

qsc_cmp_start_html(array(QSC_CMP_START_HTML_TITLE => $page_title.$content_name));

$dles_array = $db_curriculum->getAllDLEs();
?>

<h1><?= $page_title; ?> <?= $content_anchor; ?></h1>

<?php if ((! $department) && (! $subject)) : ?>

<p>No department or subject has been selected.</p>

<?php elseif (empty($dles_array)) : ?>

<p>There are presently no DLEs defined.</p>

<?php else: ?>
    <div id="alignment-report">
    <?php foreach ($dles_array as $dle) : ?>
        <h2><?= $dle->getAnchorToView(true); ?></h2>
    
        <?php
        $pllo_array = ($department) ?
            $db_curriculum->getPLLOsForDLEAndDepartment($dle->getDBID(), $department_id) :
            $db_curriculum->getPLLOsForDLEAndSubject($dle->getDBID(), $subject);            

            // Go through each of the DLE's child PLLOs
            foreach ($pllo_array as $pllo) :  
                $cllo_array = ($department) ?
                    $db_curriculum->getDirectCLLOsForPLLO($pllo->getDBID()) :
                    $db_curriculum->getDirectCLLOsForPLLOAndSubject($pllo->getDBID(), $subject);
                ?>                
            <div class="child-pllo">            
                <div class="child-pllo-header">           
                    <h3><?= $pllo->getAnchorToView(); ?></h3>            
                    <p class="pllo-text"><?= $pllo->getText(); ?></p>
                </div>
                <?php qsc_cmp_display_cllo_table($cllo_array, $db_curriculum, $db_calendar, null, false, true); ?>
            </div> <!-- .child-pllo -->            
            <?php
            
                // Now check for and go through each of the PLLO's child PLLOs
                // TO DO: make this recursive
                $grandchild_pllo_array = $db_curriculum->getChildPLLOs($pllo->getDBID());
                foreach ($grandchild_pllo_array as $grandchild_pllo) :
                    $cllo_array = ($department) ?
                        $db_curriculum->getDirectCLLOsForPLLO($grandchild_pllo->getDBID()) :
                        $db_curriculum->getDirectCLLOsForPLLOAndSubject($grandchild_pllo->getDBID(), $subject);
                    ?>                
            <div class="grandchild-pllo">            
                <div class="grandchild-pllo-header">           
                    <h4><?= $grandchild_pllo->getAnchorToView(); ?></h4>            
                    <p class="pllo-text"><?= $grandchild_pllo->getText(); ?></p>
                </div>
                    <?php qsc_cmp_display_cllo_table($cllo_array, $db_curriculum, $db_calendar, null, false, true); ?>
            </div> <!-- .grandchild-pllo -->            
                <?php endforeach;
            endforeach;
    endforeach; ?>
    </div> <!-- #alignment-report -->
<?php 
endif;

qsc_cmp_end_html();

qsc_cmp_end_page_load();
?>