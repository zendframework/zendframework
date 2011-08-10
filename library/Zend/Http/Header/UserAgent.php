<?php

namespace Zend\Http\Header;

class UserAgent extends GenericHeader
{
    
    public function getName()
    {
        return 'User-Agent';
    }
    
}
