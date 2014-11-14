<?php
$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->notPath('Zend/Barcode/Object/Upce.php')
    ->notPath('Zend/View/Stream.php')
    ->notPath('ZendTest/Code/Generator/TestAsset')
    ->notPath('ZendTest/Code/Reflection/FunctionReflectionTest.php')
    ->notPath('ZendTest/Code/Reflection/MethodReflectionTest.php')
    ->notPath('ZendTest/Code/Reflection/TestAsset')
    ->notPath('ZendTest/Code/TestAsset')
    ->notPath('ZendTest/Validator/_files')
    ->notPath('ZendTest/Loader/_files')
    ->notPath('ZendTest/Loader/TestAsset')
    ->notPath('demos')
    ->notPath('resources')
    // Following are necessary when you use `parallel` or specify a path
    // from the project root.
    ->notPath('Stream.php')
    ->notPath('Generator/TestAsset')
    ->notPath('Reflection/FunctionReflectionTest.php')
    ->notPath('Reflection/MethodReflectionTest.php')
    ->notPath('Reflection/TestAsset')
    ->notPath('TestAsset')
    ->notPath('_files')
    ->filter(function (SplFileInfo $file) {
        if (strstr($file->getPath(), 'compatibility')) {
            return false;
        }
    });
$config = Symfony\CS\Config\Config::create();
$config->level(null);
$config->fixers(
    array(
        'indentation',
        'linefeed',
        'trailing_spaces',
        'short_tag',
        'visibility',
        'php_closing_tag',
        'braces',
        'function_declaration',
        'psr0',
        'elseif',
        'eof_ending',
        'unused_use',
    )
);
$config->finder($finder);
return $config;
