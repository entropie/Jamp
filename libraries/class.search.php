<?
// $Id: class.search.php,v 1.24 2004/08/25 19:36:05 entropie Exp $ //

if(!defined("class.search.php")) {
	define ("class.search.php", true);

	// Search class
	//	@do				searches the database
	//	@do				supports search querys
	//						PHRASE, AND, OR (, &&, ||)
	//	@todo			more testing
	class search extends main {

		// Mount point for class 'search', the search class
		//	@param		string		entire search string
		//	@param		boolean		search method enum( , '[AND|&&]', [OR|||])
		//	@call			search->getSearchQuery(string, string)
		//	@call			main->getCfg()
		//	@call			main->printHeader(string)
		//	@return		nothing
		function search ($input, $secText) {
			$this->getCfg();
 			$this->printHeader($this->printLangS('titleSearch', stripslashes( htmlentities($input) )));
  		$this->getSearchQuery($input, $secText);
		}

		// Get the real searchstring and query.
		//	@param		string		entire search string
		//	@param		boolean		search method enum( , '[AND|&&]', [OR|||])
		//	@call			search->printSearchResults(array, string)
		//	@call			main->log_mysql_query(string)
		//	@call			main->dev(string)
		//	@call			main->printLangS(string)
		//	@call			main->JampTime()
		//	@call			parse->parseXmlFile(string, string)
		//	@return		nothing
		function getSearchQuery ($input, $secText) {
			$this->getCfg();
			$secText = strtoupper($secText);
			$i = -1;
			switch($secText) {
				case $secText == 'AND' || $secText == '&&':
					$str = 'WHERE ';
					$sstrings = explode (" ", stripslashes($input));
					foreach($sstrings as $a) {
						$a = trim($a);
						if(!empty($a)) {
							$str.= 'concat(a.path, b.file) LIKE \'%'.mysql_escape_string(trim($a)).'%\' && ';
							$this->searchS[] = trim($a);
						}
					}
					// fixme
					$result[0] = $this->log_mysql_query("SELECT a.id as pid, a.path, b.id, b.file, c.path as fullpath FROM ".$this->cfg["mysql"]["table_path"]." as a,  ".$this->cfg["mysql"]["table_symlink"]." as c,  ".$this->cfg["mysql"]["table_files"]." as b  ".$str." a.id = b.pathid AND c.prim_path_id = a.prim_path_id", TRUE, "SEARCH");
					$title [0] = $input;
					$extString = '(AND)';
				break;
				case $secText == 'OR' || $secText == '||':
					$sstrings = explode (" ", $input);
					foreach($sstrings as $a)
						if(!empty($a)) {
							$this->searchS[] = trim($a);
						}
					foreach($sstrings as $inp) {
						$i++;
						$result[$i] = $this->log_mysql_query("SELECT a.id as pid, a.path, b.id, b.file, c.path as fullpath FROM ".$this->cfg["mysql"]["table_path"]." as a, ".$this->cfg["mysql"]["table_symlink"]." as c, ".$this->cfg["mysql"]["table_files"]." as b WHERE concat(a.path, b.file) LIKE '%".$inp."%' AND a.id = b.pathid AND c.prim_path_id = a.prim_path_id", TRUE, "SEARCH");
						$title [$i] = $inp;
					}
					$extString = '(OR)';
				break;
				default:
					$result[0] = $this->log_mysql_query("SELECT a.id as pid, a.path, b.id, b.file, c.path as fullpath FROM ".$this->cfg["mysql"]["table_path"]." as a, ".$this->cfg["mysql"]["table_symlink"]." as c, ".$this->cfg["mysql"]["table_files"]." as b WHERE concat(a.path, b.file) LIKE '%".$input."%' AND a.id = b.pathid AND c.prim_path_id = a.prim_path_id", TRUE, "SEARCH");
					$title [0] = $input;
					$extString = '';
				break;
			}

			$content = $this->parseXmlFile('style.xml', 'search');
			$str = '';
			$myn = ereg_replace(':backid:', "0", $content['main']);
			$sr = '';
			$this->dirI = 0;
			for($i = 0; $i < count($result); $i++)
				$sr.= $this->printSearchResults($result[$i], $title[$i]);
			$myn = ereg_replace(':searchresult:', $sr, $myn);
			print $myn;
			$this->dev($this->printlangS('logSearchQuery', (defined('USER') ? USER : $_SERVER['REMOTE_ADDR']), $input, $secText, $this->JampTime()));
			$this->printFooter('help', stripslashes(htmlentities($input)) . ' &mdash; ' . $extString);
			return TRUE;
		}


		// Prepares content for output
		//	@param		resource	mysql result resource
		//	@param		string		entire search string
		//	@call			parse->parseXmlFile(string, string)
		//	@call			main->evalStyleS(string)
		//	@return		string		formatet search output
		function printSearchResults ($result, $input) {
			while($row = mysql_fetch_array($result)) {
				$this->collectArray[$row["pid"]] = $row["pid"];
				$parray[ $row["pid"] ]['path']      = $row["path"];
				$parray[ $row["pid"] ]['id']        = $row["pid"];
				$parray[ $row["pid"] ]['countDirs'] = '0';
				$farray[ $row["pid"] ][ $row['id']  ]['fileName']  = $row["file"];
				$farray[ $row["pid"] ][ $row['id']  ]['id']        = $row["id"];
			}
			$this->backId = isset($_GET["backid"]) ? $_GET["backid"] : 0;
			$i = (isset($this->dirI)) ? $this->dirI : 0;
			$str = '';
			if(isset($parray) && is_array($parray)) {
				$content = $this->parseXmlFile('style.xml', 'searchTop');
				if(isset($this->searchS)) {
					if(!is_array($this->searchS))
						$this->searchS = array($this->searchS);
				} else
						$this->searchS = array($input);

				foreach($parray as $pid => $co) {
					$parray[$pid][$pid]['countFiles'] = count($farray[$pid]);
					$bgColor = ($i++ % 2) ? $this->cfg["bgcolor1"] : $this->cfg["bgcolor2"];
					$count = count($farray[$pid]);
					$countFiles = empty($count) ? NULL : (($count != 1) ? '<span class="num">' . $count . '</span> files'        : '<span class="num">' . $count . '</span> file');
					$myn = ereg_replace(':fileRowPlay:',   $this->evalStyle('fileTableRowTop', $co['id']), $content['main']);
					$myn = ereg_replace(':inc:',           "$i", $myn);
					$myn = ereg_replace(':pointerColor:', $this->cfg['BrowsePointerColor'], $myn);
					$myn = ereg_replace(':markerColor:',  $this->cfg['BrowseMarkerColor'], $myn);
					$myn = ereg_replace(':bgColor:',      $bgColor, $myn);
					foreach($this->searchS as $s)
						$dirname = eregi_replace('('.$s.')', "<span class=\"searchMatchDir\">\\0</span>", (isset($dirname) ? $dirname : $co['path']));
					$myn = ereg_replace(':fileRowDir:',   $this->evalStyle('fileRowDir', $co['id'], "/" . $dirname), $myn);
					unset($dirname);
					$myn = ereg_replace(':fileRowStat:',  $this->evalStyle('fileRowStat', 0, $countFiles, $co['id']), $myn);
					$myn = ereg_replace(':fileRowCheck:', 'aaa', $myn);
					$myn = ereg_replace(':fileRowSubmit:',$this->evalStyle('rowFileSubmit'), $myn);
					$top = $myn;
					if(isset($parray) && is_array($parray)) {
						$loop = $this->parseXmlFile('style.xml', 'searchFiles');
						foreach($farray[$pid] as $apid => $f) {
							$bgColor = ($i++ % 2) ? $this->cfg["bgcolor1"] : $this->cfg["bgcolor2"];
							$myn = ereg_replace(':inc:',          "$i", $loop['main']);
							$myn = ereg_replace(':pointerColor:', $this->cfg['BrowsePointerColor'], $myn);
							$myn = ereg_replace(':markerColor:',  $this->cfg['BrowseMarkerColor'], $myn);
							$myn = ereg_replace(':bgColor:',      $bgColor, $myn);
							foreach($this->searchS as $s)
								$filename = eregi_replace('('.$s.')', "<span class=\"searchMatchFile\">\\0</span>", (isset($filename) ? $filename : $f['fileName']));
							$myn = ereg_replace(':rowFile:',      $this->evalStyle('search_rowfile', $f['id'], $filename), $myn);
							unset($filename);
							$myn = ereg_replace(':rowFileCheckb:',$this->evalStyle('rowFileCheckb', $f['id']), $myn);
							$myn = ereg_replace(':rowFileSubmit:',$this->evalStyle('rowFileSubmit'), $myn);
							$top.= $myn;
						}
						$str.= $top;
					}
				}
			}
			$this->dirI++;
			return $str;
		}
	}
}
?>
