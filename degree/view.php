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
use DatabaseObjects\Degree;

qsc_cmp_start_page_load();

$db_curriculum = new CMD();
$db_calendar = new CCD();

$degree_id = qsc_core_get_id_from_get();

if ($degree_id === false) :
    qsc_cmp_start_html(array(QSC_CMP_START_HTML_TITLE => "Error Displaying Degree"));
?>

<h1>Error Displaying Degree</h1>
    <?php qsc_core_log_and_display_error("The degree ID could not be extracted as an integer from the URL.");
else:
    $degree = $db_curriculum->getDegreeFromID($degree_id);
    if (! $degree) :
        qsc_cmp_start_html(array(QSC_CMP_START_HTML_TITLE => "Error Finding Degree"));
?>

<h1>Error Finding Degree</h1>
    <?php qsc_core_log_and_display_error("A degree with that ID could not be retrieved from the database.");
    else :
        qsc_cmp_start_html(array(QSC_CMP_START_HTML_TITLE => "View ".$degree->getName()));
    
        $faculty_array = $db_curriculum->getFacultiesFromDegree($degree_id);
        $faculty_anchor_array = qsc_core_map_member_function($faculty_array, 'getAnchorToView');
        
        $program_array = $db_curriculum->getProgramsFromDegree($degree_id);
        ?>

<h1><?= $degree->getName();?></h1>

        <?php qsc_cmp_display_property_columns(array(
            "Code" => $degree->getCode(),
            "Honours" => ($degree->isHonours()) ? "Yes" : "No",
            "Faculties" => implode("<br/>", $faculty_anchor_array)
            )
        ); ?>

<h2>Programs and Plans</h2>
        <?php if (empty($program_array)) : ?>
<p>There are no programs associated with this degree.</p>
        <?php else : 
            qsc_cmp_display_program_table($program_array, $db_curriculum);
        endif;
    endif;
endif;

qsc_cmp_end_html();

qsc_cmp_end_page_load();
?>
