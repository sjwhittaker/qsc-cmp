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

use Managers\SessionManager;


SessionManager::initialize();

qsc_cmp_start_html(array(QSC_CMP_START_HTML_TITLE => "Error"));
?>

<h1><i class="fas fa-exclamation-triangle" aria-hidden="true"></i>Sorry, but something's gone very wrong.</h1>

<p>A significant error which could not be managed gracefully has occurred. The last recorded error information is as follows:</p>

<?php qsc_core_display_message(SessionManager::getErrorMessage(), QSC_CORE_MESSAGE_TYPE_ERROR); ?>

<p>If possible, please don't try logging in again or using the system until the bug can be fixed.</p>

<?php
qsc_cmp_end_html();

qsc_cmp_log_user_out();
?>
