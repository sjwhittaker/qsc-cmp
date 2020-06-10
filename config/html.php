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

use Managers\SessionManager;
use Managers\CurriculumMappingDatabase as CMD;

/**
 * Starts or begins the HTML of a site, including the head element, opening
 * body tag and header with navigation.
 *
 * @param $arguments    An array of arguments for this function:
 *                      <ul>
 *                          <li>QSC_CMP_START_HTML_TITLE: the string page title 
 *                          (default: 'Curriculum Mapping System')</li>
 *                          <li>QSC_CMP_START_HTML_SCRIPTS: an array of string paths 
 *                          to scripts (default: empty)</li>
 *                      </ul>
 *                      The defaults are used if the key isn't set in the array.
 */
define("QSC_CMP_START_HTML_TITLE", "QSC_CMP_START_HTML_TITLE");
define("QSC_CMP_START_HTML_CSS", "QSC_CMP_START_HTML_CSS");
define("QSC_CMP_START_HTML_SCRIPTS", "QSC_CMP_START_HTML_SCRIPTS");

if (! function_exists("qsc_cmp_start_html")) {
    function qsc_cmp_start_html($arguments = array()) {
        $default_arguments = array(
            QSC_CMP_START_HTML_TITLE => "Curriculum Mapping System",
            QSC_CMP_START_HTML_CSS => array(),
            QSC_CMP_START_HTML_SCRIPTS => array()
        );
        
        $merged_arguments = qsc_core_merge_arrays($arguments, $default_arguments);
        qsc_cmp_write_head($merged_arguments); ?>
    <body>
        <?php qsc_cmp_write_header($merged_arguments); ?>
        <div id="content" class="container-fluid">
        <?php
    }
}

if (! function_exists("qsc_cmp_write_head")) {
    function qsc_cmp_write_head($merged_arguments) {
        ?>
<!DOCTYPE html>
<html>

    <head>
        <title><?= $merged_arguments[QSC_CMP_START_HTML_TITLE]; ?></title>

        <!-- Meta -->
        <meta charset="utf-8"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <!-- CSS -->
        <?php qsc_core_echo_stylesheets_in_head() ; ?>
        <link href="https://fonts.googleapis.com/css?family=Roboto:400,400i,500,500i,700,700i&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="<?= QSC_CMP_CSS_DIRECTORY_LINK; ?>/styles.css">
        <link rel="stylesheet" href="<?= QSC_CMP_CSS_DIRECTORY_LINK; ?>/responsive.css">
        <link rel="stylesheet" href="<?= QSC_CMP_CSS_DIRECTORY_LINK; ?>/print.css">
        <?php foreach ($merged_arguments[QSC_CMP_START_HTML_CSS] as $style_href) : ?>
        <link rel="stylesheet" href="<?= $style_href; ?>">
        <?php endforeach; 
        if (function_exists('qsc_cmp_write_head_css')) {
            qsc_cmp_write_head_css();
        }
        ?>

        <!-- JS -->
        <?php qsc_core_echo_scripts_in_head(); ?>      
        <script src="<?= QSC_CMP_JS_DIRECTORY_LINK; ?>/config.js"></script>        
        <script src="<?= QSC_CMP_JS_DIRECTORY_LINK; ?>/forms.js"></script>
        <?php foreach ($merged_arguments[QSC_CMP_START_HTML_SCRIPTS] as $script_src) : ?>
        <script src="<?= $script_src; ?>"></script>
        <?php endforeach;
        if (function_exists('qsc_cmp_write_head_js')) {
            qsc_cmp_write_head_js();
        }
        ?>
    </head>
        <?php
    }
}

if (! function_exists("qsc_cmp_write_header")) {
    function qsc_cmp_write_header($merged_arguments) {
        ?>
        <header class="container-fluid">
            <div class="print-only" id="header-branding-print">
                <?= QSC_CMP_INSTITUTION_NAME_HTML ?> <?= QSC_CMP_SOFTWARE_NAME_HTML ?>
            </div>
            <div class="row justify-content-between" id="header-branding">
                <div class="col-md-auto">
                    <a href="<?= QSC_CMP_DASHBOARD_PAGE_LINK; ?>"><?= QSC_CMP_SOFTWARE_NAME_HTML ?></a>
                </div>
                <div class="col-md-auto">
                    <a href="<?= QSC_CMP_INSTITUTION_URL; ?>"><?= QSC_CMP_INSTITUTION_NAME_HTML ?></a>
                </div>
            </div> <!-- .row -->
        <?php qsc_cmp_write_primary_navigation($merged_arguments); ?>
        </header> <!-- .container-fluid -->
        <?php
    }
}

