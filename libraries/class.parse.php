<?
// $Id: class.parse.php,v 1.6 2004/02/28 18:30:26 entropie Exp $ //

if(!defined("class.parse.php")) {
	define ("class.parse.php", TRUE);


	// XML-parsing class
	//	@do				reads style file
	//	@do				writes the parsed file in a temp file
	//	@do				reads cached|parsed file, return string
	//
	//	@todo			hm... that must works better.
	class parse extends main {
		var $tagArray = array(
			"special",
			"tag",
			"/special",
			"/tag",
		);

		// Mount point for class 'parse', the xml-parse class
		//	@param		string		xml style-file to load
		//	@call								main->checkfileSlash()
		//	@call								main->JampDie()
		//	@call								parse->readXmlFile()
		//	@return   boolean		true | false
		function parse ($file) {
			$this->getCfg();
			$xml = $this->checkfileSlash($this->cfg['dir_content']) . 'xml_style.' . basename($file) . '.inc';
			if(!is_file($file)) {
				$this->JampDie('Could not load XML-StyleSheet ' . $file, TRUE);
				return FALSE;
			} else {
				if(!is_file($xml)) {
					$this->content = $this->readXMLFile($file);
					$fp = fopen($xml, 'w+');
					fwrite($fp, $this->content);
					fclose($fp);
				} else {
					$fp = fopen($xml, 'r');
					$this->content = fread($fp, filesize ($xml));
				}
			}
			return TRUE;
		}

		// Reads cached file, return pieces
		//	@param		string		the tag we want to
		//	@return   array			main and loop contents
		function getLoopStr ($str) {
			$s1 = ".*<".$str.">";
			$s2 = "</".$str.">.*";
			$myStr = ereg_replace($s1, "", $this->content);
			$myStr = ereg_replace($s2, "", trim($myStr));
			$all = $myStr;

			$str = 'loop';
			$s1 = ".*<".$str.">";
			$s2 = "</".$str.">.*";
			$myStr = ereg_replace($s1, "", trim($myStr));
			$myStr = ereg_replace($s2, "", trim($myStr));
			return array('main' => $all, 'loop' => $myStr);
		}

		// Parse XML file and write the temporary file
		//	@param		string		XML-file
		//	@return   string		contents of file
		function readXMLFile ($file) {
			$fp = fopen ($file, "r");
			$i = 0; $mstr = '';
			while (!feof($fp)) {
				$l = fgets($fp, 4096);
				$space = strpos($l, "<");
				$l = trim($l);
				$str = '';
				$content = strip_tags($l);
				$tag = substr($l, 1, strpos($l, ">")-1) . " ";
				$tagn = substr($tag, 0, strpos($tag, " "));
				if(!in_array($tagn, $this->tagArray))
					continue;
				$opts = explode('"', $tag);
				if(isset($opts[1]))
					$str = str_repeat(" ", $space*2) . (!empty($opts[1]) ? "<" . $opts[1] : '') . (!empty($content) ? ' ' . $content : '') . (!empty($opts[1]) ? ">" : '');
				if(isset($opts[5]))
					$str.= $opts[5];
				if(isset($opts[3]))
					if($opts[3] == 'closed')
						$str.= '</' . $opts[1] . '>';
				if(!empty($str))
					$mstr.= $str . "\n";
			}
			fclose ($fp);
			return $mstr;
		}
	}
}
?>
