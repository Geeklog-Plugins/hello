<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Hello Plugin 2.1.1                                                               |
// +---------------------------------------------------------------------------+
// | email_group.php                                                           |
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
function display_mailform ($vars=array())
{
    global $_CONF, $_SCRIPTS, $_TABLES, $_USER, $LANG31, $LANG24, $LANG_HELLO01;

    $retval = '';

    if ($_CONF['advanced_editor'] == 1) {
        $postmode = 'html';
        $_SCRIPTS->setJavaScriptLibrary('jquery');
        $_SCRIPTS->setJavaScriptFile('ckeditor', '/editors/ckeditor/ckeditor.js');
        $ckeditor = '        var geeklogEditorName = "ckeditor";
        var geeklogAllowedHtml = [];
        jQuery(function() {
            CKEDITOR.replace( \'message_html\', {
             customConfig: \'' .  $_CONF['site_url'] . '/editors/ckeditor/config.js\',
             toolbar: \'toolbar0\',
             height:500
            });
        });';
        $_SCRIPTS->setJavaScript($ckeditor , true);
    } elseif (empty ($postmode)) {
        $postmode = $_CONF['postmode'];
    }
	
    $mail_templates = new Template ($_CONF['path'] . 'plugins/hello/templates/admin/');
    if (($_CONF['advanced_editor'] == 1)) {
        $mail_templates->set_file('form','mailform_advanced.thtml');
    } else {
        $mail_templates->set_file('form','mailform.thtml');
	}
    $mail_templates->set_var('geeklogStyleBasePath',$_CONF['site_url'] . '/fckeditor');
	
    if ($postmode == 'html') {
        $mail_templates->set_var ('show_texteditor', 'none');
        $mail_templates->set_var ('show_htmleditor', '');
    } else {
        $mail_templates->set_var ('show_texteditor', '');
        $mail_templates->set_var ('show_htmleditor', 'none');
    }
    $mail_templates->set_var('lang_postmode', $LANG24[4]);
    $mail_templates->set_var('postmode_options', COM_optionList($_TABLES['postmodes'],'code,name',$postmode));
    $mail_templates->set_var ('site_url', $_CONF['site_url']);
    $mail_templates->set_var ('site_admin_url', $_CONF['site_admin_url']);
    $mail_templates->set_var ('layout_url', $_CONF['layout_url']);
    $mail_templates->set_var ('startblock_email', COM_startBlock ($LANG31[1],
            '', COM_getBlockTemplate ('_admin_block', 'header')));
    $mail_templates->set_var ('php_self', $_CONF['site_admin_url'] . '/plugins/hello/email_group.php');
    $mail_templates->set_var ('lang_note', $LANG31[19]);
    $mail_templates->set_var ('lang_to', $LANG31[18]);
	
	$mail_templates->set_var('import_message', $LANG_HELLO01['import_message']);
	$mail_templates->set_var('separator_in', $LANG_HELLO01['separator']);
	$mail_templates->set_var('select_file', $LANG_HELLO01['select_file']);
	$separator_options = '<option value=",">,</option>' . LB;
	$separator_options .= '<option value=";">;</option>' . LB;
	$separator_options .= '<option value="tab">tab</option>' . LB;
	
	$mail_templates->set_var('separator_options_in', $separator_options);
	
    $mail_templates->set_var ('lang_selectgroup', $LANG31[25]);
    $group_options = '';
    $result = DB_query("SELECT grp_id, grp_name FROM {$_TABLES['groups']} WHERE grp_name <> 'All Users'");
    $nrows = DB_numRows ($result);
    $groups = array ();
    for ($i = 0; $i < $nrows; $i++) {
        $A = DB_fetchArray ($result);
        $groups[$A['grp_id']] = ucwords ($A['grp_name']);
    }
    asort ($groups);
    foreach ($groups as $groupID => $groupName) {
        $group_options .= '<option value="' . $groupID . '">' . $groupName
                       . '</option>';
    }
    $mail_templates->set_var ('group_options', $group_options);
    $mail_templates->set_var ('lang_from', $LANG31[2]);
    $mail_templates->set_var ('site_name', $_CONF['site_name']);
    $mail_templates->set_var ('lang_replyto', $LANG31[3]);
    $mail_templates->set_var ('site_mail', $_CONF['site_mail']);
    $mail_templates->set_var ('lang_subject', $LANG31[4]);
    $mail_templates->set_var ('lang_body', $LANG31[5]);
    $mail_templates->set_var ('lang_sendto', $LANG31[6]);
    $mail_templates->set_var ('lang_allusers', $LANG31[7]);
    $mail_templates->set_var ('lang_admin', $LANG31[8]);
    $mail_templates->set_var ('lang_options', $LANG31[9]);
    $mail_templates->set_var ('lang_HTML', $LANG31[10]);
    $mail_templates->set_var ('lang_urgent', $LANG31[11]);
    $mail_templates->set_var ('lang_ignoreusersettings', $LANG31[14]);
    $mail_templates->set_var ('lang_send', $LANG31[12]);
    $mail_templates->set_var ('end_block', COM_endBlock (COM_getBlockTemplate ('_admin_block', 'footer')));
    $mail_templates->set_var ('xhtml', XHTML);
    $mail_templates->set_var('gltoken_name', CSRF_TOKEN);
    $mail_templates->set_var('gltoken', SEC_createToken());
    $mail_templates->set_var('subject', $vars['subject']);
    $mail_templates->set_var('message_html', $vars['content']);

    $mail_templates->parse ('output', 'form');
    $retval = $mail_templates->finish ($mail_templates->get_var ('output'));

    return $retval;
}

