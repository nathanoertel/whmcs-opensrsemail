<?php
if (!class_exists('openSRS_mail')) {
    require_once dirname(__FILE__).DIRECTORY_SEPARATOR."opensrs".DIRECTORY_SEPARATOR."openSRS_mail.php";
}

function opensrsemail_ConfigOptions() {
	$config = array(
		"Username"	=> array("Type" => "text", "Size" => "20", "Description" => "Enter your company admin username here",),
		"Password"	=> array("Type" => "password", "Size" => "32", "Description" => "Enter your company admin password here",),
		"Domain"	=> array("Type" => "text", "Size" => "32", "Description" => "Enter your company admin domain here",),
		"Cluster"	=> array("Type" => "dropdown", "Options" => "A,B", "Description" => "Select the cluster associated with your account",),
		"Mode"		=> array("Type" => "dropdown", "Options" => "Test,Live", "Description" => "Select the operating mode",),
	);
		
	return $config;
}

function opensrsemail_ClientAreaCustomButtonArray() {
    $buttonarray = array(
	 "Add Mailbox" => "mailbox",
	);
	return $buttonarray;
}

function opensrsemail_CreateAccount($params) {
	$openSRS = new openSRS_mail($params["configoption1"], $params["configoption2"], $params["configoption3"], $params["configoption4"], $params["configoption5"]);
	
	try {
		$result = $openSRS->createDomain($params["domain"]);
		if($result["is_success"]) return "success";
		else return $result["response_text"];
	} catch(Exception $e) {
		return "Communication error, please contact the administrator.";
	}
}

function opensrsemail_SuspendAccount($params) {
	$openSRS = new openSRS_mail($params["configoption1"], $params["configoption2"], $params["configoption3"], $params["configoption4"], $params["configoption5"]);
	
	try {
		$result = $openSRS->disableDomain($params["domain"]);
		if($result["is_success"]) return "success";
		else return $result["response_text"];
	} catch(Exception $e) {
		return "Communication error, please contact the administrator.";
	}
}

function opensrsemail_UnsuspendAccount($params) {
	$openSRS = new openSRS_mail($params["configoption1"], $params["configoption2"], $params["configoption3"], $params["configoption4"], $params["configoption5"]);
	
	try {
		$result = $openSRS->enableDomain($params["domain"]);
		if($result["is_success"]) return "success";
		else return $result["response_text"];
	} catch(Exception $e) {
		return "Communication error, please contact the administrator.";
	}
}

function opensrsemail_TerminateAccount($params) {
	$openSRS = new openSRS_mail($params["configoption1"], $params["configoption2"], $params["configoption3"], $params["configoption4"], $params["configoption5"]);
	try {
		$result = $openSRS->deleteDomain($params["domain"]);
		if($result["is_success"]) return "success";
		else return $result["response_text"];
	} catch(Exception $e) {
		return "Communication error, please contact the administrator.";
	}
}

function opensrsemail_ClientArea($params) {
	if(!isset($_GET["modop"]) && !$_GET["modop"] == "custom") {
		$controller = new opensrsemail_Controller($params);
		
		if(isset($_POST["modaction"])) {
			if($_POST["modaction"] == "delete-workgroup") {
				return $controller->deleteWorkgroup($params);
			} else if($_POST["modaction"] == "delete-mailbox") {
				return $controller->deleteMailbox($params);
			}
		} else if(isset($_GET["modaction"]) && $_GET["modaction"] == "workgroups") {
			return $controller->listWorkgroups($params);
		}
		
		return $controller->listMailboxes($params);
	}
}

function opensrsemail_mailbox($params) {
	$controller = new opensrsemail_Controller($params);

	if(!isset($_POST["modaction"])) {
		if(isset($_GET["modaction"])) {
			if($_GET["modaction"] == "workgroup") {
				return $controller->addWorkgroup($params);
			}
		}
		
		return $controller->addEditMailbox($params);
	} else if($_POST["modaction"] == "save-mailbox") {
		return $controller->saveMailbox($params);
	} else if($_POST["modaction"] == "save-forward") {
		return $controller->saveForward($params);
	} else if($_POST["modaction"] == "save-alias") {
		return $controller->saveAlias($params);
	} else if($_POST["modaction"] == "save-workgroup") {
		return $controller->saveWorkgroup($params);
	}
}

