<?php

$BASEPATH = realpath(dirname(__FILE__));

class sioCallMgmt {

	private $con;
	private $userId = -1;
	private $user;
	private $firstname;
	private $lastname;
	private $phoneNumbers = array();


	function sioCallMgmt() {
		$this->con = new mysqli(MY_SERVER, MY_USER, MY_PASSWORD, MY_DB);
		if($this->con->connect_errno) {
			die("Sadly, the database as passed away.\n");
		}
	}

	function isValidUser($user, $hash) {
		$stmt = $this->con->prepare("SELECT id, firstname, lastname FROM users WHERE user=? AND password=?");
		$stmt->bind_param("ss",$user,$hash);
		$stmt->execute();
		$stmt->bind_result($id,$firstname,$lastname);
		$stmt->fetch();
		$stmt->close();
		if(!empty($id)) {
			$this->firstname = $firstname;
			$this->lastname = $lastname;
			$this->userId = $id;
			$this->user = $user;
			return true;
		}
		else {
			return false;
		}
	}

	function isValidUserId($id) {
		$stmt = $this->con->prepare("SELECT user, firstname, lastname FROM users WHERE id=?");
		echo $this->con->error;
		$stmt->bind_param("i",$id);
		$stmt->execute();
		$stmt->bind_result($user,$firstname,$lastname);
		$stmt->fetch();
		$stmt->close();
		if(!empty($user)) {
			$this->firstname = $firstname;
			$this->lastname = $lastname;
			$this->userId = $id;
			$this->user = $user;
			return true;
		}
		else {
			return false;
		}
	}

	function isValidNumber($number) {
		$stmt = $this->con->prepare("SELECT user_id FROM numbers WHERE number=?");
		$stmt->bind_param("s",$number);
		$stmt->execute();
		$stmt->bind_result($id);
		$stmt->fetch();
		$stmt->close();
		if(!empty($id)) {
			return $this->isValidUserId($id);
		}
		else {
			return false;
		}

	}

	function getPhoneNumbers() {
		if(empty($this->phoneNumbers)) {
			$stmt = $this->con->prepare("SELECT number FROM numbers WHERE user_id=?");
			$stmt->bind_param("i",$this->userId);
			$stmt->execute();
			$stmt->bind_result($number);
			while($stmt->fetch()) {
				$this->phoneNumbers[] = $number;
			}
			$stmt->close();
		}
		return $this->phoneNumbers;
	}

	function getUserId() {
		return $this->userId;
	}

	function setUserId($userId) {
		return $this->isValidUserId($userId);
	}

	function getFullName() {
		return $this->firstname . " " . $this->lastname;
	}

	function getDnd() {
		$stmt = $this->con->prepare("SELECT dnd FROM users WHERE id=?");
		$stmt->bind_param("i",$this->userId);
		$stmt->execute();
		$stmt->bind_result($dnd);
		$stmt->fetch();
		$stmt->close();
		switch($dnd) {
		case "Y":
			return true;
		case "N":
		default:
			return false;
		}
	}

	function setDnd($val) {
		$stmt = $this->con->prepare("UPDATE users SET dnd=? WHERE user_id=?");
		switch($val) {
		case true:
			$sqlVal = "Y";
			break;
		case false:
			$sqlVal = "N";
			break;
		}
		$stmt->bind_param("si",$sqlVal,$this->userId);
		$stmt->execute();
		$stmt->close();
	}

	function getRedirect() {
		$stmt = $this->con->prepare("SELECT redirect FROM users WHERE id=?");
		$stmt->bind_param("i",$this->userId);
		$stmt->execute();
		$stmt->bind_result($redirect);
		$stmt->fetch();
		$stmt->close();
		switch($redirect) {
		case "Y":
			return true;
		case "N":
		default:
			return false;
		}
	}

	function setRedirect($val) {
		$stmt = $this->con->prepare("UPDATE users SET redirect=? WHERE user_id=?");
		switch($val) {
		case true:
			$sqlVal = "Y";
			break;
		case false:
			$sqlVal = "N";
			break;
		}
		$stmt->bind_param("si",$sqlVal,$this->userId);
		$stmt->execute();
		$stmt->close();
	}

	function getRedirectTo() {
		$stmt = $this->con->prepare("SELECT redirect_to FROM users WHERE id=?");
		$stmt->bind_param("i",$this->userId);
		$stmt->execute();
		$stmt->bind_result($redirectTo);
		$stmt->fetch();
		$stmt->close();
		return $redirectTo;
	}

	function setRedirectTo($number) {
		if(is_numeric($number)) {
			$stmt = $this->con->prepare("UPDATE users SET redirect_to=? WHERE user_id=?");
			$stmt->bind_param("si",$number,$this->userId);
			$stmt->execute();
			$stmt->close();
		}
	}

	function authenticateUserName($user,$hash) {
		return $this->isValidUser($user,$hash);
	}

	function authenticatePhoneNumber($number) {
		return $this->isValidNumber($number);
	}

	function getUserDetails() {
		return array(
			"fullName" => $this->getFullName(),
			"phoneNumbers" => $this->getPhoneNumbers(),
			"dnd" => $this->getDnd(),
			"redirect" => $this->getRedirect(),
			"redirectTo" => $this->getRedirectTo()
		);
	}

	function sendPushMessage($pushMessage) {
		if(PUSH_ENABLED) {
			curl_setopt_array($ch = curl_init(), array(
				CURLOPT_URL => PUSH_URL,
				CURLOPT_POSTFIELDS => array(
					"token" => PUSH_API_TOKEN,
					"user" => PUSH_USER_TOKEN,
					"message" => $pushMessage,
				),
				CURLOPT_RETURNTRANSFER => true,
			));
			curl_exec($ch);
			curl_close($ch);
		}
	}
}


?>
