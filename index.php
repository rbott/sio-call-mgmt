<?php

include_once('includes/sio-call-mgmt.php');
include_once('includes/rain.tpl.class.php');
include_once('config/config.inc.php');

$sio = new sioCallMgmt();

raintpl::$tpl_dir = "tpl/";
raintpl::$cache_dir = "/tmp/";
raintpl::$base_url = "/sio-call-mgmt/";
raintpl::$path_replace = true;

$tpl = new raintpl();

session_start();
if(!empty($_SESSION["isAuthenticated"]) && $_SESSION["isAuthenticated"]) {
	$tpl->assign("authenticated", true);
	$sio->setUserId($_SESSION["userId"]);
	$tpl->assign("userDetails",$sio->getUserDetails());
}
else {
	$tpl->assign("authenticated", false);
}


#$tpl->assign("settings",$sio->getSettings());

$tpl->draw('index');

?>
