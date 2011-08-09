<?php

namespace Zend\Http\Header;

class ProxyAuthorization extends Header
{

    public function getName()
    {
        return 'Proxy-Authorization';
    }

}
