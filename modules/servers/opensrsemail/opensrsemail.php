<?php
if (!class_exists('openSRS_mail')) {
    require_once dirname(__FILE__).DIRECTORY_SEPARATOR."opensrs".DIRECTORY_SEPARATOR."openSRS_mail.php";
}

/**
 * Setup the config options
 * @return array the configurations for the module
 */
function opensrsemail_ConfigOptions() {
	$config = array(
		"Username"	=> array("Type" => "text", "Size" => "20", "Description" => "Enter your company admin username here, exclude the @domain portion",),
		"Password"	=> array("Type" => "password", "Size" => "32", "Description" => "Enter your company admin password here",),
		"Domain"	=> array("Type" => "text", "Size" => "32", "Description" => "Enter your company admin domain here",),
		"Cluster"	=> array("Type" => "dropdown", "Options" => "A,B", "Description" => "Select the cluster associated with your account",),
		"Mode"		=> array("Type" => "dropdown", "Options" => "Test,Live", "Description" => "Select the operating mode",),
	);
		
	return $config;
}

/**
 * Add the custom buttons, default to adding a mailbox to allow the mailbox custom function
 * @return array the custom functions
 */
function opensrsemail_ClientAreaCustomButtonArray() {
    $buttonarray = array(
	 "Add Mailbox" => "mailbox",
	);
	return $buttonarray;
}

/**
 * Create the domain
 * @param array $params the parameters for the request
 * @return string the status of the domain creation
 */
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

/**
 * Suspend the domain
 * @param array $params the parameters for the request
 * @return string the status of the suspension
 */
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

/**
 * Unsuspend the domain
 * @param array $params the parameters for the request
 * @return string the status of the unsuspension
 */
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

/**
 * Delete the domain
 * @param array $params the parameters for the request
 * @return string the status of the termination
 */
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

/**
 * Display the client area
 * @param array $params the parameters for the request
 * @return array the output parameters to be displayed on the page
 */
