<?
// $Id: cfg.php,v 1.49 2004/10/06 02:11:31 entropie Exp $ //

/*
 ADMIN LOGIN: after successful database setup (setup/Jamp.sql):
 Change in <setup/Jamp.sql) if you want to.
  Username: admin
  Password: admin
*/

/*
 *  System Settings
 */

if(!defined('LAN')) {

	function getRemoteAddress () {
		if(ereg("10.0.187.(.*)", $_SERVER['REMOTE_ADDR'], $regs)) {
			define('LAN', true);
		} else
			define('LAN', false);
	}
}
getRemoteAddress();

# The absolute http-address of the script.
$cfg['serverroot']              = 'http://jamp.enti.ty/';
if(!LAN)
	$cfg['serverroot'] = 'http://ackro.ath.cx:23080/';


# The folder where all symlinks and playlists are stored.
$cfg['streamroot']              = 'stream/';
# The address of the 'stream' folder, not to edit usually.
$cfg['httpstreamroot']          = $cfg['serverroot'] . $cfg['streamroot'];
# The folder where cached files are stored.
$cfg['dir_content']             = 'content/';
# With this string you can enter commands in the textline
$cfg['command_string']          = '!';
# Activates the HTTP authentication.

$cfg['userLogin']               = 0;

if(!LAN)
	$cfg['userLogin'] = 1;



# User of the webserver.
$cfg['httpdUser']               = 'apache';
# Group of the webserver.
$cfg['httpdGroup']              = 'apache';

// Used only if $cfg['userLogin'] == FALSE || 0.
// All machines with one of this IP's will become admin rights.
// You have also the possibility to log in via '!login'.
// Leave empty if you don't want to use this.

# Search without any input in the 2. textfield:
# PHRASE | AND | OR (not good!)
$cfg['defaultSearchType']        = 'AND';

# A (pseudo)random string.
$cfg['m3ufile']                 = substr(md5(time()),0,16) . '.m3u';
# Natural sort of files during updatedb.
$cfg['sort_updatedb']           = 1;
# Set all folders where your songs are stored.
$cfg['mp3_dirs'][]              = '/home/music/';
#$cfg['mp3_dirs'][]              = '/home/r00t/vault.b/audiostuff/mp3';
#$cfg['mp3_dirs'][]              = '/mnt/mp3_ralph/';
# Lifetime of the cookie. 3600*24*30*12 = 1 Jear
$cfg['cookie_lifetime']         = (3600*24*30*12);
# Folder where mp3blaster playlists are stored.
# Used for local playing. httpd user must have write rights.
$cfg['mp3blaster_plist_dir']    = '/home/apache/jamp/playlists';
# Shoutbox. Set to 0 if you dont want.
$cfg['showShoutBox']            = 1;
# Shoutbox max entrys are visible.
$cfg['sbLimit']                 = 15;
# Max textlenght visible.
$cfg['sbMaxTextLength']         = 255;

/*
 *  Layout Settings
*/

# Which template I should use.
$cfg['style']                   = 'templates/GreenWar';
//$cfg['style']                   = './templates/Serenity';
# Folder where language files are stored.
$cfg['langFolder']              = 'cfg/language';
# Language
$cfg['language']                = 'en';
# menu for each use, set to 1.
$cfg['showusermenu']            = 1;
# Playlist height in rows.
$cfg['max_plist_height']        = 15;

/*
 * MySql Settings
*/

# I create a new database during setup if is set to 1.
$cfg['mysql']['new_db']         = 1;

# MySql Server
$cfg['mysql']['server']         = 'localhost';
# MySql Username
$cfg['mysql']['username']       = 'root';
# MySql Password
$cfg['mysql']['pw']             = 'keke@rofl.de';
# MySql Database
$cfg['mysql']['db']             = 'Jamp';

// If you dont want, leave empty and set the vars above.
#$cfg['mysql']['PrivFile']       = '/home/entropie/source/cfg.privateMySQL.php';
#
# Attentione: SET 'PrivFile' to '' if you dont want to use it.
#


# Some tables, not important to edit.
$cfg['mysql']['table_path']     = 'Jamp_path';
$cfg['mysql']['table_files']    = 'Jamp_files';
$cfg['mysql']['table_plist']    = 'Jamp_playlists';
$cfg['mysql']['table_tmpplist'] = 'Jamp_tmpplaylist';
$cfg['mysql']['table_symlink']  = 'Jamp_fullpath';
$cfg['mysql']['table_sb']       = 'Jamp_shoutbox';
$cfg['mysql']['table_time']     = 'Jamp_time';
$cfg['mysql']['table_user']     = 'Jamp_user';

/*
 *  Log Settings
*/

# Folder where logfiles are stored
$cfg['logFolder']               = 'logs/';
# Maximal logfile size:
$cfg['max_log_size']            = 1024*20; // MB
# Maximal logfile size of devlog.
$cfg['maxDevLogSize']           = 1024*1;
# Save old logs?
$cfg['saveOldDevLogs']          = 1;
# Show devlog for admin.
// Not active yet!
$cfg['showDevLog']              = 1;
# Lines to show.
$cfg['devLogLines']	            = 23;
// If you dont want logging leave specific file empty. [extension: log]
$cfg['log']['udb']              = 'udb';
$cfg['log']['access']           = 'access';
$cfg['log']['dev']              = 'dev';
$cfg['log']['mysql']            = 'mysql';
$cfg['version']                 = 'version 0.3.0';

/*
 *  Mail
*/

# Sends mail on error?
$cfg['mailOnError']             = 1;
# Mail address of the user.
$cfg['mailAddr']                = 'entropie@particle.enti.ty';


# Dont edit below!
include($cfg['langFolder'] . '/' . $cfg['language'] . '.php');

//mysql> SELECT a.id, b.file, a.path, a.id AS pid, c.path as prim_path, c.prim_path_id as ppid FROM Jamp_path AS a LEFT JOIN Jamp_fullpath AS c ON c.id=a.id LEFT JOIN Jamp_files AS b ON a.id = b.pathid WHERE a.path LIKE '%goa/%' || a.id = '71';;
?>
