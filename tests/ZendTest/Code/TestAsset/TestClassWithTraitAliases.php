<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

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
