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

qsc_cmp_start_page_load();

$db_curriculum = new CMD();
$db_calendar = new CCD();

$subject = qsc_core_get_id_from_get(QSC_CORE_QUERY_STRING_NAME_ID, FILTER_SANITIZE_STRING);

if ($subject === false) :
    qsc_cmp_start_html(array(QSC_CMP_START_HTML_TITLE => "Error Displaying Subject"));
?>

<h1>Error Displaying Subject</h1>
    <?php qsc_core_log_and_display_error("The subject could not be extracted as a string from the URL.");
else:
    if (! $db_curriculum->subjectExists($subject)) :
        qsc_cmp_start_html(array(QSC_CMP_START_HTML_TITLE => "Error Finding Subject"));
?>

<h1>Error Finding Subject</h1>
    <?php qsc_core_log_and_display_error("A subject with that ID could not be retrieved from the database.");
    else :
        qsc_cmp_start_html(array(QSC_CMP_START_HTML_TITLE => "View Subject $subject")); 
    
        $course_array = $db_curriculum->getCoursesWithSubject($subject);
        $department_array = $db_curriculum->getDepartmentsForSubject($subject);
        
        $alignment_report_link = qsc_cmp_get_link_to_alignment_report(
            QSC_CORE_QUERY_STRING_NAME_SUBJECT, $subject);        
        $course_matrix_report_link = qsc_cmp_get_link_to_course_matrix(
            QSC_CORE_QUERY_STRING_NAME_SUBJECT, $subject);        
        ?>

<h1>Subject: <?= $subject; ?></h1>

<div class="row">
    <div class="col-auto">
        <?php qsc_cmp_display_link_button($alignment_report_link, "View Alignment Report"); ?>
    </div>
    <div class="col-auto">
        <?php qsc_cmp_display_link_button($course_matrix_report_link, "View Course Matrix"); ?>
    </div>
</div>

<h2>Departments</h2>
        <?php qsc_cmp_display_department_table($department_array, $db_curriculum); ?>

<h2>Courses</h2>
        <?php qsc_cmp_display_course_table($course_array, $db_calendar); 
    endif;
endif;

qsc_cmp_end_html();

qsc_cmp_end_page_load();
?>
