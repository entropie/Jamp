<?
// $Id: class.play.php,v 1.10 2004/02/23 04:45:09 entropie Exp $ //

if(!defined("class.play.php")) {
  define ("class.play.php", true);

  // Class to play songs.
  //	@do				log all activitys
  //	@do				open the m3u-playlist
  //	@do				writes songs to the m3ufile
  //	@do				exract recursiv all ids from a path-pid
  //
  //	@todo			db-query in extract ids works with 'WHERE LIKE '%foo%'
  //						I'am not shure but this is not so nice, IMHO
  class play extends main {


    // Writes the logstring in the logfile
    //	@param		string			the file which we play
    //	@call									main->log(string)
    //	@call									main->printLangS(string)
    //	@return   nothing
    function playsongLoggin($song) {
      if(!empty($this->cfg['log']['access'])) {
        $this->log($this->printLangS('logSongLogFormat', $song, $_SERVER['REMOTE_ADDR']), $this->cfg['log']['access']);
      }
    }

    // Opens the m3u-playlist-file
    //	@param		string			the file which we want to open
    //	@call									main->JampDie(string)
    //	@call									main->printLangS(string)
    //	@return   resource		filehandler
    function openM3ufile($m3ufile) {
      $fp = @fopen($m3ufile, 'w+');
      $fwrite = @fwrite($fp, "#EXTM3U\n");
      if(!$fp || !$fwrite)
        $this->JampDie($this->printLangS('logCanFileNotOpen', $m3ufile, $_SERVER['REMOTE_ADDR']));
      else
        return $fp;
    }

    // Writes songs (ids) to the m3ufile
    //	@param		mixed				string -> one id, array -> more ids
    //	@call									main->checkfileSlash(string)
    //	@call									main->log_mysql_query(string)
    //	@call									play->openM3ufile(string)
    //	@call									play->playSongLogging(string)
    //	@return   resource		filename
    function playsongs($ids) {
      if(!is_array($ids))
        $ids = array($ids);

      $ids = array_unique($ids);

      $m3ufile = $this->checkfileSlash($this->cfg["streamroot"]) . $this->cfg["m3ufile"];
      $fp = $this->openM3ufile($m3ufile);
      natsort($ids);
      foreach($ids as $id) {
        $result = $this->log_mysql_query("SELECT b.path, a.file, a.pathid, a.prim_path_id as fullpath FROM ".$this->cfg["mysql"]["table_files"]." as a LEFT OUTER JOIN ".$this->cfg["mysql"]["table_path"]." as b ON a.pathid = b.id WHERE a.id = $id", TRUE, "PLAY:FOREACH");
        $row = mysql_fetch_array($result);
        if(!empty($row["path"])) {
          $songurl = $this->checkfileSlash($this->cfg["httpstreamroot"]) . $row["fullpath"] . "/" . $row["pathid"] . "/" . rawurlencode($row["file"]);
        } else {
          $songurl = $this->checkfileSlash($this->cfg["httpstreamroot"]) . $row["fullpath"] . "/" . "TOPLVL~/" . rawurlencode($row["file"]);
        }
        $this->playsongLoggin($row["file"]);
        $songname = $row["file"];
				
        fwrite($fp, "#EXTINF:-1,".$songname."\n".$songurl."\n");
      }
      fclose($fp);
      return $m3ufile;
    }

    // Extract ids from a pid (recursiv)
    //	@param		mixed				string -> one id, array -> more ids
    //	@param		boolean			true | false
    //												if true the return array is much more exploratory
    //	@call									main->checkfileSlash(string)
    //	@call									main->log_mysql_query(string)
    //	@call									play->playSongLogging(string)
    //	@return   mixed				array -> ids
    //												boolean -> false
    function extractids ($plids, $xml = false) {
      if(!is_array($plids))
        $plids = array($plids);
      foreach($plids as $plid) {
        $presult = $this->log_mysql_query("SELECT path FROM ".$this->cfg["mysql"]["table_path"]." WHERE id = '$plid'");
        $path = mysql_fetch_array($presult);
        $path =
          $this->checkfileSlash($path["path"]);
        //$result = $this->log_mysql_query("SELECT b.id, b.file, a.path, a.id as pid,c.path as prim_path, c.prim_path_id as ppid FROM ".$this->cfg["mysql"]["table_path"]." as a, ".$this->cfg["mysql"]["table_symlink"]." as c LEFT JOIN ".$this->cfg["mysql"]["table_files"]." as b ON a.id = b.pathid WHERE a.path LIKE '%".mysql_escape_string($path)."%' || a.id = '$plid'", TRUE, "PLAY:EXTRACIDS");

        $result = $this->log_mysql_query("SELECT b.id, b.file, a.path, a.id AS pid FROM ".$this->cfg["mysql"]["table_path"]." AS a LEFT JOIN ".$this->cfg["mysql"]["table_symlink"]." AS c ON c.id = a.id LEFT JOIN ".$this->cfg["mysql"]["table_files"]." AS b ON a.id = b.pathid WHERE a.path LIKE '%".mysql_escape_string($path)."%' || a.id = '$plid'", TRUE, "PLAY:EXTRACIDS");
        while($row = mysql_fetch_array($result)) {
          
          $prim_p = $this->log_mysql_query("SELECT id, prim_path_id, path from ".$this->cfg["mysql"]["table_symlink"]." where prim_path_id = '".$row["ppid"]."'", true, 'asd');
          $res = mysql_fetch_array($prim_p);
          // print_r($res['path']);
          // print_r($res['id']);

          if(!empty($row["id"])) {
            if($xml && !empty($row["path"]))
              $ids[] = array("id" => $row["id"], "path" => $row["path"], "file" => $row["file"], "prim_path" => $res["path"], "pid" => $row["pid"],"prim_pid" => $res["id"]);
            else
              $ids[] = $row["id"];
          }
        }
      }
      if(!empty($ids)) return $ids;
      else return false;
    }

    // Reads ID3 tags from a mp3-file
    //	@param		integer			increment of the file (for titlenumber)
    //	@param		string			name of the song
    //	@param		string			entire path of the song
    //	@call									main->checkfileSlash(string)
    //	@return   string			contents
    function id3 ($inc, $songname, $songpath) {
      if(!empty($this->cfg["id3_in_playlist"])) {
        $id3 = $this->getid3tag($this->checkfileSlash($this->cfg["streamroot"]) . $songpath);
        if(strlen($inc) < 2)
          $inc='0' . $inc;
        $str=$inc;
        if($id3['validid3']) {
          if(!empty($id3['artist']))
            $str.= ' - ' . $id3['artist'];
          if(!empty($id3['album']))
            $str.= '[' . $id3['album'].']';
          if(!empty($id3['songname']))
            $str.= ' - ' . $id3['songname'];
        } else
          return $songname;
      }
      return isset($str) ? $str : $songname;
    }

  }
 }
?>
