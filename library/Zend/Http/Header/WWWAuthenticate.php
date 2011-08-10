<?php

namespace Zend\Http\Header;

class WWWAuthenticate extends GenericHeader
{

    public function getName()
    {
        return 'WWW-Authenticate';
    }
    
}
