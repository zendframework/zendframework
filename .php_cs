<?php
$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->exclude('demos')
    ->exclude('resources')
    ->notPath('tests/ZendTest/Code/Reflection/FunctionReflectionTest.php')
    ->notPath('tests/ZendTest/Code/Reflection/MethodReflectionTest.php')
    ->notPath('tests/ZendTest/Code/Reflection/TestAsset/closures.php')
    ->notPath('tests/ZendTest/Code/Reflection/TestAsset/functions.php')
    ->notPath('tests/ZendTest/Code/Reflection/TestAsset/TestSampleClass10.php')
    ->notPath('tests/ZendTest/Code/Reflection/TestAsset/TestSampleClass11.php')
    ->notPath('tests/ZendTest/Code/TestAsset')
    ->notPath('tests/ZendTest/Validator/_files')
    ->notPath('tests/ZendTest/Loader/_files')
    ->notPath('tests/ZendTest/Loader/TestAsset')
    ->filter(function (SplFileInfo $file) {
        if (strstr($file->getPath(), 'compatibility')) {
            return false;
        }
    })
    ->in(__DIR__ . '/library')
    ->in(__DIR__ . '/tests')
    ->in(__DIR__ . '/bin');
$config = Symfony\CS\Config\Config::create();
$config->fixers(Symfony\CS\FixerInterface::PSR2_LEVEL);
$config->finder($finder);
return $config;
