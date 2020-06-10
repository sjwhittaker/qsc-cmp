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

include_once('./config/config.php');

qsc_cmp_start_page_load();

qsc_cmp_start_html(array(QSC_CMP_START_HTML_TITLE => "Search",
    QSC_CMP_START_HTML_SCRIPTS => array(QSC_CMP_SCRIPT_SEARCH_LINK)));
?>

<h1>Search</h1>

<form id="<?= QSC_CMP_FORM_SEARCH; ?>">
    <div class="form-section">
        <input type="text" name="<?= QSC_CMP_FORM_SEARCH_TEXT; ?>" id="<?= QSC_CMP_FORM_SEARCH_TEXT; ?>" aria-describedby="<?= QSC_CMP_FORM_SEARCH_TEXT_HELP; ?>"/>
        <small id="<?= QSC_CMP_FORM_SEARCH_TEXT_HELP; ?>" class="form-text">Please type the search text and the results will dynamically appear on the page.</small>
    </div>

    <div class="form-section">
        <div class="row">
            <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                <?php qsc_core_form_display_checkbox("CLLOs", 
                    QSC_CMP_FORM_SEARCH_OPTION_CLLOS); ?>
            </div>
            <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                <?php qsc_core_form_display_checkbox("Courses", 
                    QSC_CMP_FORM_SEARCH_OPTION_COURSES, 
                    array(QSC_CORE_FORM_CHECKED => true)); ?>
            </div>
            <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                <?php qsc_core_form_display_checkbox("Degrees", 
                    QSC_CMP_FORM_SEARCH_OPTION_DEGREES); ?>
            </div>
            <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                <?php qsc_core_form_display_checkbox("DLEs", 
                    QSC_CMP_FORM_SEARCH_OPTION_DLES); ?>
            </div>
            <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                <?php qsc_core_form_display_checkbox("Departments", 
                    QSC_CMP_FORM_SEARCH_OPTION_DEPARTMENTS); ?>
            </div>
            <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                <?php qsc_core_form_display_checkbox("Faculties", 
                    QSC_CMP_FORM_SEARCH_OPTION_FACULTIES); ?>
            </div>
            <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                <?php qsc_core_form_display_checkbox("ILOs", 
                    QSC_CMP_FORM_SEARCH_OPTION_ILOS); ?>
            </div>
            <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                <?php qsc_core_form_display_checkbox("Plans", 
                    QSC_CMP_FORM_SEARCH_OPTION_PLANS); ?>
            </div>
            <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                <?php qsc_core_form_display_checkbox("PLLOs", 
                    QSC_CMP_FORM_SEARCH_OPTION_PLLOS); ?>
            </div>
            <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                <?php qsc_core_form_display_checkbox("Programs", 
                    QSC_CMP_FORM_SEARCH_OPTION_PROGRAMS); ?>
            </div>
            <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                <?php qsc_core_form_display_checkbox("Revisions", 
                    QSC_CMP_FORM_SEARCH_OPTION_REVISIONS); ?>
            </div>
        </div> <!-- .row -->
    </div> <!-- .form-section -->
</form>

<div class="row">
    <?php qsc_cmp_display_search_results_block(QSC_CMP_FORM_SEARCH_RESULTS_CLLOS, "CLLOs"); ?>
    <?php qsc_cmp_display_search_results_block(QSC_CMP_FORM_SEARCH_RESULTS_COURSES, "Courses"); ?>
    <?php qsc_cmp_display_search_results_block(QSC_CMP_FORM_SEARCH_RESULTS_DEGREES, "Degrees"); ?>
    <?php qsc_cmp_display_search_results_block(QSC_CMP_FORM_SEARCH_RESULTS_DEPARTMENTS, "Departments"); ?>
    <?php qsc_cmp_display_search_results_block(QSC_CMP_FORM_SEARCH_RESULTS_DLES, "DLEs"); ?>
    <?php qsc_cmp_display_search_results_block(QSC_CMP_FORM_SEARCH_RESULTS_FACULTIES, "Faculties"); ?>
    <?php qsc_cmp_display_search_results_block(QSC_CMP_FORM_SEARCH_RESULTS_ILOS, "ILOs"); ?>
    <?php qsc_cmp_display_search_results_block(QSC_CMP_FORM_SEARCH_RESULTS_PLANS, "Plans"); ?>
    <?php qsc_cmp_display_search_results_block(QSC_CMP_FORM_SEARCH_RESULTS_PLLOS, "PLLOs"); ?>
    <?php qsc_cmp_display_search_results_block(QSC_CMP_FORM_SEARCH_RESULTS_PROGRAMS, "Programs"); ?>
    <?php qsc_cmp_display_search_results_block(QSC_CMP_FORM_SEARCH_RESULTS_REVISIONS, "Revisions"); ?>
</div>

<div class="form-section">
    <i class="fas fa-question-circle" aria-hidden="true"></i>The search is conducted in individual fields using the entire value, including spaces. For example, &quot;nd sp&quot; will match:
    <ul>
        <li>the PLLO with &quot;and specify&quot; in its text;</li>
        <li>the ILO with &quot;and skills&quot; in its description; and,</li>
        <li>the faculty of &quot;Arts and Science&quot;.</li>
    </ul>
    The exception is for courses:
    <ul>
        <li>searching for &quot;CIS 10&quot; will return all courses with &quot;CIS&quot; in the code and &quot;10&quot; in the number.</li>        
    </ul>
</div>


<?php
qsc_cmp_end_html();

qsc_cmp_end_page_load();
?>
