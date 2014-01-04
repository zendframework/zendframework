<?php
$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->files()
    ->notPath('ZendTest/Code/Reflection/FunctionReflectionTest.php')
    ->notPath('ZendTest/Code/Reflection/MethodReflectionTest.php')
    ->notPath('ZendTest/Code/Reflection/TestAsset/closures.php')
    ->notPath('ZendTest/Code/Reflection/TestAsset/functions.php')
    ->notPath('ZendTest/Code/Reflection/TestAsset/TestSampleClass10.php')
    ->notPath('ZendTest/Code/Reflection/TestAsset/TestSampleClass11.php')
    ->notPath('ZendTest/Code/TestAsset')
    ->notPath('ZendTest/Validator/_files')
    ->notPath('ZendTest/Loader/_files')
    ->notPath('ZendTest/Loader/TestAsset')
    ->in(__DIR__);

return Symfony\CS\Config\Config::create()
    ->finder($finder);
