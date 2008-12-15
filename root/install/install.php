<?php

/**
*
* @package - NV recent topics
* @version $Id$
* @copyright (c) nickvergessen ( http://mods.flying-bits.org/ )
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @ignore
*/
define('IN_PHPBB', true);
$phpbb_root_path = '../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.'.$phpEx);
include($phpbb_root_path . 'includes/acp/acp_modules.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup();

// Load language and custom template-path
$user->add_lang('mods/lang_install_rt');
$template->set_custom_template('style', 'install_recent_topics');
$template->assign_var('T_TEMPLATE_PATH', 'style');

$new_mod_version = '1.0.2';
$page_title = 'NV recent topics v' . $new_mod_version;

function install_back_link($u_action)
{
	global $user;
	return '<br /><br /><a href="' . $u_action . '">&laquo; ' . $user->lang['BACK_TO_PREV'] . '</a>';
}

$mode = request_var('mode', 'else');
$version = request_var('version', '0.0.0');
$confirm = request_var('confirm', 0);
switch ($mode)
{
	case 'install':
		if ($confirm)
		{
			// Use phpBB-Stuff
			include($phpbb_root_path . 'includes/db/db_tools.' . $phpEx);
			$phpbb_db_tools = new phpbb_db_tools($db);
			if (!$phpbb_db_tools->sql_column_exists(FORUMS_TABLE, 'forum_recent_topics'))
			{
				$phpbb_db_tools->sql_column_add(FORUMS_TABLE, 'forum_recent_topics', array('TINT:1', 1));
			}

			$sql = 'DELETE FROM ' . CONFIG_TABLE . "
				WHERE config_name = 'rt_anti_topics'
					OR config_name = 'rt_mod_version'
					OR config_name = 'rt_number'
					OR config_name = 'rt_page_number'
					OR config_name = 'rt_others'
					OR config_name = 'rt_memberlist'
					OR config_name = 'rt_index'
					OR config_name = 'rt_search'
					OR config_name = 'rt_faq'
					OR config_name = 'rt_mcp'
					OR config_name = 'rt_ucp'
					OR config_name = 'rt_viewforum'
					OR config_name = 'rt_viewtopic'
					OR config_name = 'rt_viewonline'
					OR config_name = 'rt_posting'
					OR config_name = 'rt_report'";
			$result = $db->sql_query($sql);

			// create the acp modules
			$modules = new acp_modules();
			$recenttopics = array(
				'module_basename'	=> '',
				'module_enabled'	=> 1,
				'module_display'	=> 1,
				'parent_id'			=> 31,
				'module_class'		=> 'acp',
				'module_langname'	=> 'RECENT_TOPICS_MOD',
				'module_mode'		=> '',
				'module_auth'		=> ''
			);
			$modules->update_module_data($recenttopics);
			$adjust_recenttopics = array(
				'module_basename'	=> 'recenttopics',
				'module_enabled'	=> 1,
				'module_display'	=> 1,
				'parent_id'			=> $recenttopics['module_id'],
				'module_class'		=> 'acp',
				'module_langname'	=> 'RT_CONFIG',
				'module_mode'		=> 'overview',
				'module_auth'		=> ''
			);
			$modules->update_module_data($adjust_recenttopics);

			set_config('rt_mod_version', $new_mod_version);
			set_config('rt_number', 5);
			set_config('rt_page_number', 0);
			set_config('rt_anti_topics', 0);
			set_config('rt_index', 1);

			// clear cache and log what we did
			$cache->purge();
			add_log('admin', sprintf($user->lang['INSTALLER_INSTALL_SUCCESSFUL'], $new_mod_version));

			$template->assign_vars(array(
				'S_INFORMATION'		=> sprintf($user->lang['INSTALLER_INSTALL_SUCCESSFUL'], $new_mod_version),
			));
		}
		$template->assign_vars(array(
			'S_NOT_INTRO'		=> true,
			'S_INSTALL'			=> true,
			'L_WELCOME'			=> $user->lang['INSTALLER_INSTALL_WELCOME'],
			'L_WELCOME_NOTE'	=> $user->lang['INSTALLER_INSTALL_WELCOME_NOTE'],
			'L_LEGEND'			=> $user->lang['INSTALLER_INSTALL'],
			'L_LABLE'			=> 'v' . $new_mod_version,
			'S_ACTION'			=> append_sid("{$phpbb_root_path}install/index.$phpEx", 'mode=install'),
		));
	break;
	case 'update':
		if ($confirm)
		{
			switch ($version)
			{
				case '1.0.0d':
					set_config('rt_page_number', 0);
				break;
				case '1.0.1':
					
				break;
			}
			set_config('rt_mod_version', $new_mod_version);
			$cache->purge();
			add_log('admin', sprintf($user->lang['INSTALLER_UPDATE_SUCCESSFUL'], $version, $new_mod_version));

			$template->assign_vars(array(
				'S_INFORMATION'		=> sprintf($user->lang['INSTALLER_INSTALL_SUCCESSFUL'], $new_mod_version),
			));
		}
		$template->assign_vars(array(
			'S_NOT_INTRO'		=> true,
			'S_UPDATE'			=> $version,
			'L_WELCOME'			=> $user->lang['INSTALLER_UPDATE_WELCOME'],
			'L_LEGEND'			=> $user->lang['INSTALLER_UPDATE'],
			'L_LABLE'			=> sprintf($user->lang['INSTALLER_UPDATE_NOTE'], $version, $new_mod_version),
			'S_ACTION'			=> append_sid("{$phpbb_root_path}install/index.$phpEx", 'mode=update&amp;version=' . $version),
		));
	break;
	default:
		if ($user->data['user_type'] == USER_FOUNDER)
		{
			$template->assign_vars(array(
				'S_INTRO'			=> true,
				'L_WELCOME'			=> $user->lang['INSTALLER_INTRO_WELCOME'],
				'L_WELCOME_NOTE'	=> $user->lang['INSTALLER_INTRO_WELCOME_NOTE'],
			));
		}
	break;
}
if ($user->data['user_type'] != USER_FOUNDER)
{
	$template->assign_vars(array(
		'S_WARNING'		=> $user->lang['INSTALLER_NEEDS_FOUNDER'],
	));
}

$template->assign_vars(array(
	'L_PAGE_TITLE'		=> $page_title,
	'L_INSTALL_VERSION'	=> sprintf($user->lang['INSTALLER_INSTALL_VERSION'], $new_mod_version),

	'S_VERSION'			=> $version,

	'U_INTRO'			=> append_sid("{$phpbb_root_path}install/index.$phpEx"),
	'U_INSTALL'			=> append_sid("{$phpbb_root_path}install/index.$phpEx", 'mode=install'),
	'U_UPDATE_1_0_0d'	=> append_sid("{$phpbb_root_path}install/index.$phpEx", 'mode=update&amp;version=1.0.0d'),
	'U_UPDATE_1_0_1'	=> append_sid("{$phpbb_root_path}install/index.$phpEx", 'mode=update&amp;version=1.0.1'),
));

page_header($page_title);

$template->set_filenames(array(
	'body' => 'install_body.html')
);

page_footer();

?>