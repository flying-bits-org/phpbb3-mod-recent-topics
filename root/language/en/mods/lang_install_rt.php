<?php

/**
*
* @package - NV recent topics
* @version $Id: lang_install_rt.php 70 2008-01-06 11:10:11Z nickvergessen $
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
	'INSTALLER_INTRO'					=> 'Intro',
	'INSTALLER_INTRO_WELCOME'			=> 'Welcome to the MOD Installation',
	'INSTALLER_INTRO_WELCOME_NOTE'		=> 'Please choose what you want to do.',

	'INSTALLER_INSTALL'					=> 'Install',
	'INSTALLER_INSTALL_MENU'			=> 'Installmenu',
	'INSTALLER_INSTALL_SUCCESSFUL'		=> 'Installation of the MOD v%s was successful. You may delete the install-folder now.',
	'INSTALLER_INSTALL_UNSUCCESSFUL'	=> 'Installation of the MOD v%s was <strong>not</strong> successful.',
	'INSTALLER_INSTALL_VERSION'			=> 'Install MOD v%s',
	'INSTALLER_INSTALL_WELCOME'			=> 'Welcome to the Installationmenu',
	'INSTALLER_INSTALL_WELCOME_NOTE'	=> 'When you choose to install the MOD, any database of previous versions will be dropped.',

	'INSTALLER_NEEDS_FOUNDER'			=> 'You must be logged in as a founder.',

	'INSTALLER_UPDATE'					=> 'Update',
	'INSTALLER_UPDATE_MENU'				=> 'Updatemenu',
	'INSTALLER_UPDATE_NOTE'				=> 'Update MOD from v%s to v%s',
	'INSTALLER_UPDATE_SUCCESSFUL'		=> 'Update of the MOD from v%s to v%s was successful. You may delete the install-folder now.',
	'INSTALLER_UPDATE_UNSUCCESSFUL'		=> 'Update of the MOD from v%s to v%s was <strong>not</strong> successful.',
	'INSTALLER_UPDATE_VERSION'			=> 'Update MOD from v',
	'INSTALLER_UPDATE_WELCOME'			=> 'Welcome to the Updatemenu',

	'RT_UNSUPPORTED'					=> 'Sorry, but your database is not supported.',

	'WARNING'							=> 'Warning',
));

?>