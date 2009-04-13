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
	'RECENT_TOPICS'						=> 'Recent topics',
	'RECENT_TOPICS_MOD'					=> 'Recent topics MOD',
	'RT_CONFIG'							=> 'Configuration',
	'RT_ANTI_TOPICS'					=> 'Excluded topics',
	'RT_ANTI_TOPICS_EXP'				=> 'Seperated by ", " (Example: "7, 9")<br />If you don\'t want to exclude a topic, just enter 0.',
	'RT_NUMBER'							=> 'Recent topics',
	'RT_NUMBER_EXP'						=> 'Number of topics displayed on the index.',
	'RT_PAGE_NUMBER'					=> 'Recent topics pages',
	'RT_PAGE_NUMBER_EXP'				=> 'You can display some more recent topics on a little pagination. Just enter 0 to disable this feature.',
	'RECENT_TOPICS_LIST'				=> 'View on "recent topics"',
	'RECENT_TOPICS_LIST_EXPLAIN'		=> 'Shall topics of this forum be displayed on the index in "recent topics"?',
	'RT_SAVED'							=> 'Saved adjustments.',

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

	// Installer
	'INSTALLER_INTRO'					=> 'Intro',
	'INSTALLER_INTRO_WELCOME'			=> 'Welcome to the MOD Installation',
	'INSTALLER_INTRO_WELCOME_NOTE'		=> 'Please choose what you want to do.',

	'INSTALLER_INSTALL'					=> 'Installation',
	'INSTALLER_INSTALL_MENU'			=> 'Installationmenu',
	'INSTALLER_INSTALL_SUCCESSFUL'		=> 'Installation of the MOD v%s was successful. You may delete the install-folder now.',
	'INSTALLER_INSTALL_VERSION'			=> 'Install MOD v%s',
	'INSTALLER_INSTALL_WELCOME'			=> 'Welcome to the Installationmenu',
	'INSTALLER_INSTALL_WELCOME_NOTE'	=> 'When you choose to install the MOD, even though it is already installed, the previous adjustments will be overwritten.',

	'INSTALLER_NEEDS_FOUNDER'			=> 'You must be logged in as a founder.',

	'INSTALLER_UPDATE'					=> 'Update',
	'INSTALLER_UPDATE_MENU'				=> 'Updatemenu',
	'INSTALLER_UPDATE_NOTE'				=> 'Update MOD from v%s to v%s',
	'INSTALLER_UPDATE_SUCCESSFUL'		=> 'Update of the MOD from v%s to v%s was successful. You may delete the install-folder now.',
	'INSTALLER_UPDATE_VERSION'			=> 'Update MOD from v',
	'INSTALLER_UPDATE_WELCOME'			=> 'Welcome to the Updatemenu',

	'WARNING'							=> 'Warning',
));

?>