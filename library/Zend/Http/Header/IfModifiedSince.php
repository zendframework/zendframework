<?php

namespace Zend\Http\Header;

class IfModifiedSince extends Header
{
    
    public function getName()
    {
        return 'If-Modified-Since';
    }
    
}
