<?
// $Id: cfg.preLoad.php,v 1.15 2004/02/18 23:33:28 entropie Exp $ //


if(!empty($cfg['mysql']['PrivFile'])) {
	if(is_file($cfg['mysql']['PrivFile'])) {
		require($cfg['mysql']['PrivFile']);
	} else
		die('Cannot find MySql private include file (\''.$cfg['mysql']['PrivFile'].'\')');
}

$cfg['pre']['folder2create'][] = $cfg['logFolder'];
$cfg['pre']['folder2create'][] = $cfg['dir_content'];
$cfg['pre']['folder2create'][] = $cfg['streamroot'];

for($i = 0; $i < count($cfg['pre']['folder2create']); $i++) {
	if(!is_dir($cfg['pre']['folder2create'][$i])) {
		mkdir($cfg['pre']['folder2create'][$i]);
		if($cfg['pre']['folder2create'][$i] != $cfg['streamroot'])
			@copy('./cfg/.htaccess', $cfg['pre']['folder2create'][$i] . '/.htaccess');
	}
}

// if isn't, copy htaccess to $cfg['dir_content'] - this make me feel better.
if(!is_file($cfg['dir_content'] . '/' . '.htaccess'))
	@copy('cfg/.htaccess', $cfg['dir_content'] . '/.htaccess');


// deletes to large logfiles
$cfg['log']['Jamp'] = 'Jamp';
foreach($cfg['log'] as $lname => $logf) {
	if($lname == 'dev') // handled with method main->getLog()
		continue;
	$logf = $cfg['logFolder'] . $logf . ".log";
	if(is_file($logf) && (filesize($logf) / 1024) > $cfg['max_log_size']) {
		unlink($logf);
		$this->dev($this->printLangS('logDeleted', $logf));
	}
}

unset($cfg['log']['Jamp']);
?>
