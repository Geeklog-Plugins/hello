<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Hello Plugin 2.1.1                                                        |
// +---------------------------------------------------------------------------+
// | search.php                                                                |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2016 by the following authors:                              |
// |                                                                           |
// | Authors: ::Ben - ben AT geeklog DOT fr                                    |
// +---------------------------------------------------------------------------+
// |                                                                           |
// | This program is free software; you can redistribute it and/or             |
// | modify it under the terms of the GNU General Public License               |
// | as published by the Free Software Foundation; either version 2            |
// | of the License, or (at your option) any later version.                    |
// |                                                                           |
// | This program is distributed in the hope that it will be useful,           |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of            |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the             |
// | GNU General Public License for more details.                              |
// |                                                                           |
// | You should have received a copy of the GNU General Public License         |
// | along with this program; if not, write to the Free Software Foundation,   |
// | Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.           |
// |                                                                           |
// +---------------------------------------------------------------------------+
// 


require_once('../../../lib-common.php');

$display = '';

// Make sure user has access to this page
if (!SEC_hasRights ('user.edit')) {
    $display .= COM_startBlock ($MESSAGE[30]);
    $display .= $MESSAGE[37];
    $display .= COM_endBlock ();

    COM_errorLog ("User {$_USER['username']} tried to illegally access the Hello search page.", 1);
    
    $display = COM_createHTMLDocument($display);
    COM_output($display);
    exit;
}


function display_form ($query = '')
{
    global $LANG_HELLO01, $PHP_SELF;

    $display = '';

    $display .= '<form action="' . $PHP_SELF . '" method="GET">' . LB;
    $display .= '<p>' . $LANG_HELLO01['search_text'] . '</p>' . LB;
    $display .= '<input type="text" size="40" name="query" value="' . $query . '">' . LB;
    $display .= '<input type="submit" value="' . $LANG_HELLO01['search_button'] . '">' . LB;
    $display .= '<input type="hidden" name="mode" value="search">' . LB;
    $display .= '</form>' . LB;

    return $display;
}

function search_user ($query)
{
    global $_TABLES, $_CONF, $LANG28, $LANG_HELLO01, $PHP_SELF;

    $retval = '';

    if (empty ($query) || (is_numeric ($query) && ($query < 2))) {
        $retval .= display_form ();
    } else {
        $retval .= '[ <a href="' . $_CONF['site_admin_url'] . '/plugins/hello/search.php">' . $LANG_HELLO01['new_search'] . '</a>  ]</p>' . LB;

        if (is_numeric ($query)) {
            $sql = "SELECT uid,username,fullname,email FROM {$_TABLES['users']} WHERE uid = '{$query}'";
        } else {
            $sql = "SELECT uid,username,fullname,email FROM {$_TABLES['users']} WHERE (uid > 1) AND (username LIKE '%{$query}%' OR fullname LIKE '%{$query}%' OR email LIKE '%{$query}%') ORDER BY uid";
        }

        $result = DB_query ($sql);
        $num = DB_numRows ($result);
        if ($num > 0) {
            $retval .= '<p>' . $LANG_HELLO01['inspect_text'] . '</p>' . LB;
            $retval .= '<table width="100%" border="0">' . LB;
            $retval .= '<tr><th colspan="2" align="left">' . $LANG28[3] . '</th><th align="left">' . $LANG28[4] . '</th><th align="left">' . $LANG28[7] . '</th></tr>' . LB;
            for ($i = 0; $i < $num; $i++) {
                $A = DB_fetchArray ($result);
                $retval .= '<tr>';
                $retval .= '<td><a href="' . $_CONF['site_url'] . '/users.php?mode=profile&amp;uid=' . $A['uid'] . '"><img src="' . $_CONF['layout_url'] . '/images/person.png" border="0"></a></td>';
                $retval .= '<td><a href="' . $PHP_SELF . '?mode=inspect&amp;uid=' . $A['uid'] . '">' . $A['username'] . '</a></td>';
                $retval .= '<td>' . $A['fullname'] . '</td>';
                $retval .= '<td><a href="mailto:' . $A['email'] . '">' . $A['email'] . '</a></td>';
                $retval .= '</tr>' . LB;
            }
            $retval .= '</table>' . LB;
        } else {
            if (is_numeric ($query)) {
                $retval .= '<p>' . sprintf ($LANG_HELLO01['uid_not_found'], $query);
            } else {
                $retval .= '<p>' . sprintf ($LANG_HELLO01['not_found'], $query);
            }
            $retval .= ' ' . $LANG_HELLO01['try_again'] . '</p>' . LB;
            $retval .= display_form ($query);
        }
    }

    return $retval;
}

