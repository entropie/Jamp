<?
// $Id: handle.php,v 1.17 2004/07/06 00:55:30 entropie Exp $ //

include('./cfg/cfg.php');
include('./libraries/class.main.php');
include('./libraries/class.play.php');
include('./libraries/class.playlist_in.php');
include('./libraries/class.shoutbox.php');
$mp3 = new main;
$mp3play = new play;
$mp3playlist_in = new playlist_in;
$mp3sb = new shoutbox;

if(empty($_GET) && empty($_POST)) {
	header("location: index.php");
	exit;
} else
	$irray = array_merge($_GET, $_POST);

switch($irray) {
	// login
	case isset($irray['login']):
		define('LOGIN', TRUE);
		$_SESSION['LOGIN'] = true;
	break;

	// shoutbox: add
	case isset($irray["addsbentry"]) && isset($_POST['sb_text']) && !empty($_POST['sb_text']) && isset($_POST['sb_name']) && !empty($_POST['sb_name']):
		$mp3sb->insertSbEntry($_POST["sb_name"], $_POST["sb_text"]);
	break;


	// shoutbox: del
	case isset($irray["sbdel"]) && defined('ADMIN') && ADMIN == 'admin':
		$mp3sb->delSbEntry($irray["sbdel"]);
	break;


	// shoutbox: view more
	case isset($irray["sbmore"]) && $irray["sbmore"] != '':
		$mp3sb->moreSbEntry($irray["sbmore"]);
		exit;
	break;


	// mainbrowser: add one folder to playlist
	case isset($irray["add_pl_folder"]) && !empty($irray["add_pl_folder"]):
		$songids = $mp3play->extractids($irray["add_pl_folder"]);
		if($songids)
			$insert = $mp3playlist_in->insert_songids($songids);
	break;


	// mainbrowser: add one song to playlist
	case isset($irray["playlist_inid"]) && !empty($irray["playlist_inid"]):
		$mp3playlist_in->insert_songids($_GET["playlist_inid"]);
	break;


	// mainbrowser: enqueue song[s]/folder[s] in playlist per checkbox
	case isset($irray["fids_enqueue"]) && !empty($irray["fids_enqueue"]) || isset($irray["ids"]) && !empty($irray["ids"]):
		if(isset($irray["fids_enqueue"]) && !empty($irray["fids_enqueue"]))
			$mp3playlist_in->insert_songids($irray["fids_enqueue"]);
		if(isset($irray["ids"]) && !empty($irray["ids"])) {
			$songids = $mp3play->extractids($irray["ids"]);
			if($songids)
				$insert = $mp3playlist_in->insert_songids($songids);
		}
	break;


	// playlist option handling
	case isset($irray["pl_action"]):
		// play all
		if(isset($irray["playlist_play"]) && !empty($irray["playlist_play"]) && $irray["pl_action"] == "play_all") {
		   	$m3ufile = $mp3play->playsongs($irray["playlist_play"]);
		   	$is_play = true;
		// play selected
		} elseif (isset($irray["playlist_ids"]) && !empty($irray["playlist_ids"]) && $irray["pl_action"] == "play_sel") {
			$m3ufile = $mp3play->playsongs($irray["playlist_ids"]);
			$is_play = true;
		// remove selected
		} elseif (isset($irray["playlist_ids"]) && !empty($irray["playlist_ids"]) && $irray["pl_action"] == "rem_sel") {
			$mp3playlist_in->remove_songids($irray["playlist_ids"]);
		// clear all
		} elseif (isset($irray["playlist_play"]) && !empty($irray["playlist_play"]) && $irray["pl_action"] == "new_playlist") {
			$mp3playlist_in->pl_clear();
		// save as static playlist
		} elseif (isset($irray["playlist_play"]) && !empty($irray["playlist_play"]) && $irray["pl_action"] == "save_all" && isset($irray["save_plname"]) && !empty($irray["save_plname"])) {
			$mp3playlist_in->save_playlist($irray["playlist_play"], $irray["save_plname"]);
		}
	break;


	// logout
	case isset($irray["logout"]):
		session_destroy();
	break;


	// play random song
	case isset($irray["random"]):
		$result = $mp3->log_mysql_query("SELECT COUNT(id) FROM ".$cfg["mysql"]["table_files"]);
		$row = mysql_fetch_array($result);
		$randsong = rand('1', $row["0"]);
		$m3ufile = $mp3play->playsongs($randsong);
		$is_play = true;
	break;
} // switch

if(isset($is_play) && $is_play) {
	include("play.php");
} else {
	$mp3->sendHeader('index.php?pathid=' . (isset($_GET['backid']) ? $_GET['backid'] : 0), 1);
}
?>
