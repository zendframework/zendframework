<?php
$finder = Symfony\CS\Finder\DefaultFinder::create()
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
