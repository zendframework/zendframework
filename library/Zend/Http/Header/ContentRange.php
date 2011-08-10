<?php

namespace Zend\Http\Header;

class ContentRange extends GenericHeader
{

    public function getName()
    {
        return 'Content-Range';
    }
    
}
