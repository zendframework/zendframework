<?php

$component = $_SERVER['argv'][1];

if (!$component) {
    die('A component is required');
}

$path = realpath('../tests/Zend/' . $component . '/');
$tests_path = realpath('../tests/');
$output = realpath('./tmp');

foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path)) as $fs_item) {
    $relative_to_tests = preg_replace('#^' . preg_quote($tests_path) . '/#', '', $fs_item->getRealPath());
    
    $subdir = $output . '/' . substr($relative_to_tests, 0, strrpos($relative_to_tests, '/'));
    if (strpos($relative_to_tests, '/') && !file_exists($subdir)) {
        mkdir($subdir, 0777, true);
        //echo 'Making dir: ' . $subdir . PHP_EOL;
    }
    
    if (preg_match('#\.php$#', $fs_item->getRealPath()) && !preg_match('#_files#', $fs_item->getRealPath())) {
        $command = '/usr/bin/php ./test-namespacer.php ../tests ' . $relative_to_tests . ' > ' . $output . '/' . $relative_to_tests;
        system($command);
    } else {
        echo copy($fs_item->getRealPath(), $output . '/' . $relative_to_tests);
    }

}
