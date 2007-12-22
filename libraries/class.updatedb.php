<?
// $Id: class.updatedb.php,v 1.18 2004/08/25 19:36:05 entropie Exp $ //
if(!defined("class.updatedb.php")) {
	define ("class.updatedb.php", TRUE);

	class updatedb extends main {

		function updatedb () {
			$this->getCfg();
			return TRUE;
		}

		function init () {
			$this->getCfg();
			$this->JampTime(1);
			$this->cleardb();
			$this->updatedb_logging();
			$i = 0;
			foreach($this->cfg["mp3_dirs"] as $dir) {
				$files = $this->_updatedb($dir, $i++);
				$this->dev($this->printLangS('udbFolder', $dir));
			}
			$this->create_symlinks(0);
			$this->sendHeader('index.php?pathid=0');
			$this->dev($this->printLangS('udbTime', $this->JampTime()));
			exit;
		}

		function updatedb_logging() {
			if(!empty($this->cfg['log']['udb']))
				$this->log($this->printLangS('udbLogging', 'updatedb', (defined('USER') ? USER : '')) , $this->cfg['log']['udb']);
		}

		function cleardb_logging($cfg) {
			if(!empty($this->cfg['log']['udb']))
				$this->log($this->printLangS('udbLogging', 'cleardb', (defined('USER') ? USER : '')) , $this->cfg['log']['udb']);
		}

		function create_symlinks ($playlist) {
			$this->dev($this->printLangS('udbSymlinkCreate'));
			$presult = $this->log_mysql_query("SELECT path as toppath, prim_path_id FROM ".$this->cfg["mysql"]["table_symlink"]);
			while($row = mysql_fetch_array($presult)) {
				if(!is_dir($topdir = $this->checkfileSlash($this->cfg["streamroot"]) . $row["prim_path_id"]))
					mkdir($topdir);
				$prim_path_id = $row["prim_path_id"];
				$prim_path = $row["toppath"];

				$result = $this->log_mysql_query("SELECT b.id, b.path, a.file, a.pathid as pid FROM ".$this->cfg["mysql"]["table_files"]." as a LEFT OUTER JOIN ".$this->cfg["mysql"]["table_path"]." as b ON a.pathid = b.id WHERE a.prim_path_id = '$prim_path_id'", true, "UDB:SYMLINK");
				while($row = mysql_fetch_array($result)) {
					if(!file_exists($topdir."/".$row["id"]))
						symlink($prim_path.$row["path"], $topdir."/".$row["id"]);
					if(empty($row["pid"]) && !file_exists($topdir."/TOPLVL~"))
						symlink($prim_path.$row["path"], $topdir."/TOPLVL~");
				}
			}
			$this->dev($this->printLangS('udbSymlinkCreateDone'));
		}

		function cleardb () {
			if(defined('ADMIN') && ADMIN != 'admin') {
				$this->sendHeader('index.php?pathid=0');
				$user = defined('USER') ? USER : $_SERVER["REMOTE_ADDR"];
				$this->dev($this->printLangS('udbCleardbDenied', $user));
				exit;
			}
			$delstr = ($this->get_mysql_version() > 3) ? 'TRUNCATE TABLE ' : 'DELETE FROM ';
			// Ligi said deleting is faster than updateting. Ligi is smart :-)
	    $this->log_mysql_query($delstr . $this->cfg["mysql"]["table_path"]);
	    $this->log_mysql_query($delstr . $this->cfg["mysql"]["table_files"]);
	    $this->log_mysql_query($delstr . $this->cfg["mysql"]["table_plist"]);
	    $this->log_mysql_query($delstr . $this->cfg["mysql"]["table_tmpplist"]);
	    $this->log_mysql_query($delstr . $this->cfg["mysql"]["table_symlink"]);
	    $this->log_mysql_query($delstr . $this->cfg["mysql"]["table_time"]);
			$this->dev($this->printLangS('udbDbDown'));

			system('rm -Rf '.$this->checkfileSlash($this->cfg["streamroot"]));
			system('rm -Rf '.$this->checkfileSlash($this->cfg["dir_content"]));

			$this->dev($this->printLangS('udbSymlinkDel'));
			mkdir ($this->cfg["streamroot"]);
			mkdir ($this->cfg["dir_content"]);
			$this->dev($this->printLangS('udbSymlinkCreateFolder'));
		}

		function _updatedb ($dir, $abscount = 0, $count = 0, $id = 0) {
			if(!is_dir($dir))
				$this->JampDie($this->printLangS('folderNotExist', $dir));
			if(empty($count))
				$this->log_mysql_query("INSERT INTO ".$this->cfg["mysql"]["table_symlink"]." (path, prim_path_id) VALUES ('".mysql_escape_string($dir)."', '".$abscount."')", TRUE, "UDB:SYMLINK:RECURSIV");
			$dir = $this->checkfileSlash($dir);
			$handle = opendir($dir);
			while($file = readdir($handle)) {
				if($file != "." && $file != "..") {
					$new_dir = $dir . $file;
					if(is_dir($new_dir)) {
						if($file == "lost+found") continue;
						$short_dir = ereg_replace ($this->cfg["mp3_dirs"][$abscount], '', $new_dir);
						$query = "INSERT INTO ".$this->cfg["mysql"]["table_path"]." (pid, path) VALUES ('".$id."', '".mysql_escape_string($short_dir)."')";
						$this->log_mysql_query($query, TRUE, "UDB:FOLDER:RECURSIV");
						$this->_updatedb($new_dir, $abscount, $count + 1, mysql_insert_id());
					} else {
						if(!$this->checkfilePlayable(strtolower($dir . $file)))
							continue;
						$files[] = $file;
					}
				}
			}
			$cl = closedir($handle);
			if(isset($files)) {
				if(!empty($this->cfg["sort_updatedb"]))
					natsort($files);
				foreach($files as $file)
					$this->log_mysql_query("INSERT INTO ".$this->cfg["mysql"]["table_files"]." (pathid, file, prim_path_id) VALUES ('".$id."','".mysql_escape_string($file)."', '".$abscount."')", TRUE, "UDB:FILES:RECURSIV");
			}
		}
		function createCash ($pid = 0) {
			$ca = $this->countSongBase(TRUE);
			$imgPendE = '<img src="' . $this->checkfileSlash($this->cfg['style'])  . 'img/process_endE.gif" width="15" height="13">';
			$imgP = '<img src="' . $this->checkfileSlash($this->cfg['style'])      . 'img/process.gif" width="%1" height="13">';
			$imgPend = '<img src="' . $this->checkfileSlash($this->cfg['style'])   . 'img/process_end.gif" width="15" height="13">';
			$imgPE= '<img src="' . $this->checkfileSlash($this->cfg['style'])      . 'img/process_e.gif" width="%1" height="13">';
			$maxw = "600";
			$m = $maxw / $ca['dirsC'];
			$sl = ceil($pid * $m);
			$el = ceil(($ca['dirsC'] - $pid) * $m);
			$msg1 = 'Please take a coffee and/ or smoke some weed. This may take a while.';
			$msg2 = 'Wake up... <b>HEY?</b>. Ahh... well, ok. I\'am ready soon.';
			$msg3 = 'Phew, this was not so easy as it looks...';
			echo $this->evalStyle('doctype');
?>
<html>
	<head>
		<title><?=$this->cfg['command_string']?>createcache</title>
<? if($pid < $ca['dirsC']) { ?>
		<meta http-equiv="refresh" content="0; URL=form.php?text=!createcache&nextPid=<?=++$pid ?>">
<? } ?>
		<link rel="stylesheet" type="text/css" href="<?=$this->checkfileSlash($this->cfg['style'])?>screen.css">
	</head>
<body>
<div id="content" style="top:35%">
<h1>Progress</h1>
<p>
	Done with page <b><?=$pid?></b> of <b><?=$ca['dirsC'] ?></b> (<a href="<?=$this->checkfileSlash($this->cfg['serverroot'])?>index.php">abort</a>).</p>
<p>
<?

if($sl < $el)
	echo $msg1;
elseif($pid < $ca['dirsC'])
	echo $msg2;
if($pid >= $ca['dirsC']) {
	echo "$msg3 <a href=\"index.php\">Back to Jamp</a>";
}
echo "<br>";
echo str_replace('%1', $sl, $imgP);
echo str_replace('%1', $el, $imgPE);
if($pid < $ca['dirsC'])
	echo $imgPendE;
else {
	echo $imgPend;
	$exit = TRUE;
}
?>
	</p>
</body>
</html><?
			$file = @fopen($this->checkfileSlash($this->cfg['serverroot']) . 'index.php?pathid=' . $pid, "r");
			        @fopen($this->checkfileSlash($this->cfg['serverroot']) . 'xmldump.php?xmlDump=' . $pid, "r");
			if(!$file)
				$this->sendHeader('index.php', TRUE);
			if(isset($exit))
				exit;
			fclose($file);
		}
	}
}
?>
