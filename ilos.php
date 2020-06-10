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

use Managers\CurriculumMappingDatabase as CMD;

qsc_cmp_start_page_load();

qsc_cmp_start_html(array(QSC_CMP_START_HTML_TITLE => "View All ILOs"));

$db_curriculum = new CMD();

$top_level_ilos_array = $db_curriculum->getTopLevelILOs();
?>

<h1>All ILOs</h1>

<?php if (empty($top_level_ilos_array)) : ?>
<p>There are presently no top-level/parent ILOs defined.</p>

<?php else: ?>

<ul>    
    <?php foreach ($top_level_ilos_array as $top_level_ilo) : ?>
    <li><?= $top_level_ilo->getAnchorToView(true); ?>
        <?php
        $child_ilos_array = $db_curriculum->getChildILOs($top_level_ilo->getDBID());
        if (! empty($child_ilos_array)) : ?>
        
        <ul>
        <?php foreach ($child_ilos_array as $child_ilo) : ?>
            <li><?= $child_ilo->getAnchorToView(true); ?></li>
        <?php endforeach; ?>
        </ul>
        <div class="ilo-description"><?= $top_level_ilo->getDescription();?></div>
    </li>
<?php endif;
    endforeach; ?>
</ul>
<?php endif;

qsc_cmp_end_html();

qsc_cmp_end_page_load();
?>