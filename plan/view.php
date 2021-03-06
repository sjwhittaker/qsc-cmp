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

use DatabaseObjects\Plan;

qsc_cmp_start_page_load();

$db_curriculum = new CMD();

$plan_id = qsc_core_get_id_from_get();

if ($plan_id === false) :
    qsc_cmp_start_html(array(QSC_CMP_START_HTML_TITLE => "Error Displaying Plan"));
?>

<h1>Error Displaying Plan</h1>
    <?php qsc_core_log_and_display_error("The plan ID could not be extracted as an integer from the URL.");
else :
    $plan = $db_curriculum->getPlanFromID($plan_id);
    if (! $plan) :
        qsc_cmp_start_html(array(QSC_CMP_START_HTML_TITLE => "Error Finding Plan"));
?>

<h1>Error Finding Plan</h1>
    <?php qsc_core_log_and_display_error("A plan with that ID could not be retrieved from the database.");
    else :
        qsc_cmp_start_html(array(QSC_CMP_START_HTML_TITLE => "View ".$plan->getName()));

        $parent_plan = $db_curriculum->getAncestorPlanForPlan($plan_id);

        $program_array = $db_curriculum->getProgramsFromPlan($plan_id);
        $program_achor_array = qsc_core_map_member_function($program_array, 
            'getAnchorToView');
               
        $department_array = $db_curriculum->getDepartmentsForPlan($plan_id);
        
        $course_matrix_report_link = qsc_cmp_get_link_to_course_matrix(
            QSC_CORE_QUERY_STRING_NAME_PLAN_ID, $plan_id);
        $plan_pllos_report_link = qsc_core_create_link_with_id(
            QSC_CMP_PLAN_VIEW_PLLOS_LINK, $plan_id);        
        ?>

<h1><?= $plan->getDescriptiveName() ?></h1>

<div class="row">
    <div class="col-auto">
        <?php qsc_cmp_display_link_button($course_matrix_report_link, "View Required Course Matrix"); ?>
    </div>
    <div class="col-auto">
        <?php qsc_cmp_display_link_button($plan_pllos_report_link, "View PLLOs for This Plan"); ?>
    </div>
</div>

        <?php qsc_cmp_display_property_columns(array(
            "Code" => $plan->getCode(),
            "Subject" => Plan::getSubjectHTML($plan, $db_curriculum),
            "Programs" => empty($program_achor_array) ? QSC_CMP_TEXT_NONE_SPECIFIED : join("<br/>", $program_achor_array),
            "Parent Plan" => ($parent_plan) ? $parent_plan->getAnchorToView() : QSC_CMP_TEXT_NONE_SPECIFIED,
            "Professional Internship Option" => ($plan->hasInternship()) ? "Yes" : "No",
            "Enrolment End Date" => $plan->getPriorTo(),
            "Notes" => $plan->getNotes()            
            )
        ); ?>

<p><?= $plan->getText(''); ?></p>

        <?php qsc_cmp_display_plan_requirements($plan, $db_curriculum);

    endif;
endif;

qsc_cmp_end_html();

qsc_cmp_end_page_load();
?>
