<?php

namespace Zend\Http\Header;

class AuthenticationInfo extends GenericHeader
{

    public function getName()
    {
        return 'Authentication-Info';
    }
    
}
