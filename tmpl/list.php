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

use Joomla\CMS\Uri\Uri;
use ValorApps\Module\EasyFolderListing\Site\Helper\EasyFolderListingHelper;


//get the total number of files
$total = count($rows); ?>
<ul class="easyfolderlisting<?php echo $params->get('moduleclass_sfx'); ?>" style="list-style:none;">
<?php for ($i = 0; $i < $total; $i++) : ?>
	<li>
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
	
	//show size?
	if ($params->get('efl_size') == true)
	{
		echo ' ['.$rows[$i]['size'].']';
	}
	
	//show date?
	if ($params->get('efl_date') == true)
	{
		echo ' ['.EasyFolderListingHelper::formatDate($params, $rows[$i]['date']).']';
	}
	?>
	</li>
<?php endfor; ?>
</ul>

