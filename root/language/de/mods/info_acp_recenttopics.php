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
	'RECENT_TOPICS'						=> 'aktuelle Themen',
	'RECENT_TOPICS_MOD'					=> 'aktuelle Themen MOD',
	'RT_CONFIG'							=> 'Konfiguration von aktuelle Themen',
	'RT_ANTI_TOPICS'					=> 'ausgeschlossene Themen',
	'RT_ANTI_TOPICS_EXP'				=> 'Mit ", " trennen (Beispiel: "7, 9")<br />Wenn kein Thema ausgeschlossen werden soll 0 eingeben.',
	'RT_NUMBER'							=> 'aktuelle Themen',
	'RT_NUMBER_EXP'						=> 'Anzahl der Themen die angezeigt werden',
	'RT_PAGE_NUMBER'					=> 'aktuelle Themen Seitenanzahl',
	'RT_PAGE_NUMBER_EXP'				=> 'Du kannst weiter aktuelle Themen mit einer kleinen Seitennavigation anzeigen lassen. Um das Feature zu deaktivieren einfach 0 eintragen.',
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

	// Installer
	'INSTALLER_INTRO'					=> 'Intro',
	'INSTALLER_INTRO_WELCOME'			=> 'Willkommen zur MOD-Installation',
	'INSTALLER_INTRO_WELCOME_NOTE'		=> 'Bitte wähle aus, was du tun möchtest.',

	'INSTALLER_INSTALL'					=> 'Installieren',
	'INSTALLER_INSTALL_MENU'			=> 'Installation',
	'INSTALLER_INSTALL_SUCCESSFUL'		=> 'Installation der MOD v%s war erfolgreich.',
	'INSTALLER_INSTALL_VERSION'			=> 'Installiere MOD v%s',
	'INSTALLER_INSTALL_WELCOME'			=> 'Willkommen zur Installation',
	'INSTALLER_INSTALL_WELCOME_NOTE'	=> 'Wenn du den MOD installierst, obwohl er schon installiert ist, werden die Einstellungen überschrieben.',

	'INSTALLER_NEEDS_FOUNDER'			=> 'Du musst als Gründer eingeloggt sein.',

	'INSTALLER_UPDATE'					=> 'Update',
	'INSTALLER_UPDATE_MENU'				=> 'Updatemenü',
	'INSTALLER_UPDATE_NOTE'				=> 'Update MOD von v%s nach v%s',
	'INSTALLER_UPDATE_SUCCESSFUL'		=> 'Update der MOD von v%s nach v%s war erfolgreich.',
	'INSTALLER_UPDATE_VERSION'			=> 'Update MOD von v',
	'INSTALLER_UPDATE_WELCOME'			=> 'Willkommen zum Update',

	'WARNING'							=> 'Warnung',
));

?>