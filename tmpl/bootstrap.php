<?php
/**
* @version		3.0
* @author		Michael A. Gilkes (jaido7@yahoo.com)
* @copyright	Michael Albert Gilkes
* @license		GNU/GPLv2
*/

/*

Easy Folder Listing Module for Joomla!
Copyright (C) 2010-2019  Michael Albert Gilkes

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

*/

//no direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use ValorApps\Module\EasyFolderListing\Site\Helper\EasyFolderListingHelper;


//get the total number of files
$total = count($rows); 

//Note: All custom styling is ignored in this layout.
?>

<table class="table <?php echo $params->get('moduleclass_sfx');?>" style="width:100%; text-align:left;">
	<tr>
		<th style="padding:4px;"><?php echo Text::_('MOD_EFL_FILENAME');?></th>
		<?php if ($params->get('efl_size') == true) : ?>
		<th style="padding:4px;"><?php echo Text::_('MOD_EFL_SIZE');?></th>
		<?php endif; ?>
		<?php if ($params->get('efl_date') == true) : ?>
		<th style="padding:4px;"><?php echo (($params->get('efl_time') == true) ? Text::_('MOD_EFL_DATETIME') : Text::_('MOD_EFL_DATE')); ?></th>
		<?php endif; ?>
	</tr>
	<?php for ($i = 0; $i < $total; $i++) : ?>
	<tr>
		<td style="padding:1px;">
		<?php
		//show icons?
		if ($params->get('efl_icons') == true)
		{
			echo EasyFolderListingHelper::attachIcon($rows[$i]['ext']);
		}
		
		//fix the name
		$fixedName = EasyFolderListingHelper::fixLang($params, $rows[$i]['name']);
		
		//link it?
		if ($params->get('efl_linktofiles') == true)
		{
			$target = '';
			if ($params->get('efl_linktoblank') == true)
			{
				$target = 'target="_blank"';
			}
			echo '<a '.$target.' href="'.Uri::base().$folder.'/'.$fixedName.'.'.$rows[$i]['ext'].'">';
		}
		
		//show the file's name
		echo $fixedName;
		
		//show extension?
		if ($params->get('efl_extensions') == true)
		{
			echo '.'.$rows[$i]['ext'];
		}
		
		//close the tag, if we are linking it
		if ($params->get('efl_linktofiles') == true)
		{
			echo '</a>';
		}
		?>
		</td>
		<?php
		//show size?
		if ($params->get('efl_size') == true) : ?>
		<td style="padding:1px;"><?php echo $rows[$i]['size']; ?></td>
		<?php endif; 
		//show date?
		if ($params->get('efl_date') == true) : ?>
		<td style="padding:1px;"><?php echo EasyFolderListingHelper::formatDate($params, $rows[$i]['date']); ?></td>
		<?php endif; ?>
	</tr>
	<?php endfor; ?>
</table>
