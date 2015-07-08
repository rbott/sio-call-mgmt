<?php
include_once('includes/sio-call-mgmt.php');
include_once('config/main-config.php');

$sio = new sioCallMgmt();

switch($_POST["method"]) {
case "login":
	session_start();
	session_destroy();
	$sio = new sioCallMgmt();
	if($sio->authenticateUserName($_POST["username"],hash("sha256",$_POST["password"]))) {
		session_start();
		$_SESSION["isAuthenticated"] = true;
		$_SESSION["userId"] = $sio->getUserId();
		Header("Location: index.php");
	} else {
		session_destroy();
		Header("Location: index.php?login=false");
	}
	break;
default:
	Header("Location: index.php");
}

?>
