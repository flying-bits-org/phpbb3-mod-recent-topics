<?php

/**
*
* @package - NV recent topics
* @version $Id$
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
	'RT_ANTI_TOPICS'					=> 'disabled topics',
	'RT_ANTI_TOPICS_EXP'				=> 'seperated by ,<br />If you don\'t want to leave out a topic, just enter 0',
	'RT_NUMBER'							=> 'recent topics',
	'RT_NUMBER_EXP'						=> 'number of topics displayed on the index',
	'RT_PAGE_NUMBER'					=> 'recent topics pages',
	'RT_PAGE_NUMBER_EXP'				=> 'You can display some more recent topics on a little pagination. Just enter 0 to disable this feature.',
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