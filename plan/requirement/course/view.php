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

include_once('../../../config/config.php');

use Managers\SubsetManager;
use Managers\CurriculumMappingDatabase as CMD;

use DatabaseObjects\CourseList;
use DatabaseObjects\CPR;

qsc_cmp_start_page_load();

$db_curriculum = new CMD();

$cpr_id = qsc_core_get_id_from_get();

if ($cpr_id === false) :
    qsc_cmp_start_html(array(QSC_CMP_START_HTML_TITLE => "Error Displaying CPR"));
?>

<h1>Error Displaying CPR</h1>
    <?php qsc_core_log_and_display_error("The course plan requirement ID could not be extracted as an integer from the URL.");
else:
    $cpr = $db_curriculum->getCPRFromID($cpr_id);
    if (! $cpr) :
        qsc_cmp_start_html(array(QSC_CMP_START_HTML_TITLE => "Error Finding CPR"));
?>

<h1>Error Finding CPR</h1>
    <?php qsc_core_log_and_display_error("A course plan requirement with that ID could not be retrieved from the database.");
    else :
        $plan = $db_curriculum->getAncestorPlanForCPR($cpr_id);
        $planName = ($plan) ? ' for '.$plan->getName() : '';
        $planAnchor = ($plan) ? ' for '.$plan->getAnchorToView() : '';
        
        $dle_array = $db_curriculum->getAllDLEs();
        $dle_supported_array = array();

        $courselist = $db_curriculum->getCourseListForCPR($cpr_id);
        
        $course_array = ($courselist) ? 
            $courselist->getAllCourses() :
            array();
        $course_subset_manager = ($courselist) ? 
            $courselist->getAllCourseSubsets(floatval($cpr->getUnits())) :
            new SubsetManager();
        $course_subset_array = $course_subset_manager->getSubsetArray();
        
        $number_of_courses = count($course_array);
        $number_of_course_subsets = count($course_subset_array);
        
        $parent_cpr_list = $db_curriculum->getParentCPRListForCPR($cpr_id);        
        $cpr_type = $parent_cpr_list->getType();
                        
        qsc_cmp_start_html(array(QSC_CMP_START_HTML_TITLE => "View $cpr_type Requirement ".$cpr->getNumber().$planName));    
        ?>

<h1><?= $cpr_type ?> Requirement <?= $cpr->getNumber();?><?= $planAnchor; ?></h1>

        <?php qsc_cmp_display_property_columns(array(
            "Units" => $cpr->getUnits(),
            "Course List" => ($courselist ? $courselist->getAnchorToView() : QSC_CMP_TEXT_NONE_SPECIFIED),
            "Connector" => $cpr->getConnector(),
            "Text" => $cpr->getText(QSC_CMP_TEXT_NONE_SPECIFIED),
            "Notes" => $cpr->getNotes(QSC_CMP_TEXT_NONE_SPECIFIED)            
            )
        ); 
        
        if ($cpr->hasSubLists()) : 
            $child_cpr_list_array = $cpr->getChildCPRListArray(); 
        
            $list_names = $cpr->getSubListNamesHTML($parent_cpr_list->getNumber());            
            $lists_required = $cpr->getSubListsRequiredHTML(true); ?>

<h2>Sub-lists</h2>

<p><?= $lists_required ?> of <?= $cpr_type ?> Lists <?= $list_names ?> must be satisfied for this requirement.</td>

        <?php foreach ($child_cpr_list_array as $child_cpr_list) {
            qsc_cmp_display_cpr_table($child_cpr_list, $db_curriculum);
        }
        
        else: ?>

<h2>Learning Outcome Analysis</h2>
        <?php if (! $courselist) : ?>
<p>There are no courses associated with this requirement.</p>
        <?php elseif (empty($course_subset_array)) : ?>
<p>There are no sets of courses that meet this requirement.</p>
        <?php else :
            $subset_or_course_heading_title = "Course Subsets";
            if (($number_of_course_subsets > QSC_CMP_COURSELIST_SUBSETS_DISPLAY_LIMIT) && ($number_of_course_subsets > $number_of_courses)) : ?>
