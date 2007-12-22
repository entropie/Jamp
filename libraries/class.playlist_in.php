<?
// $Id: class.playlist_in.php,v 1.13 2004/07/06 00:55:39 entropie Exp $ //

if(!defined('class.playlist_in.php')) {
	define ('class.playlist_in.php', true);


	// The playlist_in class
	//	@do				optimize the playlist_in mysql-table
	//	@do				writes tmp playlists to a file
	//	@do				reads and edits the file
	//	@do				inserts and removes entrys from the mysql table
	//	@do				playlist_in output
	class playlist_in extends main {

		// Mount point for class playlist_in
		//	@call									main->getCfg()
		//	@call									main->log_mysql_query(string)
		//	@call									main->checkfileSlash(string)
		//	@return   always true
		function playlist_in () {
			$this->getCfg();
			$this->log_mysql_query('OPTIMIZE TABLE '.$this->cfg['mysql']['table_tmpplist']);
			if(defined('COOKIE_ID'))
				$this->plfile = $this->checkfileSlash($this->cfg["dir_content"]) . 'playlist.' . COOKIE_ID . '.inc.php';
			return TRUE;
		}

		// initializes the playlist output
		//	@call									main->checkfileSlash(string)
		//	@call									this->playlist_output()
		//	@return   mixed				string -> content of the result
		//												boolean -> false
		function init () {
			if(defined('COOKIE_ID')) {
				if(file_exists($this->plfile)) {
					return $this->playlist_output();
				} else {
					return FALSE;
				}
			}
		}

		// writes the playlist to a textfile
		//	@call									main->checkfileSlash(string)
		//	@call									main->log_mysql_query(string)
		//	@return   returns nothing
		function write_playlist_txt () {
			$optimize = $this->log_mysql_query("OPTIMIZE TABLE ".$this->cfg["mysql"]["table_tmpplist"]);
			if(defined('COOKIE_ID')) {
				$result = $this->log_mysql_query("SELECT b.id as songid, b.file as name FROM ".$this->cfg["mysql"]["table_tmpplist"]." as a LEFT JOIN ".$this->cfg["mysql"]["table_files"]." as b ON a.songid = b.id WHERE a.cookie_string = '".COOKIE_ID."' ORDER BY b.id");
				while($row = mysql_fetch_array($result)) {
					$playlistids[] = $row["songid"];
					$playlistnames[$row["songid"]] = $row["name"];
				}
				$count = mysql_num_rows($result);
			} else
				$count = 0;

			if(!empty($count)) {
				$str = '';
				$playlistarray = $playlistids;
				$songnames = $playlistnames;
				$size = (count($playlistarray) > $this->cfg["max_plist_height"]) ? $this->cfg["max_plist_height"] : count($playlistarray) + 1;
				$str.= "            " . '<select name="playlist_ids[]" id="playedit" size="'.$size.'" multiple>' . "\n";
				foreach($playlistarray as $songid)
					$str.= "              " . '<option value="'.$songid.'">'.$songnames[$songid].'</option>' . "\n";
				$str.="            " . '</select>' . "\n";
				foreach($playlistarray as $songid)
					$str.= "            " . '<input type="hidden" name="playlist_play[]" value="'.$songid.'">' . "\n";
				$fp = fopen($this->plfile, 'w+');
				fwrite($fp, $str);
				fclose($fp);
			}
		}

		function insert_songids($songids) {
			if(!is_array($songids))
				$songids = array($songids);
			if(defined('COOKIE_ID')) {
				foreach($songids as $songid) {
					$result = $this->log_mysql_query("SELECT songid FROM ".$this->cfg["mysql"]["table_tmpplist"]." WHERE songid = '$songid' && cookie_string = '".COOKIE_ID."'");
					$is_in_db = mysql_num_rows($result);
					if(empty($is_in_db))
						$insert = $this->log_mysql_query("INSERT INTO ".$this->cfg["mysql"]["table_tmpplist"]." (cookie_string, songid) VALUES ('".COOKIE_ID."', '$songid')");
				}
			}
			$this->write_playlist_txt();
		}

		function remove_songids($songids) {
			global $cfg;
			if(!is_array($songids)) $songids = array($songids);
			foreach($songids as $songid) {
				$delete = $this->log_mysql_query("DELETE FROM ".$cfg["mysql"]["table_tmpplist"]." WHERE songid = '$songid'");
			}
			$this->write_playlist_txt();
		}

		function save_playlist($ids, $m3ufile) {
			global $cfg;
			if(!is_array($ids)) $ids = array($ids);
				foreach($ids as $id) {
					$insert = $this->log_mysql_query("INSERT INTO ".$cfg["mysql"]["table_plist"]." (m3ufile, songid, cookie_string)
						VALUES ('".$m3ufile."', '$id', '".COOKIE_ID."')");
				}
			$this->pl_clear();
		}

		function pl_clear () {
			$delete = $this->log_mysql_query("DELETE FROM ".$this->cfg["mysql"]["table_tmpplist"]." WHERE cookie_string = '".COOKIE_ID."'");
			unlink($this->checkfileSlash($this->cfg["dir_content"]) . 'playlist.'.COOKIE_ID.'.inc.php');
		}


		function playlist_output () {
			$content = $this->parseXmlFile('style.xml', 'PlayListIn');
			$file = $this->plfile;
			$fp  = fopen($file, 'r');
			$myn = ereg_replace(':backid:',             $_GET['pathid'], $content['main']);
			$myn = ereg_replace(':playlist_in:',        fread ($fp, filesize($file)), $myn);
			$myn = ereg_replace(':STR_plaction:',       $this->printLangS('STR_plaction'), $myn);
			$myn = ereg_replace(':STR_plplayall:',      $this->printLangS('STR_plplayall'), $myn);
			$myn = ereg_replace(':STR_plplayselected:', $this->printLangS('STR_plplayselected'), $myn);
			$myn = ereg_replace(':STR_plrmsel:',        $this->printLangS('STR_plrmsel'), $myn);
			$myn = ereg_replace(':STR_plsave:',         $this->printLangS('STR_plsave'), $myn);
			$myn = ereg_replace(':STR_plnew:',          $this->printLangS('STR_plnew'), $myn);
			$myn = ereg_replace(':STR_plselectlink:',   $this->printLangS('STR_plselectlink'), $myn);
			$myn = ereg_replace(':STR_pldeselectlink:', $this->printLangS('STR_pldeselectlink'), $myn);
			$myn = ereg_replace(':STR_plformlabel:',    $this->printLangS('STR_plformlabel'), $myn);
			$myn = ereg_replace(':plin_sel_desel:',     $this->evalStyle ('plin_sel_desel', $this->printLangS('STR_plselectlink'), $this->printLangS('STR_pldeselectlink')), $myn);
			fclose($fp);
			return $myn;
		}
	}
}
?>