class opensrsemail_Controller {
	
	private $vars;
	
	public function listMailboxes($params) {
		$openSRS = new openSRS_mail($params["configoption1"], $params["configoption2"], $params["configoption3"], $params["configoption4"], $params["configoption5"]);
		
		try {
			$mailboxesAllowed = $params["configoptions"]["Mailboxes"];
			$forwardsAllowed = $params["configoptions"]["Forwards"];

			$result = $openSRS->getNumDomainMailboxes($params["domain"]);
			
			$mailboxCount = $result["attributes"]["mailbox"];
			$forwardCount = $result["attributes"]["forward"];
			
			$result = $openSRS->getDomainMailboxes($params["domain"]);
			
			if($result["is_success"]) {
				$mailboxes = isset($result["attributes"]["list"]) ? $result["attributes"]["list"] : array();

				foreach($mailboxes as $index => $mailbox) $mailboxes[$index]["uctype"] = ucwords($mailbox["type"]);
				
				$this->vars["addMailbox"] = $mailboxesAllowed > $mailboxCount;
				$this->vars["deleteMailbox"] = $mailboxCount > $mailboxesAllowed;
				$this->vars["addForward"] = $forwardsAllowed > $forwardCount;
				$this->vars["deleteForward"] = $forwardCount > $forwardsAllowed;
				$this->vars["addAlias"] = count($mailboxes) > 0;
				$this->vars["mailboxes"] = $mailboxes;
				if(isset($_GET["added"])) $this->vars["addedMailbox"] = true;
				if(isset($_GET["edited"])) $this->vars["editedMailbox"] = true;
			} else {
				$this->vars["error"] = array($result["response_text"]);
			}	
		} catch(Exception $e) {
			$this->vars["error"] = array("Communication error, please contact the administrator.");
		}

		return array(
			'templatefile' => 'mailboxes',
			'vars' => $this->vars
		);
	}
	
	public function listWorkgroups($params) {
		$openSRS = new openSRS_mail($params["configoption1"], $params["configoption2"], $params["configoption3"], $params["configoption4"], $params["configoption5"]);
		
		try {
			$result = $openSRS->getDomainWorkgroups($params["domain"]);

			$workgroups = isset($result["attributes"]["list"]) ? $result["attributes"]["list"] : array();
			
			if($result["is_success"]) {
				$this->vars["workgroups"] = $workgroups;
				if(isset($_GET["added"])) {
					$this->vars["addSuccess"] = true;
				}
			} else {
				$this->vars["error"] = array($result["response_text"]);
			}	
		} catch(Exception $e) {
			$this->vars["error"] = array("Communication error, please contact the administrator.");
		}

		return array(
			'templatefile' => 'workgroups',
			'vars' => $this->vars
		);
	}
	
