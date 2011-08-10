<?php

namespace Zend\Http\Header;

class IfNoneMatch extends GenericHeader
{
    
    public function getName()
    {
        return 'If-None-Match';
    }

}
