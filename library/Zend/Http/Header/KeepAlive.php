<?php

namespace Zend\Http\Header;

class KeepAlive extends GenericHeader
{
    
    public function getName()
    {
        return 'Keep-Alive';
    }
    
}
