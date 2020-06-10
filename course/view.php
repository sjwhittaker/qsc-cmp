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
use DatabaseObjects\InstitutionLearningOutcome as ILO;

qsc_cmp_start_page_load();

$db_curriculum = new CMD();
$db_calendar = new CCD();

$course_id = qsc_core_get_id_from_get();

if ($course_id === false) :
    qsc_cmp_start_html(array(QSC_CMP_START_HTML_TITLE => "Error Displaying Course"));
?>

<h1>Error Displaying Course</h1>
    <?php qsc_core_log_and_display_error("The course ID could not be extracted as an integer from the URL.");
else:
    $course = $db_curriculum->getCourseFromID($course_id);
    if (! $course) :
        qsc_cmp_start_html(array(QSC_CMP_START_HTML_TITLE => "Error Finding Course"));
?>

<h1>Error Finding Course</h1>
    <?php qsc_core_log_and_display_error("A course with that ID could not be retrieved from the database.");
    else :            
        qsc_cmp_start_html(array(QSC_CMP_START_HTML_TITLE => "View ".$course->getName()));
        
        // Did the user choose to delete a CLLO attached to this course?
        $form_result = qsc_cmp_get_form_result();
        if ($form_result == QSC_CMP_FORM_RESULT_DELETE_CLLO_SUCCESSFUL) {
            qsc_core_display_success_message("The CLLO was deleted successfully."
            );
        }
        
        // Get all of the CLLOS for the course and extract the IDs
        $pllo_array = array();
        $ilo_array = array();
        $cllo_array = $db_curriculum->getCLLOsForCourse($course->getDBID());
        $cllo_id_array = qsc_core_get_db_id_array($cllo_array);

        if (! empty($cllo_id_array)) {
            // Get all of the direct PLLOs for the course and extract the IDs
            $pllo_array = $db_curriculum->getDirectPLLOsForCLLOs($cllo_id_array);

            // Get all of the direct ILOs for the course and extract the IDs
            $ilo_array = $db_curriculum->getDirectILOsForCLLOs($cllo_id_array);
        }
        
        // Get the course's name and all of its entries
        $course_name = $course->getCalendarName($db_calendar);
        $course_entry_array = $course->getCourseEntries();
        $is_cross_referenced = $course->getNumberOfCourseEntries() > 1;
        $column_span = floor(12 / $course->getNumberOfCourseEntries());
?>

<h1><?= $course->getName(); ?><?= ($course_name) ? ': '.$course_name : ''; ?></h1>

<h2>Calendar <?= ($is_cross_referenced) ? "Entries" : "Entry"; ?></h2>

<div class="row">
        <?php foreach ($course_entry_array as $course_entry) : 
            $calendar_course = $db_calendar->getCourseFromID($course_entry->getCalendarCourseDBID());
            $calendar_course_name = ($calendar_course) ? $calendar_course->getName() : '';?>
    <div class="col-lg-<?= $column_span; ?>">
        <?php if ($is_cross_referenced) : ?><h3 class="cross-referenced-course-name"><?= $course_entry->getName(); ?><?= ($calendar_course_name) ? ': '.$calendar_course_name : ''; ?></h3><?php endif; ?>
            <?php if (! $calendar_course) : ?>    
        <p>This course code does not have a corresponding calendar entry.</p>    
            <?php else: ?>
        <p><?= $calendar_course->getDescription(); ?></p>
            <?php qsc_cmp_display_property_columns(array(
                "Subject" => qsc_cmp_get_anchor_to_view_subject($course_entry->getSubject()),
                "Units" => $calendar_course->getUnits(),
                "Learning Hours" => $calendar_course->getLearningHours(),
                "Prerequisites" => $calendar_course->getPrerequisites(QSC_CMP_TEXT_NONE_SPECIFIED),
                "Corequisites" => $calendar_course->getCorequisites(QSC_CMP_TEXT_NONE_SPECIFIED),
                "Note" => $calendar_course->getNote(QSC_CMP_TEXT_NONE_SPECIFIED),
                "Exclusion(s)" => $calendar_course->getExclusions(QSC_CMP_TEXT_NONE_SPECIFIED),
                "One Way Exclusion(s)" => $calendar_course->getOneWayExclusion(QSC_CMP_TEXT_NONE_SPECIFIED),
                "Recommendation" => $calendar_course->getRecommendation(QSC_CMP_TEXT_NONE_SPECIFIED),
                "Equivalency" => $calendar_course->getEquivalency(QSC_CMP_TEXT_NONE_SPECIFIED)
                ),
                ($column_span < 12)
            ); ?>    
            <?php endif; ?>
    </div>
        <?php endforeach; ?>
</div>

<h2>Course Level Learning Outcomes</h2>
        <?php if (empty($cllo_array)) : ?>
    <p>There are no CLLOs set for this course.</p>
        <?php else :
            qsc_cmp_display_cllo_table($cllo_array, $db_curriculum, $db_calendar, false, true, true);
        endif;
    endif;
endif;

qsc_cmp_end_html();

qsc_cmp_end_page_load();
?>