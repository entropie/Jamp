<?
// $Id: class.xml.php,v 1.9 2004/10/06 02:11:53 entropie Exp $ //
if(!defined("class.xml.php")) {
	define ("class.xml.php", TRUE);

	class xml extends main {

		function xml ($files, $go, $xmlPid, $is_file = FALSE) {
			@session_start();
			$this->getCfg();
			if($is_file) {
				$this->loadCachedXmlFile($xmlPid);
			}
			$this->get_info_array ($files, $go, $xmlPid);
			return TRUE;
		}

		function loadCachedXmlFile ($xmlPid) {
			header("content-type: text/xml");
			readfile($this->checkfileSlash($this->cfg['dir_content']) . 'xml.' . $xmlPid . '.inc.xml');
			exit;
		}

		function get_info_array ($files, $go, $xmlPid) {
			foreach($files as $id => $file) {
				if(is_array($file)) {
					if($xmlfarray[$file["path"]][$file["id"]] = $file["file"]) {
						$xmlfarray[$file["path"]]['pid'] = $file["pid"];
						if($go != "plist")
							$xmlfarray[$file["path"]]["fullpath"] = $file["prim_path"];
						else
							$xmlfarray[$file["path"]]["fullpath"] = $file["prim_pid"]."/".$file["pid"];
					}
				}
			} // foreach
			if ($go == "print")
				$this->print_xml_site($xmlfarray, $xmlPid);
		} // function get_info_array

		function xml_text ($string) {
			$string = ereg_replace("&", "&amp;", $string);
			return $string;
		}

		function get_songurl ($id) {
			$result = $this->log_mysql_query("SELECT b.path, a.file, a.pathid, a.prim_path_id as fullpath FROM ".$this->cfg["mysql"]["table_files"]." as a LEFT OUTER JOIN ".$this->cfg["mysql"]["table_path"]." as b ON a.pathid = b.id WHERE a.id = $id");
				$row = mysql_fetch_array($result);
				if(!empty($row["path"])) {
					$songurl = $this->checkfile_slash($this->cfg["httpstreamroot"]) . $row["fullpath"] . "/" . $row["pathid"] . "/" . rawurlencode($row["file"]);
				} else {
					$songurl = $this->checkfile_slash($this->cfg["httpstreamroot"]) . $row["fullpath"] . "/" . "TOPLVL~/" . rawurlencode($row["file"]);
				}
				return $songurl;
		}

		function print_xml_site ($xmlfarray, $xmlPid) {
			$x = ' ';
			$title = 'xmldump';
			$songstr = '';
			$dsize = '';
			$str = "<?xml version=\"1.0\" encoding=\"ISO-8859-1\" standalone=\"yes\"?>" . "\n";
			if(!isset($_GET['noStyleSheet']))
				$str.= '<?xml-stylesheet type="text/xsl" href="'.$this->checkfileSlash($this->cfg['serverroot']).'inc/xmlStyle.xsl" ?>' . "\n";
			$str.= "<!-- JampXML -->" . "\n";
			$str.= str_repeat($x, 2) . "<JampXML>" . "\n";
			foreach($xmlfarray as $path => $filea) {
				$_path = (defined('ADMIN') && ADMIN == 'admin') ? $this->checkfileSlash($filea['fullpath']).$path : "/".$path;
				$filestr = str_repeat($x, 4) . "<JampCAT PID=\"".$filea['pid']."\" path=\"".$this->xml_text($_path)."\" songcount=\"".(count($filea) -2)."\" fullsize=\"%%FSIZE%%\">". "\n";
				foreach($filea as $id => $file) {
					if($id != "fullpath" && $id != "pid") {
						$size = $this->getFileSize($filea["fullpath"].$path."/".$file);
						$songstr.=  str_repeat($x, 6) . "<song ID=\"".$id."\" name=\"".$this->xml_text($file)."\" size=\"$size\">";
						$songstr.= "</song>" .  "\n";
						if(substr($size, -2) == 'KB')
							$dsize = $dsize + ($this->getFileSize($filea["fullpath"].$path."/".$file, TRUE) / 1024) ;
						else
							$dsize = $dsize + ($this->getFileSize($filea["fullpath"].$path."/".$file, TRUE)) ;
					}
				}
				$str.= ereg_replace('%%FSIZE%%', sprintf("%1.2f MB", $dsize), $filestr);
				$str.= $songstr;
				$str.=   str_repeat($x, 4) . "</JampCAT>" . "\n";
				$dsize = '';
				$filestr = '';
				$songstr = '';
			}
			$str.= str_repeat($x, 2) . "</JampXML>" . "\n";

			$fp = fopen($this->checkfileSlash($this->cfg['dir_content']) . 'xml.' . $xmlPid . '.inc.xml', 'w+');
			fwrite($fp, $str);
			fclose($fp);

			header("content-type: text/xml");
			echo $str;
		}
	}
}
?>