if (! function_exists("qsc_cmp_write_primary_navigation")) {
    function qsc_cmp_write_primary_navigation($merged_arguments) {
        ?>
            <nav class="navbar sticky-top navbar-expand-lg">
                <button class="navbar-toggler custom-toggler" type="button" data-toggle="collapse" data-target="#primary-navigation" aria-controls="primary-navigation" aria-expanded="false" aria-label="Toggle primary navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="primary-navigation">
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="<?= QSC_CMP_DASHBOARD_PAGE_LINK; ?>">Dashboard</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="pn-outcomes-dropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                View All
                            </a>
                            <div class="dropdown-menu" aria-labelledby="pn-outcomes-dropdown">
                                <a class="dropdown-item" href="<?= QSC_CMP_FACULTIES_AND_DEPARTMENTS_PAGE_LINK; ?>">Faculties and Departments</a>
                                <a class="dropdown-item" href="<?= QSC_CMP_PLANS_PAGE_LINK; ?>">Plans</a>
                                <a class="dropdown-item" href="<?= QSC_CMP_PROGRAMS_PAGE_LINK; ?>">Programs</a>
                                <a class="dropdown-item" href="<?= QSC_CMP_DLES_PAGE_LINK; ?>">DLEs</a>
                                <a class="dropdown-item" href="<?= QSC_CMP_ILOS_PAGE_LINK; ?>">ILOs</a>
                            </div>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="pn-outcomes-dropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Add
                            </a>
                            <div class="dropdown-menu" aria-labelledby="pn-outcomes-dropdown">
                                <a class="dropdown-item" href="<?= QSC_CMP_CLLO_ADD_PAGE_LINK; ?>">CLLO</a>
                                <a class="dropdown-item" href="<?= QSC_CMP_PLLO_ADD_PAGE_LINK; ?>">PLLO</a>
                                <!--a class="dropdown-item disabled" href="<?php /* echo QSC_CMP_ILO_ADD_PAGE_LINK; */ ?>">ILO</a>
                                <a class="dropdown-item disabled" href="<?php /* echo QSC_CMP_DLE_ADD_PAGE_LINK; */ ?>">DLE</a-->
                            </div>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= QSC_CMP_SEARCH_PAGE_LINK; ?>">Search</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= QSC_CMP_REVISIONS_PAGE_LINK; ?>">Revisions</a>
                        </li>
                    </ul>
                </div> <!-- #primary-navigation -->
            </nav> <!-- .navbar -->
    <?php
    }
}

/**
 * Ends the HTML of a site, including the footer and closing body and html tags.
 *
 * @param $arguments    An array of arguments for this function:
 *                      <ul>
 *                          <li>None at this time.</li>
 *                      </ul>
 *                      The defaults are used if the key isn't set in the array.
 */
function qsc_cmp_end_html($arguments = array()) {
    $default_arguments = array();

    $merged_arguments = qsc_core_merge_arrays($arguments, $default_arguments);
    ?>
        </div> <!-- #content .container-fluid -->
        <?php qsc_cmp_write_footer(); ?>
        <div id="credits" class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm">
                    <a href="https://www.queensu.ca/artsci/"><img alt="Queen's Faculty of Arts and Science logo" src="<?= QSC_CMP_IMG_DIRECTORY_LINK ?>/qfas_unit_signature.png"/></a>
                </div>
                <div class="col-sm">
                    <a href="https://www.cs.queensu.ca/"><img alt="Queen's School of Computing logo" src="<?= QSC_CMP_IMG_DIRECTORY_LINK ?>/qsc_logo_serif_blue.png"/></a>
                </div>
            </div>            
        </div>
    </body>
</html>
<?php
}

if (! function_exists("qsc_cmp_write_footer")) {
    function qsc_cmp_write_footer($arguments = array()) {
        ?>
        <footer class="container-fluid">
            <div class="row align-items-center" id="footer-branding">
                <div class="col-sm-auto">
                    <a href="<?= QSC_CMP_INSTITUTION_URL; ?>"><img id="footer-logo" alt="<?php QSC_CMP_INSTITUTION_NAME_TEXT ?> logo" src="<?= QSC_CMP_INSTITUTION_LOGO_SRC ?>"/></a>
                </div>
                <div class="col-sm-auto">
                    <?= QSC_CMP_INSTITUTION_ADDRESS_HTML ?>
                </div>
            </div>
        </footer> <!-- .container-fluid -->
        <?php
    }
}

