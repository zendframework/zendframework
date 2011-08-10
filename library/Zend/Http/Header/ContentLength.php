<?php

namespace Zend\Http\Header;

class ContentLength extends GenericHeader
{
    
    public function getName()
    {
        return 'Content-Length';
    }
    
}
