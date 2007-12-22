<?
// $Id: cfg.admin.php,v 1.13 2004/02/19 03:18:43 entropie Exp $ //

// If $cfg["userLogin"] == FALSE, we need an admin hack.
// Its not soo clean, I know, but it works anyway.
// Will be fixed sometime - maybe.

if(isset($this->cfg['adminIps']) && !empty($this->cfg['adminIps'])) {
	foreach($this->cfg['adminIps'] as $aps) {
		if($aps == $_SERVER["REMOTE_ADDR"]) {
			!defined('USER') ? define('USER', 'admin') : '';
			!defined('ADMIN')? define('ADMIN','admin') : '';
			break;
		}
	}
}
?>
