<?php

namespace Zend\Http\Header;

class ContentRange extends Header
{

    public function getName()
    {
        return 'Content-Range';
    }
    
}
