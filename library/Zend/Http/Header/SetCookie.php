<?php

namespace Zend\Http\Header;

class SetCookie extends GenericHeader
{

    public function getName()
    {
        return 'Set-Cookie';
    }
    
}
