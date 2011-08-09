<?php

namespace Zend\Http\Header;

class KeepAlive extends Header
{
    
    public function getName()
    {
        return 'Keep-Alive';
    }
    
}
