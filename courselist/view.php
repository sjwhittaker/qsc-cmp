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

use Managers\SubsetManager;
use Managers\CurriculumMappingDatabase as CMD;
use Managers\CourseCalendarDatabase as CCD;

use DatabaseObjects\Course;
use DatabaseObjects\CalendarCourse;
use DatabaseObjects\CourseList;
use DatabaseObjects\RelationshipCourseList;

qsc_cmp_start_page_load();

$db_curriculum = new CMD();
$db_calendar = new CCD();

$courselist_id = qsc_core_get_id_from_get();
$courselist_level = qsc_core_extract_form_value(INPUT_GET, QSC_CORE_QUERY_STRING_NAME_LEVEL, FILTER_SANITIZE_STRING);
$courselist_or_above = qsc_core_extract_form_value(INPUT_GET, QSC_CORE_QUERY_STRING_NAME_OR_ABOVE, FILTER_VALIDATE_BOOLEAN);

if (is_null($courselist_level)) {
    $courselist_level = CMD::TABLE_COURSELIST_AND_COURSELIST_LEVEL_NONE;
}

if ($courselist_id === false) :
    qsc_cmp_start_html(array(QSC_CMP_START_HTML_TITLE => "Error Displaying Course List"));
?>

<h1>Error Displaying Course List</h1>
    <?php qsc_core_log_and_display_error("The course list ID could not be extracted as an integer from the URL.");
else:
    $courselist = $db_curriculum->getCourseListFromID($courselist_id, $courselist_level, $courselist_or_above);
    if (! $courselist) :
        qsc_cmp_start_html(array(QSC_CMP_START_HTML_TITLE => "Error Finding Course List"));
?>

<h1>Error Finding Course List</h1>
    <?php qsc_core_log_and_display_error("A course list with that ID could not be retrieved from the database.");
    else :
        qsc_cmp_start_html(array(QSC_CMP_START_HTML_TITLE => "View ".$courselist->getNameSnippet())); 
            
        // Get any parent or ancestor CPRs
        $cpr_array = $db_curriculum->getCPRsForCourseList($courselist_id, 
            $courselist_level, $courselist_or_above);
        $number_of_cprs = count($cpr_array);

        // Get the lists of courses (child and descendant)
        $child_course_array = $courselist->getChildCourseArray();
        $child_courselist_array = $courselist->getChildCourseListArray();

        // Determine the number of courses and subsets
        $number_of_courses = $courselist->getNumberOfCourses();               
        $number_of_course_subsets = $courselist->getNumberOfCourseSubsets();
                
        $property_array = array(
            "Number of Courses" => $number_of_courses,
            "Possible Course Subsets" => $number_of_course_subsets.(
                ($number_of_course_subsets > QSC_CMP_COURSELIST_SUBSETS_DISPLAY_LIMIT) ?
                    " (too many to display)" : ""
            )
        );
        
        if ($courselist instanceof RelationshipCourseList) {
            $property_array["Relationship"] = $courselist->getRelationship();
        }
        
        $property_array["Notes"] = $courselist->getNotes();        
        ?>

<h1><?= $courselist->getNameSnippet(); ?></h1>

        <?php qsc_cmp_display_property_columns($property_array); ?>

<h2>Plan Requirements</h2>
        <?php if ($number_of_cprs == 0) : ?>
<p>This course list isn't associated with any plan requirement.</p>
        <?php else: ?>
<table>
    <thead>
        <tr>
            <th>Plan</th>
            <th>Requirement</th>
            <th><span class="sr-only">Units</span></th>
            <th><span class="sr-only">Connector</span></th>
            <th><span class="sr-only">Courses</span></th>
        </tr>
    </thead>
    <tbody>
            <?php foreach ($cpr_array as $cpr) :
            $cpr_link = '<a href="'.$cpr->getLinkToView().'">'.$cpr->getType().' '.$cpr->getName().'</a>';
            
            $cpr_courselist =  $db_curriculum->getCourseListForCPR($cpr->getDBID());            
            $cpr_courselist_link = $cpr_courselist ? $cpr_courselist->getHTML() : QSC_CMP_TEXT_NONE_SPECIFIED;                

            $cpr_plan = $db_curriculum->getPlanForCPR($cpr->getDBID());
            $plan_link = $cpr_plan ? $cpr_plan->getAnchorToView() : QSC_CMP_TEXT_NONE_SPECIFIED;                
?>
        <tr>
            <td><?= $plan_link; ?></td>
            <td><?= $cpr_link ?></td>
            <td><?= $cpr->getUnits(); ?> units</td>
            <td><?= $cpr->getConnector(); ?></td>            
            <td><?= $cpr_courselist_link ?></td>
        </tr>        
            <?php endforeach; ?>        
    </tbody>    
</table>
        <?php endif; ?>

<div class="row">
    <div class="col-lg-6">
        <?php if ($number_of_course_subsets > QSC_CMP_COURSELIST_SUBSETS_DISPLAY_LIMIT) : ?>
        <h2>Courses in This List </h2>
            <?php if (empty($child_course_array)) : ?>
        <p>There are no courses directly associated with this list.</p>
            <?php else:
                // Display the courses directly associated with this list
                qsc_cmp_display_course_table($child_course_array, $db_calendar);
            endif;
        
            // Display the courses in the child lists
            foreach ($child_courselist_array as $child_courselist) :
                $child_courselist_course_array = $child_courselist->getChildCourseArray();
                if (! empty($child_courselist_course_array)) : ?>
        <h2>Courses in <?= $child_courselist->getHTML(); ?></h2>
                <?php qsc_cmp_display_course_table($child_courselist_course_array, $db_calendar); 
                endif;                     
            endforeach;
        else :
            $course_subset_manager = $courselist->getAllCourseSubsets();
            $course_subset_array = $course_subset_manager->getSubsetArray(); 
            ?>
        <h2>Possible Course Subsets</h2>        
        <table>
            <thead>
                <tr>
                    <th>Courses</th>
                    <th>Total Units</th>
                </tr>
            </thead>
            <tbody>
             <?php foreach ($course_subset_array as $course_subset) : ?>
                <tr>
                    <td><?= implode(', ', qsc_core_map_member_function($course_subset, 'getAnchorToView')); ?></td>
                    <td><?= number_format(Course::getTotalUnitsForCourses($course_subset), 1); ?></td>
                </tr>
             <?php endforeach; ?>
            </tbody>
        </table>            
        <?php endif; ?>        
    </div> <!-- .col-lg-6 -->
    <div class="col-lg-6">
        <?php if (! empty($child_courselist_array)) : ?>
        <h2>Sub-Lists</h2>
        <table>
            <thead>
                <tr>
                    <th>Name</th>
                </tr>
            </thead>
                <tbody>                            
            <?php foreach ($child_courselist_array as $child_courselist) : ?>
                    <tr>
                        <td><?= $child_courselist->getAnchorToView(); ?></td>
                    </tr>
            <?php endforeach; ?>
                </tbody>             
        </table>
        <?php endif; ?>        
    </div> <!-- .col-lg-6 -->    
</div> <!-- .row -->

    <?php endif;
endif;

qsc_cmp_end_html();

qsc_cmp_end_page_load();
?>
