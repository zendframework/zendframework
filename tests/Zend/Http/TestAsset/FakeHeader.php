<?php

namespace ZendTest\Http\TestAsset;

use Zend\Http\Header\GenericHeader;

class FakeHeader extends GenericHeader
{
    public function getName()
    {
        return 'Fake';
    }
}
