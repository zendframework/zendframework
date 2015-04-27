<?php
$finder = Symfony\CS\Finder\DefaultFinder::create()
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
        'braces',
        'duplicate_semicolon',
        'elseif',
        'empty_return',
        'encoding',
        'eof_ending',
        'function_call_space',
        'function_declaration',
        'indentation',
        'join_function',
        'line_after_namespace',
        'linefeed',
        'lowercase_constants',
        'lowercase_keywords',
        'parenthesis',
        'multiple_use',
        'method_argument_space',
        'object_operator',
        'php_closing_tag',
        'psr0',
        'remove_lines_between_uses',
        'single_line_after_imports',
        'short_tag',
        'standardize_not_equal',
        'trailing_spaces',
        'unused_use',
        'visibility',
        'whitespacy_lines',
    )
);
$config->finder($finder);
return $config;
