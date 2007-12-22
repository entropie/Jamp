<?
// $Id: playlist.php,v 1.8 2004/03/04 22:20:02 entropie Exp $ //

include('./cfg/cfg.php');
include('./libraries/class.main.php');
include('./libraries/class.playlist.php');

$mp3 = new main;
$mp3playlist = new playlist;

if(isset($_GET['load'])) {
	$mp3playlist->load_playlist($_GET["load"]);
}

if(!isset($_GET['playlist']) || empty($_GET['playlist']))
	$mp3->sendHeader('index.php');
else
	$mp3playlist->view_playlists($_GET['playlist']);
?>
