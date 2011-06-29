<?php
/**
*
* @package - NV recent topics
* @version $Id$
* @copyright (c) nickvergessen ( http://www.flying-bits.org/ )
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

/**
* @ignore
*/
define('UMIL_AUTO', true);
define('IN_PHPBB', true);
define('IN_INSTALL', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : '../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);

$user->session_begin();
$auth->acl($user->data);
$user->setup();

if (!file_exists($phpbb_root_path . 'umil/umil_auto.' . $phpEx))
{
	trigger_error('Please download the latest UMIL (Unified MOD Install Library) from: <a href="http://www.phpbb.com/mods/umil/">phpBB.com/mods/umil</a>', E_USER_ERROR);
}

$mod_name = 'RECENT_TOPICS_MOD';

$version_config_name = 'rt_mod_version';
$language_file = 'mods/info_acp_recenttopics';

$versions = array(
	// Version 1.0.0
	'1.0.0'	=> array(
		'config_add' => array(
			array('rt_number', 5),
			array('rt_page_number', 0),
			array('rt_anti_topics', 0),
			array('rt_index', 1),
		),
		'table_column_add' => array(
			array(FORUMS_TABLE, 'forum_recent_topics', array('TINT:1', 1)),
		),
		'module_add' => array(
			array('acp', 'ACP_CAT_DOT_MODS', 'RECENT_TOPICS_MOD'),

			array('acp', 'RECENT_TOPICS_MOD', array(
					'module_basename'	=> 'recenttopics',
					'module_langname'	=> 'RT_CONFIG',
					'module_mode'		=> 'overview',
					'module_auth'		=> 'acl_a_board',
				),
			),
		),
	),

	// Version 1.0.1
	'1.0.1'	=> array(),

	// Version 1.0.2
	'1.0.2'	=> array(),

	// Version 1.0.3
	'1.0.3'	=> array(),

	// Version 1.0.4
	'1.0.4'	=> array(),

	// Version 1.0.5
	'1.0.5'	=> array(),

	// Version 1.0.6
	'1.0.6'	=> array(
		'config_add' => array(
			array('rt_parents', 1),
		),
	),
);

// Include the UMIL Auto file and everything else will be handled automatically.
include($phpbb_root_path . 'umil/umil_auto.' . $phpEx);

?>