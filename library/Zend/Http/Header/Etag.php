<?php

namespace Zend\Http\Header;

class Etag extends GenericHeader
{
    
    public function getName()
    {
        return 'ETag';
    }
    
}
