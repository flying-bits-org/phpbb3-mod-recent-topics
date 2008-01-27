<?php

/**
*
* @package - NV recent topics
* @version $Id: info_acp_recenttopics.php 68 2008-01-06 01:03:56Z nickvergessen $
* @copyright (c) nickvergessen ( http://mods.flying-bits.org/ )
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/
if (!defined('IN_PHPBB'))
{
	exit;
}
if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	'RECENT_TOPICS'						=> 'recent topics',
	'RECENT_TOPICS_MOD'					=> 'recent topics MOD',
	'RT_CONFIG'							=> 'configuration',
	'RT_ANTI_TOPICS'					=> 'enabled topics',
	'RT_ANTI_TOPICS_EXP'				=> 'seperated by ,<br />If you don&#039;t want to leave out a topic, just enter 0',
	'RT_NUMBER'							=> 'recent topics',
	'RT_NUMBER_EXP'						=> 'number of topics displayed on the index',
	'RECENT_TOPICS_LIST'				=> 'view on "recent topics"',
	'RECENT_TOPICS_LIST_EXPLAIN'		=> 'Shall topics of this forum be displayed on the index in "recent topics"?',
	'RT_SAVED'							=> 'saved adjustments',

	'RT_VIEW_ON'		=> 'view NV recent-topics on',
	'RT_MEMBERLIST'		=> 'Memberlist',
	'RT_INDEX'			=> 'Index',
	'RT_SEARCH'			=> 'Search',
	'RT_FAQ'			=> 'FAQ',
	'RT_MCP'			=> 'MCP (Moderator Control Panel)',
	'RT_UCP'			=> 'UCP (User Control Panel)',
	'RT_VIEWFORUM'		=> 'Viewforum',
	'RT_VIEWTOPIC'		=> 'Viewtopic',
	'RT_VIEWONLINE'		=> 'Viewonline',
	'RT_POSTING'		=> 'Posting',
	'RT_REPORT'			=> 'Reporting',
	'RT_OTHERS'			=> 'other Site',
));

?>