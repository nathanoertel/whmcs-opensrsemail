<?php
class openSRS_mail {
	
	protected $username;
	
	protected $password;
	
	protected $domain;
	
	protected $cluster;
	
	protected $mode;

	public function createDomain($domain, $timezone = null, $language = null, $filtermx = null, $spamTag = null, $spamFolder = null, $spamLevel = null) {
		
		$compile = "";
		
		$compile .= " domain=\"".$domain."\"";
		
		if(!empty($timezone)) $compile .= " timezone=\"".$timezone."\"";
		if(!empty($language)) $compile .= " language=\"".$language."\"";
		if(!empty($filtermx)) $compile .= " filtermx=\"".$filtermx."\"";
		if(!empty($spamTag)) $compile .= " spam_tag=\"".$spamTag."\"";
		if(!empty($spamFolder)) $compile .= " spam_folder=\"".$spamFolder."\"";
		if(!empty($spamLevel)) $compile .= " spam_level=\"".$spamLevel."\"";
		
		return $this->_processRequest ("create_domain", $compile);
	}

	public function disableDomain($domain) {
		$compile = " domain=\"".$domain."\"";
		$compile .= " disabled=\"T\"";
		
		return $this->_processRequest ("set_domain_disabled_status", $compile);
	}
	
	public function enableDomain($domain) {
		$compile = " domain=\"".$domain."\"";
		$compile .= " disabled=\"F\"";
		
		return $this->_processRequest ("set_domain_disabled_status", $compile);
	}

	public function deleteDomain($domain) {
		$compile = " domain=\"".$domain."\"";
		$compile .= " cascade=\"T\"";
		
		return $this->_processRequest ("delete_domain", $compile);
	}

	public function getDomainMailboxes($domain) {
		$compile = " domain=\"".$domain."\"";
		
		return $this->_processRequest ("get_domain_mailboxes", $compile);
	}

	public function getNumDomainMailboxes($domain) {
		$compile = " domain=\"".$domain."\"";
		
		return $this->_processRequest ("get_num_domain_mailboxes", $compile);
	}

	public function getDomainWorkgroups($domain) {
		$compile = " domain=\"".$domain."\"";
		
		return $this->_processRequest ("get_domain_workgroups", $compile);
	}
	
	public function getMailbox($domain, $mailbox) {
		$compile = " domain=\"".$domain."\"";
		$compile .= " mailbox=\"".$mailbox."\"";
		
		return $this->_processRequest("get_mailbox", $compile);
	}
	
	public function createMailbox($domain, $mailbox, $workgroup, $password, $firstName, $lastName, $title, $phone, $fax) {
		$compile = " domain=\"".$domain."\"";
		$compile .= " mailbox=\"".$mailbox."\"";
		$compile .= " workgroup=\"".$workgroup."\"";
		$compile .= " password=\"".$password."\"";
		$compile .= " first_name=\"".$firstName."\"";
		$compile .= " last_name=\"".$lastName."\"";
		$compile .= " title=\"".$title."\"";
		$compile .= " phone=\"".$phone."\"";
		$compile .= " fax=\"".$fax."\"";
		
		return $this->_processRequest("create_mailbox", $compile);
	}
	
	public function changeMailbox($domain, $mailbox, $workgroup, $password, $firstName, $lastName, $title, $phone, $fax) {
		$compile = " domain=\"".$domain."\"";
		$compile .= " mailbox=\"".$mailbox."\"";
		$compile .= " workgroup=\"".$workgroup."\"";
		if(!empty($password)) $compile .= " password=\"".$password."\"";
		$compile .= " first_name=\"".$firstName."\"";
		$compile .= " last_name=\"".$lastName."\"";
		$compile .= " title=\"".$title."\"";
		$compile .= " phone=\"".$phone."\"";
		$compile .= " fax=\"".$fax."\"";
		
		return $this->_processRequest("change_mailbox", $compile);
	}
	
	public function getMailboxForwardOnly($domain, $mailbox) {
		$compile = " domain=\"".$domain."\"";
		$compile .= " mailbox=\"".$mailbox."\"";
		
		return $this->_processRequest("get_mailbox_forward_only", $compile);
	}
	
	public function createMailboxForwardOnly($domain, $mailbox, $workgroup, $forwardEmails) {
		$compile = " domain=\"".$domain."\"";
		$compile .= " mailbox=\"".$mailbox."\"";
		$compile .= " workgroup=\"".$workgroup."\"";
		$compile .= " forward_email=\"".$forwardEmails."\"";
		
		return $this->_processRequest("create_mailbox_forward_only", $compile);
	}
	
	public function changeMailboxForwardOnly($domain, $mailbox, $forwardEmails) {
		$compile = " domain=\"".$domain."\"";
		$compile .= " mailbox=\"".$mailbox."\"";
		$compile .= " forward_email=\"".$forwardEmails."\"";
		
		return $this->_processRequest("change_mailbox_forward_only", $compile);
	}
	
