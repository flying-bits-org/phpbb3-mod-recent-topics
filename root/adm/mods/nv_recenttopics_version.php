<?php
/**
*
* @package acp
* @version $Id$
* @copyright (c) 2005 phpBB Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @package nv_altt
*/

if (!defined('IN_PHPBB'))
{
	exit;
}

class nv_recenttopics_version
{
	function version()
	{
		return array(
			'author'	=> 'nickvergessen',
			'title'		=> 'NV Recent Topics',
			'tag'		=> 'nv_recenttopics',
			'version'	=> '1.0.1',
			'file'		=> array('www.flying-bits.org', 'updatecheck', 'nv_recenttopics.xml'),
		);
	}
}

?>