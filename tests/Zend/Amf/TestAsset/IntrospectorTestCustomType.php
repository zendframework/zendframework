<?php

namespace ZendTest\Amf\TestAsset;

class IntrospectorTestCustomType
{
    /**
     * @var string
     */
    public $foo;

    public $baz;

    /**
     * DocBlock without an annotation
     */
    public $bat;

    /**
     * @var bool
     */
    protected $_bar;
}
