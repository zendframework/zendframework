<?php
namespace ZendTest\Code\Scanner\TestAsset;

require_once(__DIR__ . '/TraitWithSameMethods.php');
require_once(__DIR__ . '/BarTrait.php');

use ZendTest\Code\TestAsset\FooTrait;
use ZendTest\Code\TestAsset\BarTrait;
use ZendTest\Code\TestAsset\TraitWithSameMethods;

/**
 * This class is used to test the ClassScanner as it should throw
 * a RuntimeException due to the fact that bar method exists on
 * multiple traits.
 */
class TestClassWithAliasException
{
    use BarTrait, FooTrait, TraitWithSameMethods {
        FooTrait::foo insteadof TraitWithSameMethods;
        TraitWithSameMethods::foo as private test;
    }

    public function bazFooBar()
    {

    }
}
