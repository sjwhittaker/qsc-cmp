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
use DatabaseObjects\CLLOAndPLLO;
use DatabaseObjects\Department;

qsc_cmp_start_page_load();

$db_curriculum = new CMD();
$db_calendar = new CCD();

// Determine the set of courses for the matrix
$course_array = array();
$page_title = "Course Matrix for ";
$content_name = null;
$content_anchor = null;

$department = null;
$department_id = qsc_core_get_id_from_get(QSC_CORE_QUERY_STRING_NAME_DEPARTMENT_ID);
$plan_id = qsc_core_get_id_from_get(QSC_CORE_QUERY_STRING_NAME_PLAN_ID);
$subject = qsc_core_get_id_from_get(QSC_CORE_QUERY_STRING_NAME_SUBJECT, FILTER_SANITIZE_STRING);

if ($department_id) {
    $department = $db_curriculum->getDepartmentFromID($department_id);
    $course_array = $db_curriculum->getCoursesInDepartment($department_id);
    $page_title .= 'the ';    
    $content_name = $department->getName();
    $content_anchor = $department->getAnchorToView();
}
elseif ($plan_id) {
    $plan = $db_curriculum->getPlanFromID($plan_id);
    $course_array = $plan->getRequiredCourses($db_curriculum);
    $page_title = "Required $page_title";
    $content_name = $plan->getName();
    $content_anchor = $plan->getAnchorToView();
}
elseif ($subject) {
    $course_array = $db_curriculum->getCoursesWithSubject($subject);
    $page_title .= 'the Subject ';    
    $content_name = $subject;
    $content_anchor = qsc_cmp_get_anchor_to_view_subject($subject);
}

qsc_cmp_start_html(array(QSC_CMP_START_HTML_TITLE => $page_title.$content_name));

$dles_array = $db_curriculum->getAllDLEs();
?>

<h1><?= $page_title; ?> <?= $content_anchor; ?></h1>

<?php if ((! $department) && (! $subject) && (! $plan)) : ?>

<p>No department, plan or subject has been selected.</p>

<?php elseif (empty($dles_array)) : ?>

<p>There are presently no DLEs defined.</p>

<?php elseif (empty($course_array)) : ?>

<p>There are presently no <?= $plan_id ? " required " : ""?>courses associated with <?= $content_name; ?>.</p>

<?php else: ?>
    <table id="course-pllo-matrix">
        <thead>
            <tr>
                <th>Course</th>
    <?php foreach ($dles_array as $dle) : ?>
                <th><?= $dle->getAnchorToView(); ?></th>
    <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
    <?php foreach ($course_array as $course) : ?>
            <tr>
                <td><?= $course->getAnchorToView($db_calendar); ?></td>
        <?php foreach ($dles_array as $dle) : 
            $cllos_and_pllos_array = $db_curriculum->getCLLOsAndPLLOsForDLEAndCourse($dle->getDBID(), $course->getDBID());            
            ?>
                <td class="result tooltip-container">
                    <?php if (! empty($cllos_and_pllos_array)) : ?>
                    <i class="fas fa-check" aria-hidden="true" title="Supported"></i>
                        <span class="sr-only">supported</span>
                    <div class="tooltip-popup">
                        <a class="tooltip-popup-title" href="<?= $course->getLinkToView(); ?>"><?= $course->getName(); ?></a>
                        <ul>
                        <?php foreach ($cllos_and_pllos_array as $cllo_and_pllo) :                            
                            $cllo = $db_curriculum->getCLLOFromID($cllo_and_pllo->getCCMDBID());                            
                            $pllo = $db_curriculum->getPLLOFromID($cllo_and_pllo->getPCMDBID());
                        ?>
                            <li>
                                <?= $cllo->getAnchorToView(); ?>
                                <i class="fas fa-arrow-right" aria-hidden="true" title="supports"></i>
                                    <span class="sr-only">supports</span>
                                <?= $pllo->getAnchorToView(); ?>
                            </li>
                        
                        <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                </td>
        <?php endforeach; ?>
            </tr>
    <?php endforeach; ?>            
        </tbody>
    </table>
<?php 
endif;

qsc_cmp_end_html();

qsc_cmp_end_page_load();
?>