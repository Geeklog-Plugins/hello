<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | hello Plugin 1.0.0                                                         |
// +---------------------------------------------------------------------------+
// | Installation SQL                                                          |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2000-2008 by the following authors:                         |
// |                                                                           |
// | Authors: Tony Bibbs        - tony AT tonybibbs DOT com                    |
// |          Mark Limburg      - mlimburg AT users DOT sourceforge DOT net    |
// |          Jason Whittenburg - jwhitten AT securitygeeks DOT com            |
// |          Dirk Haun         - dirk AT haun-online DOT de                   |
// |          Trinity Bays      - trinity93 AT gmail DOT com                   |
// +---------------------------------------------------------------------------+
// |                                                                           |
// | This program is licensed under the terms of the GNU General Public License|
// | as published by the Free Software Foundation; either version 2            |
// | of the License, or (at your option) any later version.                    |
// |                                                                           |
// | This program is distributed in the hope that it will be useful,           |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of            |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                      |
// | See the GNU General Public License for more details.                      |
// |                                                                           |
// | You should have received a copy of the GNU General Public License         |
// | along with this program; if not, write to the Free Software Foundation,   |
// | Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.           |
// |                                                                           |
// +---------------------------------------------------------------------------+
//


//database queries
$_SQL[] = "
CREATE TABLE {$_TABLES['hello']} (
  hello_id int(11) NOT NULL auto_increment,
  subject varchar(100) NOT NULL default '',
  creation varchar(12) NOT NULL default '',
  email_group varchar(50) NOT NULL default '',
  quantity int(11) NOT NULL default '0',
  content blob NOT NULL,
  PRIMARY KEY  (hello_id)
) ENGINE=MyISAM
";

$_SQL[] = "
CREATE TABLE {$_TABLES['hello_queue']} (
  id int(11) NOT NULL auto_increment,
  expediteur varchar(100) NOT NULL default '',
  destinataire varchar(100) NOT NULL default '',
  date varchar(12) NOT NULL default '',
  hello_id int(11) NOT NULL default '0',
  subject varchar(100) NOT NULL default '',
  content blob NOT NULL,
  priority tinyint(1) default 0,
  uid mediumint(8) NOT NULL default '0',
  PRIMARY KEY  (id)
) ENGINE=MyISAM
";

?>