	public function addEditMailbox($params) {
		$openSRS = new openSRS_mail($params["configoption1"], $params["configoption2"], $params["configoption3"], $params["configoption4"], $params["configoption5"]);
	
		$type = $_GET["type"];
		
		$this->vars["type"] = $type;
		
		try {
			$result = $openSRS->getDomainWorkgroups($params["domain"]);
			
			$this->vars["workgroups"] = isset($result["attributes"]["list"]) ? $result["attributes"]["list"] : array();

			if($type == "mailbox") {
				if(isset($_GET["mailbox"]) && !empty($_GET["mailbox"])) {
					$result = $openSRS->getMailbox($params["domain"], $_GET["mailbox"]);
	
					$result["attributes"]["workgroup"] = $_GET["workgroup"];
					
					if($result["is_success"]) {
						$this->vars["editable"] = true;
						$this->vars["mailbox"] = $result["attributes"];
						$this->vars["deleteRequired"] = false;
						$this->vars["new"] = false;
					} else {
						$this->vars["error"] = array($result["response_text"]);
					}
				} else {
					$result = $openSRS->getNumDomainMailboxes($params["domain"]);
					
					if($result["is_success"]) {
						$this->vars["editable"] = $params["configoptions"]["Mailboxes"] > $result["attributes"]["mailbox"];
						$mailbox = array(
							"domain" => $params["domain"],
							"first_name" => $this->getVar("firstName"),
							"last_name" => $this->getVar("lastName"),
							"title" => $this->getVar("title"),
							"phone" => $this->getVar("phone"),
							"fax" => $this->getVar("fax"),
							"mailbox" => $this->getVar("mailbox"),
							"workgroup" => $this->getVar("workgroup"),
							"password" => "",
							"provisioning_state" => "",
							"timezone" => "",
							"language" => "",
							"spam_tag" => "",
							"spam_folder" => "",
							"spam_level" => "normal"
						);
						
						$this->vars["mailbox"] = $mailbox;
						$this->vars["deleteRequired"] = !$this->vars["editable"];
						$this->vars["new"] = true;
					} else {
						$this->vars["error"][] = $result["response_text"];
					}
				}
			} else if($type == "forward") {
				
				if(isset($_GET["mailbox"]) && !empty($_GET["mailbox"])) {
					$result["attributes"]["workgroup"] = $_GET["workgroup"];
					
					$result = $openSRS->getMailboxForwardOnly($params["domain"], $_GET["mailbox"]);
					
					$result["attributes"]["mailbox"] = $_GET["mailbox"];
					
					if($result["is_success"]) {
						$this->vars["mailbox"] = $result["attributes"];
						$this->vars["new"] = false;
					} else {
						$this->vars["error"] = array($result["response_text"]);
					}
				} else {
					$result = $openSRS->getNumDomainMailboxes($params["domain"]);
					
					if($result["is_success"]) {
						$this->vars["editable"] = $params["configoptions"]["Forwards"] > $result["attributes"]["forward"];
						$mailbox = array(
							"domain" => $params["domain"],
							"mailbox" => $this->getVar("mailbox"),
							"forward_email" => $this->getVar("forwardEmail"),
							"workgroup" => $this->getVar("workgroup")
						);
						
						$this->vars["mailbox"] = $mailbox;
						$this->vars["deleteRequired"] = !$this->vars["editable"];
						$this->vars["new"] = true;
					} else {
						$this->vars["error"][] = $result["response_text"];
					}
				}
			} else if($type == "alias") {
				$result = $openSRS->getDomainMailboxes($params["domain"]);
				
				if($result["is_success"]) {
					$mailboxes = isset($result["attributes"]["list"]) ? $result["attributes"]["list"] : array();
	
					foreach($mailboxes as $mailbox) {
						$mailbox["type"] = ucwords($mailbox["type"]);
					}
					
					$this->vars["mailboxes"] = $mailboxes;
					$this->vars["alias"] = $this->getVar("alias");
				} else {
					$this->vars["error"] = array($result["response_text"]);
				}	
			}
		} catch(Exception $e) {
			$this->vars["error"][] = "Communication error, please contact the administrator.";
		}
	
		return array(
			'templatefile' => $type,
			'vars' => $this->vars
		);
	}
	
	public function addWorkgroup($params) {
		$openSRS = new openSRS_mail($params["configoption1"], $params["configoption2"], $params["configoption3"], $params["configoption4"], $params["configoption5"]);
		
		$this->vars["workgroup"] = array(
			"workgroup" => $this->getVar("workgroup")
		);
	
		return array(
			'templatefile' => 'workgroup',
			'vars' => $this->vars
		);
	}
	
