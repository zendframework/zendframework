<?php

namespace ZendTest\Http\TestAsset;

use Zend\Http\Header\GenericMultiHeader;

class MultiFakeHeader extends GenericMultiHeader
{
    public function getName()
    {
        return 'Multi-Fake';
    }
}
