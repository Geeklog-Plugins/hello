<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Hello Plugin 2.1.1                                                               |
// +---------------------------------------------------------------------------+
// | manual.php                                                                |
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

    COM_accessLog ("User {$_USER['username']} tried to illegally access the hello administration screen.");
    
    $display = COM_createHTMLDocument($display);
    COM_output($display);
    exit;
}

function HELLO_count_hello () {

    global $_CONF, $_TABLES, $_USER, $LANG_HELLO01, $_HE_CONF;
    
    $hellos = DB_count($_TABLES['hello_queue'],'1','1');
    $retval = '<p>' . $hellos . ' ' . $LANG_HELLO01['email_schedule'] .'</p>';
    if ($hellos > 0) $retval .= '<p><a href="' . $_CONF['site_admin_url'] . '/plugins/hello/manual.php?action=go">' . $LANG_HELLO01['send_next'] . '</a> ' . $_HE_CONF['max_email'] . ' ' . $LANG_HELLO01['max'] . '</p>';  
    
    return $retval;
}

// MAIN
$action = '';
if (isset($_REQUEST['action'])) {
    $action = $_REQUEST['action'];
}

$display .= hello_admin_menu();

if($action=='go') {
    $display .= HELLO_send_hello (true);
} else {
    $display .= HELLO_count_hello ();
}

$display = COM_createHTMLDocument($display);
    COM_output($display);

?>
