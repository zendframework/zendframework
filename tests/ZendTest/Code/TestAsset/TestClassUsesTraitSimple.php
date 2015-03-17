<?php
namespace ZendTest\Code\TestAsset;

use ZendTest\Code\TestAsset\FooTrait;

class TestClassUsesTraitSimple
{
    use \ZendTest\Code\TestAsset\BarTrait, FooTrait;
}