/**
 * 
 * @param type $revision_array
 * @param type $db_curriculum
 * @param type $display_prior_value
 * @return type
 */
function qsc_cmp_display_revision_table($revision_array, $db_curriculum, $display_prior_value = false) { 
    if (empty($revision_array)) {
        return;
    }
?>
    <table>
        <thead>
            <tr>
                <th>Component</th>
                <th>Action</th>
                <th>Property</th>
                <?php if ($display_prior_value) : ?> <th>Prior Value</th> <?php endif; ?>
                <th>Date</th>
                <th>Time</th>
                <th>Editor</th>
            </tr>
        </thead>
        <tbody>
    <?php
    foreach ($revision_array as $revision) :
        $date_and_time = DateTime::createFromFormat(QSC_CORE_DATE_AND_TIME_FORMAT, $revision->getDateAndTime());
        $user = $db_curriculum->getUserFromID($revision->getUserID());
        ?>
            <tr>
                <td><?= $revision->getComponentNameAndLink($db_curriculum); ?></td>
                <td><?= ucfirst($revision->getAction()); ?></td>
            <?php if ($revision->isEdit()) : ?>
                    <td><?= $revision->getPropertyName(); ?></td>
                    <?php if ($display_prior_value) : ?> <td><?= $revision->getPriorValueName($db_curriculum); ?></td> <?php endif; ?>
            <?php else: ?>
                    <td></td>
                    <?php if ($display_prior_value) : ?> <td></td> <?php endif; ?>
            <?php endif; ?>
                <td><?= $date_and_time->format("M j, Y"); ?></td>
                <td><?= $date_and_time->format("g:i:sA"); ?></td>
                <td><?= $user->getFirstName(); ?> <?= $user->getLastName(); ?></td>
            </tr>
    <?php endforeach; ?>
        </tbody>
    </table>   
<?php
}

/**
 * 
 * @param type $faculty_array
 * @param type $db_curriculum
 */
function qsc_cmp_display_faculty_and_department_table($faculty_array, $db_curriculum) {
    if (empty($faculty_array)) {
        return;
    }
    ?>
    <table>
        <thead>
            <tr>
                <th>Faculty</th>
                <th>Department</th>
            </tr>
        </thead>
        <tbody>        
    <?php foreach ($faculty_array as $faculty) :
        $new_faculty = true;
        $department_array = $db_curriculum->getDepartmentsInFaculty($faculty->getDBID());
        $number_of_departments = count($department_array);
        
        if ($number_of_departments === 0) : ?>
            <tr>
                <td><?= $faculty->getAnchorToView(); ?></td>
                <td><?= QSC_CMP_TEXT_NONE; ?></td>
            </tr>
        <?php else :        
            foreach ($department_array as $department) : ?>
            <tr>
                <?php if ($new_faculty) : ?>
                <td rowspan="<?= $number_of_departments; ?>"><?= $faculty->getAnchorToView(); ?></td>
                <?php endif; ?>
                <td><?= $department->getAnchorToView(); ?></td>
            </tr>
            <?php
            $new_faculty = false;
            endforeach; 
        endif; 
    endforeach; ?>
        </tbody>
    </table>
    <?php
}

/**
 * 
 * @param type $program_array
 * @param type $db_curriculum
 * @param type $include_plans
 * @param type $include_departments
 * @return type
 */
function qsc_cmp_display_program_table($program_array, $db_curriculum, $include_plans = true, $include_departments = false) {
    if (empty($program_array)) {
        return;
    }
    ?>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Code</th>
                <?php if ($include_plans) : ?><th>Plan</th><?php endif; ?>
                <?php if ($include_departments) : ?><th>Department(s)</th><?php endif; ?>
            </tr>
        </thead>
        <tbody>        
    <?php foreach ($program_array as $program) : 
        $plan_anchor = '';
        $department_anchor_array = array();
        if ($include_plans) {
            $plan = $db_curriculum->getPlanForProgram($program->getDBID());
            $plan_anchor = ($plan) ? $plan->getAnchorToView() : ''; 
        }
        if ($include_departments) {
            $department_array = $db_curriculum->getDepartmentsForProgram($program->getDBID());
            $department_anchor_array = qsc_core_map_member_function($department_array, 'getAnchorToView'); 
        }?>            
            <tr>
                <td><?= $program->getAnchorToView(); ?></td>
                <td><?= $program->getCode(); ?></td>
                <?php if ($include_plans) : ?><td><?= $plan_anchor; ?></td><?php endif; ?>
                <?php if ($include_departments) : ?>
                <td>
                    <?php if (count($department_anchor_array) == 1) : ?>
                    <?= $department_anchor_array[0]; ?>
                    <?php elseif (! empty($department_anchor_array)) : ?>
                    <ul>
                        <?php foreach ($department_anchor_array as $department_anchor) : ?>
                        <li><?= $department_anchor; ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                </td>
                <?php endif; ?>
            </tr>
    <?php endforeach; ?>
        </tbody>
    </table>
    <?php
}

