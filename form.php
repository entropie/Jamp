<?
// $Id: form.php,v 1.29 2004/10/06 02:11:16 entropie Exp $ //

include('./cfg/cfg.php');
include('./libraries/class.main.php');
include('./libraries/class.play.php');
include('./libraries/class.search.php');
include('./libraries/class.playlist.php');
include('./libraries/class.updatedb.php');
include('./libraries/class.admin.php');

$mp3     = new main;
$mp3play = new play;
$mp3playlist = new playlist;
$mp3updatedb = new updatedb;

if(isset($_POST['text']))
	$text = trim($_POST['text']);
elseif(isset($_GET['text']))
	$text = trim($_GET['text']);
else
	$mp3->sendHeader('index.php', TRUE);

$cs = $cfg['command_string'];

switch ($text) {
	// main: help
	case substr($text, 0, 5) == $cs. 'help' :
		$mp3->sendHeader('help.php', TRUE);
	break;

	// login
	case substr($text, 0, 6) == $cs. 'login':
		$mp3->adminLogin();
		if(!defined('LOGIN')) define('LOGIN', TRUE);
		$_SESSION['LOGIN'] = true;
	break;

	// let me my fun!
	case substr($text, 0, 5) == 'hello':
		print('Hello World!');
		exit;
	break;

	// admin: admin
	case defined('ADMIN') && ADMIN == 'admin' && substr($text, 0, 6) == $cs. "admin":
		$mp3->sendHeader('admin.php', TRUE);
	break;

	// admin: createCash
	case defined('ADMIN') && ADMIN == 'admin' && substr($text, 0, 12) == $cs. "createcache":
		$mp3updatedb->createCash(isset($_GET['nextPid']) ? $_GET['nextPid'] : 0);
		exit;
	break;

	// main: dellog
	case defined('ADMIN') && ADMIN == 'admin' && substr($text, 0, 5) == $cs. "dlog":
		unlink('./logs/dev.log');
	break;

	// playlist: load [name]
	case substr($text, 0, 6) == $cs. "load ":
		$mp3->sendHeader('playlist.php?load='.substr($text, 6), TRUE);
	break;

	// playlist: view
	case substr($text, 0, 5) == $cs. "view":
		$mp3->sendHeader('playlist.php', TRUE);
	break;

	// playlist: save
	case substr($text, 0, 6) == $cs. "save ":
		$plname = substr($text, 6);
		if(isset($_POST["ids"])) {
			if(empty($plname))
				$mp3->JampDie("gimme a name");
			$mp3playlist->write_playlist_dirs($_POST["ids"], $plname);
		}
	break;

	// udb: udb
	case substr($text, 0, 9) == $cs. "updatedb" && defined('ADMIN') && ADMIN == 'admin':
    	$mp3updatedb->init();
	break;

	// udb: cleardb
	case substr($text, 0, 8) == $cs. "cleardb" && defined('ADMIN') && ADMIN == 'admin':
		$mp3updatedb->cleardb();
    	$mp3updatedb->cleardb_logging($cfg);
	break;

	// udb: clearcache
	case substr($text, 0, 11) == $cs. "clearcache" && defined('ADMIN') && ADMIN == 'admin':
		system('rm -Rf ' . $mp3play->checkfileSlash($cfg["dir_content"]) . '*.*');
		system('rm -Rf ' . $mp3play->checkfileSlash($cfg["streamroot"])  . '*.m3u');
	break;

	// shoutbox: clearsb
	case substr($text, 0, 8) == $cs. "clearsb" && defined('ADMIN') && ADMIN == 'admin':
		$mp3->log_mysql_query("DELETE FROM ".$cfg["mysql"]["table_sb"]);
	break;

	// main: logout
	case substr($text, 0, 7) == $cs. "logout" && defined('USER'):
		session_destroy();
	break;

	// main: phpinfo();
	case substr($text, 0, 8) == $cs. "phpinfo" && defined('ADMIN') && ADMIN == 'admin':
		phpinfo();
		exit;
	break;

	// main: gpl
	case substr($text, 0, 8) == $cs. "license" || substr($text, 0, 7) == "license" || substr($text, 0, 3) == "gpl" || substr($text, 0, 4) == $cs . "gpl":
		$mp3->sendHeader('License', TRUE);
	break;

	// default: search $string
	default:
		$secText = (!empty($_POST['secText']) ? $_POST['secText'] : $cfg['defaultSearchType']);
		$mp3search = new search($text, $secText);
		exit;
	break;
}
$mp3play->sendHeader('index.php?pathid=' . (isset($_GET['backid']) ? $_GET['backid'] : 0), 1);
?>
