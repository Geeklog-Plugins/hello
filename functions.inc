<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | hello Plugin 2.1.1                                                        |
// +---------------------------------------------------------------------------+
// | functions.inc                                                             |
// |                                                                           |
// | This file does two things: 1) it implements the necessary Geeklog Plugin  |
// | API method and 2) implements all the common code needed by the hello      |
// | plugin' PHP files.                                                        |
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


if (strpos(strtolower($_SERVER['PHP_SELF']), 'functions.inc') !== false) {
    die('This file can not be used on its own.');
}

$pi_name    = 'hello';

// database table names 
$_TABLES['hello']               = $_DB_table_prefix . 'hello';
$_TABLES['hello_queue']    = $_DB_table_prefix . 'hello_queue';
	
// path to the plugin
$plugin_path = $_CONF['path'] . 'plugins/hello/';

// Set lang file or english by default
$langfile = $plugin_path . 'language/' . $_CONF['language'] . '.php';

if (file_exists($langfile)) {
    require_once $langfile;
} else {
    require_once $plugin_path . 'language/english.php';
}


// Check and see if we need to load the plugin configuration. HE and he refert to hello plugin
 
if (!isset($_HE_CONF['max_email'])) {
    require_once $_CONF['path_system'] . 'classes/config.class.php';
    
    $he_config = config::get_instance();
    $_HE_CONF = $he_config->get_config('hello');
}


// +---------------------------------------------------------------------------+
// | Geeklog Plugin API Implementations                                        |
// +---------------------------------------------------------------------------+


// Returns the administrative option for this plugin
// Adds the plugin to the Admin menu
// @return   array   Array containing (plugin name, plugin admin url, # of items in plugin or '')

function plugin_getadminoption_hello() {
    
    global $_CONF, $LANG_HELLO01, $_TABLES, $_SCRIPTS;
    
	$blink = '';
	
    if (SEC_hasRights('hello.edit')) {
        if (SEC_hasRights('user.mail')) {
			if ($_CONF['emailstories'] == 1) {
				$lastrun = DB_getItem ($_TABLES['vars'], 'value', "name = 'lastemailedstories'");
				if ($lastrun == '') $lastrun = 0;
				$storysql = "SELECT sid FROM {$_TABLES['stories']} WHERE draft_flag = 0 AND date <= NOW() AND date >= '{$lastrun}'";
				$result = DB_query ($storysql);
				$count = DB_numRows ($result);
			} else {
				$count = 0;
			}

			if ($count > 0) {
				
				$alert = ' <strong><span style="color:red">' . $count . '</span></strong>';
			}
		}
		
		return array($LANG_HELLO01['plugin_name'] . $alert, $_CONF['site_admin_url'] . '/plugins/hello/index.php');
    } else {
       return;
    }
}

function plugin_commentsupport_hello()
{
    // hello will use comments
    return false;
}

// This will put an option for hello in the command and control block on
// moderation.php

function plugin_cclabel_hello()
{
    global $_CONF, $LANG_HELLO01;

    if (SEC_hasRights ('hello.edit')) {
        return array ($LANG_HELLO01['plugin_name'],
                $_CONF['site_admin_url'] . '/plugins/hello/index.php',
                plugin_geticon_hello ());
    }

    return false;
}


// Returns the URL of the plugin's icon
// @return   string      URL of the icon

function plugin_geticon_hello ()
{
    global $_CONF;
    return $_CONF['site_admin_url']  .'/plugins/hello/images/hello.png';
}

// Return the current version of code.
// Used in the Plugin Editor to show the registered version and code version


function plugin_chkVersion_hello()
{
    global $_CONF;

    require_once $_CONF['path'] . 'plugins/hello/autoinstall.php';

    $inst_parms = plugin_autoinstall_hello('hello');

    return $inst_parms['info']['pi_version'];
}

// Automatic uninstall function for plugins
// @return   array
// This code is automatically uninstalling the plugin.
// It passes an array to the core code function that removes
// tables, groups, features and php blocks from the tables.
// Additionally, this code can perform special actions that cannot be
// foreseen by the core code (interactions with other plugins for example)