/**
 * 
 * @param type $degree_array
 */
function qsc_cmp_display_degree_table($degree_array) {
    if (empty($degree_array)) {
        return;
    }
    ?>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Code</th>
            </tr>
        </thead>
        <tbody>        
    <?php foreach ($degree_array as $degree) : ?>
            <tr>
                <td><?= $degree->getAnchorToView(); ?></td>
                <td><?= $degree->getCode(); ?></td>
            </tr>
    <?php endforeach; ?>
        </tbody>
    </table>
    <?php
}

/**
 * 
 * @param type $faculty_array
 */
function qsc_cmp_display_faculty_table($faculty_array) {
    if (empty($faculty_array)) {
        return;
    }
    ?>
    <table>
        <thead>
            <tr>
                <th>Faculty</th>
            </tr>
        </thead>
        <tbody>        
    <?php foreach ($faculty_array as $faculty) : ?>
            <tr>
                <td><?= $faculty->getAnchorToView(); ?></td>
            </tr>
    <?php endforeach; ?>
        </tbody>
    </table>
    <?php
}

/**
 * 
 * @param type $department_array
 * @param type $db_curriculum
 * @param type $include_faculties
 * @param type $faculty_id_exclude
 * @param type $plan_id
 * @return type
 */
function qsc_cmp_display_department_table($department_array, $db_curriculum, $include_faculties = true, $faculty_id_exclude = 0, $plan_id = 0) {
    if (empty($department_array)) {
        return;
    }
    ?>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <?php if ($plan_id) : ?><th>Role</th><?php endif; ?>
                <?php if ($include_faculties) : ?><th><?= ($faculty_id_exclude) ? "Other " : ""; ?>Faculties</th><?php endif; ?>
            </tr>
        </thead>
        <tbody>
    <?php foreach ($department_array as $department) : 
        $faculty_array = array();
        $faculty_anchor_array = array();
        
        $plan_role = '';

        if ($include_faculties) {
            $faculty_array = $db_curriculum->getFacultiesFromDepartment($department->getDBID(), $faculty_id_exclude);
            $faculty_anchor_array = qsc_core_map_member_function($faculty_array, 'getAnchorToView');
        }
        if ($plan_id) {
            $plan_role = $db_curriculum->getRoleForDepartmentAndPlan($department->getDBID(), $plan_id);
        }
    ?>
            <tr>
                <td><?= $department->getAnchorToView(); ?></td>
                <?php if ($plan_id) : ?><td><?= $plan_role; ?></td><?php endif; ?>
                <?php if ($include_faculties) : ?><td><?= implode("<br/>", $faculty_anchor_array); ?></td><?php endif; ?>
            </tr>
    <?php endforeach; ?>
        </tbody>
    </table>
    <?php
}

/**
 * 
 * @param type $subject_array
 * @param type $db_curriculum
 * @param type $db_calendar
 */
function qsc_cmp_display_subject_and_course_table($subject_array, $db_curriculum, $db_calendar) {
    if (empty($subject_array)) {
        return;
    }
    ?>
    <table>
        <thead>
            <tr>
                <th>Subject</th>
                <th>Course Code</th>
                <th>Course Name</th>
            </tr>
        </thead>
        <tbody>
    <?php foreach ($subject_array as $subject) :
        $new_subject = true;
        $course_array = $db_curriculum->getCoursesWithSubject($subject);
        $number_of_courses = count($course_array);
        
        if ($number_of_courses === 0) : ?>
            <tr>
                <td><?= qsc_cmp_get_anchor_to_view_subject($subject); ?></td>
                <td><?= QSC_CMP_TEXT_NONE; ?></td>
            </tr>
        <?php else :        
            foreach ($course_array as $course) : ?>
            <tr>
                <?php if ($new_subject) : ?>
                <td rowspan="<?= $number_of_courses; ?>"><?= qsc_cmp_get_anchor_to_view_subject($subject); ?></td>
                <?php endif; ?>
                <td><?= $course->getAnchorToView(); ?></td>
                <td><?= $course->getCalendarName($db_calendar); ?></td>
            </tr>
            <?php
            $new_subject = false;
            endforeach; 
        endif; 
    endforeach; ?>
        </tbody>
    </table>
    <?php
}

