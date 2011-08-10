<?php

namespace Zend\Http\Header;

class Authorization extends GenericHeader
{
    
    public function getName()
    {
        return 'Authorization';
    }
    
}
