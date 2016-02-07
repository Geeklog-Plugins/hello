<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | hello plugin 1.0.0                                                        |
// +---------------------------------------------------------------------------+
// | install_defaults.php                                                      |
// |                                                                           |
// | Initial Installation Defaults used when loading the online configuration  |
// | records. These settings are only used during the initial installation     |
// | and not referenced any more once the plugin is installed.                 |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2008 by the following authors:                              |
// |                                                                           |
// | Authors: Ben        - ben AT geeklog DOT fr                               |
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

if (strpos(strtolower($_SERVER['PHP_SELF']), 'install_defaults.php') !== false) {
    die('This file can not be used on its own!');
}

/*
 * hello default settings
 *
 * Initial Installation Defaults used when loading the online configuration
 * records. These settings are only used during the initial installation
 * and not referenced any more once the plugin is installed
 *
 */
  
global $_HE_DEFAULT;
$_HE_DEFAULT = array();

$_HE_DEFAULT['max_email'] = 80;


/**
* Initialize hello plugin configuration
*
* Creates the database entries for the configuation if they don't already
* exist. 
*
* @return   boolean     true: success; false: an error occurred
*
*/
function plugin_initconfig_hello()
{
    global $_CONF, $_HE_DEFAULT;

    $c = config::get_instance();
    if (!$c->group_exists('hello')) {

        //This is main subgroup #0
		$c->add('sg_0', NULL, 'subgroup', 0, 0, NULL, 0, true, 'hello');
		
		//This is fieldset #1  in subgroup #0   
		$c->add('fs_01', NULL, 'fieldset', 0, 0, NULL, 0, true, 'hello');
        $c->add('max_email', $_HE_DEFAULT['max_email'],
                'text', 0, 0, 0, 10, true, 'hello');				
    }

    return true;
}

?>