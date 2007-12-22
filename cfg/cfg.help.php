<?
// $Id: cfg.help.php,v 1.2 2004/04/22 17:09:52 entropie Exp $ //
$cfg['serverroot']              = '<b>Default</b>: \'<i>none</i>\'<br> The address of the script. (like: <i>http://www.ackro.de/Jamp</i>)';
$cfg['streamroot']              = '<b>Default</b>: \'stream\'<br> The directory where we store our cached files (XML, HTML.inc). The Apache user must have write rights.<br> <b>Don\'t</b> create this folder, the script does that.';
$cfg['dir_content']             = '<b>Default</b>: \'./content\'<br> The directory where we store the symlinks to our songfolders and files.';
$cfg['httpstreamroot']          = '<b>Default</b>: \'<i>$cfg[\'serverroot\'] + $cfg[\streamroot\']</i>\'<br> The directory where the cached files are stored, too. But this one must be the HTTP address. (like: <i>http://www.ackro.de/Jamp/stream</i>).<br> This one has to match with \'<b>$cfg[\'streamroot\']</b>.<br>Usually you have not to edit this one.';
$cfg['command_string']          = '<b>Default</b>: \'!\'<br> A string which is used to add commands in the \'command line\'. If this string is the first char, the command will be evaluated als command, if not we search the database.';
$cfg['userLogin']               = '<b>Default</b>: \'0\'<br> If is set to \'1\' visiters have to log in, bevor they can do anything else.';
$cfg['adminIps']                = '<b>Default</b>: \'none\'<br> Set a bounce of IP\'s if $cfg[\'userLogin\'] is set to \'0\'. Each user with this ip has admin rights.<br>Example:<br>&nbsp; &nbsp; $cfg[\'adminIps\'][] = "192.168.42.1";<br>&nbsp; &nbsp; $cfg[\'adminIps\'][] = "192.168.42.2";<br>';
$cfg['m3ufile']                 = '<b>Default</b>: \'a random string\'<br> Just a random string.';
$cfg['sort_updatedb']           = '<b>Default</b>: \'1\'<br> If is set to \'1\' the files will be "natural" sorted bevor we write them into the database';
$cfg['mp3_dirs']                = '<b>Default</b>: \'none\'<br> For each folder \'updatedb\' starts a new instance, and searches the folder recursiv for music-files.<br>Example:<br>&nbsp; &nbsp; $cfg[\'mp3_dirs\'][] = \'/home/ftp/music\';<br>&nbsp; &nbsp; $cfg[\'mp3_dirs\'][] = \'/usr/local/music\'; ';
$cfg['cookie_lifetime']         = '<b>Default</b>: \'(3600*24*30*12)\'<br> The time in seconds the cookie lifes (3600*24*30*12 = 1 Jear).';
$cfg['mp3blaster_plist_dir']    = '<b>Default</b>: \'/etc/playlists\'<br> The folder where playlists for offline using are stored, I use this with lirc.';
$cfg['showShoutBox']            = '<b>Default</b>: \'1\'<br> If is set to \'1\' the Shoutbox is visible.';
$cfg['sbLimit']                 = '<b>Default</b>: \'15\'<br> The number of Shoutbox entrys which are visible.';
$cfg['sbMaxTextLength']         = '<b>Default</b>: \'255\'<br> If a user writes more letters than this number, the text will be cutted and we show a link to the entire text.';
$cfg['allowAnonymousXmlDump']   = '<b>Default</b>: \'1\'<br> If is set to \'1\' all users can do an xml dump (without authentication). If you put this script on public webserver <b>turn this off</b>. I use thhis for daily backups with wget.';
$cfg['style']                   = '<b>Default</b>: \'./templates/GreenWar\'<br> The full path to the template.';
$cfg['langFolder']              = '<b>Default</b>: \'./cfg/language\'<br> The full path to the language folder.';
$cfg['language']                = '<b>Default</b>: \'en\'<br> The language you want to use.';
$cfg['showusermenu']            = '<b>Default</b>: \'1\'<br> if is set to \'1\' users see her navigation (playlists etc). Strogly recomend.';
$cfg['max_plist_height']        = '<b>Default</b>: \'15\'<br> The height of the playlist &lt;select&gt;. On 800x600 15 is the right number.';
$cfg['mysql']                   = 'An array which contains MySql settings.';
$cfg['logFolder']               = '<b>Default</b>: \'logs/\'<br> The log folder.';
$cfg['max_log_size']            = '<b>Default</b>: \'1024*20\'<br> Maximum size bevore a logfile is renamed/deleted.';
$cfg['maxDevLogSize']           = '<b>Default</b>: \'1024*1\'<br> Maximum size bevore a <b>DEV</b>logfile is renamed/deleted. (much less, performance issue)';
$cfg['saveOldDevLogs']          = '<b>Default</b>: \'1\'<br> If is set to \'1\' old logfiles will be renamed if the max size is reached.';
$cfg['showDevLog']              = '<b>Default</b>: \'1\'<br> If is set to \'1\' the devolog is visible for admin users.';
$cfg['devLogLines']	            = '<b>Default</b>: \'23\'<br> The numbers of devlog-lines which are visible.';
$cfg['log']                     = 'The names of all logfiles. Without *.log';
?>
