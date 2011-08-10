<?php

namespace Zend\Http\Header;

class MaxForwards extends GenericHeader
{
    
    public function getName()
    {
        return 'Max-Forwards';
    }
    
}
