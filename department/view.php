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

use DatabaseObjects\Faculty;
use DatabaseObjects\Department;

qsc_cmp_start_page_load();

$db_curriculum = new CMD();
$db_calendar = new CCD();

$department_id = qsc_core_get_id_from_get();

if ($department_id === false) :
    qsc_cmp_start_html(array(QSC_CMP_START_HTML_TITLE => "Error Displaying Department"));
?>

<h1>Error Displaying Department</h1>
    <?php qsc_core_log_and_display_error("The department ID could not be extracted as an integer from the URL.");
else:
    $department = $db_curriculum->getDepartmentFromID($department_id);
    if (! $department) :
        qsc_cmp_start_html(array(QSC_CMP_START_HTML_TITLE => "Error Finding Department"));
?>

<h1>Error Finding Department</h1>
    <?php qsc_core_log_and_display_error("A department with that ID could not be retrieved from the database.");
    else :
        qsc_cmp_start_html(array(QSC_CMP_START_HTML_TITLE => "View ".$department->getName()));
    
        $faculty_array = $db_curriculum->getFacultiesFromDepartment($department_id);
        $faculty_anchor_array = qsc_core_map_member_function($faculty_array, 'getAnchorToView');
        
        $subject_array = $db_curriculum->getSubjectsForDepartment($department_id);
        
        $plan_array = $db_curriculum->getPlansFromDepartment($department_id);
        
        $alignment_report_link = qsc_cmp_get_link_to_alignment_report(
            QSC_CORE_QUERY_STRING_NAME_DEPARTMENT_ID, $department_id);        
        $course_matrix_report_link = qsc_cmp_get_link_to_course_matrix(
            QSC_CORE_QUERY_STRING_NAME_DEPARTMENT_ID, $department_id);         
        ?>

<h1><?= $department->getName();?></h1>

<div class="row">
    <div class="col-auto">
        <?php qsc_cmp_display_link_button($alignment_report_link, "View Alignment Report"); ?>
    </div>
    <div class="col-auto">
        <?php qsc_cmp_display_link_button($course_matrix_report_link, "View Course Matrix"); ?>
    </div>
</div>

        <?php qsc_cmp_display_property_columns(array(
            "Faculties" => implode("<br/>", $faculty_anchor_array)
            )
        ); ?>
<div class="row">
    <div class="col-lg-6">
<h2>Courses by Subject</h2>
        <?php if (empty($subject_array)) : ?>
<p>There are no subjects listed for this department.</p>
        <?php else : 
            qsc_cmp_display_subject_and_course_table($subject_array, $db_curriculum, $db_calendar);
        endif; ?>
    </div> <!-- .col-lg-6 -->
    <div class="col-lg-6">
<h2>Plans</h2>
        <?php if (empty($plan_array)) : ?>
<p>There are no plans associated with this department.</p>
        <?php else : 
            qsc_cmp_display_plan_table($plan_array, $db_curriculum, $department_id);
        endif; ?>
    </div> <!-- .col-lg-6 -->
</div>
    <?php endif;
endif;

qsc_cmp_end_html();

qsc_cmp_end_page_load();
?>