function plugin_autouninstall_hello ()
{
    $out = array (
        /* give the name of the tables, without $_TABLES[] */
        'tables' => array('hello', 'hello_queue'),
        /* give the full name of the group, as in the db */
        'groups' => array('hello Admin'),
        /* give the full name of the feature, as in the db */
        'features' => array('hello.edit'),
        /* give the full name of the block, including 'phpblock_', etc */
        'php_blocks' => array(),
        /* give all vars with their name */
        'vars'=> array()
    );

    return $out;
}


// Get path for the template files.
//* @param    string  $path   subdirectory within the base template path
// @return   string          full path to template directory

function hello_templatePath ($path = '')
{
    global $_CONF;

    if (empty ($path)) {
        $layout_path = $_CONF['path_layout'] . $pi_name;
    } else {
        $layout_path = $_CONF['path_layout'] . $pi_name .  '/' . $path;
    }

    if (is_dir ($layout_path)) {
        $retval = $layout_path;
    } else {
        $retval = $_CONF['path'] . 'plugins/' . $pi_name . '/templates';
        if (!empty ($path)) {
            $retval .= '/' . $path;
        }
    }

    return $retval;
}

// Update the hello plugin

function plugin_upgrade_hello()
{
    global $_CONF, $_TABLES, $_DB_table_prefix;

    $installed_version = DB_getItem($_TABLES['plugins'], 'pi_version',
                                    "pi_name = 'hello'");
    
    $code_version = plugin_chkVersion_hello();
    
    if ($installed_version == $code_version) {
        return true;
    }

    require_once $_CONF['path'] . 'plugins/hello/autoinstall.php';
	require_once $_CONF['path'] . 'plugins/hello/install_defaults.php';
	require_once $_CONF['path_system'] . 'classes/config.class.php';

    if (! plugin_compatible_with_this_version_hello('hello')) {
        return 3002;
    }
    
    switch( $installed_version ) {
        
        case '1.0.0':
            //rename db hello_file_envoi
            DB_query("RENAME TABLE $_DB_table_prefix"."hello_file_envoi TO {$_TABLES['hello_queue']};");
            
            //upgrade config values
            $c = config::get_instance();
            $c->del('site_name', 'classifieds');
            $c->del('hellologinrequired', 'hello');
            $c->del('hidehellomenu', 'hello');
            $c->del('showleftblocks1', 'hello');
            $c->del('showrightblocks1', 'hello');	
            $c->del('showleftblocks2', 'hello');
            $c->del('showrightblocks2', 'hello');
            
            $c->add('max_email', $_HE_DEFAULT['max_email'],
                'text', 0, 0, 0, 10, true, 'hello');
            
        case '2.0':
        case '2.1':
        case '2.1.1':

    }

    // update plugin version number
    $inst_parms = plugin_autoinstall_hello('hello');
    $pi_gl_version = $inst_parms['info']['pi_gl_version'];

    DB_query("UPDATE {$_TABLES['plugins']} SET pi_version = '$code_version', pi_gl_version = '$pi_gl_version' WHERE pi_name = 'hello'");
    
    /* This code is for statistics ONLY */
    $message =  'Completed hello plugin upgrade: ' . date('m d Y',time()) . "   AT " . date('H:i', time()) . "\n";
    $message .= 'Site: ' . $_CONF['site_url'] . ' and Sitename: ' . $_CONF['site_name'] . "\n";
    COM_mail("ben@geeklog.fr","Updated hello plugin from v$installed_version to v$code_version",$message);

    return true;
}

/**
 * Returns admin menu display
 *
 * Generates the admin menu from the template and returns the result as a string of HTML
 *
 * @return string HTML of admin menu
 * @todo add support for pending (physical) transaction, not fully implemented
 */
