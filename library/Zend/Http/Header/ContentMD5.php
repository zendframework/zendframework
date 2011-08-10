<?php

namespace Zend\Http\Header;

class ContentMD5 extends GenericHeader
{
    
    public function getName()
    {
        return 'Content-MD5';
    }

}
