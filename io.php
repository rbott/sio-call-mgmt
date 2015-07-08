<?php

include_once('includes/sio-call-mgmt.php');
include_once('config/config.inc.php');

$sio = new sioCallMgmt();

switch($_POST["direction"]) {
case "in":
	if(!$sio->authenticatePhoneNumber($_POST["to"])) {
		die("Go Away\n");
	}
	break;
case "out":
	if(!$sio->authenticatePhoneNumber($_POST["from"])) {
		die("Go Away\n");
	}
	break;
default:
	die("This isn't going anywhere.\n");
}

Header("Content-type: application/xml");

if($sio->getDnd()) {
	$pushMessage = "Call from " . $_POST["from"] . " blocked.";
	$sio->sendPushMessage($pushMessage);

	$dom = new DOMDocument('1.0', 'UTF-8');
	$response = $dom->createElement('Response');
	$dom->appendChild($response);
	$hangup = $dom->createElement('Reject');
	$hangupReason = $dom->createAttribute('reason');
	$hangupReason->value = 'busy';
	$hangup->appendChild($hangupReason);
	$response->appendChild($hangup);
	echo $dom->saveXML();
	exit;
}

if($sio->getRedirect()) {
	$pushMessage = "Call from " . $_POST["from"] . " redirected.";
	$sio->sendPushMessage($pushMessage);

	$dom = new DOMDocument('1.0', 'UTF-8');
	$response = $dom->createElement('Response');
	$dom->appendChild($response);
	$dial = $dom->createElement('Dial');
	$number = $dom->createElement('Number',$sio->getRedirectTo());
	$dial->appendChild($number);
	$response->appendChild($dial);
	echo $dom->saveXML();
	exit;
}

?>
