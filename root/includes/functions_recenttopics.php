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

$limit			= $config['rt_number'];
$page_limit		= $config['rt_page_number'] * $config['rt_number'];
$start			= request_var('start', 0);
$rt_anti_topics	= $config['rt_anti_topics'];
$onlyforum		= request_var('f', 0);

// Only call the query, if we need to
if ($onlyforum)
{
	$onlyforum_ary = array($onlyforum);
	$sql = 'SELECT parent_id, forum_id FROM ' . FORUMS_TABLE . "
		ORDER BY left_id";
	$result = $db->sql_query($sql);
	while ($row = $db->sql_fetchrow($result))
	{
		if (in_array($row['parent_id'], $onlyforum_ary))
		{
			$onlyforum_ary[] = $row['forum_id'];
		}
	}
	$db->sql_freeresult($result);
}

// Get the allowed forums
$forum_ary = $topic_ary = array();
$forum_read_ary = $auth->acl_getf('f_read');
foreach ($forum_read_ary as $forum_id => $allowed)
{
	if ($allowed['f_read'])
	{
		$forum_ary[] = (int) $forum_id;
	}
}
$forum_ary = array_unique($forum_ary);

// Get the allowed topics
$sql = 'SELECT t.topic_id
	FROM ' . TOPICS_TABLE . ' t
	LEFT JOIN ' . FORUMS_TABLE . ' f
		ON f.forum_id = t.forum_id
	WHERE (
			f.forum_recent_topics = 1
			' . (($onlyforum) ? ' AND ' . $db->sql_in_set('t.forum_id', $onlyforum_ary) : '') . '
			AND ' . $db->sql_in_set('t.topic_id', $rt_anti_topics, true) . '
			AND ' . $db->sql_in_set('t.forum_id', $forum_ary, false, true) . '
		)
		OR t.topic_type = ' . POST_GLOBAL . '
	GROUP BY t.topic_last_post_id
	ORDER BY t.topic_last_post_time DESC';
$result = $db->sql_query_limit($sql, $limit + 1, $start);
while ($row = $db->sql_fetchrow($result))
{
	$topic_ary[] = $row['topic_id'];
}
$db->sql_freeresult($result);

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
	WHERE ' . $db->sql_in_set('t.topic_id', $topic_ary, false, true) . '
	ORDER BY t.topic_last_post_time DESC';
$result = $db->sql_query_limit($sql, $limit, 0);