	public function createAliasMailbox($domain, $alias, $mailbox) {
		$compile = " domain=\"".$domain."\"";
		$compile .= " mailbox=\"".$mailbox."\"";
		$compile .= " alias_mailbox=\"".$alias."\"";
		
		return $this->_processRequest("create_alias_mailbox", $compile);
	}
	
	public function deleteMailboxAny($domain, $mailbox) {
		$compile = " domain=\"".$domain."\"";
		$compile .= " mailbox=\"".$mailbox."\"";
		
		return $this->_processRequest("delete_mailbox_any", $compile);
	}
	
	public function createWorkgroup($domain, $workgroup) {
		$compile = " domain=\"".$domain."\"";
		$compile .= " workgroup=\"".$workgroup."\"";
		
		return $this->_processRequest("create_workgroup", $compile);
	}
	
	public function deleteWorkgroup($domain, $workgroup) {
		$compile = " domain=\"".$domain."\"";
		$compile .= " workgroup=\"".$workgroup."\"";
		
		return $this->_processRequest("delete_workgroup", $compile);
	}

	// Post validation functions
	private function _processRequest($method, $command = ""){

		$sequence = array (
			0 => "ver ver=\"3.5\"",
			1 => "login user=\"". $this->username ."\" domain=\"". $this->domain ."\" password=\"". $this->password ."\"",
			2 => $method. $command,
			3 => "quit"
		);		

		$tucRes = $this->makeCall($sequence);
		return $this->parseResults35($tucRes);
	}
	
	// Class constructor
	public function __construct ($username, $password, $domain, $cluster, $mode) {
		$this->username = $username;
		$this->password = $password;
		$this->domain = $domain;
		$this->cluster = strtolower($cluster);
		$this->mode = $mode;
	}

	// Class destructor
	public function __destruct () {
	}

	// Class functions
	protected function makeCall ($sequence){
		$result = '';
		// Open the socket
		$fp = pfsockopen ("ssl://admin.".$this->cluster.".hostedemail.com", "4449", $errno, $errstr, "10");

		if (!$fp) {
			throw new Exception("Error connecting to OpenSRS");			// Something went wrong
			error_log("OpenSRS Email - Socket Open Failed: ".$errno." - ".$errstr);
		} else {
			// Send commands to APP server
			for ($i=0; $i<count($sequence); $i++){
				$servCatch = "";
			
				// Write the port
				$writeStr = $sequence[$i] ."\r\n";
				$fwrite = fwrite($fp, $writeStr);
				if (!$fwrite) {
					error_log("OpenSRS Email - Failed writing to socket");
					throw new Exception("Error connecting to OpenSRS");			// Something went wrong
				}

				$dotStr = ".\r\n";
				$fwrite = fwrite($fp, $dotStr);
				if (!$fwrite) {
					error_log("OpenSRS Email - Failed writing to socket");
					throw new Exception("Error connecting to OpenSRS");			// Something went wrong
				}
								
							// read the port rightaway
				// Last line of command has be done with different type of reading
				if ($i == (count($sequence)-1) ){
					// Loop until End of transmission
					while (!feof($fp)) {
						$servCatch .= fgets($fp, 128);
					}
				} else {
					// Plain buffer read with big data packet
					$servCatch .= fread($fp, 8192);
				}
				
				// Possible parsing and additional validation will be here
				// If error accours in the communication than the script should quit rightaway
				// $servCatch
				
				$result .= $servCatch;
			}
		}

		//Close the socket
		fclose($fp);
		return $result;
	}