/**
 * 
 * @param type $course_array
 * @param type $db_calendar
 */
function qsc_cmp_display_course_table($course_array, $db_calendar) {
    if (empty($course_array)) {
        return;
    }
    ?>
    <table>
        <thead>
            <tr>
                <th>Code</th>
                <th>Name</th>
            </tr>
        </thead>
        <tbody>
    <?php foreach ($course_array as $course) : ?>
            <tr>
                <td><?= $course->getAnchorToView(); ?></td>
                <td><?= $course->getCalendarName($db_calendar); ?></td>
            </tr>
    <?php endforeach; ?>
        </tbody>
    </table>
    <?php
}

/**
 * 
 * @param type $plan_array
 * @param type $db_curriculum
 * @param type $department_id
 */
function qsc_cmp_display_plan_table($plan_array, $db_curriculum = null, $department_id = 0) {
    if (empty($plan_array)) {
        return;
    }
?>
    <table>
        <thead>
            <tr>
                <?php if ($department_id) : ?><th>Role</th><?php endif; ?>
                <th>Name</th>
                <th>Code</th>
                <th>Sub-Plans</th>
                <?php if (! $department_id) : ?>
                <th>Administrator</th>
                <th>Partners</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($plan_array as $plan) : 
                $role = '';
                $admin_dept_array = array();
                $partner_dept_array = array();
                $sub_plan_anchor_array = array();
                
                if ($department_id) {
                    $role = $db_curriculum->getRoleForDepartmentAndPlan($department_id, $plan->getDBID());
                }
                else if ($db_curriculum) {
                    $admin_dept_array = $db_curriculum->getAdministrativeDepartmentsForPlan($plan->getDBID());
                    $partner_dept_array = $db_curriculum->getPartnerDepartmentsForPlan($plan->getDBID());                        
                }
                ?>
            <tr>
                <?php if ($department_id) : ?><td><?= $role; ?></td><?php endif; ?>
                <td><a href="<?= $plan->getLinkToView() ?>"><?= $plan->getName() ?></a></td>
                <td><?= $plan->isSubPlan() ? $plan->getCode() : $plan->getFullCode(); ?></td>
                <td><?php if ($plan->hasSubPlans()) : 
                        $sub_plan_array = $plan->getSubPlanArray(); ?>
                    <ul>
                        <?php foreach ($sub_plan_array as $sub_plan) : ?>
                        <li><?= $sub_plan->getAnchorToView(); ?></li>
                        <?php endforeach; ?>                        
                    </ul>
                    <?php else : echo QSC_CMP_TEXT_NONE; 
                    endif; ?>
                </td>
                <?php if (! $department_id) : ?>
                <td>
                    <?php if (empty($admin_dept_array)) : 
                        echo QSC_CMP_TEXT_NONE_SPECIFIED;
                    elseif (count($admin_dept_array) == 1) : ?>
                    <?= $admin_dept_array[0]->getAnchorToView(); ?>
                    <?php else : ?>
                    <ul>
                        <?php foreach ($admin_dept_array as $admin_dept) : ?>
                        <li><?= $admin_dept->getAnchorToView(); ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if (empty($partner_dept_array)) : 
                        echo QSC_CMP_TEXT_NONE;
                    elseif (count($partner_dept_array) == 1) : ?>
                    <?= $partner_dept_array[0]->getAnchorToView(); ?>
                    <?php else : ?>
                    <ul>
                        <?php foreach ($partner_dept_array as $partner_dept) : ?>
                        <li><?= $partner_dept->getAnchorToView(); ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>                    
                </td>
                <?php endif; ?>              
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>        
<?php
}

/**
 * 
 * @param type $pllo_array
 * @param type $db_curriculum
 */
