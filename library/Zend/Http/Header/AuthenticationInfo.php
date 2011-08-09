<?php

namespace Zend\Http\Header;

class AuthenticationInfo extends Header
{

    public function getName()
    {
        return 'Authentication-Info';
    }
    
}