function hello_admin_menu() {
    global $_CONF, $_TABLES, $LANG_HELLO01;

    $display = COM_startBlock($LANG_HELLO01['menu_label']);

    // generate the menu from the template
    $menu = new Template($_CONF['path'] . 'plugins/hello/templates/admin');
    $menu->set_file(array('menu' => 'admin_menu.thtml'));
    $menu->set_var('site_url', $_CONF['site_url']);
    $menu->set_var('homepage_label', $LANG_HELLO01['homepage_label']);
    $menu->set_var('send_email_group', $LANG_HELLO01['send_email_group']);
    $menu->set_var('read_email', $LANG_HELLO01['read_email']);
    $menu->set_var('manual', $LANG_HELLO01['manual']);
	
	$configuration = "<a href=\"#\" onclick=\"document.hello_conf_link.submit()\">{$LANG_HELLO01['configuration']}</a>
    <form name='hello_conf_link' action='{$_CONF['site_admin_url']}/configuration.php' method='POST'>
    <input type='hidden' name='conf_group' value='hello'></form>";
    
	$menu->set_var('configuration', $configuration);
	
    $display .= $menu->parse('output', 'menu');

    $display .= COM_endBlock();

    // return results
    return $display;
}

function HELLO_undoSpecialChars( $string )
{
    $string = str_replace( '&#36;',     '$', $string );
    $string = str_replace( '&#123;',    '{', $string );
    $string = str_replace( '&#125;',    '}', $string );
    $string = str_replace( '&gt;',      '>', $string );
    $string = str_replace( '&lt;',      '<', $string );
    $string = str_replace( '&quot;',    '"', $string );
    $string = str_replace( '&nbsp;',    ' ', $string );
    $string = str_replace( '&amp;',     '&', $string );
    $string = str_replace( '&Agrave;',  'A', $string );
    $string = str_replace( '&agrave;',  'à', $string );
    $string = str_replace( '&Acirc;',   'A', $string );
    $string = str_replace( '&acirc;',   'â', $string );
    $string = str_replace( '&Ccedil;',  'C', $string );
    $string = str_replace( '&ccedil;',  'ç', $string );
    $string = str_replace( '&Egrave;',  'E', $string );
    $string = str_replace( '&egrave;',  'è', $string );
    $string = str_replace( '&Eacute;',  'E', $string );
    $string = str_replace( '&eacute;',  'é', $string );
    $string = str_replace( '&Ecirc;',   'E', $string );
    $string = str_replace( '&ecirc;',   'ê', $string );
    $string = str_replace( '&Euml;',    'E', $string );
    $string = str_replace( '&euml;',    'ë', $string );
    $string = str_replace( '&Icirc;',   'I', $string );
    $string = str_replace( '&icirc;',   'î', $string );
    $string = str_replace( '&Iuml;',    'I', $string );
    $string = str_replace( '&iuml;',    'ï', $string );
    $string = str_replace( '&Ocirc;',   'O', $string );
    $string = str_replace( '&ocirc;',   'ô', $string );
    $string = str_replace( '&OElig;',  'OE', $string );
    $string = str_replace( '&oelig;',  'oe', $string );
    $string = str_replace( '&Ugrave;',  'U', $string );
    $string = str_replace( '&ugrave;',  'ù', $string );
    $string = str_replace( '&Ucirc;',   'U', $string );
    $string = str_replace( '&ucirc;',   'û', $string );
    $string = str_replace( '&Uuml;',    'U', $string );
    $string = str_replace( '&uuml;',    'ü', $string );
    $string = str_replace( '&#376;',    'Y', $string );
    $string = str_replace( '&yuml;',    'ÿ', $string );
    $string = str_replace( '&rsquo;',  '\'', $string );
 
    return( $string );
}

