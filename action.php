<?php
include_once('includes/sio-call-mgmt.php');
include_once('config/config.inc.php');

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
case "settings":
	session_start();
	if(!empty($_SESSION["isAuthenticated"]) && $_SESSION["isAuthenticated"]) {
		$sio->setUserId($_SESSION["userId"]);
		switch($_POST["dnd"]) {
		case "on":
			$sio->setDnd(true);
			break;
		case "off":
			$sio->setDnd(false);
			break;
		}
		switch($_POST["redirect"]) {
		case "on":
			$sio->setRedirect(true);
			break;
		case "off":
			$sio->setRedirect(false);
			break;
		}
		$sio->setRedirectTo($_POST["redirectTo"]);
		$message = "OK";
	}
	else {
		$message = "not OK";
	}
	echo json_encode($message);
	break;
default:
	Header("Location: index.php");
}

?>
