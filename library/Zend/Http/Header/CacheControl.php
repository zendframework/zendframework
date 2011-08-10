<?php

namespace Zend\Http\Header;

class CacheControl extends GenericHeader
{
    
    public function getName()
    {
        return 'Cache-Control';
    }

}