/**
* Send an email.
*
* All emails sent by hello plugin are sent through this function.
*
* NOTE: Please note that using CC: will expose the email addresses of
*       all recipients. Use with care.
*
* @param    string      $to         recipients name and email address
* @param    string      $subject    subject of the email
* @param    string      $message    the text of the email
* @param    string      $from       (optional) sender of the the email
* @param    boolean     $html       (optional) true if to be sent as HTML email
* @param    int         $priority   (optional) add X-Priority header, if > 0
* @param    mixed       $optional   (optional) other headers or CC:
* @return   boolean                 true if successful,  otherwise false
*
*/
function HELLO_mail($to, $subject, $message, $from = '', $html = false, $priority = 0, $optional = null, $alternate ='')
{
    global $_CONF;

    static $mailobj;

    if (empty($from)) {
        $from = COM_formatEmailAddress($_CONF['site_name'], $_CONF['site_mail']);
    }

    $to = substr($to, 0, strcspn($to, "\r\n"));
	
	if ($to == '') return false;
	
    if (($optional != null) && !is_array($optional)) {
        $optional = substr($optional, 0, strcspn($optional, "\r\n"));
    }
    $from = substr($from, 0, strcspn($from, "\r\n"));
    $subject = substr($subject, 0, strcspn($subject, "\r\n"));
    $subject = COM_emailEscape($subject);
	$subject = utf8_decode($subject);

    include_once 'Mail.php';
    include_once 'Mail/RFC822.php';

    $method = $_CONF['mail_settings']['backend'];

    if (! isset($mailobj)) {
        if (($method == 'sendmail') || ($method == 'smtp')) {
            $mailobj =& Mail::factory($method, $_CONF['mail_settings']);
        } else {
            $method = 'mail';
            $mailobj =& Mail::factory($method);
        }
    }

    $charset = COM_getCharset();
    $headers = array();

    $headers['From'] = $from;
    if ($method != 'mail') {
        $headers['To'] = $to;
    }
    if (($optional != null) && !is_array($optional) && !empty($optional)) {
        // assume old (optional) CC: header
        $headers['Cc'] = $optional;
    }
    $headers['Date'] = date('r'); // RFC822 formatted date
    if($method == 'smtp') {
        list($usec, $sec) = explode(' ', microtime());
        $m = substr($usec, 2, 5);
        $headers['Message-Id'] = '<' .  date('YmdHis') . '.' . $m
                               . '@' . $_CONF['mail_settings']['host'] . '>';
    }
	
	$headers['Subject'] = $subject;
    if ($priority > 0) {
        $headers['X-Priority'] = $priority;
    }
    $headers['X-Mailer'] = 'Geeklog ' . VERSION;

    if (!empty($_SERVER['REMOTE_ADDR']) && !empty($_SERVER['SERVER_ADDR']) &&
            ($_SERVER['REMOTE_ADDR'] != $_SERVER['SERVER_ADDR'])) {
        $url = COM_getCurrentURL();
        if (substr($url, 0, strlen($_CONF['site_admin_url']))
                != $_CONF['site_admin_url']) {
            $headers['X-Originating-IP'] = $_SERVER['REMOTE_ADDR'];
        }
    }

    // add optional headers last
    if (($optional != null) && is_array($optional)) {
        foreach ($optional as $h => $v) {
            $headers[$h] = $v;
        }
    }
	
    if ($html) {
        # Setup mime boundary
		$mime_boundary = 'Multipart_Boundary_x'.md5(time()).'x';
		
		//$headers['Content-Type'] = 'text/html; charset=' . $charset;
		$headers['Content-Type'] = 'multipart/alternative; boundary=' . $mime_boundary;
        $headers['Content-Transfer-Encoding'] = '8bit';
		$body	 = "This is a multi-part message in mime format.\n\n";
		$body	.= "--$mime_boundary\n";

		$headers['MIME-Version'] = "1.0\r\n";

		$body	 = "This is a multi-part message in mime format.\n\n";

		# Add in plain text version
		$body	.= "--$mime_boundary\n";
		$body	.= "Content-Type: text/plain; charset=\"charset=$charset\"\n";
		$body	.= "Content-Transfer-Encoding: 8bit\n\n";
		$body	.= $alternate;
		$body	.= "\n\n";

		# Add in HTML version
		$body	.= "--$mime_boundary\n";
		$body	.= "Content-Type: text/html; charset=\"$charset\"\n";
		$body	.= "Content-Transfer-Encoding: 8bit\n\n";
		$body	.= $message;
		$body	.= "\n\n";

		# Attachments would go here
		# But this whole email thing should be turned into a class to more logically handle attachments, 
		# this function is fine for just dealing with html and text content.

		# End email
		$body	.= "--$mime_boundary--\n"; # <-- Notice trailing --, required to close email

    } else {
        $headers['Content-Type'] = 'text/plain; charset=' . $charset;
    }

    $retval = $mailobj->send($to, $headers, $body);
    
	if ($retval !== true) {
         COM_errorLog('Hello plugin - Error sending email to ' . $to . ' | ' . $retval->toString(), 1);
    }

    return($retval === true ? true : false);
}

