<?php

/**
*
* @package - NV recent topics
* @version $Id: install.php 75 2008-01-06 14:11:53Z nickvergessen $
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
$user->add_lang('mods/lang_install_rt');
$new_mod_version = '1.0.1';
$page_title = 'NV recent topics v' . $new_mod_version;

function install_back_link($u_action)
{
	global $user;
	return '<br /><br /><a href="' . $u_action . '">&laquo; ' . $user->lang['BACK_TO_PREV'] . '</a>';
}

$mode = request_var('mode', 'else', true);
switch ($mode)
{
	case 'install':
		$install = request_var('install', 0);
		$installed = false;
		if ($install == 1)
		{
				switch ($db->sql_layer)
				{
					case 'firebird':
						$sql = 'ALTER TABLE "' . FORUMS_TABLE . '" ADD "forum_recent_topics"  tinyint(1) unsigned DEFAULT "1" NOT NULL AFTER forum_topics_real;';
						$result = $db->sql_query($sql);
					break;

					case 'mssql':
						$sql = 'ALTER TABLE [' . FORUMS_TABLE . '] ADD [forum_recent_topics]  tinyint(1) unsigned DEFAULT "1" NOT NULL AFTER forum_topics_real;';
						$result = $db->sql_query($sql);
					break;

					case 'mysql':
					case 'mysqli':
					case 'mysql4':
						$sql = 'ALTER TABLE `' . FORUMS_TABLE . '` ADD COLUMN `forum_recent_topics` tinyint(1) unsigned DEFAULT "1" NOT NULL AFTER forum_topics_real;';
						$result = $db->sql_query($sql);
					break;

					case 'oracle':
						$sql = 'ALTER TABLE ' . FORUMS_TABLE . ' ADD forum_recent_topics  tinyint(1) unsigned DEFAULT "1" NOT NULL AFTER forum_topics_real;';
						$result = $db->sql_query($sql);
					break;

					case 'postgres':
						$sql = 'ALTER TABLE ' . FORUMS_TABLE . ' ADD COLUMN "forum_recent_topics"  tinyint(1) unsigned DEFAULT "1" NOT NULL AFTER forum_topics_real;';
						$result = $db->sql_query($sql);
					break;

					default:
						//ALTER TABLE `phpbb_forums` ADD `forum_recent_topics` tinyint(1) unsigned DEFAULT "1" NOT NULL AFTER forum_topics_real;
						trigger_error($user->lang['RT_UNSUPPORTED'] . install_back_link($this->u_action), E_USER_WARNING);
					break;
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
			$installed = true;
		}
	break;
	case 'update011':
		$update = request_var('update', 0);
		$version = request_var('v', '0.0.0', true);
		$updated = false;
		if ($update == 1)
		{
			$sql = 'DELETE FROM ' . CONFIG_TABLE . "
				WHERE config_name = 'rt_mod_version'
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

			set_config('rt_mod_version', $new_mod_version);
			set_config('rt_number', 5);
			set_config('rt_page_number', 0);
			set_config('rt_index', 1);
			// clear cache and log what we did
			$cache->purge();
			add_log('admin', sprintf($user->lang['INSTALLER_UPDATE_SUCCESSFUL'], $version, $new_mod_version));
			$updated = true;
		}
	break;
	case 'update100d':
		$update = request_var('update', 0);
		$version = request_var('v', '0.0.0', true);
		$updated = false;
		if ($update == 1)
		{
			set_config('rt_page_number', 0);
			set_config('rt_mod_version', $new_mod_version);
			$cache->purge();
			add_log('admin', sprintf($user->lang['INSTALLER_UPDATE_SUCCESSFUL'], $version, $new_mod_version));
			$updated = true;
		}
	break;
	default:
		//we had a little cheater
	break;
}

include($phpbb_root_path . "install_rt/layout.$phpEx");
?>