	public function saveMailbox($params) {
		$openSRS = new openSRS_mail($params["configoption1"], $params["configoption2"], $params["configoption3"], $params["configoption4"], $params["configoption5"]);

		$success = false;
		
		if($this->getVar("new")) {
			$mailboxName = $this->getVar("mailbox");
			
			$result = $openSRS->getMailbox($params["domain"], $mailboxName);
			
			if($result["is_success"]) {
				
				$this->vars["error"][] = "The mailbox cannot be added because it already exists.";
				
				return $this->addEditMailbox($params);
			} else if($result["response_code"] == 17) {
				
				$password = $this->getVar("password");
				$passwordConfirm = $this->getVar("passwordConfirm");
				$mailbox = $this->getVar("mailbox");
				
				if(empty($password)) $this->vars["error"][] = "The password cannot be empty";
				if($password != $passwordConfirm) $this->vars["error"][] = "The passwords do not match";
				if(empty($mailbox)) $this->vars["error"][] = "The mailbox name cannot be empty";
				
				if(empty($this->vars["error"])) {
					$result = $openSRS->createMailbox(
						$params["domain"],
						$mailboxName,
						$this->getVar("workgroup"),
						$password,
						$this->getVar("firstName"),
						$this->getVar("lastName"),
						$this->getVar("title"),
						$this->getVar("phone"),
						$this->getVar("fax")
					);
					
					if($result["is_success"]) {
						$success = "&added=true";
					} else {
						$this->vars["error"][] = $result["response_text"];
					}
				}
			} else {
				$this->vars["error"][] = $result["response_text"];
			}
		} else {
			$mailboxName = $this->getVar("mailbox");
			
			$result = $openSRS->getMailbox($params["domain"], $mailboxName);

			if($result["is_success"]) {
				
				$password = $this->getVar("password");
				$passwordConfirm = $this->getVar("passwordConfirm");
				$mailbox = $this->getVar("mailbox");
				
				if(!empty($password) && $password != $passwordConfirm) $this->vars["error"][] = "The passwords do not match";
				
				if(empty($this->vars["error"])) {
					$result = $openSRS->changeMailbox(
						$params["domain"],
						$mailboxName,
						$this->getVar("workgroup"),
						$password,
						$this->getVar("firstName"),
						$this->getVar("lastName"),
						$this->getVar("title"),
						$this->getVar("phone"),
						$this->getVar("fax")
					);
					
					if($result["is_success"]) {
						$success = "&edited=true";
					} else {
						$this->vars["error"][] = $result["response_text"];
					}
				}
			} else {
				$this->vars["error"][] = $result["response_text"];
			}
		}
		
		if($success) header("Location: /clientarea.php?action=productdetails&id=".$params["serviceid"].$success);
		else return $this->addEditMailbox($params);
	}
	
	public function saveForward($params) {
		$openSRS = new openSRS_mail($params["configoption1"], $params["configoption2"], $params["configoption3"], $params["configoption4"], $params["configoption5"]);

		$success = false;
		
		if($this->getVar("new")) {
			$mailboxName = $this->getVar("mailbox");
			
			$result = $openSRS->getMailbox($params["domain"], $mailboxName);
			
			if($result["is_success"]) {
				
				$this->vars["error"][] = "The mailbox cannot be added because it already exists.";
				
				return $this->addEditMailbox($params);
			} else if($result["response_code"] == 17) {
				
				$forwards = $this->getVar("forwardEmail");
				
				if(empty($forwards)) $this->vars["error"][] = "You must provide addresses to forward to.";
				else {
					$forwardAddresses = explode(",", $forwards);
					
					foreach($forwardAddresses as $forwardAddress) {
						if(!filter_var($forwardAddress, FILTER_VALIDATE_EMAIL)) {
							$this->vars["error"][] = "The address ".$forwardAddress." is not valid.";
						}
					}
				}
				
				if(empty($this->vars["error"])) {
					$result = $openSRS->createMailboxForwardOnly(
						$params["domain"],
						$mailboxName,
						$this->getVar("workgroup"),
						$forwards
					);
					
					if($result["is_success"]) {
						$success = "&added=true";
					} else {
						$this->vars["error"][] = $result["response_text"];
					}
				}
			} else {
				$this->vars["error"][] = $result["response_text"];
			}
		} else {
			$mailboxName = $this->getVar("mailbox");
			
			$result = $openSRS->getMailbox($params["domain"], $mailboxName);

			if($result["is_success"]) {
				
				$forwards = $this->getVar("forwardEmail");
				
				if(empty($forwards)) $this->vars["error"][] = "You must provide addresses to forward to.";
				else {
					$forwardAddresses = explode(",", $forwards);
					
					foreach($forwardAddresses as $forwardAddress) {
						if(!filter_var($forwardAddress, FILTER_VALIDATE_EMAIL)) {
							$this->vars["error"][] = "The address ".$forwardAddress." is not valid.";
						}
					}
				}
				
				if(empty($this->vars["error"])) {
					$result = $openSRS->changeMailboxForwardOnly(
						$params["domain"],
						$mailboxName,
						$forwards
					);
					
					if($result["is_success"]) {
						$success = "&edited=true";
					} else {
						$this->vars["error"][] = $result["response_text"];
					}
				}
			} else {
				$this->vars["error"][] = $result["response_text"];
			}
		}
		
		if($success) header("Location: /clientarea.php?action=productdetails&id=".$params["serviceid"].$success);
		else return $this->addEditMailbox($params);
	}
	
