#!/usr/bin/env php
<?
// $Id: chmod.php,v 1.10 2004/03/04 22:20:24 entropie Exp $ //
// Sets the mod for directorys and files

@include('../cfg/cfg.php');

$user  = $cfg['httpdUser'];
$group = $cfg['httpdGroup'];
$name  = dirname(getcwd());

@system("chown -R ".$user.":".$group." $name 1>/dev/null 2>/dev/null");
@system("chmod 0770 $name 1>/dev/null 2>/dev/null");


function dirRights ($mdir) {
	$handle = opendir($mdir);
	while($file = readdir($handle)) {
		if($file == '.' || $file == '..')
			continue;
		if(is_dir($ndir = $mdir.'/'.$file)) {
			@chmod($ndir, 0770);
			dirRights($ndir);
		} elseif(!empty($ndir)) {
			@chmod($ndir, 0660);
		}
	}
	closedir($handle);
}
dirRights($name);
?>
