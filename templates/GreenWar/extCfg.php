<?
// $Id: extCfg.php,v 1.8 2004/02/23 16:11:11 entropie Exp $ //

!defined('VERSION') ? define('VERSION', $cfg['version']) : '';
!defined('STYLE')   ? define('STYLE'  , 'GreenWar 1.5') : '';
!defined('PROLL')   ? define('PROLL', '<b><a class="dark" style="font-size:12px;" href="http://www.ackro.de/Jamp">J a m p</a></b> <br><dfn>Just another mp3 player?</dfn><br> Version: <i>'.VERSION.'</i> &mdash; Style: <i>'.STYLE.'</i>') : '';

// bgcolor of table row a
$cfg['bgcolor1']                     = '#51652C';
// bgcolor of table row b
$cfg['bgcolor2']                     = '#556B2F';
// browser highlight-row-color
$cfg['BrowsePointerColor']           = '#006400';
// browser click-row-color
$cfg['BrowseMarkerColor']            = '#006400';

$cfg['s']['botline']                 = '<div id="botline">%2<br>%1</div>';

// used doctype, dont change
$cfg['s']['doctype']                 = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"   "http://www.w3.org/TR/html4/loose.dtd">';

$cfg['s']['LoginRealm']              = $this->cfg['serverroot'] . ' - Authentication required!';
$cfg['s']['noLoginHead']             = '<h1>%1</h1>';
$cfg['s']['noLoginBody']             = '<p>%1</p>';

// strings fo the FOLDERNAME in the first (top) row.

#none
$cfg['s']['fileTableRowTop']         = '<a href="play.php?playlist=%1"><img src="templates/GreenWar/img/playlista.gif" width="16" height="16" alt=""></a>';
#id,backid,
$cfg['s']['fileRowEnqueueTop']       = '<a href="handle.php?add_pl_folder=%1&amp;backid=%2"><img src="templates/GreenWar/img/topica.gif" height="16" width="16" alt=""></a>';
#none
$cfg['s']['fileRowSubmitTop']        = '<input type="image" src="templates/GreenWar/img/senda.gif" alt="">';

// strings fo the FOLDERNAMES in all the other rows.

#id
$cfg['s']['fileTableRow']            = '<a href="play.php?playlist=%1"><img src="templates/GreenWar/img/playlist.gif" width="16" height="16" alt=""></a>';
#id,backid,
$cfg['s']['fileRowEnqueue']          = '<a href="handle.php?add_pl_folder=%1&amp;backid=%2"><img src="templates/GreenWar/img/topic.gif" height="16" width="16" alt=""></a>';
#id,foldername
$cfg['s']['fileRowDir']              = '<a href="index.php?pathid=%1">%2</a>';
#countdirs,countfiles,id
$cfg['s']['fileRowStat']             = '%1 %2 &nbsp; <a href="xmldump.php?xmlDump=%3" target="_blank"><img src="templates/GreenWar/img/xmldump.gif" height="10" width="10" alt=""></a>';
#id
$cfg['s']['fileRowCheck']            = '<input type="checkbox" name="ids[]" value="%1" class="checkbox">';
#none
$cfg['s']['fileRowSubmit']           = '<input type="image" src="templates/GreenWar/img/send.gif" alt="">';

// strings fo the FILENAMES in all rows.

#id,backid,
$cfg['s']['rowFile']                 = '<a href="handle.php?playlist_inid=%1&amp;backid=%2"><img src="templates/GreenWar/img/play.gif" height="13" width="15" alt=""></a> <a href="play.php?trackid=%1">%3</a>';
#id
$cfg['s']['rowFileCheckb']           = '<input type="checkbox" name="fids_enqueue[]" value="%1" class="checkbox">';
#none
$cfg['s']['rowFileSubmit']           = '<input type="image" src="templates/GreenWar/img/send.gif">';

// the image link to a validator
#none
$cfg['s']['validator']               = '<p style="text-align:right; width:670px;"><img src="templates/GreenWar/img/valid-css.gif" width="88" height="31" alt="Valid CSS"><a href="http://validator.schwarmcluster/check/referrer"><img src="templates/GreenWar/img/valid-html401.gif" width="88" height="31" alt="Valid HTML 4.01"></a></p>';
#none
// homepage link, top of the page, next to the form
$cfg['s']['homelink']                = '<a href="index.php?pathid=0" class="dark" onmouseover="home.src=\'templates/GreenWar/img/homea.gif\'" onmouseout="home.src=\'templates/GreenWar/img/home.gif\'" title="homepage"><img src="templates/GreenWar/img/home.gif" width="35" height="20" name="home" alt="homepage" style="margin-top:1px"></a>';

// the links in the menu

#none
$cfg['s']['playlist']                = '&bull; %2<br>';
#menuitem
$cfg['s']['hplink']                  = '&bull; <a href="./" class="menu">%1</a><br>';
#menuitem
$cfg['s']['randlink']                = '&bull; <a href="handle.php?random=1" class="menu">%1</a><br>';
#menuitem
$cfg['s']['helplink']                = '&bull; <a href="help.php" class="menu">%1</a><br>';
$cfg['s']['adminlink']               = '&bull; <a href="admin.php" class="menu">%1</a><br>';
$cfg['s']['checkalldirs']            = '&bull; <a href="#" class="menu" onclick="setCheckboxes(\'mainForm\', \'ids[]\', true); return false;">%1</a>/<a href="#" class="menu" onclick="setCheckboxes(\'mainForm\', \'ids[]\', false); return false;">%2</a> %3<br>';
$cfg['s']['checkallfiles']           = '&bull; <a href="#" class="menu" onclick="setCheckboxes(\'mainForm\', \'fids_enqueue[]\', true); return false;">%1</a>/<a href="#" class="menu" onclick="setCheckboxes(\'mainForm\', \'fids_enqueue[]\', false); return false;">%2</a> %3<br>';

// shoutbox forms

#username
$cfg['s']['shoutboxform_hidden']     = '<input type="hidden" name="sb_name" value="%1">';
#none
$cfg['s']['shoutboxform_visible']    = '            <input type="text" name="sb_name" value="" class="sb_field" id="sb_text0" maxlength="36">';
#[ |0]
$cfg['s']['shoutboxform_text']       = '            <input type="text" name="sb_text" value="" class="sb_field%1" id="sb_text1" maxlength="1024">';

$cfg['s']['adminLogin']              = '<input style="margin:0px; background-color:#000000; width:50px;" type="password" name="adminLogin" value="" class="secText">';

$cfg['s']['plin_sel_desel']          = '<a href="#" class="pllink" onclick="setSelectOptions(\'playedit\', \'playlist_ids[]\', true); return false;">%1</a>/<a href="#" class="pllink" onclick="setSelectOptions(\'playedit\', \'playlist_ids[]\', false); return false;">%2</a> All';
$cfg['s']['playlistlink']            = '&nbsp;&nbsp;<b><a title="Play list: %1" href="playlist.php?load=%1" class="menub">&rsaquo;</a></b> <a href="playlist.php?playlist=%1" class="menus">%2</a><br>';


$cfg['s']['pl_rowstat']              = '%1';
$cfg['s']['pl_rowfile']              = '<a href="play.php?trackid=%1">%2</a>';
$cfg['s']['pl_filetablerow']         = '<a href="playlist.php?load=%1"><img src="templates/GreenWar/img/playlist.gif" width="16" height="16" alt=""></a>';
$cfg['s']['search_rowfile']          = '<a href="play.php?trackid=%1">%2</a>';
?>