function opensrsemail_ClientArea($params) {
	if(!isset($_GET["modop"]) && !$_GET["modop"] == "custom") {
		// standard request without any custom operation being called, load basic pages
		$controller = new opensrsemail_Controller($params);
		
		if(isset($_POST["modaction"])) {
			// process the posted action
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

/**
 * Custom function for handling all mailbox actions
 * @param array $params the parameters for the request
 * @return array the output parameters to be displayed on the page
 */
function opensrsemail_mailbox($params) {
	$controller = new opensrsemail_Controller($params);

	if(!isset($_POST["modaction"])) {
		if(isset($_GET["modaction"]) && $_GET["modaction"] == "workgroup") {
			return $controller->addWorkgroup($params);
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

/**
 * Basic controller class to delegate all processing of requests and setup the required arrays for display or redirections
 * @property array $vars the variables to be included in the template output
 */
class opensrsemail_Controller {
	
	/**
	 * The OpenSRS mail class for completing operations
	 * @var openSRS_mail $openSRS
	 */
	private $openSRS;
	
	/**
	 * The variables to be displayed in the output
	 * @var array $vars
	 */
	private $vars;
	
	/**
	 * The languages for the current request
	 * @var array $language
	 */
	private $language;
	
	/**
	 * List the mailboxes for the active service
	 * @param array $params the parameters for the request
	 * @return array the template file and output for the mailbox list
	 */
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

		return $this->returnDisplay("mailboxes");
	}
	
	/**
	 * List the workgroups for the active service
	 * @param array $params the parameters for the request
	 * @return array the output vars and template for the list of workgroups
	 */
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

		return $this->returnDisplay("workgroups");
	}
	
	/**
	 * Add or edit a mailbox
	 * @param array $params the parameters for the request
	 * @return array the output vars and template for the adding or editing of mailboxes
	 */
	public function addEditMailbox($params) {
		$openSRS = new openSRS_mail($params["configoption1"], $params["configoption2"], $params["configoption3"], $params["configoption4"], $params["configoption5"]);
	
		$type = isset($_GET["type"]) ? $_GET["type"] : "mailbox";
		
		$this->vars["type"] = $type;
		
		try {
			$result = $openSRS->getDomainWorkgroups($params["domain"]);
			
			$this->vars["workgroups"] = isset($result["attributes"]["list"]) ? $result["attributes"]["list"] : array();

			if($type == "mailbox" ) {
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
	
					// remove any alias mailboxes since you can't alias to an alias
					foreach($mailboxes as $offset => $mailbox) {
						if($mailbox["type"] == "alias") unset($mailboxes[$offset]);
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
	
		return $this->returnDisplay($type);
	}
	
	/**
	 * Add a workgroup
	 * @param array $params the parameters for the request
	 * @return array the vars and template for the workgroup addition page
	 */
	public function addWorkgroup($params) {
		$openSRS = new openSRS_mail($params["configoption1"], $params["configoption2"], $params["configoption3"], $params["configoption4"], $params["configoption5"]);
		
		$this->vars["workgroup"] = array(
			"workgroup" => $this->getVar("workgroup")
		);
	
		return $this->returnDisplay("workgroup");
	}
	
	/**
	 * Save a mailbox and either redirect the client or display the add/edit with errors
	 * @param array $params the parameters for the request
	 * @return array the vars and template for the mailbox adding/editing when there are errors saving
	 */
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
	
	/**
	 * Save a forward and redirect the client on success or display the form with errors
	 * @param array $params the parameters for the request
	 * @return array the vars and template for the forward form when there are errors
	 */
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
	
	/**
	 * Save the alias and redirect the client or display errors
	 * @param array $params the parameters for the request
	 * @return array the vars and template for the alias form when there are errors present
	 */
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
	
	/**
	 * Delete the given mailbox and return the mailbox list
	 * @param array $params the parameters for the request
	 * @return array the mailbox list with the status of the deletion
	 */
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
	
	/**
	 * Save the workgroup and redirect the user or display the form with errors
	 * @param array $params the parameters for the request
	 * @return array the vars and template for the form
	 */
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
	
	/**
	 * Delete the given workgroup and display the list of workgroups
	 * @param array $params the parameters for the request
	 * @return array the vars and template for displaying the list of workgroups
	 */
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
	
	/**
	 * Return the appropriate display with the given template and all vars setup with the language correctly retrieved for the given template
	 * @param string $template the template to be used
	 * @return array the correctly formatted array with all parameters for the given template
	 */
	private function returnDisplay($template) {
		
		// include all language files for the current client
		foreach($this->language as $language) {
			include(dirname(__FILE__)."/lang/".$language.".php");
		}
		
		// if there are language entries for the current template, set them up
		if(isset($lang[$template])) {
			$language = $lang[$template];
			
			// look for any variables to place into the texts from the vars
			foreach($language as $offset => $text) {
				// use the same pattern as the template file for assigning variables
				preg_match_all('/{\$([^}]*)}/', $text, $matches, PREG_SET_ORDER);
				
				foreach($matches as $match) {
					if(isset($this->vars[$match[1]])) {
						$language[$offset] = str_replace($match[0], $this->vars[$match[1]], $text);
					}
				}
			}
			
			$this->vars["lang"] = $language;
		}

		return array(
			'templatefile' => $template,
			'vars' => $this->vars
		);
	}
	
	/**
	 * Get the post variable for the given name if set otherwise an empty string
	 * @param string $name the name of the post variable to get
	 * @return string the value of the post variable if set or an empty string
	 */
	private function getVar($name) {
		return isset($_POST[$name]) ? $_POST[$name] : "";
	}
	
	private function getLanguage($template) {
		$result = (string)$valueString;
		
		preg_match_all('/\${([^}]*)}/', $result, $matches, PREG_SET_ORDER);
		
		foreach($matches as $match) {
			$result = str_replace($match[0], self::getConfiguration($match[1]), $result);
		}
		
		return $result;
	}

	/**
	 * Construct the controller setting up the basic necessary functions
	 * @param array $params the parameters for the request
	 */
	public function __construct($params) {
		// initialize the opensrs client
		$this->openSRS = new openSRS_mail($params["configoption1"], $params["configoption2"], $params["configoption3"], $params["configoption4"], $params["configoption5"]);

		// setup the basic parameters for all requests
		$this->vars = $params;
		if(file_exists(dirname(__FILE__)."/style.css")) {
			$this->vars["css"] = file_get_contents(dirname(__FILE__)."/style.css");
		} else $this->vars["css"] = "";
		
		// determine the language, defaulting to english but using teh session language and then the client language
		$this->language = array();
		
		// load the default, english, then the client selected language, then the session language
		// starting with the default working down the the session selected one allows all necessary items to be defined
		// and then any overridden as you go through the other languages
		$this->language[] = "english";
		$this->language[] = $params["clientsdetails"]["language"];
		if(isset($_SESSION["Language"])) $this->language[] = $_SESSION["Language"];
		
		foreach($this->language as $offset => $language) {
			if(empty($language) || !file_exists(dirname(__FILE__)."/lang/".$language.".php")) unset($this->language[$offset]);
		}
	}
}
?>