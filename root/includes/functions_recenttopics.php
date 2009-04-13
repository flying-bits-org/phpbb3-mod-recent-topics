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
if (!defined('IN_PHPBB'))
{
	exit;
}

if (!function_exists('display_forums'))
{
	include($phpbb_root_path . 'includes/functions_display.' . $phpEx);
}

$user->add_lang('mods/info_acp_recenttopics');

function display_recent_topics($topics_per_page, $num_pages, $excluded_topics)
{
	global $auth, $cache, $config, $db, $template, $user;
	global $phpbb_root_path, $phpEx;


	$spec_forum_id	= request_var('f', 0);
	$start			= request_var('start', 0);
	$excluded_topic_ids = explode(', ', $excluded_topics);
	$total_limit	= $topics_per_page * $num_pages;
	$ga_forum_id	= 0; // Forum id we use for global announcements

	// Get the allowed forums
	$forum_ary = array();
	$forum_read_ary = $auth->acl_getf('f_read');
	foreach ($forum_read_ary as $forum_id => $allowed)
	{
		if ($allowed['f_read'])
		{
			$forum_ary[] = (int) $forum_id;
		}
	}
	$forum_ary = array_unique($forum_ary);
	if (!sizeof($forum_ary))
	{
		return;
	}

	// Only call the query, if we need to
	$spec_forum_ary = array();
	if ($spec_forum_id)
	{
		$spec_forum_ary = array($spec_forum_id);
		$sql = 'SELECT parent_id, forum_id
			FROM ' . FORUMS_TABLE . '
			WHERE ' . $db->sql_in_set('forum_id', $forum_ary) . '
			ORDER BY left_id';
		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result))
		{
			if (in_array($row['parent_id'], $spec_forum_ary))
			{
				$spec_forum_ary[] = $row['forum_id'];
			}
		}
		$db->sql_freeresult($result);
	}

	$sql = 'SELECT forum_id
		FROM ' . FORUMS_TABLE . '
		WHERE ' . $db->sql_in_set('forum_id', $forum_ary) . '
			' . ((sizeof($spec_forum_ary)) ? ' AND ' . $db->sql_in_set('forum_id', $spec_forum_ary) : '') . '
			AND forum_recent_topics = 1';
	$result = $db->sql_query($sql);

	$forum_ids = array();
	while ($row = $db->sql_fetchrow($result))
	{
		$forum_ids[] = $row['forum_id'];
	}
	$db->sql_freeresult($result);

	// No forums with f_view
	if (!sizeof($forum_ids))
	{
		return;
	}

	// Moderator forums
	$m_approve_ids = array();
	$m_approve_ary = $auth->acl_getf('m_approve');
	foreach ($m_approve_ary as $forum_id => $allowed)
	{
		if ($allowed['m_approve'] && in_array($forum_id, $forum_ids))
		{
			$m_approve_ids[] = (int) $forum_id;
		}
	}

	// Get the allowed topics
	$sql = 'SELECT forum_id, topic_id, topic_type
		FROM ' . TOPICS_TABLE . '
		WHERE ((' . $db->sql_in_set('topic_id', $excluded_topic_ids, true) . '
				AND ' . $db->sql_in_set('forum_id', $forum_ids) . ')
			OR topic_type = ' . POST_GLOBAL . ')
			AND topic_status <> ' . ITEM_MOVED . '
			AND (' . $db->sql_in_set('forum_id', $m_approve_ids, false, true) . '
				OR topic_approved = 1)
		ORDER BY topic_last_post_time DESC';
	$result = $db->sql_query_limit($sql, $total_limit);

	$forums = $ga_topic_ids = $topic_ids = array();
	$num_topics = 0;
	while ($row = $db->sql_fetchrow($result))
	{
		$num_topics++;
		if (($num_topics > $start) && ($num_topics <= ($start + $topics_per_page)))
		{
			$topic_ids[] = $row['topic_id'];
			if ($row['topic_type'] == POST_GLOBAL)
			{
				$ga_topic_ids[] = $row['topic_id'];
			}
			else
			{
				$forums[$row['forum_id']][] = $row['topic_id'];
			}
		}
	}
	$db->sql_freeresult($result);

	// No topics to display
	if (!sizeof($topic_ids))
	{
		return;
	}

	// Grab icons
	$icons = $cache->obtain_icons();

	// Now only pull the data of the requested topics
	$sql = 'SELECT t.*, i.icons_url, i.icons_width, i.icons_height, tp.topic_posted, f.forum_name
		FROM ' . TOPICS_TABLE . ' t
		LEFT JOIN ' . TOPICS_POSTED_TABLE . ' tp
			ON (t.topic_id = tp.topic_id
				AND tp.user_id = ' . $user->data['user_id'] . ')
		LEFT JOIN ' . FORUMS_TABLE . ' f
			ON f.forum_id = t.forum_id
		LEFT JOIN ' . ICONS_TABLE . ' i
			ON t.icon_id = i.icons_id
		WHERE ' . $db->sql_in_set('t.topic_id', $topic_ids) . '
		ORDER BY t.topic_last_post_time DESC';
	$result = $db->sql_query_limit($sql, $topics_per_page);

	foreach ($forums as $forum_id => $topic_ids)
	{
		$topic_tracking_info[$forum_id] = get_complete_topic_tracking($forum_id, $topic_ids, $ga_topic_ids);
	}

	while ($row = $db->sql_fetchrow($result))
	{
		$topic_id = $row['topic_id'];
		$forum_id = $row['forum_id'];

		// Cheat for Global Announcements on the unread-link: copied from search.php
		if (!$forum_id && !$ga_forum_id)
		{
			$sql2 = 'SELECT forum_id
				FROM ' . FORUMS_TABLE . '
				WHERE forum_type = ' . FORUM_POST . '
					AND ' . $db->sql_in_set('forum_id', $forum_ary, false, true);
			$result2 = $db->sql_query_limit($sql2, 1);
			$ga_forum_id = (int) $db->sql_fetchfield('forum_id');
			$db->sql_freeresult($result2);
			$forum_id = $ga_forum_id;
		}
		else if (!$forum_id && $ga_forum_id)
		{
			$forum_id = $ga_forum_id;
		}

		$s_type_switch_test = ($row['topic_type'] == POST_ANNOUNCE || $row['topic_type'] == POST_GLOBAL) ? 1 : 0;
		$replies = ($auth->acl_get('m_approve', $forum_id)) ? $row['topic_replies_real'] : $row['topic_replies'];
		$unread_topic = (isset($topic_tracking_info[$forum_id][$topic_id]) && $row['topic_last_post_time'] > $topic_tracking_info[$forum_id][$topic_id]) ? true : false;

		$folder_img = $folder_alt = $topic_type = '';
		switch ($row['topic_type'])
		{
			case POST_GLOBAL:
				$topic_type = $user->lang['VIEW_TOPIC_GLOBAL'];
				$folder_img = (!$unread_topic) ? 'global_read' : 'global_unread';
			break;
			case POST_ANNOUNCE:
				$topic_type = $user->lang['VIEW_TOPIC_ANNOUNCEMENT'];
				$folder_img = (!$unread_topic) ? 'announce_read' : 'announce_unread';
			break;
			case POST_STICKY:
				$topic_type = $user->lang['VIEW_TOPIC_STICKY'];
				$folder_img = (!$unread_topic) ? 'sticky_read' : 'sticky_unread';
			break;
			default:
				$topic_type = '';
				$folder_img = (!$unread_topic) ? 'topic_read' : 'topic_unread';
				if ($config['hot_threshold'] && $replies >= $config['hot_threshold'] && $row['topic_status'] != ITEM_LOCKED)
				{
					$folder_img .= '_hot';
				}
			break;
		}
		if ($row['topic_status'] == ITEM_LOCKED)
		{
			$topic_type = $user->lang['VIEW_TOPIC_LOCKED'];
			$folder_img .= '_locked';
		}
		if ($row['topic_posted'])
		{
			$folder_img .= '_mine';
		}
		if ($row['topic_type'] == POST_GLOBAL)
		{
			$global_announce_list[$row['topic_id']] = true;
		}
		$folder_alt = ($unread_topic) ? 'NEW_POSTS' : (($row['topic_status'] == ITEM_LOCKED) ? 'TOPIC_LOCKED' : 'NO_NEW_POSTS');
		if ($row['poll_start'] && $row['topic_status'] != ITEM_MOVED)
		{
			$topic_type = $user->lang['VIEW_TOPIC_POLL'];
		}

		$view_topic_url = append_sid("{$phpbb_root_path}viewtopic.$phpEx", 'f=' . $forum_id . '&amp;t=' . $topic_id);
		$view_forum_url = append_sid("{$phpbb_root_path}viewforum.$phpEx", 'f=' . $forum_id);
		$topic_unapproved = (!$row['topic_approved'] && $auth->acl_get('m_approve', $forum_id)) ? true : false;
		$posts_unapproved = ($row['topic_approved'] && $row['topic_replies'] < $row['topic_replies_real'] && $auth->acl_get('m_approve', $forum_id)) ? true : false;
		$u_mcp_queue = ($topic_unapproved || $posts_unapproved) ? append_sid("{$phpbb_root_path}mcp.$phpEx", 'i=queue&amp;mode=' . (($topic_unapproved) ? 'approve_details' : 'unapproved_posts') . "&amp;t=$topic_id", true, $user->session_id) : '';
		$s_type_switch = ($row['topic_type'] == POST_ANNOUNCE || $row['topic_type'] == POST_GLOBAL) ? 1 : 0;

		$template->assign_block_vars('recenttopicrow', array(
			'FORUM_ID'					=> $forum_id,
			'TOPIC_ID'					=> $topic_id,
			'TOPIC_AUTHOR_FULL'			=> get_username_string('full', $row['topic_poster'], $row['topic_first_poster_name'], $row['topic_first_poster_colour']),
			'FIRST_POST_TIME'			=> $user->format_date($row['topic_time']),

			'LAST_POST_SUBJECT'			=> censor_text($row['topic_last_post_subject']),
			'LAST_POST_TIME'			=> $user->format_date($row['topic_last_post_time']),
			'LAST_VIEW_TIME'			=> $user->format_date($row['topic_last_view_time']),
			'LAST_POST_AUTHOR'			=> get_username_string('username', $row['topic_last_poster_id'], $row['topic_last_poster_name'], $row['topic_last_poster_colour']),
			'LAST_POST_AUTHOR_COLOUR'	=> get_username_string('colour', $row['topic_last_poster_id'], $row['topic_last_poster_name'], $row['topic_last_poster_colour']),
			'LAST_POST_AUTHOR_FULL'		=> get_username_string('full', $row['topic_last_poster_id'], $row['topic_last_poster_name'], $row['topic_last_poster_colour']),

			'PAGINATION'				=> topic_generate_pagination($replies, $view_topic_url),
			'REPLIES'					=> $replies,
			'VIEWS'						=> $row['topic_views'],
			'TOPIC_TITLE'				=> censor_text($row['topic_title']),
			'FORUM_NAME'				=> $row['forum_name'],

			'TOPIC_TYPE'			=> $topic_type,
			'TOPIC_FOLDER_IMG'		=> $user->img($folder_img, $folder_alt),
			'TOPIC_FOLDER_IMG_SRC'	=> $user->img($folder_img, $folder_alt, false, '', 'src'),
			'TOPIC_FOLDER_IMG_ALT'	=> $user->lang[$folder_alt],
			'NEWEST_POST_IMG'		=> $user->img('icon_topic_newest', 'VIEW_NEWEST_POST'),
			'TOPIC_ICON_IMG'		=> (!empty($icons[$row['icon_id']])) ? $icons[$row['icon_id']]['img'] : '',
			'TOPIC_ICON_IMG_WIDTH'	=> (!empty($icons[$row['icon_id']])) ? $icons[$row['icon_id']]['width'] : '',
			'TOPIC_ICON_IMG_HEIGHT'	=> (!empty($icons[$row['icon_id']])) ? $icons[$row['icon_id']]['height'] : '',
			'ATTACH_ICON_IMG'		=> ($auth->acl_get('u_download') && $auth->acl_get('f_download', $forum_id) && $row['topic_attachment']) ? $user->img('icon_topic_attach', $user->lang['TOTAL_ATTACHMENTS']) : '',
			'UNAPPROVED_IMG'		=> ($topic_unapproved || $posts_unapproved) ? $user->img('icon_topic_unapproved', ($topic_unapproved) ? 'TOPIC_UNAPPROVED' : 'POSTS_UNAPPROVED') : '',

			'S_TOPIC_TYPE'			=> $row['topic_type'],
			'S_USER_POSTED'			=> (isset($row['topic_posted']) && $row['topic_posted']) ? true : false,
			'S_UNREAD_TOPIC'		=> $unread_topic,
			'S_TOPIC_REPORTED'		=> (!empty($row['topic_reported']) && $auth->acl_get('m_report', $forum_id)) ? true : false,
			'S_TOPIC_UNAPPROVED'	=> $topic_unapproved,
			'S_POSTS_UNAPPROVED'	=> $posts_unapproved,
			'S_HAS_POLL'			=> ($row['poll_start']) ? true : false,
			'S_POST_ANNOUNCE'		=> ($row['topic_type'] == POST_ANNOUNCE) ? true : false,
			'S_POST_GLOBAL'			=> ($row['topic_type'] == POST_GLOBAL) ? true : false,
			'S_POST_STICKY'			=> ($row['topic_type'] == POST_STICKY) ? true : false,
			'S_TOPIC_LOCKED'		=> ($row['topic_status'] == ITEM_LOCKED) ? true : false,
			'S_TOPIC_MOVED'			=> ($row['topic_status'] == ITEM_MOVED) ? true : false,
			'S_TOPIC_TYPE_SWITCH'	=> ($s_type_switch == $s_type_switch_test) ? -1 : $s_type_switch_test,

			'U_NEWEST_POST'			=> $view_topic_url . '&amp;view=unread#unread',
			'U_LAST_POST'			=> $view_topic_url . '&amp;p=' . $row['topic_last_post_id'] . '#p' . $row['topic_last_post_id'],
			'U_LAST_POST_AUTHOR'	=> get_username_string('profile', $row['topic_last_poster_id'], $row['topic_last_poster_name'], $row['topic_last_poster_colour']),
			'U_TOPIC_AUTHOR'		=> get_username_string('profile', $row['topic_poster'], $row['topic_first_poster_name'], $row['topic_first_poster_colour']),
			'U_VIEW_TOPIC'			=> $view_topic_url,
			'U_VIEW_FORUM'			=> $view_forum_url,
			'U_MCP_REPORT'			=> append_sid("{$phpbb_root_path}mcp.$phpEx", 'i=reports&amp;mode=reports&amp;f=' . $forum_id . '&amp;t=' . $topic_id, true, $user->session_id),
			'U_MCP_QUEUE'			=> $u_mcp_queue,
		));
	}
	$db->sql_freeresult($result);

	$template->assign_vars(array(
		'RT_DISPLAY'			=> true,
		'NEWEST_POST_IMG'		=> $user->img('icon_topic_newest', 'VIEW_NEWEST_POST'),
		'RT_PAGE_NUMBER'		=> on_page($num_topics, $topics_per_page, $start),
		'RT_PAGINATION'			=> generate_pagination(append_sid($phpbb_root_path . $user->page['page_name']), $num_topics, $topics_per_page, $start),
	));

}


?>