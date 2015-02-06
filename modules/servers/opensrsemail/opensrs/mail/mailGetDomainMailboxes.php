<?php
/*
 *  Required object values:
 *  data - 
 */
 
class openSRS_mailGetDomainMailboxes extends openSRS_mail {

	public function getAll($domain) {
		$compile = " domain=\"".$domain."\"";
		
		return $this->_processRequest ($compile);
	}

	public function __destruct () {
		parent::__destruct();
	}

	// Post validation functions
	private function _processRequest ($command = ""){
		$sequence = array (
			0 => "ver ver=\"3.4\"",
			1 => "login user=\"". $this->username ."\" domain=\"". $this->domain ."\" password=\"". $this->password ."\"",
			2 => "get_domain_mailboxes". $command,
			3 => "quit"
		);		
		$tucRes = $this->makeCall($sequence);
		return $this->parseResults($tucRes);
	}
}