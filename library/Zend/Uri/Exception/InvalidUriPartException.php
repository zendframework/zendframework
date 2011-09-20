<?php

namespace Zend\Uri\Exception;

class InvalidUriPartException 
    extends \InvalidArgumentException
    implements \Zend\Uri\Exception
{
    /**
     * Part-specific error codes
     * 
     * @var integer
     */
    const INVALID_SCHEME    = 1;
    const INVALID_USER      = 2;
    const INVALID_PASSWORD  = 4;
    const INVALID_USERINFO  = 6;
    const INVALID_HOSTNAME  = 8;
    const INVALID_PORT      = 16;
    const INVALID_AUTHORITY = 30;
    const INVALID_PATH      = 32;
    const INVALID_QUERY     = 64;
    const INVALID_FRAGMENT  = 128;  
}