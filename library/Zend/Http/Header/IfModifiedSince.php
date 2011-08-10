<?php

namespace Zend\Http\Header;

class IfModifiedSince extends GenericHeader
{
    
    public function getName()
    {
        return 'If-Modified-Since';
    }
    
}
