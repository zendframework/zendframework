<?php

namespace ZendTest\AMF\TestAsset;

class IntrospectorTestCustomType
{
    /**
     * @var string
     */
    public $foo;

    public $baz;

    /**
     * Docblock without an annotation
     */
    public $bat;

    /**
     * @var bool
     */
    protected $_bar;
}
