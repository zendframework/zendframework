<?php

namespace Zend\Http\Header;

class SetCookie extends Header
{

    public function getName()
    {
        return 'Set-Cookie';
    }
    
}
