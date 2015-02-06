<?php
/*
 *  Required object values:
 *  data - 
 */
 
class openSRS_mailSetDomainDisabledStatus extends openSRS_mail {

	public function disable($domain) {
		$compile = " domain=\"".$domain."\"";
		$compile .= " disabled=\"T\"";
		
		return $this->_processRequest ($compile);
	}
	
	public function enable($domain) {
		$compile = " domain=\"".$domain."\"";
		$compile .= " disabled=\"F\"";
		
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
			2 => "set_domain_disabled_status". $command,
			3 => "quit"
		);		
		$tucRes = $this->makeCall($sequence);
		return $this->parseResults($tucRes);
	}
}