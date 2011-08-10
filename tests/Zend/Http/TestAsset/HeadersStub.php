<?php

namespace ZendTest\Http\TestAsset;

use Zend\Http\Headers;

class HeadersStub extends Headers
{
    protected static $headerClasses = array(
        'fake' => 'ZendTest\Http\TestAsset\FakeHeader',
        'multifake' => 'ZendTest\Http\TestAsset\MultiFakeHeader',
    );
    
}
