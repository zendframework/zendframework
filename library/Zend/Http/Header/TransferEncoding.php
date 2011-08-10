<?php

namespace Zend\Http\Header;

class TransferEncoding extends GenericHeader
{

    public function getName()
    {
        return 'Transfer-Encoding';
    }
    
}
