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
	'RECENT_TOPICS'						=> 'aktuelle Themen',
	'RECENT_TOPICS_MOD'					=> 'aktuelle Themen MOD',
	'RT_CONFIG'							=> 'Konfiguration von aktuelle Themen',
	'RT_ANTI_TOPICS'					=> 'ausgeschlossene Themen',
	'RT_ANTI_TOPICS_EXP'				=> 'mit , (Komma) trennen<br />Wenn kein Thema ausgeschlossen werden soll 0 eingeben.',
	'RT_NUMBER'							=> 'aktuelle Themen',
	'RT_NUMBER_EXP'						=> 'Anzahl der Themen die angezeigt werden',
	'RECENT_TOPICS_LIST'				=> 'unter "aktuelle Themen" anzeigen',
	'RECENT_TOPICS_LIST_EXPLAIN'		=> 'Sollen Themen aus diesem Forum "aktuelle Themen" angezeigt werden?',
	'RT_SAVED'							=> 'Einstellungen gespeichert.',

	'RT_VIEW_ON'		=> 'NV recent-topics anzeigen',
	'RT_MEMBERLIST'		=> 'Mitgliederliste',
	'RT_INDEX'			=> 'Index',
	'RT_SEARCH'			=> 'Suche',
	'RT_FAQ'			=> 'FAQ',
	'RT_MCP'			=> 'MCP (Moderations-Bereich)',
	'RT_UCP'			=> 'UCP (Persönlicher-Bereich)',
	'RT_VIEWFORUM'		=> 'Forum',
	'RT_VIEWTOPIC'		=> 'Thema',
	'RT_VIEWONLINE'		=> 'Online-Anzeige',
	'RT_POSTING'		=> 'Posten',
	'RT_REPORT'			=> 'Beitrag melden',
	'RT_OTHERS'			=> 'andere Seiten',
));

?>