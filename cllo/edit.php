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

use DatabaseObjects\CourseLevelLearningOutcome as CLLO;

qsc_cmp_start_page_load();

$db_curriculum = new CMD();
$db_calendar = new CCD();

$cllo_id = qsc_core_get_id_from_post(QSC_CMP_FORM_CLLO_ID);

if ($cllo_id === false) :
    qsc_cmp_start_html(array(
        QSC_CMP_START_HTML_TITLE => "Error Displaying CLLO"));
?>

<h1>Error Displaying Course Level Learning Outcome</h1>
    <?php qsc_core_log_and_display_error("The CLLO ID could not be extracted.");
else:
    $cllo = $db_curriculum->getCLLOFromID($cllo_id);
    if (! $cllo) :
        qsc_cmp_start_html(array(QSC_CMP_START_HTML_TITLE => "Error Finding CLLO"));
?>

<h1>Error Finding Course Level Learning Outcome</h1>
    <?php qsc_core_log_and_display_error("A CLLO with that ID could not be retrieved from the database.");
    else :
        // Get the course associated with the CLLO
        $course = $db_curriculum->getCourseForCLLO($cllo_id);
        
        qsc_cmp_start_html(array(
            QSC_CMP_START_HTML_TITLE => "Edit ".$cllo->getName()." for ".$course->getName(),
            QSC_CMP_START_HTML_SCRIPTS => array(QSC_CMP_SCRIPT_CLLO_FORMS_LINK)));
                
?>

<h1>Edit <?= $cllo->getName(); ?> for <?= $course->getName(); ?></h1>

<?php
        qsc_cmp_display_cllo_form(QSC_CMP_ACTION_EDIT_LINK, QSC_CMP_FORM_CLLO_EDIT, 
            QSC_CMP_FORM_TYPE_EDIT_CLLO, "Save Changes", $cllo); 
    endif;
endif;

qsc_cmp_end_html();

qsc_cmp_end_page_load();
?>
