<?php

namespace ZendTest\Application\Resource;

use Zend\Mail\AbstractTransport;

/**
 * @group ZF-9136
 */
class mailTestCAsE extends AbstractTransport 
{
    public function _sendMail() 
    {
		// We dont want to do anything here, do we?
	}
}
