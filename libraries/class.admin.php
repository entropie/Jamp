<?
// $Id: class.admin.php,v 1.7 2004/02/29 20:51:23 entropie Exp $ //

if(!defined("class.admin.php")) {
	define ("class.admin.php", TRUE);

	// Admin class
	//	@do				user handling
	//	@do				commands listing
	//
	//	@todo			admin output with templates
	class admin extends main {

		// Mount point for class 'admin', the admin class
		//	@call								main->JampTime()
		//	@call								main->getCfg()
		//	@call								main->countSongBase()
		//	@call								main->authenticate()
		//	@call								main->sendHeader(string)
		//	@call								main->printLangS()
		//	@call								main->printHeader(string)
		//	@call								admin->global_admin()
		//	@call								admin->user_admin()
		//	@call								main->printFooter()
		//	@return   nothing
		function admin () {
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
			if(!defined('ADMIN') || ADMIN != 'admin')
				$this->sendHeader('index.php');


			$this->printHeader($this->printLangS('titleAdmin'));
				if(empty($_POST))
					$this->global_admin();
				$this->user_admin(isset($_GET['user']) ? TRUE : FALSE);
			$this->printFooter();

		}

		// Handles posted form data, user modifications
		//	@call								main->log_mysql_query()
		//	@return   nothing
		function user_handling() {
			switch($_POST['option']) {

				case 'add':
					if(!empty($_POST['useradd'])) {
						$this->log_mysql_query('INSERT INTO '.$this->cfg['mysql']['table_user'].' SET username="'.mysql_escape_string($_POST['useradd']).'"');
					}
				break;
				case 'rem':
					if(!empty($_POST['user'])) {
						$this->log_mysql_query('DELETE FROM '.$this->cfg['mysql']['table_user'].' WHERE username="'.mysql_escape_string($_POST['user']).'"');
					}
				break;
				case 'edit_insert':

					$id = $_POST['ID'];
					if(!empty($_POST['hpw'])) {
						if($_POST['hpw'] == $_POST['hpw1'])
							$pw = md5($_POST['hpw']);
						else
							$errors[] = 'The passwords does not match each other';
					}
					$this->new_user = $user = $_POST['huname'];
					$email= $_POST['hemail'];
					$lvl  = $_POST['hlvl'];
					$pwstr = isset($pw) ? 'password = "'.$pw.'",' : '';
					if(!isset($errors)) {
						$res = $this->log_mysql_query('UPDATE '.$this->cfg['mysql']['table_user'].' SET username="'.mysql_escape_string($user).'", '.$pwstr.' email="'.mysql_escape_string($email).'", admin="'.$lvl.'" WHERE id="'.$id.'"');
					}
				case 'edit':
					$user = isset($this->new_user) ? $this->new_user : $_POST['user'];
					if(empty($user))
						return TRUE;
					$result = $this->log_mysql_query('SELECT * FROM '.$this->cfg['mysql']['table_user'].' WHERE username="'.mysql_escape_string($user).'"');
					$row = mysql_fetch_array($result);
					if($row['admin'] == 'admin') {
						$opt1 = 'admin';
						$opt2 = 'user';
					} else {
						$opt1 = 'user';
						$opt2 = 'admin';
					}
				default:
?>
				<h1>User Details</h1>
<?
				if(isset($errors)) {
					echo "<p class=\"red\">";
					foreach($errors as $e)
							echo $e . "\n";
					echo "</p>";
				}
?>
				<form action="admin.php?user=1&amp;edit=1" method="post">
				<table border="0" style="background-color:<?=$this->cfg['bgcolor1']?>;">
					<tr>
					<td width="50%">Username:</td><td><input type="text" name="huname" value="<?=$row['username']?>"></td></tr><tr>
					<td>ID (not editable)</td><td><input type="text" value="<?=$row['id']?>"></td></tr><tr>
					<td>Password: (empty: old password)</td><td><input type="password" name="hpw" value=""><input type="password" name="hpw1" value=""></td></tr><tr>
					<td>Email:</td><td><input type="text" name="hemail" value="<?=$row['email']?>" size="44"></td></tr><tr>
					<td>Access Level:</td><td>
						<select name="hlvl" size="1">
								<option value="<?=$opt1?>"><?=$opt1?></option>
								<option value="<?=$opt2?>"><?=$opt2?></option>
						</select></td></tr><tr>
					<td>&nbsp;</td><td><input type="submit" value="Edit user"></td></tr><tr>
					</table>
					<input type="hidden" name="option" value="edit_insert">
					<input type="hidden" name="ID" value="<?=$row['id']?>">
					<input type="hidden" name="user" value="<?=$row['username']?>">
				</form>
<?
				break;
			}

		}


		// Handles posted form data, list users, prints form
		//	@call									main->log_mysql_query()
		//	@return   nothing
		function user_admin ($ext = FALSE) {

		if($ext) {
			if(!empty($_POST['option']))
				$this->user_handling();
		}
?>
		<h1>User Administration</h1>
		<form method="post" action="admin.php?user=1">
		<table cellspacing="2" cellpadding="3">
			<tr>
				<td width="50%">Username</td>
				<td>
					<select name="user">
						<option value="">USERNAME</option>
<?
			$result = $this->log_mysql_query('SELECT * FROM '.$this->cfg['mysql']['table_user'].'');
			while($row = mysql_fetch_array($result)) {
				echo $usera[] = "\t\t\t\t\t\t<option value=\"".$row['username']."\">".$row['username']."</option>" . "\n";
			}
?>
					</select>
				</td>
			</tr>
			<tr>
				<td><input type="radio" value="add" name="option"> Add new user</td>
				<td><input type="text" name="useradd"></td>
			</tr>
			<tr>
				<td><input type="radio" value="rem" name="option"> Remove User</td>
				<td></td>
			</tr>
			<tr>
				<td><input type="radio" value="edit" name="option" checked> Edit User</td>
				<td><input type="submit" value="Submit Form"></td>
			</tr>
			</table>
		</form>

<?

		}


		// Listing of commands
		//	@return   nothing
		function global_admin () {
?>
		<h1>Jamp Administration</h1>
		<table cellspacing="10">
			<tr>
				<td width="20%"><a href="form.php?text=<?=$this->cfg['command_string']?>updatedb">updatedb</a></td>
				<td>Inserts all song/folder entrys setted in the config variable <b>['mp3_dirs'][]</b> in the database.<br>Note: All existing entrys will be deleted and the cache will be cleared.</td>
			</tr>
			<tr>
				<td><a href="form.php?text=<?=$this->cfg['command_string']?>cleardb">cleardb</a></td>
				<td>Truncate all MySql tables. The database is empty after it.</td>
			</tr
			<tr>
				<td><a href="form.php?text=<?=$this->cfg['command_string']?>clearcache">clearcache</a></td>
				<td>All temporary files will be deleted. If you change any style settings this is usefull and important.</td>
			</tr>
			<tr>
				<td><a href="form.php?text=<?=$this->cfg['command_string']?>createcache">createcache</a></td>
				<td>Writes all temporary main files. This may take a while. <b>You have to set ['userLogin'] to 0</b>!</td>
			</tr>
			<tr>
				<td><a href="form.php?text=<?=$this->cfg['command_string']?>clearsb">clearsb</a></td>
				<td>The shoutbox entrys will be deleted. <b>ALL</b></td>
			</tr>
			<tr>
				<td><a href="form.php?text=<?=$this->cfg['command_string']?>phpinfo">phpinfo</a></td>
				<td>The php info page (phpinfo()).</td>
			</tr>
			<tr>
				<td><a href="form.php?text=<?=$this->cfg['command_string']?>license">license</a></td>
				<td>Pirnts out the Gnu public license.</td>
			</tr>
			<tr>
				<td><a href="form.php?text=<?=$this->cfg['command_string']?>login">login</a></td>
				<td>ala</td>
			</tr>
			</table>
			<p>All of these commands you can enter in the command line with the <b>['command_string']</b> before it.<br>
			Such as &quot;<b><?=$this->cfg['command_string']?>clearcache</b>&quot;.</p>
<?
		}
	}
}
?>