function HELLO_send_hello ($manual=false) 
{
    global $_CONF, $_TABLES, $_USER, $LANG_HELLO01, $_HE_CONF;
	
	$retval = '';
    $results = 0;
    $falses = 0;
	$sujet = '';

    set_time_limit (260);

    $heure_script = date ('YmdHi', time ());
	
    $sql ="SELECT * FROM {$_TABLES['hello_queue']} WHERE date <= " . $heure_script . " limit 0,{$_HE_CONF['max_email']}";
	$res = DB_query($sql);
	$liste_destinataire = array();
	
	while ($A = DB_fetchArray($res))
	{
		//separate subject
		if ($sujet != '' && $A['subject'] != $sujet) {
		    COM_errorLog($sujet . ' | ' . $LANG_HELLO01['hello_sent'] . ' ' . $results  . ' ' . $LANG_HELLO01['contacts']);
			$results = 0;
			$falses = 0;
			
		}
		$destinataire = $A['destinataire'];
		$sujet = $A['subject'];
		$message = stripslashes($A['content']);
		$html = true;
		$priority = $A['priority'];
		$altBody  =  HELLO_undoSpecialChars( strip_tags($message) );
		$altBody  =  preg_replace('/\n(\s*\n){2,}/', "\n\n", $altBody);
		
		// Delete hello
		DB_query("DELETE FROM {$_TABLES['hello_queue']} WHERE id='" . $A['id'] . "' ");
		
		// Send email
		$result = HELLO_mail($destinataire, $sujet, $message, $from, $html, $priority, '', $altBody);
        ($result) ? $results ++ : $falses ++;
	}
	// Optimisation
	DB_query("OPTIMIZE TABLE {$_TABLES['hello_queue']}");
	
	if ($sujet!= '') COM_errorLog($sujet . ' | ' . $LANG_HELLO01['hello_sent'] . ' ' . $results  . ' ' . $LANG_HELLO01['contacts']);
	
	if ($manual) return '<ul><li>' . $results . ' ' . $LANG_HELLO01['email_sent'] . '<li>' . $falses . ' ' . $LANG_HELLO01['email_fail'] . '</ul>';
}

/**
* This will email new stories in the topics that the user is interested in
*
* In account information the user can specify which topics for which they
* will receive any new article for in a daily digest.
*
* @return   void
*/
 
