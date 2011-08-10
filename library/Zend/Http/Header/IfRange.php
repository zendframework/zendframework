<?php

namespace Zend\Http\Header;

class IfRange extends GenericHeader
{

    public function getName()
    {
        return 'If-Range';
    }
  
}
