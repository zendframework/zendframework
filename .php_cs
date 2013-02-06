<?php
$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->notName('TestSampleClass10.php')
    ->exclude('demos')
    ->exclude('resources')
    ->filter(function (SplFileInfo $file) {
        if (strstr($file->getPath(), 'compatibility')) {
            return false;
        }
    })
    ->in(__DIR__ . '/library')
    ->in(__DIR__ . '/tests')
    ->in(__DIR__ . '/bin');
$config = Symfony\CS\Config\Config::create();
$config->fixers(array(
    'indentation',
    'linefeed',
    'trailing_spaces',
    'php_closing_tag',
    'short_tag',
    'visibility',
    'braces',
    'eof_ending',
    'psr0',
    'elseif',
));
$config->finder($finder);
return $config;