function qsc_cmp_display_pllo_table($pllo_array, $db_curriculum) {
    if (empty($pllo_array)) {
        return;
    }
?>
    <table>
        <thead>
            <tr>
                <th>Plan(s)</th>
                <th>Number</th>
                <th>Text</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($pllo_array as $pllo) : 
                $plan_array = $db_curriculum->getPlansFromPLLO($pllo->getDBID());
                $plan_anchor_array = qsc_core_map_member_function($plan_array, 'getAnchorToView'); ?>                        
            <tr>
                <td><?= implode('<br/>', $plan_anchor_array); ?></td>
                <td><?= $pllo->getAnchorToView(); ?></td>
                <td><?= $pllo->getText(); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>        
<?php
}

/**
 * 
 * @param type $ilo_array
 */
function qsc_cmp_display_ilo_table($ilo_array) {
    if (empty($ilo_array)) {
        return;
    }
?>
    <table>
        <thead>
            <tr>
                <th>Number</th>
                <th>Text</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($ilo_array as $ilo) : ?>
            <tr>
                <td><?= $ilo->getAnchorToView(); ?></td>
                <td><?= $ilo->getText(); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php
}

/**
 * 
 * @param type $cllo_array
 * @param type $db_curriculum
 * @param type $db_calendar
 * @param type $displayCourse
 * @param type $displayPLLOs
 * @param type $indentChildren
 */
function qsc_cmp_display_cllo_table($cllo_array, $db_curriculum = null, $db_calendar = null, $displayCourse = false, $displayPLLOs = false, $indentChildren = false) {
    if (empty($cllo_array)) {
        return;
    }
?>
    <table>
        <thead>
            <tr>
                <?php if ($displayCourse) : ?><th>Course</th><?php endif; ?>
                <th>Number</th>
                <th>Type</th>
                <?php if ($displayPLLOs) : ?><th>PLLOs</th><?php endif; ?>
                <th>Text</th>
            </tr>
        </thead>
        <tbody>
    <?php foreach ($cllo_array as $cllo) : 
        $course = ($displayCourse) ? $db_curriculum->getCourseForCLLO($cllo->getDBID()) : null;
        
        $cllo_pllo_array = ($displayPLLOs) ? $db_curriculum->getDirectPLLOsForCLLOs(array($cllo->getDBID())) : null;
        $cllo_pllo_last_index = ($displayPLLOs) ? count($cllo_pllo_array) - 1 : 0;
        
        $tr_tag = ($indentChildren && $cllo->hasParent()) ? 'tr class="indent"' : 'tr';
        ?>
            <<?= $tr_tag; ?>>
        <?php if ($displayCourse) : ?><td><?= $course->getAnchorToView($db_calendar); ?></td><?php endif; ?>
                <td><?= $cllo->getAnchorToView(); ?></td>                
                <td><?= $cllo->getType(); ?></td>
        <?php if($displayPLLOs) : ?>
                <td>
            <?php foreach ($cllo_pllo_array as $index => $cllo_pllo) : ?>
                <?= $cllo_pllo->getAnchorToView(); ?><?php if ($index < $cllo_pllo_last_index) { ?><br/><?php } ?>
            <?php endforeach; ?>
                </td>
        <?php endif; ?>                
                <td><?= $cllo->getText(); ?></td>            
            </tr>
    <?php endforeach; ?> 
        </tbody>
    </table>    
    <?php
}

/**
 * 
 * @param type $plan_id
 * @param type $cpr_number
 * @param type $cpr_type
 * @param type $db_curriculum
 * @param type $display_if_none
 * @return boolean
 */
function qsc_cmp_display_cpr_table_for_plan($plan_id, $cpr_number, $cpr_type, $db_curriculum, $display_if_none = true) {
    $cpr_array = $db_curriculum->getCPRsForPlan($plan_id, $cpr_type);
    $plan_units = $db_curriculum->getTotalUnitsInPlan($plan_id, $cpr_type);
    
    if (empty($cpr_array) && (! $display_if_none)) {
        return false;
    }
    ?>
<table class="plan-requirements">
    <thead>
        <tr>
            <th><?= $cpr_number; ?>. <?= $cpr_type; ?></th>
            <th><?= ($plan_units) ? $plan_units : '0.0' ; ?> units</th>
            <th><span class="sr-only">Connector</span></th>
            <th><span class="sr-only">Courses</span></th>
            <th><span class="sr-only">Link to View Course List</span></th>
        </tr>        
    </thead>
    <tbody>
    <?php if (empty($cpr_array)) : ?>
        <tr>
            <td colspan="6"><?= QSC_CMP_TEXT_NONE_SPECIFIED; ?></td>
        </tr>
    <?php else :
        foreach ($cpr_array as $cpr) :
            $course_list = $db_curriculum->getCourseListForCPR($cpr->getDBID());
        ?>
        <tr>
            <td><?= $cpr->getAnchorToView(); ?>.</td>
            <td><?= $cpr->getUnits(); ?> units</td>
            <td><?= $cpr->getConnector(); ?></td>
            <?php if (! $course_list) : ?>
            <td colspan="2"><?= QSC_CMP_TEXT_NONE_SPECIFIED ?></td>
            <?php else: ?>
            <td><?= $course_list->getHTML(); ?></td>
            <td><?php qsc_cmp_display_link_button($course_list->getLinkToView(), "View Course List"); ?></td>
            <?php endif; ?>
        </tr>
        <?php endforeach; 
    endif; ?>        
    </tbody>
</table>
<?php
    return true;
}

