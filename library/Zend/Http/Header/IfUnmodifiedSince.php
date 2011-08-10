<?php

namespace Zend\Http\Header;

class IfUnmodifiedSince extends GenericHeader
{
    
    public function getName()
    {
        return 'If-Unmodified-Since';
    }
    
}
