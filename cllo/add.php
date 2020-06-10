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

qsc_cmp_start_page_load();

qsc_cmp_start_html(
    array(
        QSC_CMP_START_HTML_TITLE => "Add New Course Level Learning Outcome",
        QSC_CMP_START_HTML_SCRIPTS => array(QSC_CMP_SCRIPT_CLLO_FORMS_LINK)
    )
);
?>

<h1>Add a New CLLO</h1>

<?php qsc_cmp_display_cllo_form(QSC_CMP_ACTION_ADD_LINK, QSC_CMP_FORM_CLLO_ADD, 
    QSC_CMP_FORM_TYPE_ADD_CLLO, "Add"); 

qsc_cmp_end_html();

qsc_cmp_end_page_load();
?>