/**
 * 
 * @param type $tpr_number
 * @param type $tpr_type
 * @param type $tpr_array
 */
function qsc_cmp_display_tpr_table_for_plan($tpr_number, $tpr_type, $tpr_array) {
    if (empty($tpr_array)) {
        return;
    }    
?>
<table class="plan-requirements">
    <thead>
        <tr>
            <th><?= $tpr_number; ?>. <?= $tpr_type; ?></th>
            <th><span class="sr-only">Description</span></th>
        </tr>        
    </thead>
    <tbody>
    <?php if (empty($tpr_array)) : ?>
        <tr>
            <td colspan="2"><?= QSC_CMP_TEXT_NONE_SPECIFIED; ?></td>
        </tr>
    <?php else :
        foreach ($tpr_array as $tpr) : ?>
        <tr>
            <td><?= $tpr->getAnchorToView(); ?>.</td>
            <td><?= $tpr->getText(); ?></td>
        </tr>
        <?php endforeach; 
    endif; ?>        
    </tbody>
</table>
<?php
}

/**
 * 
 * @param type $plan
 * @param type $db_curriculum
 */
function qsc_cmp_display_plan_requirements($plan, $db_curriculum) {
    $type_number = QSC_CMP_PLAN_REQUIREMENT_START_NUMBER;
    $sub_plan_letter = QSC_CMP_SUB_PLAN_START_LETTER;
    
    $plan_id = $plan->getDBID();
    
    $cpr_type_array = CMD::getCPRTypes();
    $tpr_type_array = CMD::getTPRTypes();
    
    if ($plan->hasSubPlans()) {
        // Get the sub-plans
        $sub_plan_array = $db_curriculum->getSubPlans($plan_id);
        
        // Figure out the total number of units; it's the same for all
        // sub-plans, so start with the first
        $plan_sub_units = $db_curriculum->getTotalUnitsInPlan($sub_plan_array[0]->getDBID());
        
        // Start with the Core CPRs
        qsc_cmp_display_cpr_table_for_plan($plan_id, $type_number, CMD::TABLE_CPR_TYPE_CORE, $db_curriculum);
        $type_number++;
        
        // Move onto the sub-plans 
        ?>
        <div class="sub-plan-requirements-header">
            <span class="title"><?= $type_number ?>. Sub-Plans</span> <?= $plan_sub_units ?> units
        </div>
        
        <?php
        foreach ($sub_plan_array as $sub_plan) : 
            $sub_plan_units = $db_curriculum->getTotalUnitsInPlan($sub_plan->getDBID());
            ?>
        <div class="sub-plan-requirements">
            <div class="plan-title"><span class="name"><?= $sub_plan_letter ?>. <?= $sub_plan->getAnchorToView() ?></span> <?= $sub_plan_units ?> units</div>
            <?php qsc_cmp_display_sub_plan_requirements($sub_plan->getDBID(), $db_curriculum); 
            $sub_plan_letter++; ?>
        </div> <!-- .sub-plan -->        
        <?php endforeach;
        $type_number++;        
        
        // End with the Supporting CPRs
        qsc_cmp_display_cpr_table_for_plan($plan_id, $type_number, CMD::TABLE_CPR_TYPE_SUPPPORTING, $db_curriculum);
        $type_number++;        
    }
    else {
        foreach ($cpr_type_array as $cpr_type) {
            qsc_cmp_display_cpr_table_for_plan($plan_id, $type_number, $cpr_type, $db_curriculum);
            $type_number++;
        }
    }

    foreach ($tpr_type_array as $tpr_type) {
        $tpr_array = $db_curriculum->getTPRsForPlan($plan_id, $tpr_type);
        qsc_cmp_display_tpr_table_for_plan($type_number, $tpr_type, $tpr_array);
        $type_number++;
    }
}