<p>
    <?= $course_subset_manager->maximumRecursiveCallsMade() ? "More than " : "" ?><?= $number_of_course_subsets ?> course subsets were found that met this requirement<?= $course_subset_manager->maximumRecursiveCallsMade() ? " before the search was stopped" : "" ?>. 
    The <?= $number_of_courses ?> individual courses will be analyzed instead. 
</p>
                <?php
                $course_subset_array = array();
                foreach ($course_array as $course) {
                    $course_subset_array[] = array($course);
                }
              
                $number_of_course_subsets = $number_of_courses;              
                $subset_or_course_heading_title = "Courses";              
            endif; ?>
<table id="course-pllo-matrix">
    <thead>
        <tr>
            <th><?= $subset_or_course_heading_title ?></th>
            <?php foreach ($dle_array as $dle_index => $dle) : 
                $dle_supported_array[$dle_index] = 0; ?>
            <th><?= $dle->getAnchorToView(); ?></th>
            <?php endforeach; ?>
        </tr>
    </thead>
    <tbody>
            <?php foreach ($course_subset_array as $course_subset) : ?>
        <tr>
            <td><?= implode(', ', qsc_core_map_member_function($course_subset, 'getAnchorToView')); ?></td>
                <?php foreach ($dle_array as $dle_index => $dle) : 
                    $cllo_and_pllo_2D_array = array();
                
                    foreach ($course_subset as $course_index => $course) :
                        $cllo_and_pllo_array = $db_curriculum->getCLLOsAndPLLOsForDLEAndCourse($dle->getDBID(), $course->getDBID());            
                        if (! empty($cllo_and_pllo_array)) :
                            $cllo_and_pllo_2D_array[$course_index] = $cllo_and_pllo_array;
                        endif;
                    endforeach;
                ?>
            <td class="result tooltip-container">
                    <?php if (! empty($cllo_and_pllo_2D_array)) : 
                        $dle_supported_array[$dle_index]++; ?>
                <i class="fas fa-check" aria-hidden="true" title="Supported"></i>
                <span class="sr-only">supported</span>
                <div class="tooltip-popup">
                        <?php foreach ($cllo_and_pllo_2D_array as $course_index => $cllo_and_pllo_array) : ?>
                    <a class="tooltip-popup-title" href="<?= $course_subset[$course_index]->getLinkToView(); ?>"><?= $course_subset[$course_index]->getName(); ?></a>
                    <ul>
                            <?php foreach ($cllo_and_pllo_array as $cllo_and_pllo) :
                                $cllo = $db_curriculum->getCLLOFromID($cllo_and_pllo->getCLLODBID());                            
                                $pllo = $db_curriculum->getPLLOFromID($cllo_and_pllo->getPLLODBID());
                        ?>
                        <li><?= $cllo->getAnchorToView(); ?>
                            <i class="fas fa-arrow-right" aria-hidden="true" title="supports"></i>
                            <span class="sr-only">supports</span>
                            <?= $pllo->getAnchorToView(); ?>
                        </li>
                            <?php endforeach; ?>
                    </ul>
                        <?php endforeach; ?>
                </div> <!-- .tooltip-popup -->
                    <?php endif; ?>
            </td>
                <?php endforeach; ?>
        </tr>
            <?php endforeach; ?>            
    </tbody>
    <tfoot>
        <tr class="support-row">
            <th>DLE Support</th>
            <?php foreach ($dle_supported_array as $dle_supported) : ?>
            <td><?php if ($dle_supported == 0) : ?>
                <i class="fas fa-times-circle" aria-hidden="true" title="unsupported"></i>
                    <span class="sr-only">Unsupported</span>
                <?php elseif ($dle_supported == $number_of_course_subsets) : ?>
                <i class="fas fa-check-square" aria-hidden="true" title="supported"></i>
                    <span class="sr-only">Fully Supported</span>
                <?php else : ?>
                    <?= $dle_supported; ?> / <?= $number_of_course_subsets; ?></td>
                <?php endif; ?>
            <?php endforeach ?>
        </tr>
    </tfoot>
</table>

        <?php
        endif;
        endif;
    endif;
endif;

qsc_cmp_end_html();

qsc_cmp_end_page_load();
?>
