<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
   "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<title>Jamp &bull; Help</title>
		<meta http-equiv="content-type" content="text/html; charset=ISO-8859-1">
		<style type="text/css">
		<!--
			body {margin:5px; padding:0px; font-family:Verdena; background-color:#7B8491; color:#000; font-size:16px;}
			#cvs { position:absolute; top:0px; right:10px; color:silver }
			h1 { background-color:#6C737D; border:1px solid #555A62; font-size:22px; }
			.cfgt { background-color:#A4ACB6; margin:10px; margin-bottom:0px; padding:3px; color:#4D5155; font-family:arial}
			.cfgt strong { color:#2D2F32 }
			.cfgh { background-color:#B8C0CC; margin:10px; margin-top:0px; padding:3px;}
		-->
		</style>
	</head>
<body>
<div id="cvs">$Id: index.php,v 1.2 2004/04/22 17:18:36 entropie Exp $</div>
<br>
<h1>Config settings</h1>
<?
	@include('../../cfg/cfg.php');
	$cfgv = $cfg;
	@include('../../cfg/cfg.help.php');
	foreach($cfgv as $v => $c) {
		echo "<div class=\"cfgt\">\$cfg['<strong>" . $v . "</strong>']</div>" . "\n";
		echo "<p class=\"cfgh\">" . $cfg[$v] . "</p>" . "\n";
	}
?>
</body>
</html>