	public function saveAlias($params) {
		$openSRS = new openSRS_mail($params["configoption1"], $params["configoption2"], $params["configoption3"], $params["configoption4"], $params["configoption5"]);

		$success = false;
		
		$alias = $this->getVar("alias");
		
		if(empty($alias)) {
			$this->vars["error"][] = "The name cannot be empty.";
		} else {
			$result = $openSRS->createAliasMailbox($params["domain"], $alias, $this->getVar("mailbox"));
	
			if($result["is_success"]) {
				
				$success = true;
			} else {
				$this->vars["error"][] = $result["response_text"];
			}
		}
		
		if($success) header("Location: /clientarea.php?action=productdetails&id=".$params["serviceid"]."&added=true");
		else return $this->addEditMailbox($params);
	}
	
	public function deleteMailbox($params) {
		$openSRS = new openSRS_mail($params["configoption1"], $params["configoption2"], $params["configoption3"], $params["configoption4"], $params["configoption5"]);

		try {
			$mailbox = $_POST["mailbox"];

			$result = $openSRS->deleteMailboxAny($params["domain"], $mailbox);
		
			if($result["is_success"]) {
				$this->vars["deleteSuccess"] = true;
			} else {
				$this->vars["error"][] = $result["response_text"];
			}
			
		} catch(Exception $e) {
			$this->vars["error"][] = "Communication error, please contact the administrator.";
		}

		return $this->listMailboxes($params);
	}
	
	public function saveWorkgroup($params) {
		$openSRS = new openSRS_mail($params["configoption1"], $params["configoption2"], $params["configoption3"], $params["configoption4"], $params["configoption5"]);

		$success = false;
		
		$workgroup = $this->getVar("workgroup");
		
		if(empty($workgroup)) {
			$this->vars["error"][] = "The name cannot be empty.";
		} else {
			$result = $openSRS->createWorkgroup($params["domain"], $workgroup);
	
			if($result["is_success"]) {
				
				$success = true;
			} else {
				$this->vars["error"][] = $result["response_text"];
			}
		}
		
		if($success) header("Location: /clientarea.php?action=productdetails&id=".$params["serviceid"]."&modaction=workgroups&added=true");
		else return $this->addWorkgroup($params);
	}
	
	public function deleteWorkgroup($params) {
		$openSRS = new openSRS_mail($params["configoption1"], $params["configoption2"], $params["configoption3"], $params["configoption4"], $params["configoption5"]);

		try {
			$workgroup = $_POST["workgroup"];

			$result = $openSRS->deleteWorkgroup($params["domain"], $workgroup);
		
			if($result["is_success"]) {
				$this->vars["deleteSuccess"] = true;
			} else {
				$this->vars["error"][] = $result["response_text"];
			}
			
		} catch(Exception $e) {
			$this->vars["error"][] = "Communication error, please contact the administrator.";
		}

		return $this->listWorkgroups($params);
	}
	
	private function getVar($name) {
		return isset($_POST[$name]) ? $_POST[$name] : "";
	}

	public function __construct($params) {
		$this->vars = $params;
		if(file_exists(dirname(__FILE__)."/style.css")) {
			$this->vars["css"] = file_get_contents(dirname(__FILE__)."/style.css");
		} else $this->vars["css"] = "";
	}
}
?>