function HELLO_emailUserTopics($online=true)
{
    global $_CONF, $_TABLES, $LANG08, $LANG24, $LANG_HELLO01;
 
    $subject = addslashes(strip_tags( '[' . $_CONF['site_name'] . '] ' . $LANG08[30] . strftime( $_CONF['shortdate'], time() )));
 
    $authors = array();
 
    // Get users who want stories emailed to them
    $usersql = "SELECT username,email,etids,{$_TABLES['users']}.uid AS uuid "
        . "FROM {$_TABLES['users']}, {$_TABLES['userindex']} "
        . "WHERE {$_TABLES['users']}.uid > 1 AND {$_TABLES['userindex']}.uid = {$_TABLES['users']}.uid AND (etids <> '-' OR etids IS NULL) ORDER BY {$_TABLES['users']}.uid";
 
    $users = DB_query( $usersql );
    $nrows = DB_numRows( $users );
 
    $lastrun = DB_getItem( $_TABLES['vars'], 'value', "name = 'lastemailedstories'" );
	
	$sql = "SELECT sid FROM {$_TABLES['stories']} WHERE draft_flag = 0 AND date <= NOW() AND date >= '{$lastrun}'";
    $result = DB_query ($sql);
    $count = DB_numRows ($result);
	
	//record hello
	$creation = date ('YmdHi', time ());
	($online) ? $email_group = $LANG_HELLO01['mdigest'] : $email_group = $LANG_HELLO01['ddigest'];
	$quantity = $nrows;

	$content  = addslashes(sprintf ($LANG_HELLO01['num_stories_digest'] . ' > ' . $lastrun, $count));
	
	$sql_ajout_hello = "INSERT INTO {$_TABLES['hello']} (subject, creation, email_group, quantity, content) VALUES ('$subject', '$creation', '$email_group', '$quantity','$content')";
	DB_query ($sql_ajout_hello);
	$new_hello_id = DB_insertId();
 
    // For each user, pull the stories they want and email it to them
	$successes = 0;
    $failures = 0;
	
    for( $x = 1; $x <= $nrows; $x++ )
    {
        $U = DB_fetchArray( $users );
 
        $storysql = array();
        $storysql['mysql'] = "SELECT sid,uid,date AS day,title,introtext,bodytext";
 
        $storysql['mssql'] = "SELECT sid,uid,date AS day,title,CAST(introtext AS text) AS introtext,CAST(bodytext AS text) AS introtext";
 
        $commonsql = " FROM {$_TABLES['stories']}, {$_TABLES['topic_assignments']} ta
            WHERE draft_flag = 0 AND date <= NOW() AND date >= '{$lastrun}'
            AND ta.type = 'article' AND ta.id = sid ";
 
        $topicsql = "SELECT tid FROM {$_TABLES['topics']}"
                  . COM_getPermSQL( 'WHERE', $U['uuid'] );
        $tresult = DB_query( $topicsql );
        $trows = DB_numRows( $tresult );
 
        if( $trows == 0 )
        {
            // this user doesn't seem to have access to any topics ...
            continue;
        }
 
        $TIDS = array();
        for( $i = 1; $i <= $trows; $i++ )
        {
            $T = DB_fetchArray( $tresult );
            $TIDS[] = $T['tid'];
        }
 
        if( !empty( $U['etids'] ))
        {
            $ETIDS = explode( ' ', $U['etids'] );
            $TIDS = array_intersect( $TIDS, $ETIDS );
        }
 
        if( sizeof( $TIDS ) > 0)
        {// We have list of Daily Digest topic ids that user has access too and that the user wants a report on
             $commonsql .= " AND (ta.tid IN ('" . implode( "','", $TIDS ) . "'))";
        }
 
        $commonsql .= COM_getPermSQL( 'AND', $U['uuid'] );
        $commonsql .= ' ORDER BY featured DESC, date DESC';
 
        $storysql['mysql'] .= $commonsql;
        $storysql['mssql'] .= $commonsql;
 
        $stories = DB_query( $storysql );
        $nsrows = DB_numRows( $stories );
 
        if( $nsrows == 0 )
        {
            // If no new stories where pulled for this user, continue with next
            continue;
        }
 
        $mailtext = $LANG08[29] . strftime( $_CONF['shortdate'], time() ) . "<br />";
 
        for( $y = 0; $y < $nsrows; $y++ )
        {
            // Loop through stories building the requested email message
            $S = DB_fetchArray( $stories );
 
            $mailtext .= "<br />------------------------------<br /><br />";
            $mailtext .= "$LANG08[31]: "
                . HELLO_undoSpecialChars( stripslashes( $S['title'] )) . "<br />";
            if( $_CONF['contributedbyline'] == 1 )
            {
                if( empty( $authors[$S['uid']] ))
                {
                    $storyauthor = COM_getDisplayName ($S['uid']);
                    $authors[$S['uid']] = $storyauthor;
                }
                else
                {
                    $storyauthor = $authors[$S['uid']];
                }
                $mailtext .= "$LANG24[7]: " . $storyauthor . "<br />";
            }
 
            $mailtext .= "$LANG08[32]: " . strftime( $_CONF['date'], strtotime( $S['day' ])) . "<br /><br />";
 
            if( $_CONF['emailstorieslength'] > 0 )
            {
                if($S['postmode']==='wikitext'){
                    $storytext = HELLO_undoSpecialChars( strip_tags( COM_renderWikiText ( stripslashes( $S['introtext'] ))));
                } else {
                    $storytext = $S['introtext'];
                }
 
                if( $_CONF['emailstorieslength'] > 1 )
                {
                    $storytext = $S['introtext'] . "<br /><br />" . $S['bodytext'] ;
                }
 
                $mailtext .= $storytext . "<br /><br />";
            }
 
            $mailtext .= $LANG08[33] . ' ' . COM_buildUrl( $_CONF['site_url']
                      . '/article.php?story=' . $S['sid'] ) . "<br />";
        }
 
        $mailtext .= "<br />------------------------------<br />";
        $mailtext .= "<br />$LANG08[34]<br />";
        $mailtext .= "<br />------------------------------<br />";
 
        $mailto = $U['username'] . ' <' . $U['email'] . '>';
 
        if ($_CONF['site_mail'] !== $_CONF['noreply_mail']) {
            $mailfrom = $_CONF['noreply_mail'];
            $mailtext .= LB . LB . $LANG04[159];
        } else {
            $mailfrom = $_CONF['site_mail'];
        }
        
		//Record in the hello queue
		$mailtext = addslashes($mailtext);
		$sql_ajout_hello = "INSERT INTO {$_TABLES['hello_queue']} (expediteur, destinataire, date, hello_id, subject, content, priority) VALUES ('$mailfrom', '$mailto', '$creation', '$new_hello_id', '$subject', '$mailtext', '0')";
		if (DB_query ($sql_ajout_hello)) {
			$successes = $successes + 1;
		} else {
			$failures = $failures + 1;
		}
    }
	
	$retval = '';
	
	if ($successes >= 0) {
        $retval .= $successes . ' ' . $LANG_HELLO01['email_schedule'];
	} 
	if ($failures > 0) {
	    $retval .= 'Oups... There was ' . $failures . ' failure(s)';
	}
 
    DB_query( "UPDATE {$_TABLES['vars']} SET value = NOW() WHERE name = 'lastemailedstories'" );
	
	if ($online) return $retval;
}

//fire daily digest
function plugin_runScheduledTask_hello() {

    global $_CONF, $_TABLES;
	
	$lastrun = DB_getItem ($_TABLES['vars'], 'value', "name = 'lastemailedstories'");
	$sql = "SELECT sid FROM {$_TABLES['stories']} WHERE draft_flag = 0 AND date <= NOW() AND date >= '{$lastrun}'";
    $result = DB_query ($sql);
    $count = DB_numRows ($result);
    
	if ($_CONF['emailstories'] == 1 && $count > 0) HELLO_emailUserTopics(false);
}

// Hello CRON every 10 minutes (600 seconds)

global $_PLUGINS;

if (in_array('hello', $_PLUGINS)) {

	$last_run = DB_getItem($_TABLES['vars'],'value',"name='last_hello_run'");

	if($last_run == '') {
		//set default value
		$next_run = time() - (600) ;
		DB_query("INSERT INTO {$_TABLES['vars']} SET name = 'last_hello_run', value = '$next_run'");
		$last_run = $next_run;
	}
	if ($last_run + 600 <= time()) {
		DB_query("UPDATE {$_TABLES['vars']} SET value=UNIX_TIMESTAMP() WHERE name='last_hello_run'");
		HELLO_send_hello ();
	}
}

?>
