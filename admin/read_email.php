<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Hello Plugin 2.1.1                                                        |
// +---------------------------------------------------------------------------+
// | read_mail.php                                                             |
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

/**
* Shows the form the admin uses to send Geeklog members a message. Right now
* you can only email an entire group.
*
* @return   string      HTML for the email form
*
*/
function list_hello ()
{
    global $_CONF, $_TABLES, $LANG_HELLO01;

    require_once $_CONF['path_system'] . 'lib-admin.php';

    $retval = '';

    //Build header list
    $header_arr = array(      # display 'text' and use table field 'field'
                    array('text' => $LANG_HELLO01['see_email'], 'field' => 'see_hello', 'sort' => false),
                    array('text' => $LANG_HELLO01['id'], 'field' => 'hello_id', 'sort' => true),
                    array('text' => $LANG_HELLO01['subjet'], 'field' => 'subject', 'sort' => true),
                    array('text' => $LANG_HELLO01['creation'], 'field' => 'creation', 'sort' => true),
                    array('text' => $LANG_HELLO01['group'], 'field' => 'email_group', 'sort' => true),
                    array('text' => $LANG_HELLO01['quantity'], 'field' => 'quantity', 'sort' => true)
    );


    $defsort_arr = array('field'     => $_TABLES['hello'] . '.hello_id',
                         'direction' => 'DESC');

    $retval .= COM_startBlock($LANG28[11], '',
                              COM_getBlockTemplate('_admin_block', 'header'));

    $text_arr = array(
        'has_extras' => true,
        'form_url'   => $_CONF['site_admin_url'] . '/plugins/hello/read_email.php',
        'help_url'   => ''
    );


    $sql = "SELECT {$_TABLES['hello']}.hello_id,subject,creation,email_group,quantity "
         . "FROM {$_TABLES['hello']} WHERE 1=1";

    $query_arr = array('table' => 'hello',
                       'sql' => $sql,
                       'query_fields' => array('hello_id', 'subject', 'creation', 'email_group', 'quantity'),
                       'default_filter' => "");

    $retval .= ADMIN_list('hello', 'HELLO_getListField_hello', $header_arr,
                          $text_arr, $query_arr, $defsort_arr);
    $retval .= COM_endBlock(COM_getBlockTemplate('_admin_block', 'footer'));

    return $retval;
}

function HELLO_getListField_hello ($fieldname, $fieldvalue, $A, $icon_arr) {

    global $_CONF;
	
	switch ($fieldname) {
        case 'see_hello':
            $retval = '';
	        $retval .= COM_createLink($icon_arr['list'], "{$_CONF['site_admin_url']}/plugins/hello/read_email.php?mode=edit&amp;hello_id={$A['hello_id']}");
		    break;
		case 'creation':
		    $creation = COM_getUserDateTimeFormat(strtotime($A['creation']));
			$retval .= $creation[0];
		    break;
	    default:
            $retval = stripslashes($fieldvalue);
            break;
    }
	
	return $retval;
}

function display_hello($hello_id) {

    global $_CONF, $_TABLES, $LANG_HELLO01;

    $display = COM_startBlock($LANG_HELLO01['email'] . ' #' . $hello_id);

    // generate the display from the template
    $display_hello = new Template($_CONF['path'] . 'plugins/hello/templates/admin');
    $display_hello->set_file(array('display_hello' => 'hello_display.thtml'));
	
	$requete ="SELECT content FROM {$_TABLES['hello']} WHERE hello_id = " . $hello_id . " limit 1";
	$result_objet_cherche = DB_query($requete);
    $objet_cherche = DB_fetchArray($result_objet_cherche);
	
    $display_hello->set_var('hello_display',  stripslashes($objet_cherche[0]));
	
    $display .= $display_hello->parse('output', 'display_hello');

    $display .= COM_endBlock();

    // return results
    return $display;
}
// MAIN

$mode = '';
if (isset($_REQUEST['mode'])) {
    $mode = $_REQUEST['mode'];
}

if ($mode == 'edit') {
	$display .= hello_admin_menu();
	$display .= display_hello($_REQUEST['hello_id']);
    } else {
    $display .= hello_admin_menu();
    $display .= list_hello();

}

$display = COM_createHTMLDocument($display);
COM_output($display);

?>
