<?php

namespace Zend\Http\Header;

class ProxyAuthenticate extends Header
{
    
    public function getName()
    {
        return 'Proxy-Authenticate';
    }

}
