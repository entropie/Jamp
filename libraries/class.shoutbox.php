<?
// $Id: class.shoutbox.php,v 1.13 2004/08/25 19:36:05 entropie Exp $ //
if(!defined("class.shoutbox.php")) {
	define ("class.shoutbox.php", TRUE);

	class shoutbox extends main {
		function shoutbox () {
			$this->getCfg();
			return TRUE;
		}

		function PrintShoutBox () {
			if(empty($this->cfg['showShoutBox']))
				return FALSE;
			$pathid = isset($_GET["pathid"]) ? $_GET["pathid"] : '0';
			$str = '';
			$content = $this->parseXmlFile('shoutbox.xml', 'ShoutBox');
			$result = $this->log_mysql_query("SELECT id, DATE_FORMAT(sb_date, '%d.%m.%Y at %H:%i CET') as sb_date, sb_name, sb_text FROM ".$this->cfg["mysql"]["table_sb"]." ORDER BY id DESC LIMIT ".$this->cfg["sbLimit"]);
			$i = 1;
			while($row = mysql_fetch_array($result)) {
				$bgcolor = ($i++ % 2) ? $this->cfg["bgcolor1"] : $this->cfg["bgcolor2"];
				$row["sb_text"] = stripslashes($row["sb_text"]);
				$name = (defined('ADMIN') && ADMIN == 'admin') ? '<a class="dark" href="handle.php?sbdel='.$row["id"].'">'.htmlentities($row["sb_name"]).'</a>:' : htmlentities($row["sb_name"] . ":");
				if(strlen($row["sb_text"]) > $this->cfg["sbMaxTextLength"]) {
					$tmp = ' <a href="handle.php?sbmore='.$row["id"].'" title="'.$this->printLangS('sbLongLinkDesc').'">&rsaquo;&rsaquo;</a>';
					$row["sb_text"] = substr($row["sb_text"], 0, $this->cfg["sbMaxTextLength"]) . '...';
				} else
					$tmp = '';

				$myn  =  ereg_replace(':bgColor:', $bgcolor, $content['loop']);
				$myn  =  ereg_replace(':sbTitle:', $row["sb_date"], $myn);
				$myn  =  ereg_replace(':sbName:',  $name, $myn);
				$myn  =  ereg_replace(':sbEntry:', htmlentities($row["sb_text"]) . $tmp, $myn);
				$str.=$myn;
			}

			$str = ereg_replace("<loop>.*</loop>", $str, $content['main']);
			$str = ereg_replace(':backid:', $pathid, $str);
			$str = ereg_replace(':STR_shoutbox:', $this->printLangS('STR_sbtitle'), $str);
			$str = ereg_replace(':bgColor:', ($i++ % 2) ? $this->cfg["bgcolor1"] : $this->cfg["bgcolor2"], $str);


			$form = '';
			if(defined('USER'))
				$form.= $this->evalStyle('shoutboxform_hidden', USER) . "\n";
			else
				$form.= $this->evalStyle('shoutboxform_visible') . "\n";
			$form.= $this->evalStyle('shoutboxform_text', (defined('USER') ? '1' : ''));
			$str = ereg_replace(':shoutboxform:', $form, $str);
			$str = ereg_replace(':STR_sbsubmit:', $this->printLangS('STR_sbsubmit'), $str);

			return $str;
		}

		function insertSbEntry ($name, $text) {
			$insert = $this->log_mysql_query("INSERT INTO ".$this->cfg["mysql"]["table_sb"]." (sb_name, sb_text) VALUES ('".mysql_escape_string($name)."', '".mysql_escape_string($text)."')");
			if($insert) {
				$user = (defined('USER')) ? USER : $_SERVER["REMOTE_ADDR"];
				$this->dev($this->printLangS('logSbEntry', $user));
			}
		}
		function delSbEntry ($sbid) {
			$del = $this->log_mysql_query("DELETE FROM ".$this->cfg["mysql"]["table_sb"]." WHERE id = '". $sbid ."'");
			if($del) {
				$user = (defined('USER')) ? USER : $_SERVER["REMOTE_ADDR"];
				$this->dev($this->printLangS('logSbDelEntry', $sbid, $user));
			}
		}

		function moreSbEntry ($sbid) {
			$result = $this->log_mysql_query("SELECT id, DATE_FORMAT(sb_date, '%d.%m.%Y at %H:%i CET') as sb_date, sb_name, sb_text FROM ".$this->cfg["mysql"]["table_sb"]." WHERE id = '".$sbid."'");
			$row = mysql_fetch_array($result);
			$content = $this->parseXmlFile('shoutbox.xml', 'ShoutBoxFull');

			$this->printHeader('SB');
			$myn  =  ereg_replace(':sbname:', htmlentities($row["sb_name"]), $content['main']);
			$myn  =  ereg_replace(':sbdate:', $row["sb_date"], $myn);
			$myn  =  ereg_replace(':sbtext:', htmlentities(stripslashes($row["sb_text"])), $myn);
			print $myn;
			$this->printFooter('sb');
		}
	}
}
?>
