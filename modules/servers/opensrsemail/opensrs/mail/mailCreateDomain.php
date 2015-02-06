<?php
/*
 *  Required object values:
 *  data - 
 */
 
class openSRS_mailCreateDomain extends openSRS_mail {

	public function createDomain($domain, $timezone = null, $language = null, $filtermx = null, $spamTag = null, $spamFolder = null, $spamLevel = null) {
		
		$compile = "";
		
		$compile .= " domain=\"".$domain."\"";
		
		if(!empty($timezone)) $compile .= " timezone=\"".$timezone."\"";
		if(!empty($language)) $compile .= " language=\"".$language."\"";
		if(!empty($filtermx)) $compile .= " filtermx=\"".$filtermx."\"";
		if(!empty($spamTag)) $compile .= " spam_tag=\"".$spamTag."\"";
		if(!empty($spamFolder)) $compile .= " spam_folder=\"".$spamFolder."\"";
		if(!empty($spamLevel)) $compile .= " spam_level=\"".$spamLevel."\"";
		
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
			2 => "create_domain". $command,
			3 => "quit"
		);		
		$tucRes = $this->makeCall($sequence);
		return $this->parseResults($tucRes);
	}
}
