<?php

namespace Zend\Http\Header;

class ProxyAuthenticate extends GenericHeader
{
    
    public function getName()
    {
        return 'Proxy-Authenticate';
    }

}
