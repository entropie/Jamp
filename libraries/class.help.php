<?
// $Id: class.help.php,v 1.8 2004/02/19 19:14:16 entropie Exp $ //
if(!defined("class.help.php")) {
	define ("class.help.php", TRUE);

	class help extends main {

		function help () {
			session_start();
			$this->JampTime();
			$this->getCfg();
			$this->countSongBase();
			// authentication
			if(!empty($this->cfg['userLogin']) && !$this->authenticate()) {
				exit;
			} elseif (empty($this->cfg['userLogin'])) {

				// make it possible to log in via form
				if($this->adminLogin())
					$_SESSION['LOGIN'] = FALSE;

				// IP based auth
				require('./cfg/cfg.admin.php');
			}

			$this->printHeader('help');
			$this->printMainHelp();
			$this->printFooter();
		}

		function printMainHelp () {
?>
	<h2><a name="top">Table of Content</a></h2>
	<p>
		&bull; <a href="./" class="dark" style="font-size:16px; font-weight:bold">Homepage</a><br>
		&bull; <a href="#nav" class="dark" style="font-size:16px;">Global Navigation &amp; User guide</a><br>
		&bull; <a href="#cmdline" class="dark" style="font-size:16px;">The Commandline</a><br>
<? if(defined('ADMIN') && ADMIN == 'admin') { ?>
		&nbsp; &nbsp; -&rsaquo; <a href="#cmdline_admin" class="dark">Admin Commands</a><br>
<? } ?>
		&nbsp; &nbsp; -&rsaquo; <a href="#cmdline_user" class="dark">User Commands</a><br>
		&bull; <a href="#sb" class="dark" style="font-size:16px;">The mystery of the Shoutbox</a><br>
		&bull; <a href="about.php" class="dark" style="font-size:16px;">About Jamp</a><br>
		&bull; <a href="func.php" class="dark" style="font-size:16px;">Functionality</a><br>
		<!-- &bull; <a href="#upl" class="dark" style="font-size:16px;">The User-Playlist</a><br> -->
	</p>
	<h3><a name="nav">Global Navigation &amp; User guide</a></h3>

	<hr width="680" style="color:black" noshade>
	<h4>First row - left to right</h4>
	<ul class="silver">
		<li>The speaker -&rsaquo; Listen to entire folder and subfolders.</li>
		<li>The folder -&rsaquo; enqueues entire folder and subfolders in your user-playlist.</li>
		<li>The name -&rsaquo; click to view the folder content.</li>
		<li>The arrow '<span style="color:black">&rsaquo;</span>' -&rsaquo; click to view a XML-Dump of all folders and subfolders.</li>
		<li>The Statistics -&rsaquo; the numbers of the subfolders and subfiles.</li>
		<li>The Checkbox -&rsaquo; Check to enqueue selected folders and subfolders in your user-playlist.</li>
		<li>Any Arrow -&rsaquo; Click any to submit the Form, only if you have checked any checkboxes.</li>
	</ul>
	<h4>Second and third row - left to right</h4>
	<ul class="silver">
		<li>The Arrow -&rsaquo; Enqueue this song in your user-playlist.</li>
		<li>The Name -&rsaquo; Click to listen to this song.</li>
		<li>The Checkbox -&rsaquo; Check to enqueue one ore more songs into your user-playlist.</li>
		<li>The Arrow -&rsaquo; Click any to submit the Form, if you have checked any checkboxes.</li>
	</ul>
    <div style="text-align:right"><a href="#top">Top of this page</a></div>

	<h3><a name="cmdline">The Commandline</a></h3>
     <p class="silver">First you should know: the little textfield above is the '<b>command line</b>'.
     Enter a word or a phrase, to <b>search the database</b><br>
     If you enter a '<b><? echo $this->cfg["command_string"] ?></b>' as the first letter,
     you can enter the following commands:</p>
<? if(defined('ADMIN') && ADMIN == 'admin') { ?>
	<h4><a name="cmdline_admin">Admin Commands</a></h4>
	<ul class="silver">
		<li>
			<b><? echo $this->cfg["command_string"] ?>updatedb</b><br>
			 updates the mysql table
		</li>
		<li>
			<b><? echo $this->cfg["command_string"] ?>cleardb</b><br>
			 clears up the mysql table
		</li>
		<li>
			<b><? echo $this->cfg["command_string"] ?>clearcache</b><br>
			 Deletes all temporary files in (<b><?=$this->cfg["dir_content"]?></b>).
		</li>
		<li>
			<b><? echo $this->cfg["command_string"] ?>clearpl</b><br>
			 Deletes <b>ALL</b> playlists.
		</li>
		<li>
			<b><? echo $this->cfg["command_string"] ?>clearsb</b><br>
			 Deletes all entrys in the shoutbox.
		</li>
	</ul>
<? } ?>


	<h4><a name="cmdline_user">User Commands</a></h4>
    <ul class="silver">
		<li>
			<b><? echo $this->cfg["command_string"] ?>load </b> [playlist_name]<br>
			 load a saved playlist
		</li>
		<li>
			<b><? echo $this->cfg["command_string"] ?>save </b> [playlist_name]<br>
			 saves all dirs current on screen in a playlist with [playlist_name]
		</li>
		<li>
			<b><? echo $this->cfg["command_string"] ?>view</b><br>
			 With this command you will get a full list of playlists.<br>
			 You can also write <b><? echo $this->cfg["command_string"] ?>view [playlist_name]</b> to edit/view/play this playlist faster
		</li>
		<li>
			<b><? echo $this->cfg["command_string"] ?>help</b> or simple <b>help</b><br>
			 prints out this site
		</li>
    </ul>
    <div style="text-align:right"><a href="#top">Top of this page</a></div>
	<h3><a name="sb">The mystery of the Shoutbox</a></h3>
	<p class="silver">The Shoutbox is a simple chat.
	Enter your name or nickname and a message. The message will be visible
	to all users.<br>
	HTML, Javascript or other Script-languages are not allowed.
	</p>


<?
		}

	}
}
?>
