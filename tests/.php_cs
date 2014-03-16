<?php
$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->files()
    ->notPath('Code/Reflection/FunctionReflectionTest.php')
    ->notPath('Code/Reflection/MethodReflectionTest.php')
    ->notPath('Code/Reflection/TestAsset/closures.php')
    ->notPath('Code/Reflection/TestAsset/functions.php')
    ->notPath('Code/Reflection/TestAsset/TestSampleClass10.php')
    ->notPath('Code/Reflection/TestAsset/TestSampleClass11.php')
    ->notPath('Code/TestAsset')
    ->notPath('Validator/_files')
    ->notPath('Loader/_files')
    ->notPath('Loader/TestAsset')
    ->in(__DIR__ . '/ZendTest');

return Symfony\CS\Config\Config::create()
    ->finder($finder);
