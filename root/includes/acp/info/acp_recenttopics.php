<?php

/**
*
* @package - NV recent topics
* @version $Id: acp_recenttopics.php 68 2008-01-06 01:03:56Z nickvergessen $
* @copyright (c) nickvergessen ( http://mods.flying-bits.org/ )
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @package module_install
*/
class acp_recenttopics_info
{
	function module()
	{
		return array(
			'filename'	=> 'acp_recenttopics',
			'title'		=> 'RECENT_TOPICS_MOD',
			'version'	=> '0.1.2',
			'modes'		=> array(
				'adjust_recenttopics'	=> array(
					'title'		=> 'RT_CONFIG',
					'auth'		=> 'acl_a_board',
					'cat'		=> array('ACP_BOARD_CONFIGURATION'),
				),
			),
		);
	}
}

?>