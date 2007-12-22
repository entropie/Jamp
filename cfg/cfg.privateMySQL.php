<?
// $Id: cfg.privateMySQL.php,v 1.7 2004/03/04 22:20:05 entropie Exp $ //

// This file will only be executet if you set the complete path in cfg.php.
// A example - bash style:
//   $ su
//   $ cp cfg.privateMySQL.php /usr/local/apache2/
//   $ cd /usr/local/apache2
//   $ chmod 0600 cfg.privateMySQL.php
//   $ chown nobody:nogroup cfg.privateMySQL.php
//   $ exit
//   $ set the correct path in cfg.php
// you should change the nobody:nogroup to your httpd group and user

$cfg['mysql']['server']   = 'localhost';
$cfg['mysql']['username'] = '';
$cfg['mysql']['pw']       = '';
$cfg['mysql']['db']       = 'Jamp';

?>
