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

if (!function_exists('display_forums') || !function_exists('topic_status'))
{
	include($phpbb_root_path . 'includes/functions_display.' . $phpEx);
}

/**
* Since #48835 is a will not fix, we copy the function and make it work, else it is just a simple cut'n'paste
*/
function recent_topics_generate_pagination($base_url, $num_items, $per_page, $start_item, $add_prevnext_text = false, $tpl_prefix = '', $start_item_name = '', $anchor = '')
{
	global $template, $user;

	// Make sure $per_page is a valid value
	$per_page = ($per_page <= 0) ? 1 : $per_page;

	$seperator = '<span class="page-sep">' . $user->lang['COMMA_SEPARATOR'] . '</span>';
	$total_pages = ceil($num_items / $per_page);

	if ($total_pages == 1 || !$num_items)
	{
		return false;
	}

	$on_page = floor($start_item / $per_page) + 1;
	$url_delim = (strpos($base_url, '?') === false) ? '?' : '&amp;';

	$page_string = ($on_page == 1) ? '<strong>1</strong>' : '<a href="' . $base_url . (($anchor) ? '#' . $anchor : '') . '">1</a>';

	if ($total_pages > 5)
	{
		$start_cnt = min(max(1, $on_page - 4), $total_pages - 5);
		$end_cnt = max(min($total_pages, $on_page + 4), 6);

		$page_string .= ($start_cnt > 1) ? ' ... ' : $seperator;

		for ($i = $start_cnt + 1; $i < $end_cnt; $i++)
		{
			$page_string .= ($i == $on_page) ? '<strong>' . $i . '</strong>' : '<a href="' . $base_url . "{$url_delim}{$start_item_name}=" . (($i - 1) * $per_page) . (($anchor) ? '#' . $anchor : '') . '">' . $i . '</a>';
			if ($i < $end_cnt - 1)
			{
				$page_string .= $seperator;
			}
		}

		$page_string .= ($end_cnt < $total_pages) ? ' ... ' : $seperator;
	}
	else
	{
		$page_string .= $seperator;

		for ($i = 2; $i < $total_pages; $i++)
		{
			$page_string .= ($i == $on_page) ? '<strong>' . $i . '</strong>' : '<a href="' . $base_url . "{$url_delim}{$start_item_name}=" . (($i - 1) * $per_page) . (($anchor) ? '#' . $anchor : '') . '">' . $i . '</a>';
			if ($i < $total_pages)
			{
				$page_string .= $seperator;
			}
		}
	}

	$page_string .= ($on_page == $total_pages) ? '<strong>' . $total_pages . '</strong>' : '<a href="' . $base_url . "{$url_delim}{$start_item_name}=" . (($total_pages - 1) * $per_page) . (($anchor) ? '#' . $anchor : '') . '">' . $total_pages . '</a>';

	if ($add_prevnext_text)
	{
		if ($on_page != 1)
		{
			$page_string = '<a href="' . $base_url . "{$url_delim}{$start_item_name}=" . (($on_page - 2) * $per_page) . (($anchor) ? '#' . $anchor : '') . '">' . $user->lang['PREVIOUS'] . '</a>&nbsp;&nbsp;' . $page_string;
		}

		if ($on_page != $total_pages)
		{
			$page_string .= '&nbsp;&nbsp;<a href="' . $base_url . "{$url_delim}{$start_item_name}=" . ($on_page * $per_page) . (($anchor) ? '#' . $anchor : '') . '">' . $user->lang['NEXT'] . '</a>';
		}
	}

	$template->assign_vars(array(
		$tpl_prefix . 'BASE_URL'		=> $base_url,
		'A_' . $tpl_prefix . 'BASE_URL'	=> addslashes($base_url),
		$tpl_prefix . 'PER_PAGE'		=> $per_page,

		$tpl_prefix . 'PREVIOUS_PAGE'	=> ($on_page == 1) ? '' : $base_url . "{$url_delim}{$start_item_name}=" . (($on_page - 2) * $per_page),
		$tpl_prefix . 'NEXT_PAGE'		=> ($on_page == $total_pages) ? '' : $base_url . "{$url_delim}{$start_item_name}=" . ($on_page * $per_page),
		$tpl_prefix . 'TOTAL_PAGES'		=> $total_pages,
	));

	return $page_string;
}

