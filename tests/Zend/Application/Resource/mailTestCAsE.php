<?php

/**
 * @group ZF-9136
 */
class Zend_Application_Resource_mailTestCAsE extends Zend_Mail_Transport_Abstract {
	public function _sendMail() {
		// We dont want to do anything here, do we?
	}
	
}
