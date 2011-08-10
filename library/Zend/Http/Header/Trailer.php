<?php

namespace Zend\Http\Header;

class Trailer extends GenericHeader
{

    public function getName()
    {
        return 'Trailer';
    }
    
}