function inspect ($uid)
{
    global $_TABLES, $_CONF, $LANG28, $LANG_HELLO01, $PHP_SELF;

    $retval = '';

    if ($uid > 1) {
        $forum = false;
        if (DB_getItem ($_TABLES['plugins'], 'pi_enabled',
                        "pi_name = 'forum'") == 1) {
            $forum = true;
        }

        $result = DB_query ("SELECT username,fullname,email FROM {$_TABLES['users']} WHERE uid = '{$uid}'");
        $U = DB_fetchArray ($result);

        $digest = DB_getItem ($_TABLES['userindex'], 'etids', "uid = '{$uid}'");

        $retval .= '[ <a href="' . $_CONF['site_admin_url'] . '/plugins/hello/search.php">' . $LANG_HELLO01['new_search'] . '</a>  ]</p>' . LB;
        $retval .= '<p>' . $LANG_HELLO01['user'] . ': <b>' . $U['username'] . '</b> ';
        if (!empty ($U['fullname'])) {
            $retval .= '(' . $U['fullname'] . ') ';
        }
        $retval .= '&lt;<a href="mailto:' . $U['email'] . '">' . $U['email'] . '</a>&gt;</p>' . LB;
        $retval .= '<p>' . $LANG_HELLO01['topics'] . ': ';
        if (empty ($digest)) {
            $retval .= '<em>' . $LANG_HELLO01['all_topics'] . '</em>';
        } else if ($digest == '-') {
            $retval .= '<em>' . $LANG_HELLO01['no_topics'] . '</em>';
        } else {
            $topics = explode (' ', $digest);
            foreach ($topics as $t) {
                $tname = DB_getItem ($_TABLES['topics'], 'topic', "tid = '{$t}'");
                $retval .= '<a href="' . $_CONF['site_url'] . '/index.php?topic=' . $t . '">' . $tname . '</a>, ';
            }
        }
        $retval .= '</p>';

        $forums = 0;
        if ($forum) {
            $retval .= '<p>' . $LANG_HELLO01['forums'] . ': ';

            $f = array ();
            $result = DB_query ("SELECT forum_id FROM {$_TABLES['gf_watch']} WHERE uid = '$uid' ORDER BY forum_id");
            $fnum = DB_numRows ($result);
            for ($i = 0; $i < $fnum; $i++) {
                $A = DB_fetchArray ($result);
                $f[$A['forum_id']] = DB_getItem ($_TABLES['gf_forums'],
                                'forum_name', "forum_id = '{$A['forum_id']}'");
            }
            if (count ($f) == 0) {
                $retval .= '<em>' . $LANG_HELLO01['no_forums'] . '</em>';
            } else {
                $forums++;
                foreach ($f as $id => $name) {
                    $retval .= '<a href="' . $_CONF['site_url']
                            . '/forum/index.php?forum=' . $id . '">' . $name
                            . '</a>, ';
                }
            }
            $retval .= '</p>';

            $result = DB_query ("SELECT COUNT(*) AS count FROM {$_TABLES['gf_watch']} WHERE (uid = '$uid') AND (topic_id > 0)");
            list($tf) = DB_fetchArray ($result);
            if ($tf > 0) {
                $retval .= '<p>' . $tf . ' ' . $LANG_HELLO01['forum_topics'] . '</p>';
                $forums += $tf;
            }
        }

        if (($digest != '-') || ($forums > 0)) {
            $retval .= '<form action="' . $PHP_SELF . '" action="GET">' . LB;
            $retval .= '<input type="submit" value="' . $LANG_HELLO01['reset_button'] . '">' . LB;
            $retval .= '<input type="hidden" name="mode" value="reset">' . LB;
            $retval .= '<input type="hidden" name="uid" value="' . $uid . '">' . LB;
            $retval .= '</form>' . LB;
        }
    } else {
        $retval .= display_form ();
    }

    return $retval;
}

function reset_it ($uid)
{
    global $_TABLES, $LANG_HELLO01;

    $retval = '';

    if ($uid > 1) {
        DB_query ("UPDATE {$_TABLES['userindex']} SET etids = '-' WHERE uid = '{$uid}'");

        if (DB_getItem ($_TABLES['plugins'], 'pi_enabled',
                        "pi_name = 'forum'") == 1) {
            DB_query ("DELETE FROM {$_TABLES['gf_watch']} WHERE uid = '$uid'");
        }

        $username = DB_getItem ($_TABLES['users'], 'username', "uid = '{$uid}'");
        $retval .= '<p>' . sprintf ($LANG_HELLO01['success'], $username) . '</p>' . LB;
    }

    $retval .= display_form ();

    return $retval;
}

$display .= hello_admin_menu();
$display .= COM_startBlock ($LANG_HELLO01['block_headline']);

switch ($_GET['mode'])
{
    case 'inspect':
        $display .= inspect ($_GET['uid']);
        break;
    case 'reset':
        $display .= reset_it ($_GET['uid']);
        break;
    case 'search':
        $display .= search_user ($_GET['query']);
        break;
    default:
        
		$display .= display_form ();
        break;
}

$display .= COM_endBlock ();

$display = COM_createHTMLDocument($display);
COM_output($display);

?>
