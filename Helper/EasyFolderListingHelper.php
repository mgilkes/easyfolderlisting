<?php
/**
* @version		3.0.2
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

namespace ValorApps\Module\EasyFolderListing\Site\Helper;

// no direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;


class EasyFolderListingHelper
{
	public static function fixLang(&$params, $text)
	{
		if ($params->get('efl_encodeutf8') == 'translit')
		{
			return iconv($params->get('efl_srcencoding'), 'UTF-8//TRANSLIT', $text);
		}
		else if ($params->get('efl_encodeutf8') == 'plain')
		{
			return iconv($params->get('efl_srcencoding'), 'UTF-8', $text);
		}
		else if ($params->get('efl_encodeutf8') == 'ignore')
		{
			return iconv($params->get('efl_srcencoding'), 'UTF-8//IGNORE', $text);
		}
		else if ($params->get('efl_encodeutf8') == 'encode')
		{
			return utf8_encode($text);
		}
		else //none
		{
			return $text;
		}
	}
	
	//this method retieves a formatted listing of the file names,
	//  which may include file extensions, modified dates, and size.
	//  it returns html text of the information
	public static function getFormattedListing(&$params, $folder)
	{
		$list = array();
		
		//get a list of all the files in the folder as a DirectoryIterator object
		$original = new \DirectoryIterator($folder);
		
		//create the final list that contains: name, ext, size, date
		$index = 0;
		foreach ($original as $key => $item)
		{
			//we're only listing files
			if ($item->isFile())
			{
				//check to see if the file type is forbidden
				if (stripos($params->get('efl_forbidden'), $item->getExtension()) === false)
				{
					//remove the file extension and the dot from the filename
					$list[$index]['name'] = substr($item->getFilename(), 0, -1*(1+strlen($item->getExtension())));
					$list[$index]['ext'] = $item->getExtension();
					$list[$index]['size'] = EasyFolderListingHelper::sizeToText($item->getSize());
					$list[$index]['bytes'] = $item->getSize();
					$list[$index]['date'] = $item->getMTime();
					$index++;
				}
			}
		}
		//sort the array in ascending order
		if ($params->get('efl_sortcolumn') == "name")
		{
			usort($list, "ValorApps\Module\EasyFolderListing\Site\Helper\EasyFolderListingHelper::compareName");
		}
		elseif ($params->get('efl_sortcolumn') == "size")
		{
			usort($list, "ValorApps\Module\EasyFolderListing\Site\Helper\EasyFolderListingHelper::compareSize");
		}
		elseif ($params->get('efl_sortcolumn') == "date")
		{
			usort($list, "ValorApps\Module\EasyFolderListing\Site\Helper\EasyFolderListingHelper::compareDate");
		}
		
		//sort in descending order
		if ($params->get('efl_sortdirection') == "desc")
		{
			$list = array_reverse($list);
		}
		
		//reset the array pointer
		reset($list);
		
		//find the maximum number of files to be displayed
		$max = (int)$params->get('efl_numberfiles');
		
		if ($max > 0 && $index >= $max)
		{
			$list = array_slice($list, 0, $max);
		}
		
		return $list;
	}
	
	public static function compareName($x, $y)
	{
		return strnatcmp($x['name'], $y['name']);
	}
	
	public static function compareSize($x, $y)
	{
		if ($x['bytes'] == $y['bytes'])
		{
			return 0;
		}
		elseif ($x['bytes'] < $y['bytes'])
		{
			return -1;
		}
		else
		{
			return 1;
		}
	}
	
	public static function compareDate($x, $y)
	{
		if ($x['date'] == $y['date'])
		{
			return 0;
		}
		elseif ($x['date'] < $y['date'])
		{
			return -1;
		}
		else
		{
			return 1;
		}
	}
	
	public static function folderPath(&$params)
	{
		//get the full path based on the parent folder selection
		$path = $params->get('efl_parent');
		$folder = trim($params->get('efl_folder')); //remove any whitespace
		if (strlen($folder) != 0) //check if a specific folder was entered
		{
			if (is_dir(JPATH_SITE.'/'.$path.'/'.$folder)) //check to see if the folder exists
			{
				$path .= '/'.$folder; //complete the path
			}
			else
			{
				//throw an exception... ouch!
				throw new \Exception(Text::sprintf("MOD_EFL_INVALID_FOLDER_ERROR", $path.'/'.$folder));
			}
		}
		return $path;
	}
	
	public static function formatDate(&$params, $mtime)
	{
		$datetime = date('Y-m-d', $mtime);
		
		if ($params->get('efl_time'))
		{
			$datetime = date('Y-m-d H:i:s', $mtime);
		}
		
		return $datetime;
	}
	
	public static function attachIcon($ext)
	{
		$path = Uri::base().'media'.'/'.'mod_easyfolderlisting'.'/'.'icons';
		$html = "";
		if (stripos("exe", $ext) !== false)
		{
			$html .= '<img src="'.$path.'/'.'application.png" alt="An executable file" />';
		}
		elseif (stripos("c", $ext) !== false)
		{
			$html .= '<img src="'.$path.'/'.'page_white_c.png" alt="A C file" />';
		}
		elseif (stripos("cpp", $ext) !== false)
		{
			$html .= '<img src="'.$path.'/'.'page_white_cplusplus.png" alt="A C plus plus file" />';
		}
		elseif (stripos("cs", $ext) !== false)
		{
			$html .= '<img src="'.$path.'/'.'page_white_csharp.png" alt="A C sharp file" />';
		}
		elseif (stripos("wmv,mp4,mov,divx,avi,flv,mkv", $ext) !== false)
		{
			$html .= '<img src="'.$path.'/'.'film.png" alt="A video file" />';
		}
		elseif (stripos("htm,html", $ext) !== false)
		{
			$html .= '<img src="'.$path.'/'.'html.png" alt="An html file" />';
		}
		elseif (stripos("bmp,jpg,jpeg,png,gif,tif", $ext) !== false)
		{
			$html .= '<img src="'.$path.'/'.'picture.png" alt="An image file" />';
		}
		elseif (stripos("mp3,wma", $ext) !== false)
		{
			$html .= '<img src="'.$path.'/'.'music.png" alt="A music file" />';
		}
		elseif (stripos("java,py", $ext) !== false)
		{
			$html .= '<img src="'.$path.'/'.'page_white_code.png" alt="A code file" />';
		}
		elseif (stripos("pdf,ps", $ext) !== false)
		{
			$html .= '<img src="'.$path.'/'.'page_white_acrobat.png" alt="An Adobe Acrobat file" />';
		}
		elseif (stripos("zip", $ext) !== false)
		{
			$html .= '<img src="'.$path.'/'.'page_white_compressed.png" alt="A compressed ZIP file" />';
		}
		elseif (stripos("swf", $ext) !== false)
		{
			$html .= '<img src="'.$path.'/'.'page_white_flash.png" alt="A flash SWF file" />';
		}
		elseif (stripos("xls,xlsx", $ext) !== false)
		{
			$html .= '<img src="'.$path.'/'.'page_white_excel.png" alt="A Microsoft Excel file" />';
		}
		elseif (stripos("php", $ext) !== false)
		{
			$html .= '<img src="'.$path.'/'.'page_white_php.png" alt="A PHP file" />';
		}
		elseif (stripos("ppt,pptx", $ext) !== false)
		{
			$html .= '<img src="'.$path.'/'.'page_white_powerpoint.png" alt="A Microsoft Powerpoint file" />';
		}
		elseif (stripos("txt", $ext) !== false)
		{
			$html .= '<img src="'.$path.'/'.'page_white_text.png" alt="A Text file" />';
		}
		elseif (stripos("sln", $ext) !== false)
		{
			$html .= '<img src="'.$path.'/'.'page_white_visualstudio.png" alt="A Visual Studio Solution file" />';
		}
		elseif (stripos("doc,docx", $ext) !== false)
		{
			$html .= '<img src="'.$path.'/'.'page_white_word.png" alt="A Microsoft Word file" />';
		}
		elseif (stripos("tar.gz,tgz,tbz,tb2,tar.bz2,taz,tar.Z,tlz,tar.lz,txz,tar.xz,7z,rar", $ext) !== false)
		{
			$html .= '<img src="'.$path.'/'.'page_white_zip.png" alt="A Compressed file" />';
		}
		else
		{
			$html .= '<img src="'.$path.'/'.'page_white.png" alt="A file of unknown type" />';
		}
		
		//add a space to the end
		$html .= ' ';
		
		return $html;
	}
	
	//this method returns the file size formatted according to an appropriate size units
	public static function sizeToText($size)
	{
		$text = "";
		$kb = 1024;
		$mb = $kb * $kb;
		$gb = $mb * $kb;
		
		if ($size >= $gb)
		{
			$size = round($size / $gb, 2);
			$text = $size." GB";
		}
		elseif ($size >= $mb)
		{
			$size = round($size / $mb, 2);
			$text = $size." MB";
		}
		elseif ($size >= $kb)
		{
			$size = round($size / $kb, 2);
			$text = $size." KB";
		}
		else
		{
			$text = $size." bytes";
		}
		return $text;
	}
}
?>