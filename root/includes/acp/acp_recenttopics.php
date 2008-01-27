<?php

/**
*
* @package - NV recent topics
* @version $Id: acp_recenttopics.php 90 2008-01-11 14:44:27Z nickvergessen $
* @copyright (c) nickvergessen ( http://mods.flying-bits.org/ )
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @package acp
*/
class acp_recenttopics
{
	var $u_action;

	function main($id, $mode)
	{
		global $db, $user, $auth, $template, $cache;
		global $config, $phpbb_root_path, $phpbb_admin_path, $phpEx;

		$user->add_lang('acp/common');
		$this->tpl_name = 'acp_recenttopics';
		$this->page_title = $user->lang['RECENT_TOPICS_MOD'];
		add_form_key('acp_recenttopics');

			$submit = (isset($_POST['submit'])) ? true : false;
			if ($submit)
			{
				if (!check_form_key('acp_recenttopics'))
				{
					trigger_error('FORM_INVALID');
				}
				$rt_anti_topics		= request_var('rt_anti_topics', '0', true);
				$rt_number			= request_var('rt_number', 5);
				$rt_index			= request_var('rt_index', 0);
				#$rt_muster			= request_var('rt_muster', 0);
				if($rt_anti_topics != $config['rt_anti_topics'])
				{
					set_config('rt_anti_topics', $rt_anti_topics);
				}
				if($rt_number != $config['rt_number'])
				{
					set_config('rt_number', $rt_number);
				}
				if($rt_index != $config['rt_index'])
				{
					set_config('rt_index', $rt_index);
				}
				#if($rt_muster != $config['rt_muster'])
				#{
				#	set_config('rt_muster', $rt_muster);
				#}
				trigger_error($user->lang['RT_SAVED'] . adm_back_link($this->u_action));
			}
			$template->assign_vars(array(
				'RT_VERSION'			=> 'v' . $config['rt_mod_version'],
				'RT_ANTI_TOPICS'		=> $config['rt_anti_topics'],
				'RT_NUMBER'				=> $config['rt_number'],
				'RT_INDEX'				=> $config['rt_index'],
				#'RT_MUSTER'				=> $config['rt_muster'],
				'U_ACTION'				=> $this->u_action,
			));
	}
}

?>