/**
* This function record in the hello queue the message to send to the specified group or to csv list
*
* @param    array   $vars   Same as $_POST, holds all the email info
* @return   string          HTML with success or error message
*
*/
function send_messages($vars)
{
    global $_CONF, $_TABLES, $LANG31, $LANG_HELLO01;

    require_once($_CONF['path_system'] . 'lib-user.php');

    $retval = '';

    if (empty ($vars['fra']) OR empty ($vars['fraepost']) OR
            empty ($vars['subject']) OR empty ($vars['content']) ) {
        $retval .= COM_startBlock ($LANG31[1], '',
                        COM_getBlockTemplate ('_msg_block', 'header'));
        $retval .= $LANG31[26];
        $retval .= COM_endBlock (COM_getBlockTemplate ('_msg_block', 'footer'));
        
        $retval .= $display .= display_mailform ($vars);

        return $retval;
    }

    // Urgent message!
    if (isset ($vars['priority'])) {
        $priority = 1;
    } else {
        $priority = 0;
    }

    if (!empty ($vars['to_group'])) {
	    $groupList = implode (',', USER_getChildGroups($vars['to_group']));
		//Group name
		$group_name = DB_query("SELECT grp_name FROM {$_TABLES['groups']} WHERE grp_id =" . $vars['to_group'] . " ");
		$group_name = DB_fetchArray ($group_name);
		$email_group = $group_name[0];
		
		if (isset ($vars['overstyr'])) {
			$sql = "SELECT DISTINCT username,fullname,email FROM {$_TABLES['users']},{$_TABLES['group_assignments']} WHERE uid > 1";
			$sql .= " AND {$_TABLES['users']}.status = 3 AND ((email is not null) and (email != ''))";
			$sql .= " AND {$_TABLES['users']}.uid = ug_uid AND ug_main_grp_id IN ({$groupList})";
		} else {
			$sql = "SELECT DISTINCT username,fullname,email,emailfromadmin FROM {$_TABLES['users']},{$_TABLES['userprefs']},{$_TABLES['group_assignments']} WHERE {$_TABLES['users']}.uid > 1";
			$sql .= " AND {$_TABLES['users']}.status = 3 AND ((email is not null) and (email != ''))";
			$sql .= " AND {$_TABLES['users']}.uid = {$_TABLES['userprefs']}.uid AND emailfromadmin = 1";
			$sql .= " AND ug_uid = {$_TABLES['users']}.uid AND ug_main_grp_id IN ({$groupList})";
		}
		$result = DB_query ($sql);
		$nrows = DB_numRows ($result);
		$quantity = $nrows;
	} else {
		// OK, let's upload csv file
	    require_once($_CONF['path_system'] . 'classes/upload.class.php');
	    $upload = new upload();

	    //Debug with story debug function
	    if (isset ($_CONF['debug_image_upload']) && $_CONF['debug_image_upload']) {
		    $upload->setLogFile ($_CONF['path'] . 'logs/error.log');
		    $upload->setDebug (true);
	    }
	    $upload->setMaxFileUploads (1);

	    $upload->setAllowedMimeTypes (array (
		    	'text/csv'   => '.csv',
		    	'text/comma-separated-values'  => '.csv',
		    	'application/vnd.ms-excel' => '.csv',
		    	'application/x-csv' => '.csv'
		    	));
	
	    if (!$upload->setPath($_CONF['path_data'])) {
		    $output = COM_siteHeader ('menu', $LANG24[30]);
		    $output .= COM_startBlock ($LANG24[30], '', COM_getBlockTemplate ('_msg_block', 'header'));
		    $output .= $upload->printErrors (false);
		    $output .= COM_endBlock (COM_getBlockTemplate ('_msg_block', 'footer'));
		    $output .= COM_siteFooter ();
		    echo $output;
		    exit;
	    }

	    // Set file permissions on file after it gets uploaded (number is in octal)
	    $upload->setPerms('0644');

		$curfile = current($_FILES);
		if (!empty($curfile['name'])) {
			$pos = strrpos($curfile['name'],'.') + 1;
			$fextension = substr($curfile['name'], $pos);
			$filename = 'import_hello_' . COM_makesid()  . '.' . $fextension;
		}
		if ($filename == '') {
		    $output = COM_siteHeader ('menu', $LANG24[30]);
		    $output .= COM_startBlock ($LANG24[30], '', COM_getBlockTemplate ('_msg_block', 'header'));
		    $output .= 'Upload error: csv file name is empty. Please try again...';
		    $output .= COM_endBlock (COM_getBlockTemplate ('_msg_block', 'footer'));
		    $output .= COM_siteFooter ();
		    echo $output;
		    exit;
	    }
		$upload->setFileNames($filename);
		reset($_FILES);
		$upload->uploadFiles();

		if ($upload->areErrors()) {
			$msg = $upload->printErrors(false);
			return $LANG24[30];
		}
		
		//email group
		$email_group = $LANG_HELLO01['csv_file'];
		$destinataires = array();
		
		$separator = $vars['separator'];
		if ( !in_array($separator, array(',','tab',';')) ) {
	        $separator = ',';
	    }
		if ($separator == 'tab') $separator = "\t";
		
		if (($handle = fopen($_CONF['path_data'] . $filename, "r")) !== FALSE) {
		    $quantity = 0;
			while (($data = fgetcsv($handle, 0, $separator)) !== FALSE) {
				//todo check if email is valid
				if ($data[0] != '' and COM_isEmail($data[0])) {
				    $quantity++;
					$destinataires[] = $data[0];
				}
			}
			fclose($handle);
		}
	}

    $retval .= COM_startBlock ($LANG31[1]);

    // register hello
	
	$creation = date ('YmdHi', time ());
	$subject = addslashes($vars['subject']);
	$content = addslashes($vars['content']);
	$from = COM_formatEmailAddress ($vars['fra'], $vars['fraepost']);

	$sql_ajout_hello = "INSERT INTO {$_TABLES['hello']} (subject, creation, email_group, quantity, content) VALUES ('$subject', '$creation', '$email_group', '$quantity','$content')";
	DB_query ($sql_ajout_hello);
	$new_hello_id = DB_insertId();	
    

    // Loop through and send the messages in the DB!
    $successes = 0;
    $failures = 0;
	if (!empty ($vars['to_group'])) {
		for ($i = 0; $i < $quantity; $i++) {
			$A = DB_fetchArray ($result);
			$destinataire = $A['email'];
			$expediteur = $from;
			$date = date ('YmdHi', time ());
		
			$sql_ajout_hello = "INSERT INTO {$_TABLES['hello_queue']} (expediteur, destinataire, date, hello_id, subject, content, priority) VALUES ('$expediteur', '$destinataire', '$date', '$new_hello_id', '$subject', '$content', '$priority')";
			if ($destinataire != '' ) {
			    if (DB_query ($sql_ajout_hello)) {
				    $successes = $successes + 1;
			    } else {
				    $failures = $failures + 1;
			    }
			} else {
			    $failures = $failures + 1;
			}
		}
	} else {
	    //csv file
		for ($i = 0; $i < $quantity; $i++) {
		    $destinataire = $destinataires[$i];
			$expediteur = $from;
			$date = date ('YmdHi', time ());
		
			$sql_ajout_hello = "INSERT INTO {$_TABLES['hello_queue']} (expediteur, destinataire, date, hello_id, subject, content, priority) VALUES ('$expediteur', '$destinataire', '$date', '$new_hello_id', '$subject', '$content', '$priority')";
			
			if (DB_query ($sql_ajout_hello)) {
				$successes = $successes + 1;
			} else {
				$failures = $failures + 1;
			}
			
		}
	}

	if ($successes >= 0) {
        $retval .= $i . ' ' . $LANG_HELLO01['email_schedule'] . '<br />' . $vars['priority'];
	} 
	if ($failures > 0) {
	    $retval .= 'Oups... There was ' . $failures . ' failure(s)';
	}
	
	if (empty ($vars['to_group'])) {
	    //list emails from csv
		reset($destinataires);
		$retval .= COM_makeList($destinataires);
	}
  
    $retval .= COM_endBlock ();

    return $retval;
}

// MAIN


$display .= hello_admin_menu();

if (isset($_POST['mail']) && ($_POST['mail'] == 'mail') && SEC_checkToken()) {
    $display .= send_messages ($_POST);
} else {
    $display .= display_mailform ();
}

$display = COM_createHTMLDocument($display);
COM_output($display);

?>