	protected function parseResults34 ($resString) {
		// Raw tucows result
		$resArray = explode (".\r\n",$resString);
		$resRinse = array ();
		for ($i=0; $i<count($resArray); $i++){							// Clean up \n, \r and empty fields
			$resArray[$i] = str_replace("\r", "", $resArray[$i]);
			$resArray[$i] = str_replace("\n", " ", $resArray[$i]);		// replace new line with space
			$resArray[$i] = str_replace("  ", " ", $resArray[$i]);		// no double space - for further parsing
			$resArray[$i] = substr($resArray[$i], 0, -1);				// take out the last space
			if ($resArray[$i] != "") array_push($resRinse, $resArray[$i]);
		}
    $result=Array(
			"is_success" => "1",
			"response_code" => "200",
			"response_text" => "Command completed successfully"
		);
		$i=1;
		// Takes the rinsed result lines and forms it into an Associative array
		foreach($resRinse as $resultLine){
			$okPattern='/^OK 0/';
			$arrayPattern = '/ ([\w\-\.\@]+)\=\"([\w\-\.\@\*\, ]*)\"/';
			$errorPattern = '/^ER ([0-9]+) (.+)$/';

			// Checks to see if this line is an information line
			$okLine = preg_match($okPattern, $resultLine, $matches);

	                if ($okLine == 0){
				// If it's not an ok line, it's an error
				$err_num_match=0;
	                        $err_num_match = preg_match($errorPattern,$resultLine,$err_match);

				// Makes sure the error pattern matched and that there isn't an error that has already happened
				if ($err_num_match==1 && $result['is_success']=="1"){
					$result['response_text']=$err_match[2];
					$result['response_code']=$err_match[1];
					$result['is_success']='0';
				}

			} else {
				// If it's an OK line check to see if it's an Array of values
				$arrayMatch=preg_match_all($arrayPattern, $resultLine, $arrayMatches);
				if ($arrayMatch !=0){
					for($j=0;$j<$arrayMatch;$j++){
						if($arrayMatches[1][$j]=="LIST")
							$result['attributes'][strtolower($arrayMatches[1][$j])]=explode("," , $arrayMatches[2][$j]);
						else
							$result['attributes'][strtolower($arrayMatches[1][$j])]=$arrayMatches[2][$j];
					}
				} else {

					// If it's not an array line or an error it could be a table
					$tableLines=explode(' , ', $resultLine);
					if (count($tableLines)>1){
						$tableLines[0] = str_replace("OK 0 ", "", $tableLines[0]);
						$tableHeaders=explode(' ',$tableLines[0]);
						$result['attributes']['list']=Array();
						for($j=1;$j<count($tableLines);$j++){
							$values=explode('" "', $tableLines[$j]);
							$k = 0;
							foreach($tableHeaders as $tableHeader){
								$result['attributes']['list'][$j-1][strtolower($tableHeader)]=str_replace('"', '', $values[$k]);
								$k++;
							}
						}

					}
				}
			}
			$i++;
		}

		return $result;
	}

	protected function parseResults35 ($resString) {
		// Raw tucows result
		$resArray = explode (".\r\n",$resString);
		$resRinse = array ();
		for ($i=0; $i<count($resArray); $i++){							// Clean up \n, \r and empty fields
			$resArray[$i] = str_replace("\r", "", $resArray[$i]);
			$resArray[$i] = str_replace("\n", " ", $resArray[$i]);		// replace new line with space
			$resArray[$i] = str_replace("  ", " ", $resArray[$i]);		// no double space - for further parsing
			$resArray[$i] = substr($resArray[$i], 0, -1);				// take out the last space
			if ($resArray[$i] != "") array_push($resRinse, $resArray[$i]);
		}
    $result=Array(
			"is_success" => "1",
			"response_code" => "200",
			"response_text" => "Command completed successfully"
		);
		$i=1;
		// Takes the rinsed result lines and forms it into an Associative array
		foreach($resRinse as $resultLine){
			$okPattern='/^OK 0/';
			$arrayPattern = '/ ([\w\-\.\@]+)\=\"([\w\-\.\@\*\, ]*)\"/';
			$errorPattern = '/^ER ([0-9]+) (.+)$/';

			// Checks to see if this line is an information line
			$okLine = preg_match($okPattern, $resultLine, $matches);

	                if ($okLine == 0){
				// If it's not an ok line, it's an error
				$err_num_match=0;
	                        $err_num_match = preg_match($errorPattern,$resultLine,$err_match);

				// Makes sure the error pattern matched and that there isn't an error that has already happened
				if ($err_num_match==1 && $result['is_success']=="1"){
					$result['response_text']=$err_match[2];
					$result['response_code']=$err_match[1];
					$result['is_success']='0';
				}

			} else {
				// If it's an OK line check to see if it's an Array of values
				$arrayMatch=preg_match_all($arrayPattern, $resultLine, $arrayMatches);
				if ($arrayMatch !=0){
					for($j=0;$j<$arrayMatch;$j++){
						if($arrayMatches[1][$j]=="LIST")
							$result['attributes'][strtolower($arrayMatches[1][$j])]=explode("," , $arrayMatches[2][$j]);
						else
							$result['attributes'][strtolower($arrayMatches[1][$j])]=$arrayMatches[2][$j];
					}
				} else {
					if(strpos($resultLine, "OK 0 Success ") === 0) {
						// If it's not an array line or an error it could be a table
						$tableLines=explode(' ', str_replace("OK 0 Success ", "", $resultLine));
						if (count($tableLines)>1){
							$tableHeaders=explode(',',$tableLines[0]);
							$result['attributes']['list']=Array();
							for($j=1;$j<count($tableLines);$j++){
								$values=explode('","', $tableLines[$j]);
								$k = 0;
								foreach($tableHeaders as $tableHeader){
									$result['attributes']['list'][$j-1][strtolower($tableHeader)]=str_replace('"', '', $values[$k]);
									$k++;
								}
							}
	
						}
					}
				}
			}
			$i++;
		}

		return $result;
	}
}
