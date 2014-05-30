<?php
namespace ZendTest\Code\TestAsset;

require_once(__DIR__ . '/TraitWithSameMethods.php');
require_once(__DIR__ . '/BarTrait.php');

use ZendTest\Code\TestAsset\FooTrait;
use ZendTest\Code\TestAsset\BarTrait;
use ZendTest\Code\TestAsset\TraitWithSameMethods;

class TestClassWithTraitAliases
{
    use BarTrait, FooTrait, TraitWithSameMethods {
        FooTrait::foo insteadof TraitWithSameMethods;
        TraitWithSameMethods::bar insteadof BarTrait;
        TraitWithSameMethods::bar insteadof FooTrait;
        TraitWithSameMethods::foo as private test;
    }

    public function bazFooBar()
    {
        
    }
}

