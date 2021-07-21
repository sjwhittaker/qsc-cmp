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

include_once('../../config/config.php');

use Managers\CurriculumMappingDatabase as CMD;
use Managers\CourseCalendarDatabase as CCD;

use DatabaseObjects\Course;
use DatabaseObjects\CourseEntry;
use DatabaseObjects\LegacyCourseEntry;
use DatabaseObjects\CourseLevelLearningOutcome as CLLO;
use DatabaseObjects\PlanLevelLearningOutcome as PLLO;
use DatabaseObjects\InstitutionLearningOutcome as ILO;

qsc_cmp_start_page_load();

$db_curriculum = new CMD();
$db_calendar = new CCD();

$course_id = qsc_core_get_id_from_get();

if ($course_id === false) :
    qsc_cmp_start_html(array(QSC_CMP_START_HTML_TITLE => "Error Displaying Legacy Course"));
?>

<h1>Error Displaying Course</h1>
    <?php qsc_core_log_and_display_error("The legacy course's ID could not be extracted as an integer from the URL.");
else:
    $course = $db_curriculum->getCourseFromID($course_id);
    if (! $course) :
        qsc_cmp_start_html(array(QSC_CMP_START_HTML_TITLE => "Error Finding Legacy Course"));
?>

<h1>Error Finding Course</h1>
    <?php qsc_core_log_and_display_error("A legacy course with that ID could not be retrieved from the database.");
    else :            
        qsc_cmp_start_html(array(QSC_CMP_START_HTML_TITLE => "View Legacy ".$course->getName()));
            
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
        
        // Get the legacy course entry
        $course_entry = $course->getLegacyCourseEntry();
        
        $departmental_notes = $course_entry->getNotes(); 
        
        $related_course_array = $db_curriculum->getCoursesRelatedToLegacyCourse($course_id);
        ?>

        <?php qsc_core_display_message(
            '<i class="fas fa-exclamation-circle"></i> This course is no longer offered.', 
            QSC_CORE_MESSAGE_TYPE_WARNING); ?>

<h1><?= $course->getName(); ?></h1>

        <?php if ($departmental_notes) : ?>
<h2>Departmental Notes</h2>
<p><?= $departmental_notes ?></p>
        <?php endif; ?>

<h2>Related Current Courses</h2>
        <?php if (empty($related_course_array)) : ?>
    <p>There are no current courses related to this course.</p>
        <?php else :
            qsc_cmp_display_course_table($related_course_array, $db_calendar);
        endif; ?>
    
<h2>Course Level Learning Outcomes</h2>
        <?php if (empty($cllo_array)) : ?>
    <p>There are no CLLOs set for this course.</p>
        <?php else :
            qsc_cmp_display_cllo_table($cllo_array, $db_curriculum, $db_calendar, $course, true, true);
        endif;
    endif;
endif;

qsc_cmp_end_html();

qsc_cmp_end_page_load();
?>