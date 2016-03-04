<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Hello Plugin 2.1.1                                                        |
// +---------------------------------------------------------------------------+
// | index.php                                                                 |
// |                                                                           |
// | Geeklog hello administration page                                         |
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


require_once '../../../lib-common.php';
require_once '../../auth.inc.php';


$display = '';

if (!SEC_hasRights ('hello.edit')) {
    $display .= COM_startBlock ($MESSAGE[30], '',
                                COM_getBlockTemplate ('_msg_block', 'header'));
    $display .= $MESSAGE[36];
    $display .= COM_endBlock (COM_getBlockTemplate ('_msg_block', 'footer'));
    $display = COM_createHTMLDocument($display);
    
    COM_accessLog ("User {$_USER['username']} tried to illegally access the hello administration screen.");
    
    COM_output($display);
    exit;
}

function display_intro() {
    global $LANG_HELLO01;
	
    $display = '<p>' . $LANG_HELLO01['overview'] . '</p>';
	
	return $display;
}

//From mdigest plugin
function HELLO_search_form ($query = '')
{
    global $_CONF, $LANG_HELLO01, $PHP_SELF;

    $display = '';

    $display .= '<form action="' . $_CONF['site_admin_url'] . '/plugins/hello/search.php" method="GET">' . LB;
    $display .= '<p>' . $LANG_HELLO01['search_text'] . '</p>' . LB;
    $display .= '<input type="text" size="40" name="query" value="' . $query . '">' . LB;
    $display .= '<input type="submit" value="' . $LANG_HELLO01['search_button'] . '">' . LB;
    $display .= '<input type="hidden" name="mode" value="search">' . LB;
    $display .= '</form>' . LB;

    return $display;
}

function HELLO_send_digest ()
{
    global $_CONF, $_TABLES, $LANG_HELLO01, $PHP_SELF;

    $display = '';

    if ($_CONF['emailstories'] == 1) {
        if (isset ($_POST['sendit']) && !empty ($_POST['sendit'])) {
            $display .= '<p>' . $LANG_HELLO01['digest_sent'] . '</p>' . LB;
			$display .= HELLO_emailUserTopics ();
        } else if (isset ($_POST['resetit']) && !empty ($_POST['resetit'])) {
            DB_query ("UPDATE {$_TABLES['vars']} SET value = NOW() WHERE name = 'lastemailedstories'");
            $display .= '<p>' . $LANG_HELLO01['digest_reset'] . '</p>' . LB;
        } else {
            $display .= '<p>' . $LANG_HELLO01['digest_intro'] . '</p>' . LB;
            $display .= '<p>' . $LANG_HELLO01['explain_reset'] . '</p>' . LB;
            $lastrun = DB_getItem ($_TABLES['vars'], 'value', "name = 'lastemailedstories'");
            if (empty ($lastrun)) {
                $display .= '<p>' . $LANG_HELLO01['digest_last_sent'] . ' ' . $LANG_HELLO01['never'] . '</p>' . LB;
				$lastrun = 0;
            } else {
                $display .= '<p>' . $LANG_HELLO01['digest_last_sent'] . ' <b>' . $lastrun . '</b></p>' . LB;
            }

            $sql = "SELECT sid FROM {$_TABLES['stories']} WHERE draft_flag = 0 AND date <= NOW() AND date >= '{$lastrun}'";
            $result = DB_query ($sql);
            $count = DB_numRows ($result);
            if ($count == 0) {
                $display .= '<p>' . $LANG_HELLO01['no_stories'] . '</p>' . LB;
            } else {
                $msg = sprintf ($LANG_HELLO01['num_stories'], $count);
                $display .= '<p>' . $msg . '</p>' . LB;

                $display .= '<form action="' . $PHP_SELF . '" method="POST">' . LB;
                $display .= '<input type="submit" value="' . $LANG_HELLO01['send_button'] . '" name="sendit">' . LB;
                $display .= ' &nbsp; <input type="submit" value="' . $LANG_HELLO01['reset_button'] . '" name="resetit">' . LB;
                $display .= '</form>' . LB;
            }
    }
    } else {
        $display .= '<p>' . $LANG_HELLO01['not_enabled1'] . '</p>' . LB;
        $display .= '<blockquote><code>$_CONF[\'emailstories\'] = 1;</code></blockquote>' . LB;
        $display .= '<p>' . $LANG_HELLO01['not_enabled2'] . '</p>' . LB;
    }

    return $display;
}

// MAIN


$display .= hello_admin_menu();
$display .= display_intro ();

if (SEC_hasRights ('user.mail')) {
    $display .= COM_startBlock ($LANG_HELLO01['mdigest']);
    $display .= HELLO_send_digest ();
    $display .= COM_endBlock();
}

if (SEC_hasRights ('user.edit')) {
    $display .= COM_startBlock ($LANG_HELLO01['block_headline']);
    $display .= HELLO_search_form ();
    $display .= COM_endBlock();
}

$display = COM_createHTMLDocument($display);
COM_output($display);

?>
