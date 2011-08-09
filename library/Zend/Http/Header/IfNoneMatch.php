<?php

namespace Zend\Http\Header;

class IfNoneMatch extends Header
{
    
    public function getName()
    {
        return 'If-None-Match';
    }

}
