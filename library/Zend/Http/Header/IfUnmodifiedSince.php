<?php

namespace Zend\Http\Header;

class IfUnmodifiedSince extends Header
{
    
    public function getName()
    {
        return 'If-Unmodified-Since';
    }
    
}
