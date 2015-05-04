<?php
namespace ZendTest\Code\Reflection\TestAsset;

use ZendTest\Code\Reflection\TestAsset\TestTraitClass3 as TestTrait;

//issue #7428
class TestTraitClass4
{
    use TestTrait;
}
