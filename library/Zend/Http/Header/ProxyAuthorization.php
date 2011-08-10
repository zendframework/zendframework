<?php

namespace Zend\Http\Header;

class ProxyAuthorization extends GenericHeader
{

    public function getName()
    {
        return 'Proxy-Authorization';
    }

}
