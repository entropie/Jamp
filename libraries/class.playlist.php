<?
// $Id: class.playlist.php,v 1.13 2004/02/22 18:07:37 entropie Exp $ //
if(!defined("class.playlist.php")) {
	define("class.playlist.php", true);


	// The playlist class
	//	@do				optimize the playlist mysql-table
	//	@do				loads a playlist for play
	//	@do				list all names of the playlist for each user
	//	@do				writes playlists for mp3blaster
	//	@do				writes the cach-playlist-file
	//	@do				makes it possible to edit playlists
	//	@do				prints out the real playlist
	//
	//	@todo			a better way to write mp3blaster playlists!
	class playlist extends main {


		// Mount point for class playlist
		//	@return   always true
		function playlist () {
			$this->getCfg();
			$this->log_mysql_query('OPTIMIZE TABLE '.$this->cfg['mysql']['table_plist']);
			return true;
		}

		// Loads a playlist, plays them
		//	@param		string			playlist file
		//	@call									main->log_mysql_query(string)
		//	@define		EXTERN_PLAY	we have to know that we play an playlist
		//	@return   bool				error
		function load_playlist ($m3ufile) {
			$result = $this->log_mysql_query("SELECT songid as id FROM ".$this->cfg["mysql"]["table_plist"]." WHERE m3ufile = '".$m3ufile."' && cookie_string = '".COOKIE_ID."'");
			while($row = mysql_fetch_array($result))
				$extern_ids[] = $row["id"];
			$count = mysql_num_rows($result);
			if(empty($count))
				return FALSE;
			else {
				if(!defined('EXTERN_PLAY'))
					define('EXTERN_PLAY', true);
				include('./play.php');
				return TRUE;
			}
		}

		// Lists playlists for each user
		//	@call								main->log_mysql_query(string)
		//	@call								main->parseXmlFile(string)
		//	@call								main->evalStyle(string)
		//	@call								main->printLangS(string)
		//	@return   string		formatted string which contains the playlists
		//                      maybe empty for shure
		function list_playlists () {
			if(!defined('COOKIE_ID'))
				return FALSE;
			$result = $this->log_mysql_query("SELECT m3ufile as plname, COUNT(songid) as count FROM ".$this->cfg["mysql"]["table_plist"]." WHERE cookie_string = '".COOKIE_ID."' GROUP BY m3ufile ORDER BY id");
			$content = $this->parseXmlFile('style.xml', 'playlist_list');
			$count = mysql_num_rows($result);
			if(empty($count))
				return FALSE;
			$str = $this->evalStyle('playlist', (isset($_GET['pathid']) ? $_GET['pathid'] : '0'), $this->printLangS('STR_pllink'));
			while($row = mysql_fetch_array($result)) {
				$str.= ereg_replace(':playlistlink:', $this->evalStyle('playlistlink', $row['plname'], htmlentities($row['plname'])), $content['loop']);
			}
			return $str;
		}

		// Writes playlist for mp3blaster in a predefined directory
		//	@param		array			ids of the songs
		//	@call								main->checkfileSlash(string)
		//	@call								main->log_mysql_query(string)
		//	@call								main->parseXmlFile(string)
		//	@return   always true
    function write_mp3blaster_list ($array) {
   		$plfile = $this->checkfile_slash($this->cfg["mp3blaster_plist_dir"]) . $_GET["playlist"] . ".lst";
			$str = '<GLOBAL PLAYMODE="allgroups">' . "\n";
			$str.= '<GROUP NAME="'.$_GET["playlist"].'">' . "\n";
			foreach ($array as $id) {
				$result = $this->log_mysql_query("SELECT b.path, a.file, a.pathid, a.prim_path_id as fullpath FROM ".$this->cfg["mysql"]["table_files"]." as a LEFT OUTER JOIN ".$this->cfg["mysql"]["table_path"]." as b ON a.pathid = b.id WHERE a.id = $id");
				$row = mysql_fetch_array($result);
				if(!empty($row["path"])) {
					$songurl = $this->checkfile_slash($this->cfg["httpstreamroot"]) . $row["fullpath"] . "/" . $row["pathid"] . "/" . rawurlencode($row["file"]);
				} else {
					$songurl = $this->checkfile_slash($this->cfg["httpstreamroot"]) . $row["fullpath"] . "/" . "TOPLVL~/" . rawurlencode($row["file"]);
				}
				$file = $this->checkfile_slash($row["file"]);

				$result = $this->log_mysql_query("SELECT path FROM ".$this->cfg["mysql"]["table_symlink"]." WHERE prim_path_id = '0'");
				$primp = mysql_fetch_array($result);

				$str.= "    " . $primp['path'] . $songname = $row["path"] . "/" . $row["file"] . "\n";
			} // foreach
			$str.='</GROUP>' . "\n";
			$fp = fopen($plfile, 'w');
			fwrite($fp, $str);
			fclose($fp);
			return TRUE;
    }

		// Lists a full specific playlist
		//	@param		string		playlist name
		//	@call								main->log_mysql_query(string)
		//	@call								main->sendHeader(string)
		//	@call								main->parseXmlFile(string, string)
		//	@call								main->printHeader(string)
		//	@call								main->printFooter()
		//	@return   always true
		function view_playlists ($playlist) {
			if(!empty($_POST))
				switch($_POST) {
					case isset($_POST["del"]) && $_POST["del"] == "delone" && isset($_POST["fids_enqueue"]):
						foreach($_POST["fids_enqueue"] as $id)
							$this->log_mysql_query("DELETE FROM ".$this->cfg["mysql"]["table_plist"]." WHERE songid = '".$id."'");
							header("location: playlist.php?playlist=".$_GET["playlist"]);
					break;
					case isset($_POST["del"]) && $_POST["del"] == "delall":
						$this->log_mysql_query("DELETE FROM ".$this->cfg["mysql"]["table_plist"]." WHERE m3ufile='".$_GET["playlist"]."'");
						header("location: playlist.php");
					break;

					case isset($_POST["del"]) && $_POST["del"] == "play" && isset($_POST["fids_enqueue"]):
						define('EXTERN_PLAY', true);
						$extern_ids = $_POST["fids_enqueue"];
						include('./play.php');
					break;

					case isset($_POST["del"]) && $_POST["del"] == "mp3blaster":
						$this->write_mp3blaster_list($_POST["fids_enqueue"]);
						header("location: playlist.php?playlist=".$_GET["playlist"]);
						exit;
					default:
				}

			$result = $this->log_mysql_query("SELECT a.songid as id, b.file as fileName FROM ".$this->cfg["mysql"]["table_plist"]." as a LEFT OUTER JOIN ".$this->cfg["mysql"]["table_files"]." as b ON a.songid = b.id WHERE a.m3ufile = '".mysql_escape_string($playlist)."' && cookie_string = '".COOKIE_ID."' GROUP BY a.songid,a.m3ufile");
			$content = $this->parseXmlFile('style.xml', 'playlist');
			$i = 2; $str = ''; $tmp = '';
			$tmpc = mysql_num_rows($result);
			if(empty($tmpc)) {
				$this->sendHeader('index.php?pathid=0');
				exit;
			} else
				$this->backId = 0;
				while($row = mysql_fetch_array($result)) {
					$myn = ereg_replace(':inc:',          "$i", $content['loop']);
					$myn = ereg_replace(':pointerColor:', $this->cfg['BrowsePointerColor'], $myn);
					$myn = ereg_replace(':markerColor:',  $this->cfg['BrowseMarkerColor'], $myn);
					$myn = ereg_replace(':bgColor:',      ($i++ % 2) ? $this->cfg["bgcolor1"] : $this->cfg["bgcolor2"], $myn);
					$myn = ereg_replace(':rowFile:',      $this->evalStyle('pl_rowfile', $row['id'], $row['fileName']), $myn);
					$myn = ereg_replace(':rowFileCheckb:',$this->evalStyle('rowFileCheckb', $row['id']), $myn);
					$myn = ereg_replace(':rowFileSubmit:',$this->evalStyle('rowFileSubmit'), $myn);
					$str.= $myn;
				}
				$count = mysql_num_rows($result);
				$count = $this->getCount(0, $count);
				$myn = ereg_replace("<loop>.*</loop>", $str, $content['main']);
				$i = 0;
				$myn = ereg_replace(':STR_mpldelone:', $this->printLangS('STR_mpldelone'), $myn);
				$myn = ereg_replace(':STR_mpldelall:', $this->printLangS('STR_mpldelall'), $myn);
				$myn = ereg_replace(':STR_mplnothing:',$this->printLangS('STR_mplnothing'), $myn);
				$myn = ereg_replace(':STR_mplmp3blaster:',$this->printLangS('STR_mplmp3blaster'), $myn);
				$myn = ereg_replace(':STR_mplplay:',   $this->printLangS('STR_mplplay'), $myn);
				$myn = ereg_replace(':mBgColor:',     ($i % 2) ? $this->cfg["bgcolor1"] : $this->cfg["bgcolor2"], $myn);
				$i++;
				$myn = ereg_replace(':playlistname:', $_GET['playlist'], $myn);
				$myn = ereg_replace(':inc:',          "0", $myn);
				$myn = ereg_replace(':pointerColor:', $this->cfg['BrowsePointerColor'], $myn);
				$myn = ereg_replace(':markerColor:',  $this->cfg['BrowseMarkerColor'], $myn);
				$myn = ereg_replace(':bgColor:',      (($i % 2) ? $this->cfg["bgcolor1"] : $this->cfg["bgcolor2"]), $myn);
				$myn = ereg_replace(':fileRowPlay:',  $this->evalStyle('pl_filetablerow', $_GET['playlist']), $myn);
				$myn = ereg_replace(':fileRowEnqueue:',$this->evalStyle('fileRowEnqueue', "", ""), $myn);
				$myn = ereg_replace(':fileRowDir:',   $this->evalStyle('fileRowDir', "", $_GET['playlist']), $myn);
				$myn = ereg_replace(':fileRowStat:',  $this->evalStyle('pl_rowstat', $count['files']), $myn);
				$myn = ereg_replace(':fileRowCheck:', $this->evalStyle('fileRowCheck', ""), $myn);
				$myn = ereg_replace(':fileRowSubmit:',$this->evalStyle('fileRowSubmit'), $myn);

				$this->printHeader($this->printLangS('titlePlaylist', $_GET['playlist']));
				print $myn;
				$this->printFooter('pl', $_GET['playlist']);
				return TRUE;
		}

		// Writes the playlist in the database
		//	@param		mixed			string -> one id
		//											array  -> more ids
		//	@call								main->log_mysql_query(string)
		//	@call								main->checkfileSlash(string)
		//	@return   always true
		function write_playlist_dirs ($trackid, $m3ufile) {
			if(!is_array($trackid)) $trackid = array($trackid);
			foreach($trackid as $playlist) {
				// first we need the startpath
				$presult = $this->log_mysql_query("SELECT path FROM ".$this->cfg["mysql"]["table_path"]." WHERE id = '".$playlist."'");
				$path = mysql_fetch_array($presult);
				$path = $this->checkfileSlash($path[0]);
				$result = $this->log_mysql_query("SELECT a.path, a.id, b.file, b.id as fid FROM ".$this->cfg["mysql"]["table_path"]." as a LEFT JOIN ".$this->cfg["mysql"]["table_files"]." as b ON a.id = b.pathid WHERE a.path LIKE '%".mysql_escape_string($path)."%' || a.id = '$playlist'");
				while($row = mysql_fetch_array($result)) {
					if(!empty($row["file"])) {
						$slink_t = $row["path"];
						$slink_n = explode("/", $row["path"]);
						$pathname = $slink_n[count($slink_n) - 1];
						$song = $row["file"];
						$songurl = $this->checkfileSlash($this->cfg["httpstreamroot"]) . $row["id"] . "/" . rawurlencode($song);
						$insert = $this->log_mysql_query("INSERT INTO ".$this->cfg["mysql"]["table_plist"]." (m3ufile, songid, cookie_string) VALUES ('".$m3ufile."','".$row["fid"]."','".COOKIE_ID."')");
					}
				}
			}
			return TRUE;
    }
	}
}
?>