while ($row = $db->sql_fetchrow($result))
{
	$topic_id = $row['topic_id'];
	$forum_id = $row['forum_id'];

	// Cheat for Global Announcements on the unread-link: copied from search.php
	if (!$forum_id && !isset($g_forum_id))
	{
		$sql2 = 'SELECT forum_id
			FROM ' . FORUMS_TABLE . '
			WHERE forum_type = ' . FORUM_POST . '
				AND ' . $db->sql_in_set('forum_id', $forum_ary, false, true);
		$result2 = $db->sql_query_limit($sql2, 1);
		$g_forum_id = (int) $db->sql_fetchfield('forum_id');
		$db->sql_freeresult($result2);
		$forum_id = $g_forum_id;
	}
	else if (!$forum_id)
	{
		$forum_id = $g_forum_id;
	}

	$s_type_switch_test = ($row['topic_type'] == POST_ANNOUNCE || $row['topic_type'] == POST_GLOBAL) ? 1 : 0;
	$replies = ($auth->acl_get('m_approve', $forum_id)) ? $row['topic_replies_real'] : $row['topic_replies'];
	$topic_tracking_info = get_complete_topic_tracking($forum_id, $topic_id, $global_announce_list = false);
	$unread_topic = (isset($topic_tracking_info[$topic_id]) && $row['topic_last_post_time'] > $topic_tracking_info[$topic_id]) ? true : false;
	$folder_img = $folder_alt = $topic_type = $folder = $folder_new = '';
	switch ($row['topic_type'])
	{
		case POST_GLOBAL:
			$topic_type = $user->lang['VIEW_TOPIC_GLOBAL'];
			$folder = 'global_read';
			$folder_new = 'global_unread';
		break;
		case POST_ANNOUNCE:
			$topic_type = $user->lang['VIEW_TOPIC_ANNOUNCEMENT'];
			$folder = 'announce_read';
			$folder_new = 'announce_unread';
		break;
		case POST_STICKY:
			$topic_type = $user->lang['VIEW_TOPIC_STICKY'];
			$folder = 'sticky_read';
			$folder_new = 'sticky_unread';
		break;
		default:
			$topic_type = '';
			$folder = 'topic_read';
			$folder_new = 'topic_unread';
			if ($config['hot_threshold'] && $replies >= $config['hot_threshold'] && $row['topic_status'] != ITEM_LOCKED)
			{
				$folder .= '_hot';
				$folder_new .= '_hot';
			}
		break;
	}
	if ($row['topic_status'] == ITEM_LOCKED)
	{
		$topic_type = $user->lang['VIEW_TOPIC_LOCKED'];
		$folder .= '_locked';
		$folder_new .= '_locked';
	}
	if ($row['topic_posted'])
	{
		$folder .= '_mine';
		$folder_new .= '_mine';
	}
	if ($row['topic_type'] == POST_GLOBAL)
	{
		$global_announce_list[$row['topic_id']] = true;
	}
	$folder_img = ($unread_topic) ? $folder_new : $folder;
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
		'PAGINATION'		=> topic_generate_pagination($replies, $view_topic_url),
		'REPLIES'			=> $replies,
		'VIEWS'				=> $row['topic_views'],
		'TOPIC_TITLE'		=> censor_text($row['topic_title']),
		'FORUM_NAME'		=> $row['forum_name'],
		'TOPIC_TYPE'		=> $topic_type,
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
		'U_NEWEST_POST'			=> $view_topic_url . '&amp;view=unread#unread',
		'U_LAST_POST'			=> $view_topic_url . '&amp;p=' . $row['topic_last_post_id'] . '#p' . $row['topic_last_post_id'],
		'U_LAST_POST_AUTHOR'	=> get_username_string('profile', $row['topic_last_poster_id'], $row['topic_last_poster_name'], $row['topic_last_poster_colour']),
		'U_TOPIC_AUTHOR'		=> get_username_string('profile', $row['topic_poster'], $row['topic_first_poster_name'], $row['topic_first_poster_colour']),
		'U_VIEW_TOPIC'			=> $view_topic_url,
		'U_VIEW_FORUM'			=> $view_forum_url,
		'U_MCP_REPORT'			=> append_sid("{$phpbb_root_path}mcp.$phpEx", 'i=reports&amp;mode=reports&amp;f=' . $forum_id . '&amp;t=' . $topic_id, true, $user->session_id),
		'U_MCP_QUEUE'			=> $u_mcp_queue,
		'S_TOPIC_TYPE_SWITCH'	=> ($s_type_switch == $s_type_switch_test) ? -1 : $s_type_switch_test,
	));
}
$db->sql_freeresult($result);

$topic_counter = 0;
if (count($topic_ary) > $limit || $start)
{
	$sql = 'SELECT t.topic_id
		FROM ' . TOPICS_TABLE . ' t
		LEFT JOIN ' . FORUMS_TABLE . ' f
			ON f.forum_id = t.forum_id
		WHERE (
				f.forum_recent_topics = 1
				' . (($onlyforum) ? ' AND ' . $db->sql_in_set('t.forum_id', $onlyforum_ary) : '') . '
				AND ' . $db->sql_in_set('t.topic_id', $rt_anti_topics, true) . '
				AND ' . $db->sql_in_set('t.forum_id', $forum_ary, false, true) . '
			)
			OR t.topic_type = ' . POST_GLOBAL . '
		GROUP BY t.topic_last_post_id';
	$result = $db->sql_query($sql);
	while ($row = $db->sql_fetchrow($result))
	{
		$topic_counter++;
	}
	$db->sql_freeresult($result);
}

$topic_counter = ($page_limit < $topic_counter) ? $page_limit : $topic_counter;
$template->assign_vars(array(
	'RT_DISPLAY'			=> true,
	'NEWEST_POST_IMG'		=> $user->img('icon_topic_newest', 'VIEW_NEWEST_POST'),
	'PAGE_NUMBER'			=> on_page($topic_counter, $limit, $start),
	'PAGINATION'			=> generate_pagination(append_sid("{$phpbb_root_path}index.$phpEx"), $topic_counter, $limit, $start),
));
?>