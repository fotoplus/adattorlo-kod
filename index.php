<?php
ini_set('include_path', __DIR__);


$msg=false;
$err=false;
$log=false;
$allow=false;

require_once ('e/config/config.php');
require_once ('e/modules/mysql/mysql.php');

require_once ('e/modules/pages/pages.php');
if($page['name'] != 'hiba') {
	require_once ('e/modules/accesscontrol/ipcheck.php');
}

if($cli) {
	include('e/pages/adatszolgaltatas.xml.php');
} else {

	if(!in_array('xml',$segments) and !$cli) {
		include('e/tampletes/main-html-top.php');
	}
	if($allow) {
		include $page['file'];
	} else if(!$allow and $page['name']=='hiba'){
		include $page['file'];
	}

	if(!in_array('xml',$segments) and !$cli) {
		include('e/tampletes/main-html-bottom.php');
	}
}
?>