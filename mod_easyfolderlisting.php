<?php
/**
* @version		3.0.3
* @author		Michael A. Gilkes (jaido7@yahoo.com)
* @copyright	Michael Albert Gilkes
* @license		GNU/GPLv2
*/

/*

Easy Folder Listing Module for Joomla!
Copyright (C) 2010-2019 Michael Albert Gilkes

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

// no direct access
defined('_JEXEC') or die('Restricted access');

// Include the Helper functions only once
require_once dirname(__FILE__).DIRECTORY_SEPARATOR.'Helper'.DIRECTORY_SEPARATOR.'EasyFolderListingHelper.php';

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;
use ValorApps\Module\EasyFolderListing\Site\Helper\EasyFolderListingHelper;

//get the module class designation
$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

//get the reference to the Joomla application
$app = Factory::getApplication();

try
{
	//specify the folder
	$folder = EasyFolderListingHelper::folderPath($params);
	//Check to see if this is being called from Admin
	if($app->isAdmin())
	{
		//set the working directory to the Joomla root
		chdir("..");
	}

	//get the formatted listing as an array
	$rows = EasyFolderListingHelper::getFormattedListing($params, $folder);

	//Check to see if this is being called from Admin
	if($app->isAdmin())
	{
		//return the working directory to what it was before
		chdir("administrator");
	}

	//use the html table
	$layout = 'default';

	//format the display
	if ($params->get('efl_method') == "list")
	{
		//use the unordered list
		$layout = 'list';
	}
	if ($params->get('efl_method') == "layout")
	{
		//set the layout based on the Alternative Layout option in the Advanced Parameters
		$layout = $params->get('layout', 'default');
	}
			
	require ModuleHelper::getLayoutPath('mod_easyfolderlisting', $layout);
}
catch (Exception $e)
{
	//display the error message on the webpage instead of crashing the page
	$app->enqueueMessage($e->getMessage(), 'error');
}