<?php

namespace Zend\Http\Header;

class CacheControl extends Header
{
    
    public function getName()
    {
        return 'Cache-Control';
    }

}