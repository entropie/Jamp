<?
// $Id: play.php,v 1.11 2004/03/04 22:20:02 entropie Exp $ //

include('./cfg/cfg.php');
include('./libraries/class.main.php');
include('./libraries/class.play.php');
$mp3play = new play;

if(defined('EXTERN_PLAY')) {
	$m3ufile = $mp3play->playsongs($extern_ids);
} else {
	if (isset($_GET['trackid']) && !empty($_GET['trackid'])) {
		$m3ufile = $mp3play->playsongs($_GET['trackid']);
	} elseif (isset($_GET['playlist'])) {
		$songids = $mp3play->extractids($_GET['playlist']);
		if($songids) {
			$m3ufile = $mp3play->playsongs($songids);
		} else {
			$mp3play->sendHeader('index.php?pathid=0');
			exit;
		}
	}
}

header('Content-Type: audio/x-mpegurl');
$mp3play->sendHeader($m3ufile);
exit;
?>