/**
 * 
 * @param type $plan_id
 * @param type $db_curriculum
 */
function qsc_cmp_display_sub_plan_requirements($plan_id, $db_curriculum) {
    $type_number = QSC_CMP_SUB_PLAN_REQUIREMENT_START_NUMBER;
    $type_numeral = qsc_core_convert_decimal_to_roman_numeral($type_number);
    
    $cpr_type_array = CMD::getCPRTypes();
    $tpr_type_array = CMD::getTPRTypes();
    
    foreach ($cpr_type_array as $cpr_type) {
        if (qsc_cmp_display_cpr_table_for_plan($plan_id, $type_numeral, $cpr_type, $db_curriculum, false)) {
            $type_number++;
            $type_numeral = qsc_core_convert_decimal_to_roman_numeral($type_number);
        }
    }

    foreach ($tpr_type_array as $tpr_type) {
        $tpr_array = $db_curriculum->getTPRsForPlan($plan_id, $tpr_type);
        if (! empty($tpr_array)) {
            qsc_cmp_display_tpr_table_for_plan($type_numeral, $tpr_type, $tpr_array);
            $type_number++;
            $type_numeral = qsc_core_convert_decimal_to_roman_numeral($type_number);
        }
    }

}

/**
 * 
 *
 * @param $subject      The string subject for the message to distinguish it in
 *                      the PHP error log
 * @param $message      The string message to log and display
 */
function qsc_cmp_log_issue($subject, $message) {
    qsc_core_log_issue($subject, $message);
    SessionManager::setErrorMessage($message);
}

/**
 * 
 * @param type $form_type
 * @param type $submit_button_text
 * @param type $hidden_controls
 */
function qsc_cmp_display_submit_group($form_type, $submit_button_text, $hidden_controls = array()) {
?>
    <input type="hidden" name="<?php echo QSC_CMP_FORM_TYPE; ?>" id="<?php echo QSC_CMP_FORM_TYPE; ?>" value="<?php echo $form_type; ?>"/>
    <?php foreach ($hidden_controls as $id => $value) : ?>
    <input type="hidden" name="<?php echo $id;?>" id="<?php echo $id;?>" value="<?php echo $value; ?>"/>
    <?php endforeach; ?>
    <input type="hidden" name="<?php echo QSC_CMP_FORM_JS_COMPLETED_ON_SUBMISSION;?>" id="<?php echo QSC_CMP_FORM_JS_COMPLETED_ON_SUBMISSION;?>" value=""/>
    <input type="submit" value="<?php echo $submit_button_text; ?>">
<?php
}

/**
 * 
 * @param type $id
 * @param type $heading
 */
function qsc_cmp_display_search_results_block($id, $heading) {
?>
    <div id="<?= $id; ?>" class="col-lg-6 search-results-outer">
        <div class="search-results-inner">
            <h2><?= $heading; ?></h2>
            <ul>
                <li>No results found.</li>
            </ul>
        </div>
    </div>
<?php
}

/**
 * 
 * @param type $property_and_value_array
 * @param type $multi_column
 */
function qsc_cmp_display_property_columns($property_and_value_array, $multi_column = false) {
    $property_class = ($multi_column) ? "col-xl-4 col-lg-5" : "col-xl-2 col-lg-3";
    $value_class = ($multi_column) ? "col-xl-8 col-lg-7" : "col-xl-10 col-lg-9";
    
?>
<div class="property-columns">
<?php foreach ($property_and_value_array as $property => $value): ?>
    <div class="row">
        <div class="<?= $property_class; ?> property"><?= $property; ?></div>
        <div class="<?= $value_class; ?> value"><?= ($value) ? $value : QSC_CMP_TEXT_NONE_SPECIFIED; ?></div>
    </div>
<?php endforeach; ?>        
</div>    
<?php
}

/**
 * 
 * @param type $href
 * @param type $text
 * @return type
 */
function qsc_cmp_get_link_button($href, $text) {
    return "<a class=\"link-button\" href=\"$href\">$text</a>";
}

/**
 * 
 * @param type $href
 * @param type $text
 */
function qsc_cmp_display_link_button($href, $text) {
    echo qsc_cmp_get_link_button($href, $text); 
}
