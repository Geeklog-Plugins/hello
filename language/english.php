<?php

###############################################################################
# english.php
#
# This is the English language file for the Geeklog hello plugin
#
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 2
# of the License, or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
#
###############################################################################


// Language for plugin users
$LANG_HELLO01 = array(
    'plugin_name'             => 'Hello',
    'overview'                => 'The Hello Plugin allows you to write, save and send html emails to groups. As some mail agents don\'t support HTML or their users prefer to receive plain text messages, Hello plugin include a plain text message as an alternate for these users. You can also send daily digest when you publish a new article or reset a user\'s daily digest settings (articles and forum) which will come in handy when the user\'s email address became invalid and emails start bouncing.',
	'email_save'              => 'Email Save',
	'email_schedule'          => 'email(s) scheduled.',
    'email_sent'              => 'email(s) sent',
    'email_fail'              => 'email(s) fail',
	'menu_label'              => 'Menu',
	'homepage_label'          => 'Hello',
	'send_email_group'        => 'Send email',
    'manual'                  => 'Manual emailing',
	'configuration'           => 'Configuration',
	'read_email'              => 'Archives',
	'see_email'               => 'See',
	'id'                      => 'ID',
	'subjet'                  => 'Subject',
    'creation'                => 'Date',
    'group'                   => 'Group/Source',
    'quantity'                => 'Quantity',
	'email_display'           => '',
	'email'                   => 'Email',
    'max'                     => 'max (see config to change this)',
    'send_next'               => 'Send emails now',
	'import_message'          => 'or select a csv file to import your contacts from your hard drive and select the delimiter for the datas. Make sure the recipient\'s email is the first field of your file. Emails will only be save temporary to the db. Make sure the recipient\'s email is the first field of your file.',
	'select_file'             => 'Select a .csv file',
	'csv_file'                => 'csv file',
	'separator'               => 'Choose delimiter',
	'hello_sent'              => 'Hello sent to',
	'contacts'                => 'contacts',
	'mdigest'                 => 'Manual Digest',
	'ddigest'                 => 'Daily Digest',
    'access_denied'           => 'Access Denied',
    'access_denied_msg'       => 'You are illegally trying access one of the Manual Digest administration pages.  Please note that all attempts to illegally access this page are logged',
    'installation_failed'     => 'Installation Failed',
    'installation_failed_msg' => 'The installation of the Manual Digest plugin failed.  Please see your Geeklog error.log file for diagnostic information.',
    'uninstall_failed'        => 'Uninstall Failed',
    'uninstall_failed_msg'    => 'The uninstall of the Manual Digest plugin failed.  Please see your Geeklog error.log file for diagnostic information.',

    'digest_sent'             => 'Digest has been sent. <a href="' . $_CONF['site_admin_url'] . '/plugins/hello/index.php">Back</a>.',
    'digest_intro'            => 'This will let you send a digested version of recent stories on your site. If you can not use a cronjob (to run the <tt>emailgeeklogstories</tt> script automatically) or if you want to send out an extra digest, simply hit the "Send" button below (only available if you have stories to send).',
    'digest_last_sent'        => 'Last Digest sent:',
    'never'                   => '(never)',
    'no_stories'              => '<b>No new stories found</b>',
    'num_stories'             => '<b>%d</b> stories will be sent',
	'num_stories_digest'      => '%d stories have been sent via manual digest',
    'send_button'             => 'Send!',
    'not_enabled1'            => '<strong>Warning:</strong> Daily Digest is not enabled. Makesure you have',
    'not_enabled2'            => 'in your config.',

    'search_text'             => 'Search for a user name, email address or user id.',
    'search_button'           => 'Search',
    'new_search'              => 'New Search',
    'inspect_text'            => "Click on the user name to inspect the user's daily digest settings.",
    'uid_not_found'           => 'There is no user account with user id %d.',
    'not_found'               => 'There were no matches for <b>%s</b>.',
    'try_again'               => 'Please try again.',
    'user'                    => 'User',
    'topics'                  => 'Topics',
    'all_topics'              => 'all topics',
    'no_topics'               => 'none',
    'reset_button'            => 'Reset',
    'success'                 => 'Daily Digest settings for user <b>%s</b> have been reset.',
    'block_headline'          => 'Daily Digest Maintenance',
    'digest_reset'            => 'Digest has been reset. <a href="' . $_CONF['site_admin_url'] . '/plugins/hello/index.php">Back</a>.',
    'explain_reset'           => 'If you don\'t want the outstanding stories to be sent, use the "Reset" button.',

    'forums'                  => 'Forums',
    'no_forums'               => 'none',
    'forum_topics'            => 'Forum topics'
);

// Localization of the Admin Configuration UI
$LANG_configsections['hello'] = array(
    'label' => 'Hello',
    'title' => 'Hello Configuration'
);

$LANG_confignames['hello'] = array(
    'max_email' => 'Number of emails to send per run?',
);

$LANG_configsubgroups['hello'] = array(
    'sg_0' => 'Main Settings',
);

$LANG_fs['hello'] = array(
    'fs_01' => 'Hello plugin'
);

// Note: entries 0, 1, and 12 are the same as in $LANG_configselects['Core']
$LANG_configselects['hello'] = array(
    0 => array('True' => 1, 'False' => 0),
    1 => array('True' => TRUE, 'False' => FALSE)
);
?>
