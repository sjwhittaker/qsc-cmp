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
use Managers\CurriculumMappingDatabase as CMD;

qsc_cmp_start_page_load();

qsc_cmp_start_html(array(QSC_CMP_START_HTML_TITLE => "Dashboard"));

$db_curriculum = new CMD();

$user_revision_array = $db_curriculum->getLatestRevisions(
    SessionManager::getUserID());
$others_revision_array = $db_curriculum->getLatestRevisions(
    SessionManager::getUserID(), false);
?>

<h1>Welcome, <?= SessionManager::getUserFirstName(); ?>.</h1>

<h2>You Recently Edited</h2>
<?php if (empty($user_revision_array)) : ?>
<p>You have not yet made any changes in the system.</p>
<?php else :
    qsc_cmp_display_revision_table($user_revision_array, $db_curriculum);
    endif; 
?>

<h2>Others Recently Edited</h2>
<?php if (empty($others_revision_array)) : ?>
<p>No other user has made any changes in the system.</p>
<?php else : 
    qsc_cmp_display_revision_table($others_revision_array, $db_curriculum);
endif;

qsc_cmp_end_html();

qsc_cmp_end_page_load();
?>
