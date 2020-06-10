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

use DatabaseObjects\Faculty;
use DatabaseObjects\Department;
use DatabaseObjects\Degree;

qsc_cmp_start_page_load();

$db_curriculum = new CMD();

$faculty_id = qsc_core_get_id_from_get();

if ($faculty_id === false) :
    qsc_cmp_start_html(array(QSC_CMP_START_HTML_TITLE => "Error Displaying Faculty"));
?>

<h1>Error Displaying Faculty</h1>
    <?php qsc_core_log_and_display_error("The faculty ID could not be extracted as an integer from the URL.");
else:
    $faculty = $db_curriculum->getFacultyFromID($faculty_id);
    if (! $faculty) :
        qsc_cmp_start_html(array(QSC_CMP_START_HTML_TITLE => "Error Finding Faculty"));
?>

<h1>Error Finding Faculty</h1>
    <?php qsc_core_log_and_display_error("A faculty with that ID could not be retrieved from the database.");
    else :
        qsc_cmp_start_html(array(QSC_CMP_START_HTML_TITLE => "View ".$faculty->getName()));
    
        $department_array = $db_curriculum->getDepartmentsInFaculty($faculty_id);
        
        $program_array = $db_curriculum->getProgramsInFaculty($faculty_id);
        
        $degree_array = $db_curriculum->getDegreesInFaculty($faculty_id);
        ?>

<h1><?= $faculty->getName();?></h1>

<div class="row">
    <div class="col-lg-4">
        <h2>Departments</h2>
        <?php if (empty($department_array)) : ?>
        <p>There are no departments listed for this faculty.</p>
        <?php else : 
            qsc_cmp_display_department_table($department_array, $db_curriculum, false);
        endif; ?>
    </div> <!-- .col-lg-4 -->
    <div class="col-lg-8">
        <h2>Programs</h2>
        <?php if (empty($program_array)) : ?>
        <p>There are no programs associated with this faculty.</p>
        <?php else : 
            qsc_cmp_display_program_table($program_array, $db_curriculum);
        endif; ?>        
    </div> <!-- .col-lg-8 -->
    <div class="col-lg-6">
        <h2>Degrees</h2>
        <?php if (empty($degree_array)) : ?>
        <p>There are no degrees associated with this faculty.</p>
        <?php else : 
            qsc_cmp_display_degree_table($degree_array);
        endif; ?>        
    </div> <!-- .col-lg-6 -->
</div> <!-- .row -->
    <?php endif;
endif;

qsc_cmp_end_html();

qsc_cmp_end_page_load();
?>
