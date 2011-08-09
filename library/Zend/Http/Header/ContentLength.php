<?php

namespace Zend\Http\Header;

class ContentLength extends Header
{
    
    public function getName()
    {
        return 'Content-Length';
    }
    
}
