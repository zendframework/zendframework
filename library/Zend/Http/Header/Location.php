<?php

namespace Zend\Http\Header;

class Location extends GenericHeader
{
    
    public function getType()
    {
        return 'Location';
    }
    
}
