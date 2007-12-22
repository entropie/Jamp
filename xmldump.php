<?
// $Id: xmldump.php,v 1.8 2004/04/04 15:45:43 entropie Exp $ //

include('./libraries/class.main.php');
include('./libraries/class.play.php');
include('./libraries/class.xml.php');

if(isset($_GET['xmlDump']) && $_GET['xmlDump'] != '') {
	$mp3 = new main;
	if(is_file($mp3->checKfileSlash($mp3->cfg['dir_content']) . 'xml.' . $_GET['xmlDump'] . '.inc.xml')) {
		$mp3xml = new xml('', 'print', $_GET['xmlDump'], TRUE);
	}
	$mp3play = new play;
	$songids = $mp3play->extractids($_GET['xmlDump'], TRUE);
	if($songids)
		$mp3xml = new xml($songids, 'print', $_GET['xmlDump']);
} else {
	$mp3 = new main;
	$mp3->sendHeader('index.php');
}
?>
