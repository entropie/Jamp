<?
// $Id: class.main.php,v 1.50 2004/10/06 02:11:53 entropie Exp $ //

if(!defined("class.main.php")) {
	define("class.main.php", true);

	// Important methods, main class
	//	@do				standart output
	//	@do				authentication
	//	@do				cookie handling
	//	@do				logfile handling
	//	@do				mysql connection handling
	//	@do				calls other classes
	class main {

		var $backId = '';

		// Mount point for class 'main', the main class
		//	@param		string		to control the output
		// 	@call								main->JampTime()
		// 	@call								main->getCfg()
		// 	@call								main->cookie()
		// 	@call								main->countSongBase()
		// 	@call								main->authenticate()
		// 	@call								main->adminLogin()
		// 	@call								main->printHeader(string)
		// 	@call								main->printLangS(string)
		// 	@call								main->includeTextFile(integer)
		// 	@call								main->getMysqlContent(integer)
		// 	@call								main->printFooter(string)
		// 	@call								main->dev(string)
		//	@return   boolean		always true
		function main ($child = 'none') {
			// used for admin login
			@session_start();

			$this->JampTime();
			$this->getCfg();
			$this->cookie();
			$this->countSongBase();

			// Authentication
			if(!empty($this->cfg['userLogin']) && !$this->authenticate()) {
				exit;
			} elseif (empty($this->cfg['userLogin'])) {
				// Makes it possible to log in via form
				if($this->adminLogin()) $_SESSION['LOGIN'] = FALSE;
				// IP based auth
				require('./cfg/cfg.admin.php');
			}

			// We don't want to go on, unless needed.
			if($child != 'print') return TRUE;

			if(!isset($_GET['pathid']) || $_GET['pathid'] == '')
				$_GET['pathid'] = 0;

			$this->printHeader($this->printLangS('titleBrowser'));
			
			if(!$this->includeTextFile($_GET['pathid'])) {
				// content from database
				$this->dev($this->printLangS('logMysqlContent', $_GET["pathid"]));
				echo $this->getMysqlContent($_GET['pathid']);
			} else
				$this->dev($this->printLangS('logCachedContent', $_GET["pathid"]));

			$this->printFooter();

			// write time after page handling
			$this->dev($this->printLangS('pageProccessing', $_GET["pathid"], $this->JampTime()));
			return TRUE;
		}

		// Count all database entrys.
		//	@param		boolean			true if we want to force the count
		//	@define		STATS				formatet string which contains the stats
		//	@return   array				'0', '1', 'dirsC', 'filesC'
		function countSongBase ($force = FALSE) {
			// fixme pls
			if(defined('STATS') && !$force)
				return TRUE;
			$result = $this->log_mysql_query('SELECT count(id) FROM '.$this->cfg['mysql']['table_files']);
			$row = mysql_fetch_array($result);
			$filesc = $row[0];

			$result = $this->log_mysql_query('SELECT count(id) FROM '.$this->cfg['mysql']['table_path']);
			$row = mysql_fetch_array($result);
			$foldersc = $row[0];
			!defined('STATS') ? define('STATS', $this->printLangS('countAllStr', $foldersc, $filesc)) : '';
			return array($foldersc, $filesc, 'dirsC' => $foldersc, 'filesC' => $filesc);
		}

		// Initialize authentication
		//	@call									main->auth()
		//	@call									main->printAuth()
		//	@call									main->setUser()
		//	@return   boolean			error
		function authenticate() {
			if(!$this->auth()) {
				$this->printAuth();
			} else {
				return $this->setUser();
			}
			return FALSE;
		}

		// Handles user authentication
		//	@call								main->auth()
		//	@call								main->setUser()
		//	@define		LOGIN			true
		//	@return   bool			always true
		function adminLogin () {
			if(isset($_POST['adminLogin']) && !empty($_POST['adminLogin']) && isset($_POST['secText']) && !empty($_POST['secText']))
				if($this->auth($_POST['secText'], md5($_POST['adminLogin']))) {
					$this->setUser(TRUE);
					return TRUE;
				}
			if(isset($_SESSION['USER']))
				if($this->auth($_SESSION['USER'], $_SESSION['PASSWD'])) {
					$this->setUser();
					return TRUE;
				}
			if(isset($_SESSION['LOGIN']) && $_SESSION['LOGIN'])
				!defined('LOGIN') ? define('LOGIN',TRUE) : '';
		}

		// HTTP authentication
		//	@call								main->evalStyle()
		//	@return   bool			always true
		function printAuth () {
			header('WWW-Authenticate: Basic realm="'.$this->evalStyle('LoginRealm').'"');
			header('HTTP/1.0 401 Unauthorized');
			header('status: 401 Unauthorized');
			print $this->evalStyle('doctype') . "\n";
			print "<html><head>\n";
			print "<title>" . $this->printLangS('noLoginHead') . "</title>\n</head>\n<body>\n";
			print $this->evalStyle('noLoginHead', $this->printLangS('noLoginHead')) . "\n";
			print $this->evalStyle('noLoginBody', $this->printLangS('noLoginBody')) . "\n";
			print "</body></html>";
			return TRUE;
		}

		// Check username and password for authentication
		//	@param		string		username to check
		//	@param		string		md5() hashed, password to check
		//	@call								main->log_mysql_query(string)
		//	@call								main->dev(string)
		//	@return   bool			true if login successful, false if not
		function auth ($usern = '', $passw = '') {
			if(empty($user) && empty($passw)) {
				$usern = (isset($_SERVER['PHP_AUTH_USER'])) ? $_SERVER['PHP_AUTH_USER'] : '';
				$passw = (isset($_SERVER['PHP_AUTH_PW']))   ? md5($_SERVER['PHP_AUTH_PW'])   : '';
			}
			$result = $this->log_mysql_query('SELECT * FROM '.$this->cfg["mysql"]["table_user"].' where username="'.mysql_escape_string($usern).'" && password="'.mysql_escape_string($passw).'"');
			if(mysql_num_rows($result) == 1) {
				$row = mysql_fetch_array($result);
				$this->login_usern = $row['username'];
				$this->login_rlvl  = $row['admin'];
				$this->login_pw    = $row['password'];
				$_SESSION['LOGIN'] = FALSE;
				return TRUE;
			} else {
				$this->dev($this->printLangS('loginNotSuccessful', $_SERVER['REMOTE_ADDR']));
				return FALSE;
			}
		}

		// Defines globales after successful authentication
		//	@param		boolean		true if user is logged in via session
		//	@define		ADMIN			enum('user', 'admin')
		//	@define		USER			username
		//	@return   boolean		always true
		function setUser($session = FALSE) {
			!defined('ADMIN') ? define('ADMIN', $this->login_rlvl)  : '';
			!defined('USER')  ? define('USER',  $this->login_usern) : '';
			if($session) {
				$_SESSION['ADMIN'] = $this->login_rlvl;
				$_SESSION['USER']  = $this->login_usern;
				$_SESSION['PASSWD']= $this->login_pw;
			}
			return TRUE;
		}

		// Get mysql version
		//	@call							main->log_mysql_query(string)
		//	@return   int			mysql version
		function get_mysql_version() {
			$result = $this->log_mysql_query('SHOW VARIABLES');
			while($row = mysql_fetch_array($result)) {
				if($row[0] == "version") {
					$version = substr($row[1], 0,1);
					break;
				}
			}
			if(isset($version))
				return (INT) $version;
		}

		// HTTP redirection
		//	@param		string		redirect to
		//	@param		boolean		if true, exit afterwards
		//	@return   boolean		always true
		function sendHeader ($str, $exit = FALSE) {
			header('location: ' . $this->checkfileSlash($this->cfg['serverroot']) . $str);
			if($exit) exit;
			return TRUE;
		}

		// Count script processing in msecs
		//	@define		STARTTIME		unix time
		//	@return   mixed				if defined -> (string) time; else (bool) TRUE
		function JampTime () {
			# ...
			if(!defined('STARTTIME')) {
				$start = gettimeofday();
				$stimem = (DOUBLE) ($start["usec"] / (1000000));
				$stime = $start["sec"] + $stimem;
				$stime = (DOUBLE) $stime;
				define ('STARTTIME', $stime);
				return true;
			} else {
				$end = gettimeofday();
				$endm = (DOUBLE) ($end["usec"] / (1000000));
				$etime = $end["sec"] + $endm;
				$etime = (DOUBLE) $etime;
			}
				return  sprintf("%01.4f Sec", $etime - STARTTIME);
		}

		// Load config variables, loads 'cfg.preLoad.php'
		//	@call								main->getExtCfg(array)
		//	@return   boolean		always true
		function getCfg () {
			if(!isset($this->cfg)) {
			  // Makes it possible to handle to cfg files (for example CVS)
				$cfgFileToLoad = is_file('./cfg/cfg.dev.php') ? './cfg/cfg.dev.php' : './cfg/cfg.php';
				include($cfgFileToLoad);
				include('./cfg/cfg.preLoad.php');
				// add new vars to $this->cfg
				$tmp = $this->getExtCfg($this->cfg = $cfg);
				$this->cfg = $tmp;
			}
			return TRUE;
		}

		// Loads template specific settings
		//	@param		array			config variables
		//	@return   array			new config variables
		function getExtCfg ($cfg) {
			include($this->cfg['style'] . '/extCfg.php');
			return $cfg;
		}

		// Gets the basename of a directory
		//	@param		string		directory name
		//	@return		string		new directory name
		function shorterDirnames ($string) {
			return basename($string);
		}

		// Kills the script, sends mail[s], flushes the buffer
		//	@param		string		error message
		//	@call								main->printLangS(string)
		//	@call								main->dev(string)
		//	@param		boolean		if the error is realy critical, sends mail
		function JampDie($str, $realError = FALSE) {
			if(!empty($this->cfg['mailOnError']) && $realError) {
				if(!is_array($this->cfg['mailAddr']))
					$this->cfg['mailAddr'] = array($this->cfg['mailAddr']);
				foreach($this->cfg['mailAddr'] as $addr) {
					mail($addr, $this->printLangS('mailSubject'), $this->printLangS('mailBody', $str));
				}
			}
			@ob_end_clean();
			$this->dev($this->printLangS('dieError', $str));
			die($str);
		}

		// Initialize the MySql database connection if is not set
		//	@param		string		mysql server address
		//	@param		string		mysql username
		//	@param		string		mysql password
		//	@param		string		mysql database
		//	@return		mixed			resource  if connection is initialized,
		//											true      if connection is always set
		//                      false     if occured an error
		function connectDb ($server, $username, $pw, $db) {
			if(isset($this->connect) && is_ressource($this->connect))
				return TRUE;
			$this->connect = @mysql_connect($server, $username, $pw);
			$this->selectdb = @mysql_select_db($db);
			if(!$this->connect || !$this->selectdb)
				return FALSE;
			else {
				return $this->connect;
			}
		}

		// Submits a db query and logs this query
		//	@param		string		database query
		//	@param		boolean		TRUE -> we log, FALSE we don't
		//	@param		string		log description
		//	@call								main->log(string)
		//	@call								main->connectDb(string, string, string, string)
		//	@call								main->JampDie(string)
		//	@return		mixed			resource -> if no error was occured,
		//											boolean  -> if anything was wrong
		function log_mysql_query($query, $log = TRUE, $ltype = '') {

			if($log)
				$this->log((empty($ltype) ? '' : ($ltype . ' - ')) . $query, $this->cfg['log']['mysql']);

			if(!isset($this->connid) || !is_resource($this->connid)) {
				if(!$this->connid = $this->connectDb($this->cfg["mysql"]["server"], $this->cfg["mysql"]["username"], $this->cfg["mysql"]["pw"], $this->cfg["mysql"]["db"])) {
					$this->JampDie(mysql_error());
					return FALSE;
				}
			}
			if(!$this->result = mysql_query($query, $this->connid)) {
				$this->JampDie(mysql_error(), TRUE);
				return FALSE;
			}
			return $this->result;
		}


		// Checks a directory about a trailing slash
		//	@param		string		directory name
		//	@return		string		new directory name
		function checkFileSlash($str) {
			if(substr($str, -1) != "/")
				$str = $str . "/";
			return $str;
		}

		// Gets the size of a file
		//	@param		string		the file
		//	@param		boolean		if true  -> return double
		//											if false -> return a string
		//	@return		mixed			[double|string]
		function getFileSize($comPath, $isStr = true) {
			$size = sprintf("%d", @filesize($comPath) / 1024);
			if($size > 999) {
				$size = ($isStr) ? (sprintf("%1.2f MB", $size / 1024)) : (REAL) (sprintf("%1.2f", $size / 1024));
				if($isStr)
					return $size;
			} else {
				$size = ($isStr) ? ($size . " KB") : (REAL) $size;
				return $size;
			}
		}

		// Check if a file is playable (extension)
		//	@param		string		the filename
		//	@call								main->printLangS(string)
		//	@return		boolean		[true|false]
		function checkFilePlayable ($string) {
			switch($string) {
				case substr($string, -4) == ".mp3":
				case substr($string, -4) == ".mp2":
				case substr($string, -4) == ".ogg":
				case substr($string, -4) == ".wav":
				// case substr($string, -4) == ".wma":
				// wmas wont work, with http
					$this->log($this->printLangS('fileIsPlayable', $string));
					return TRUE;
				break;
				default:
					$this->log($this->printLangS('fileIsNotPlayable', $string));
					return FALSE;
				break;
			}
		}

		// Includes a cached textfile
		//	@param		integer		the pid of the file
		//	@call								main->checkfileSlash(string)
		//	@return		boolean		always true
		function includeTextFile($pid) {
			if(is_file($this->incFileName = $this->checkfileSlash($this->cfg["dir_content"]) . 'path.' . $pid . '.inc.php')) {
				include($this->incFileName);
				// backid written in include file
				$this->backId = $backid;
				// pathids written in the include file
				$this->collectArray = $ids;
				return TRUE;
			} else
				return FALSE;
		}

		// Gets all content (dirs & files) from db, and writes them
		// to a textfile [$this->incFileName]
		//	@param		integer		the pid of the file
		//	@call								main->log_mysql_query(string)
		//	@call								main->printMainTableRows(array, array, array)
		//	@return		string		content of result
		//
		//	@todo			find a way to make the fuckn query faster
		function getMysqlContent($pid) {

			// main & path query
			$query = "SELECT a.id, a.pid, a.path, b.path as fullpath, count(d.id) as dc FROM ".$this->cfg["mysql"]["table_path"]." as a LEFT OUTER JOIN ".$this->cfg["mysql"]["table_symlink"]." as b ON a.prim_path_id = b.prim_path_id LEFT OUTER JOIN ".$this->cfg["mysql"]["table_path"]." as d ON d.pid = a.id WHERE a.id = '".$pid."' || a.pid ='".$pid."' GROUP BY a.id ORDER BY a.path";
			$this->mainresult = $this->log_mysql_query($query);
			// count files, with ONE query
			$countf = $this->log_mysql_query("SELECT a.id, count(b.id) as cf FROM ".$this->cfg["mysql"]["table_path"]." as a LEFT OUTER JOIN ".$this->cfg["mysql"]["table_files"]." as b ON b.pathid = a.id WHERE a.id = '".$pid."' || a.pid = '".$pid."' GROUP BY a.id");

			while($row = mysql_fetch_array($countf))
				// get count of files
				$cf[$row['id']] = $row['cf'];

			$backid = mysql_fetch_array($this->log_mysql_query("SELECT * FROM ".$this->cfg["mysql"]["table_path"]." WHERE id = ".$pid));
			$firstCountD = mysql_fetch_array($this->log_mysql_query('SELECT count(id) as count FROM '.$this->cfg["mysql"]["table_path"]. ' WHERE pid = "'.$pid.'" '));
			$this->fileResult = $this->log_mysql_query('SELECT count(id) as count, id, file  FROM '.$this->cfg["mysql"]["table_files"]. ' WHERE pathid = "'.$pid.'" GROUP by id');

			while($srow = mysql_fetch_array($this->fileResult)) {
				// get filelist
				$tblFileArray[] = array('id' => $srow["id"], 'fileName' => $srow['file']);
			}
			$this->backId = ($backid['pid'] != '') ? (STRING) $backid['pid'] : '0';

			$i = 0;
			while($row = mysql_fetch_array($this->mainresult)) {
				$i++;
				$this->collectArray[] = $row["id"];

				$countDirs = $row['dc'];
				$countFiles = $cf[$row['id']];
				if($pid == $row['id'] && !isset($extRow)) {
					$str = (substr($row['path'], 0, 1) == '/' ? '' : '/');
					$extRow[0]['countDirs'] = $countDirs;
					$extRow[0]['countFiles'] = $countFiles;
					$extRow[0]['id'] = $row["id"];
					$extRow[0]['path'] = $str.$row['path'];
					$extRow[0]['backId'] = $row['pid'];
					continue;
				} elseif (!isset($extRow)) {
					// for pid = 0;
					$extRow[0]['countDirs'] = $firstCountD[0];
					$extRow[0]['countFiles'] = $srow['count'];
					$extRow[0]['id'] = $row["id"];
					$extRow[0]['path'] = '/';
					$extRow[0]['backId'] = 1;
				}
				$tblRowArray[$i] = array('countDirs' => $countDirs, 'countFiles' => $countFiles,'id' => $row['id'], 'path' => $this->shorterDirnames($row['path']));
			}

			if(!isset($tblRowArray)) $tblRowArray = array();
			$str = $this->printMainTableRows((isset($extRow[0]) && is_array($extRow[0]) ? $extRow[0] : $extRow[1]), $tblRowArray, (isset($tblFileArray) && is_array($tblFileArray) ? $tblFileArray : ''));

			$fp = fopen($this->incFileName, 'w+');

			// write backid
			fwrite($fp, '<? $backid = "'. $this->backId .'"; ?>' . "\n<?\n");

			// write the pids, in an array in the file.
			if(!empty($this->collectArray)) {
				foreach($this->collectArray as $ids) {
					fwrite($fp, "\t" . '$ids[] = '.$ids.';' . "\n");
				}
			} else
				fwrite($fp, "\t" . '$ids[] = 0;' . "\n");

			fwrite($fp, '?>' . "\n" . $str);
			fclose($fp);
			return $str;
		}

		// Prints a cfg variable
		//	@param		string		name of array field
		//	@return		string		content of array
		function printCfgString ($s) {
			return $this->cfg[$s];
		}

		// Prints a cfg-language variable, replaces %N
		//	@param		string		name of array field
		//	@paramS   string    replaces %N with string
		//	@return		string		content of array
		function printLangS ($s, $arg1 = '', $arg2 = '', $arg3 = '', $arg4 = '') {
			if(!isset($this->cfg['lang'][$this->cfg['language']][$s]))
				return FALSE;
			$langStr = ereg_replace('%1', $arg1, $this->cfg['lang'][$this->cfg['language']][$s]);
			$langStr = ereg_replace('%2', $arg2, $langStr);
			$langStr = ereg_replace('%3', $arg3, $langStr);
			$langStr = ereg_replace('%4', $arg4, $langStr);
			return $langStr;
		}

		// Prints a cfg-style variable, replaces %N
		//	@param		string		name of array field
		//	@paramS   string    replaces %N with string
		//	@return		string		content of array
		function evalStyle ($s, $arg1 = '', $arg2 = '', $arg3 = '', $arg4 = '') {
			if(!isset($this->cfg['s'][$s]))
				return FALSE;
			$langStr = ereg_replace('%1', $arg1, $this->cfg['s'][$s]);
			$langStr = ereg_replace('%2', $arg2, $langStr);
			$langStr = ereg_replace('%3', $arg3, $langStr);
			$langStr = ereg_replace('%4', $arg4, $langStr);
			return $langStr;
		}

		// Writes strings to logfiles
		//	@param		string		the logstring which should be written
		//	@param		string		logtype (logfile)
		//	@call								main->printLangS(string)
		//	@return		nothing
		function log ($str, $type = 'Jamp') {
			$fp = fopen($this->checkfileSlash($this->cfg['logFolder']) . $type . '.log', 'a');
			if($type != $this->cfg['log']['dev'] && $type != $this->cfg['log']['access'])
				fwrite($fp, $this->printLangS('logFileLogStringSmall', $_SERVER["REMOTE_ADDR"], $str . "\n"));
			else
				fwrite($fp, $this->printLangS('logFileLogString', $str."\n"));
			fclose($fp);
		}

		// Writes dev-log to dev logfile
		//	@param		string		the logstring which should be written
		//	@call								main->printLangS(string)
		//	@return		nothing
		function dev ($str) {
			$this->log($this->printLangS('logDevLogString', $str), $this->cfg['log']['dev']);
		}

		// Gets admin log
		//	@call								main->getFileSize(string)
		//	@call								main->checkfileSlash(string)
		//	@return		string		content of the dev logfile
		function getLog () {
			if(defined('ADMIN') && ADMIN != 'admin')
				return false;
			$size = $this->getFileSize($this->checkfileSlash($this->cfg['logFolder']) . $this->cfg['log']['dev'], 0);
			$dstring = date('d-G-s-[B]') . 'dev.log';
			$devlogfile = $this->checkfileSlash($this->cfg['logFolder']) . $this->cfg['log']['dev'] . '.log';

			if(($size > $this->cfg['maxDevLogSize']) && (!empty($this->cfg['saveOldDevLogs']))) {
				copy($devlogfile, $this->checkfileSlash($this->cfg['logFolder']) . $dstring);
				$str = unlink($devlogfile);
			}

			$fp = file($devlogfile);
			$fp = array_reverse($fp);

			$str='';
			for($i = 0; $i < count($fp) && $i < $this->cfg['devLogLines']; $i++) {
				$bgcolor = ($i % 2) ? $this->cfg['bgcolor2'] : $this->cfg['bgcolor1'];
				$logline = ereg_replace('--', ' &mdash; ', $fp[$i]);
				$str.="\t<tr><td class=\"log\" bgcolor=\"$bgcolor\"><pre>" . '' . trim($logline) . "</pre></td></tr>\n";
			}
			return $str;
		}

		// Includes class.parse.php, and loads specific string
		//	@param		string		xml style file to load
		//	@param		string		the block which is to extract
		//	@call								main->checkfileSlash(string)
		//	@return		string		string contents
		function parseXmlFile ($file, $str) {
			$file = $this->checkfileSlash($this->cfg['style']) . $file;
			include('./libraries/class.parse.php');
			$c = new parse($file);
			$content = $c->getLoopStr($str);
			return array("main" => $content['main'], "loop" => $content['loop']);
		}

		// Formats the folder and filecount's
		//	@param		integer		directory count
		//	@param		integer		files count
		//	@call								main->printLangS(string)
		//	@return		array			directory count, files count
		//											html-formattet
		function getCount ($dirs, $files) {
			$countDirs = (empty($dirs)) ?
				NULL :
				(($dirs  != 1) ? ('<span class="num">' . $dirs .  '</span> '.$this->printLangS('STR_directories')) :
					'<span class="num">' . $dirs  . '</span> '.$this->printLangS('STR_directory'));
			$countFiles = empty($files) ?
				NULL :
				(($files != 1) ? ('<span class="num">' . $files . '</span> '.$this->printLangS('STR_files')) :
					'<span class="num">' . $files . '</span> '.$this->printLangS('STR_file'));
			if(empty($countDirs) && empty($countFiles))
				$countFiles = 'empty';

			if(!empty($countDirs) && !empty($countFiles))
				$countDirs.= ', ';

			return array('dirs' => $countDirs, 'files' => $countFiles);
		}


		// Prints out direcory rows
		//	@param		array			head row content
		//	@param		array			dir-loop row content
		//	@param		array			file-loop row content
		//	@call								parse->parseXmlFile(string, string)
		//	@call								main->getCount(integer, integer)
		//	@call								main->evalStyle(string)
		//	@call								main->printFileTableRows(array)
		//	@return		string		formatet sortet output
		function printMainTableRows ($extRow, $rowArray, $tblFileArray) {
			$content = $this->parseXmlFile('style.xml', 'SongListDb');
			$loop    = $content['loop'];
			$i = 1; $str = '';
			foreach($rowArray as $row) {
				$countAll = $this->getCount($row['countDirs'], $row['countFiles']);
				$bgColor = ($i++ % 2) ? $this->cfg["bgcolor1"] : $this->cfg["bgcolor2"];
				$myn = ereg_replace(':inc:',           "$i", $loop);
				$myn = ereg_replace(':pointerColor:', $this->cfg['BrowsePointerColor'], $myn);
				$myn = ereg_replace(':markerColor:',  $this->cfg['BrowseMarkerColor'], $myn);
				$myn = ereg_replace(':bgColor:',      $bgColor, $myn);
				$myn = ereg_replace(':fileRowPlay:',  $this->evalStyle('fileTableRow', $row['id']), $myn);
				$myn = ereg_replace(':fileRowEnqueue:',  $this->evalStyle('fileRowEnqueue', $row['id'], $_GET['pathid']), $myn);
				$myn = ereg_replace(':fileRowDir:',   $this->evalStyle('fileRowDir', $row['id'], $row['path']), $myn);
				$myn = ereg_replace(':fileRowStat:',  $this->evalStyle('fileRowStat', $countAll['dirs'], $countAll['files'], $row['id']), $myn);
				$myn = ereg_replace(':fileRowCheck:', $this->evalStyle('fileRowCheck', $row['id']), $myn);
				$myn = ereg_replace(':fileRowSubmit:',$this->evalStyle('fileRowSubmit'), $myn);
				$str.= $myn;
			}
			$countAll = $this->getCount($extRow['countDirs'], $extRow['countFiles']);
			$this->dirI = $i;
			$str.= $this->printFileTableRows(isset($tblFileArray) ? $tblFileArray : 0);

			if($_GET['pathid'] == 0)
				$extRow['id'] = "0";
			$str = ereg_replace("<loop>.*</loop>", $str, $content['main']);
			$str = ereg_replace(':inc:',           "1", $str);
			$str = ereg_replace(':pointerColor:', $this->cfg['BrowsePointerColor'], $str);
			$str = ereg_replace(':markerColor:',  $this->cfg['BrowseMarkerColor'], $str);
			$str = ereg_replace(':bgColor:',      ((0 % 2) ? $this->cfg["bgcolor1"] : $this->cfg["bgcolor2"]), $str);
			$str = ereg_replace(':fileRowPlay:',  $this->evalStyle('fileTableRowTop', $extRow['id']), $str);
			$str = ereg_replace(':fileRowEnqueue:',  $this->evalStyle('fileRowEnqueueTop', $extRow['id'], $_GET['pathid']), $str);
			$str = ereg_replace(':fileRowDir:',   $this->evalStyle('fileRowDir', $this->backId, $extRow['path']), $str);
			$str = ereg_replace(':fileRowStat:',  $this->evalStyle('fileRowStat', $countAll['dirs'], $countAll['files'], $extRow['id']), $str);
			$str = ereg_replace(':fileRowCheck:', $this->evalStyle('fileRowCheck', $extRow['id']), $str);
			$str = ereg_replace(':fileRowSubmit:',$this->evalStyle('fileRowSubmitTop'), $str);
			$str = ereg_replace(':backid:',       $_GET['pathid'], $str);
			return $str;
		}


		// Prints out file rows
		//	@param		array				file-loop row content
		//	@call									main->parseXmlFile(string)
		//	@return		string			formatet sortet output
		function printFileTableRows ($fileArray) {
			if(empty($fileArray))
				return FALSE;
			$content = $this->parseXmlFile('style.xml', 'SongList');
			$loop    = $content['loop'];
			$str = '';
			//	@see		main->printMainTableRows()
			$i = (isset($this->dirI)) ? $this->dirI : 1;
			$str = '';
			foreach ($fileArray as $row) {
				$bgColor = ($i++ % 2) ? $this->cfg["bgcolor1"] : $this->cfg["bgcolor2"];
				$myn = ereg_replace(':inc:',          "$i", $loop);
				$myn = ereg_replace(':pointerColor:', $this->cfg['BrowsePointerColor'], $myn);
				$myn = ereg_replace(':markerColor:',  $this->cfg['BrowseMarkerColor'], $myn);
				$myn = ereg_replace(':bgColor:',      $bgColor, $myn);
				$myn = ereg_replace(':rowFile:',      $this->evalStyle('rowFile', $row['id'], $_GET['pathid'], $row['fileName']), $myn);
				$myn = ereg_replace(':rowFileCheckb:',$this->evalStyle('rowFileCheckb', $row['id']), $myn);
				$myn = ereg_replace(':rowFileSubmit:',$this->evalStyle('rowFileSubmit'), $myn);
				$str.=  $myn;
			}
			return $str;
		}

		// Cookie handler
		//	@call									main->cookieSet()
		//	@call									main->cookieHandle()
		//	@return								nothing
		function cookie () {
			if(!isset($_COOKIE["Jamp"]))
				$this->cookieSet();
			else
				$this->cookieHandle();
		}


		// Sets cookie enviroment
		//	@define								USERMENU			true|false
		//	@define								SHOWPLAYLIST	true|false
		//	@call									main->log_mysql_query(string)
		//	@return								nothing
		function cookieHandle () {
			$result = $this->log_mysql_query("SELECT id FROM ".$this->cfg["mysql"]["table_time"]." WHERE cookie_string = '".$_COOKIE["Jamp"]."'");
			$row = mysql_fetch_array($result);
			$count = mysql_num_rows($result);
			if(!empty($count) && !defined('COOKIE_ID')) {
				define('COOKIE_ID', $row[0]);
			} else
				$this->cookieSet(true);

			// not really used at the moment
			if(!empty($this->cfg["showusermenu"]) && !defined('SHOWUSERMENU'))
				define('SHOWUSERMENU', true);
			if(!defined('SHOWPLAYLIST') && defined('COOKIE_ID'))
				define('SHOWPLAYLIST', true);
		}

		// Sets the cookie, checks for an old cookie
		//	@param		boolean			if true the user has an old cookie (mampf)
		//	@call									main->log_mysql_query(string)
		//	@call									main->dev(string)
		//	@define								COOKIE_ID		true|false
		//	@return								nothing
		function cookieSet ($old_cookie = false) {
			if($old_cookie) {
				$insert = $this->log_mysql_query("INSERT INTO ".$this->cfg["mysql"]["table_time"]." (cookie_string) VALUES ('".mysql_escape_string($_COOKIE["Jamp"])."')");
				if(!defined('COOKIE_ID'))	define('COOKIE_ID', mysql_insert_id());
			} else {
				while(1) {
					$randstring = md5(time() . $_SERVER["REMOTE_ADDR"]);
					$result = $this->log_mysql_query("SELECT id, cookie_string, date FROM ".$this->cfg["mysql"]["table_time"]." WHERE cookie_string = '".$randstring."'");
					$count = mysql_num_rows($result);
					if(empty($count)) {
						setcookie ("Jamp",  $randstring, time()+($this->cfg["cookie_lifetime"]), "/");
						if(isset($_COOKIE["Jamp"])) {
							$insert = $this->log_mysql_query("INSERT INTO ".$this->cfg["mysql"]["table_time"]." (cookie_string) VALUES ('".$_COOKIE["Jamp"]."')");
						}
						$cookieid = defined('COOKIE_ID') ? COOKIE_ID : mysql_insert_id();
						$this->dev($this->printLangS('logCookieNew', $cookieid));
						break;
					}
				}
			}
		}

		// Prints HTML-header
		//	@param		string			title of html page
		//	@call									main->parseXmlFile(string, string)
		//	@call									main->evalStyle(string)
		//	@call									main->printLangS(string)
		//	@call									main->checkfileSlash(string)
		//	@return								nothing
		function printHeader ($title) {
			ob_start();
			$content = $this->parseXmlFile('style.xml', 'header');
			$myn = ereg_replace(':doctype:', $this->evalStyle('doctype'), trim($content['main']));
			$myn = ereg_replace(':pageTitle:', $this->printLangS('title', $title), $myn);
			$myn = ereg_replace(':styleSheet:', $this->checkfileSlash($this->cfg["style"]) . 'screen.css', $myn);
			$myn = ereg_replace(':version:', VERSION, $myn);
			echo $myn;
		}

		// Prints HTML-footer
		//	@param		string			title the table
		//	@param		string			replace string fo table-title
		//	@call									main->parseXmlFile(string, string)
		//	@call									main->printLangS(string)
		//	@call									main->evalStyle(string)
		//	@call									sb->printShoutBox()
		//	@call									pl_in->init()
		//	@class								shoutbox
		//	@class								playlist_in
		//	@class								playlist
		//	@return								nothing
		function printFooter ($mainTableStr = 'browser', $append = '') {
			if(!isset($_GET['pathid']))
				$_GET['pathid'] = 0;

			// Initialize Shoutbox
			include('./libraries/class.shoutbox.php');
			$sb = new shoutbox;

			// Initialize user temporary playlists
			include('./libraries/class.playlist_in.php');
			$playlist_in = new playlist_in;

			// Initialize user playlists
			include('./libraries/class.playlist.php');
			$playl = new playlist;

			// Check if plalists are availible, if not => empty string
			if(!$lplayl = $playl->list_playlists())
				$lplayl   = '';

			$content = $this->parseXmlFile('style.xml', 'footer');
			$proll   = $this->parseXmlFile('style.xml', 'prolling');
			$proll   = ereg_replace(':proll:',          PROLL, $proll['main']);

			$mainTableStr = $this->printLangS('STR_maintabletitle_' . $mainTableStr, $append);

			$myn = ereg_replace(':user:',          ((defined('ADMIN') && ADMIN == 'admin' && defined('USER')) ? ('@'.USER) : (defined('USER') ? USER : 'nobody')), $content['main']);
			$myn = ereg_replace(':hplink:',        $this->evalStyle('hplink', $this->printLangS('STR_hplink')), $myn);
			$myn = ereg_replace(':pathid:',        (isset($_GET['pathid']) ? $_GET['pathid'] : 0), $myn);
			$myn = ereg_replace(':backid:',        $this->backId, $myn);
			$myn = ereg_replace(':playlist:',      $lplayl, $myn);
			$myn = ereg_replace(':checkalldirs:',  $this->evalStyle('checkalldirs', $this->printLangS('STR_checkalldirs'), $this->printLangS('STR_uncheckalldirs'), $this->printLangS('STR_uncheckenddirs')), $myn);
			$myn = ereg_replace(':checkallfiles:', $this->evalStyle('checkallfiles', $this->printLangS('STR_checkallfiles'), $this->printLangS('STR_uncheckallfiles'), $this->printLangS('STR_uncheckendfiles')), $myn);
			$myn = ereg_replace(':randlink:',      $this->evalStyle('randlink', $this->printLangS('STR_randlink')), $myn);
			$myn = ereg_replace(':helplink:',      $this->evalStyle('helplink', $this->printLangS('STR_helplink')), $myn);
			$myn = ereg_replace(':adminlink:',     (defined('ADMIN') && ADMIN == 'admin' ? $this->evalStyle('adminlink',$this->printLangS('STR_adminlink')) : ''), $myn);
			$myn = ereg_replace(':INIT_prolling:', $proll, $myn);
			$myn = ereg_replace(':adminLogin:',    (defined('LOGIN')) ? $this->evalStyle('adminLogin') : '', $myn);
			$myn = ereg_replace(':maintabletitle:',$mainTableStr, $myn);
			$myn = ereg_replace(':homelink:',      $this->evalStyle('homelink'), $myn);

			// Write 'inputs' for hidden form.
			$str = '';
			if(isset($this->collectArray) && !empty($this->collectArray))
				foreach($this->collectArray as $pid)
					$str.= '<input type="hidden" name="ids[]" value="'.$pid.'">' . "\n" .'              ';
			$myn = ereg_replace(':collectarray:',  $str, $myn);

			// user own temporary playlist
			$myn = ereg_replace(':playlist_in:',   $playlist_in->init(), $myn);
			// link to validator
			$myn = ereg_replace(':validator:',     $this->evalStyle('validator'), $myn);
			// shoutbox init
			$myn = ereg_replace(':INIT_shoutbox:', $sb->printShoutBox(), $myn);
			// stats & informations
			$myn = ereg_replace(':botline:',       $this->evalStyle('botline', $this->printLangS('botline', $this->JampTime()), $this->printLangS('botlineStats', STATS)), $myn);

			print $myn;
			ob_end_flush();
		}
	}
}
?>