function display_recent_topics($topics_per_page, $num_pages, $excluded_topics, $tpl_loopname = 'recenttopicrow', $spec_forum_id = 0, $include_subforums = true, $display_parent_forums = true)
{
	global $auth, $cache, $config, $db, $template, $user;
	global $phpbb_root_path, $phpEx;

	$user->add_lang('mods/info_acp_recenttopics');

	/**
	* Set some internal needed variables
	*/
	$start = request_var($tpl_loopname . '_start', 0);
	$excluded_topic_ids = explode(', ', $excluded_topics);
	$total_limit	= $topics_per_page * $num_pages;
	$ga_forum_id	= 0; // Forum id we use for global announcements

	/**
	* Get the forums we take our topics from
	*/
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
	$forum_ids = array_unique($forum_ary);

	if (!sizeof($forum_ids))
	{
		// No forums with f_read
		return;
	}

	$spec_forum_ary = array();
	if ($spec_forum_id)
	{
		// Only take a special-forum
		if (!$include_subforums)
		{
			if (!in_array($spec_forum_id, $forum_ids))
			{
				return;
			}
			$forum_ids = array();
			$sql = 'SELECT 1 as display_forum
				FROM ' . FORUMS_TABLE . '
				WHERE forum_id = ' . $spec_forum_id . '
					AND forum_recent_topics = 1';
			$result = $db->sql_query_limit($sql, 1);
			$display_forum = (bool) $db->sql_fetchfield('display_forum');
			$db->sql_freeresult($result);

			if ($display_forum)
			{
				$forum_ids = array($spec_forum_id);
			}
		}
		else
		{
			// ... and it's subforums
			$sql = 'SELECT f2.forum_id
				FROM ' . FORUMS_TABLE . ' f1
				LEFT JOIN ' . FORUMS_TABLE . " f2
					ON (f2.left_id BETWEEN f1.left_id AND f1.right_id
						AND f2.forum_recent_topics = 1)
				WHERE f1.forum_id = $spec_forum_id
					AND f1.forum_recent_topics = 1
				ORDER BY f2.left_id DESC";
			$result = $db->sql_query($sql);

			while ($row = $db->sql_fetchrow($result))
			{
				$spec_forum_ary[] = $row['forum_id'];
			}
			$db->sql_freeresult($result);

			$forum_ids = array_intersect($forum_ids, $spec_forum_ary);

			if (!sizeof($forum_ids))
			{
				return;
			}
		}
	}
	else
	{
		$sql = 'SELECT forum_id
			FROM ' . FORUMS_TABLE . '
			WHERE ' . $db->sql_in_set('forum_id', $forum_ids) . '
				AND forum_recent_topics = 1';
		$result = $db->sql_query($sql);

		$forum_ids = array();
		while ($row = $db->sql_fetchrow($result))
		{
			$forum_ids[] = $row['forum_id'];
		}
		$db->sql_freeresult($result);
	}

	// No forums with f_read
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
	$sql_query_array = array(
		'SELECT'	=> 't.forum_id, t.topic_id, t.topic_type, t.icon_id, tt.mark_time, ft.mark_time as f_mark_time',
		'FROM'		=> array(TOPICS_TABLE => 't'),
		'LEFT_JOIN'	=> array(
			array(
				'FROM'	=> array(TOPICS_TRACK_TABLE => 'tt'),
				'ON'	=> 'tt.topic_id = t.topic_id AND tt.user_id = ' . $user->data['user_id'],
			),
			array(
				'FROM'	=> array(FORUMS_TRACK_TABLE => 'ft'),
				'ON'	=> 'ft.forum_id = t.forum_id AND ft.user_id = ' . $user->data['user_id'],
			),
		),
		'WHERE'		=> '
			(
				(' . $db->sql_in_set('t.topic_id', $excluded_topic_ids, true) . '
					AND ' . $db->sql_in_set('t.forum_id', $forum_ids) . '
				)
				OR t.topic_type = ' . POST_GLOBAL . '
			)
			AND t.topic_status <> ' . ITEM_MOVED . '
			AND (' . $db->sql_in_set('t.forum_id', $m_approve_ids, false, true) . '
				OR t.topic_approved = 1)',
		'ORDER_BY'	=> 't.topic_last_post_time DESC',
	);

	// Is a soft delete MOD installed?
	if (file_exists("{$phpbb_root_path}includes/mods/soft_delete.$phpEx"))
	{
		$sql_query_array['WHERE'] .= ' AND topic_deleted = 0';
	}

	$sql = $db->sql_build_query('SELECT', $sql_query_array);
	$result = $db->sql_query_limit($sql, $total_limit);

	$forums = $ga_topic_ids = $topic_ids = array();
	$num_topics = 0;
	$obtain_icons = false;
	while ($row = $db->sql_fetchrow($result))
	{
		$num_topics++;
		if (($num_topics > $start) && ($num_topics <= ($start + $topics_per_page)))
		{
			$topic_ids[] = $row['topic_id'];

			$rowset[$row['topic_id']] = $row;
			if (!isset($forums[$row['forum_id']]) && $user->data['is_registered'] && $config['load_db_lastread'])
			{
				$forums[$row['forum_id']]['mark_time'] = $row['f_mark_time'];
			}
			$forums[$row['forum_id']]['topic_list'][] = $row['topic_id'];
			$forums[$row['forum_id']]['rowset'][$row['topic_id']] = &$rowset[$row['topic_id']];

			if ($row['icon_id'] && $auth->acl_get('f_icons', $row['forum_id']))
			{
				$obtain_icons = true;
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
	if ($obtain_icons)
	{
		$icons = $cache->obtain_icons();
	}
	else
	{
		$icons = array();
	}

	// Borrowed from search.php
	foreach ($forums as $forum_id => $forum)
	{
		if ($user->data['is_registered'] && $config['load_db_lastread'])
		{
			$topic_tracking_info[$forum_id] = get_topic_tracking($forum_id, $forum['topic_list'], $forum['rowset'], array($forum_id => $forum['mark_time']), ($forum_id) ? false : $forum['topic_list']);
		}
		else if ($config['load_anon_lastread'] || $user->data['is_registered'])
		{
			$tracking_topics = (isset($_COOKIE[$config['cookie_name'] . '_track'])) ? ((STRIP) ? stripslashes($_COOKIE[$config['cookie_name'] . '_track']) : $_COOKIE[$config['cookie_name'] . '_track']) : '';
			$tracking_topics = ($tracking_topics) ? tracking_unserialize($tracking_topics) : array();

			$topic_tracking_info[$forum_id] = get_complete_topic_tracking($forum_id, $forum['topic_list'], ($forum_id) ? false : $forum['topic_list']);

			if (!$user->data['is_registered'])
			{
				$user->data['user_lastmark'] = (isset($tracking_topics['l'])) ? (int) (base_convert($tracking_topics['l'], 36, 10) + $config['board_startdate']) : 0;
			}
		}
	}

	// Now only pull the data of the requested topics
	$sql_query_array = array(
		'SELECT'	=> 't.*, tp.topic_posted, f.forum_name',
		'FROM'		=> array(TOPICS_TABLE => 't'),
		'LEFT_JOIN'	=> array(
			array(
				'FROM'	=> array(TOPICS_POSTED_TABLE => 'tp'),
				'ON'	=> 't.topic_id = tp.topic_id AND tp.user_id = ' . $user->data['user_id'],
			),
			array(
				'FROM'	=> array(FORUMS_TABLE => 'f'),
				'ON'	=> 'f.forum_id = t.forum_id',
			),
		),
		'WHERE'		=> $db->sql_in_set('t.topic_id', $topic_ids),
		'ORDER_BY'	=> 't.topic_last_post_time DESC',
	);

	if ($display_parent_forums)
	{
		$sql_query_array['SELECT'] .= ', f.parent_id, f.forum_parents, f.left_id, f.right_id';
	}

	$sql = $db->sql_build_query('SELECT', $sql_query_array);
	$result = $db->sql_query_limit($sql, $topics_per_page);

	$topic_icons = array();
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
		topic_status($row, $replies, (isset($topic_tracking_info[$forum_id][$row['topic_id']]) && $row['topic_last_post_time'] > $topic_tracking_info[$forum_id][$row['topic_id']]) ? true : false, $folder_img, $folder_alt, $topic_type);

		$unread_topic = (isset($topic_tracking_info[$forum_id][$row['topic_id']]) && $row['topic_last_post_time'] > $topic_tracking_info[$forum_id][$row['topic_id']]) ? true : false;

		$view_topic_url = append_sid("{$phpbb_root_path}viewtopic.$phpEx", 'f=' . $forum_id . '&amp;t=' . $topic_id);
		$view_forum_url = append_sid("{$phpbb_root_path}viewforum.$phpEx", 'f=' . $forum_id);
		$topic_unapproved = (!$row['topic_approved'] && $auth->acl_get('m_approve', $forum_id)) ? true : false;
		$posts_unapproved = ($row['topic_approved'] && $row['topic_replies'] < $row['topic_replies_real'] && $auth->acl_get('m_approve', $forum_id)) ? true : false;
		$u_mcp_queue = ($topic_unapproved || $posts_unapproved) ? append_sid("{$phpbb_root_path}mcp.$phpEx", 'i=queue&amp;mode=' . (($topic_unapproved) ? 'approve_details' : 'unapproved_posts') . "&amp;t=$topic_id", true, $user->session_id) : '';
		$s_type_switch = ($row['topic_type'] == POST_ANNOUNCE || $row['topic_type'] == POST_GLOBAL) ? 1 : 0;
		if (!empty($icons[$row['icon_id']]))
		{
			$topic_icons[] = $topic_id;
		}

		$template->assign_block_vars($tpl_loopname, array(
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
			'REPORTED_IMG'			=> ($row['topic_reported'] && $auth->acl_get('m_report', $forum_id)) ? $user->img('icon_topic_reported', 'TOPIC_REPORTED') : '',

			'S_TOPIC_TYPE'			=> $row['topic_type'],
			'S_USER_POSTED'			=> (isset($row['topic_posted']) && $row['topic_posted']) ? true : false,
			'S_UNREAD_TOPIC'		=> $unread_topic,
			'S_TOPIC_REPORTED'		=> ($row['topic_reported'] && $auth->acl_get('m_report', $forum_id)) ? true : false,
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

		if ($display_parent_forums)
		{
			$forum_parents = get_forum_parents($row);

			foreach ($forum_parents as $parent_id => $data)
			{
				$template->assign_block_vars($tpl_loopname . '.parent_forums', array(
					'FORUM_ID'			=> $parent_id,
					'FORUM_NAME'		=> $data[0],
					'U_VIEW_FORUM'		=> append_sid("{$phpbb_root_path}viewforum.$phpEx", 'f=' . $parent_id),
				));
			}
		}
	}
	$db->sql_freeresult($result);

	// Get URL-parameters for pagination
	$url_params = explode('&', $user->page['query_string']);
	$append_params = false;
	foreach ($url_params as $param)
	{
		if (!$param) continue;
		if (strpos($param, '=') === false)
		{
			// Fix MSSTI Advanced BBCode MOD
			$append_params[$param] = '1';
			continue;
		}
		list($name, $value) = explode('=', $param);
		if ($name != $tpl_loopname . '_start')
		{
			$append_params[$name] = $value;
		}
	}

	$template->assign_vars(array(
		'S_TOPIC_ICONS'			=> (sizeof($topic_icons)) ? true : false,
		'NEWEST_POST_IMG'		=> $user->img('icon_topic_newest', 'VIEW_NEWEST_POST'),
		'LAST_POST_IMG'			=> $user->img('icon_topic_latest', 'VIEW_LATEST_POST'),
		strtoupper($tpl_loopname) . '_DISPLAY'		=> true,
		strtoupper($tpl_loopname) . '_PAGE_NUMBER'	=> on_page($num_topics, $topics_per_page, $start),
		strtoupper($tpl_loopname) . '_PAGINATION'	=> recent_topics_generate_pagination(append_sid($phpbb_root_path . $user->page['page_name'], $append_params), $num_topics, $topics_per_page, $start, false, strtoupper($tpl_loopname), $tpl_loopname . '_start', $tpl_loopname),
	));
}

?>