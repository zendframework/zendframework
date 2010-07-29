<?php
ini_set('display_errors', false);
$component = $_SERVER['argv'][1];

if (!$component) {
    die('A component is required');
}

$path       = realpath(__DIR__ . '/../tests/ZendTest/' . $component . '/');
$tests_path = realpath(__DIR__ . '/../tests/');
$output     = __DIR__ . '/tmp/tests';

if (!is_dir($output)) {
    echo "Attempting to create output directory '$output'\n";
    mkdir($output, 0755, true);
}

foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path)) as $fs_item) {
    $relative_to_tests = preg_replace('#^' . preg_quote($tests_path) . '/#', '', $fs_item->getRealPath());
    
    $subdir = $output . '/' . substr($relative_to_tests, 0, strrpos($relative_to_tests, '/'));
    if (strpos($relative_to_tests, '/') && !file_exists($subdir)) {
        echo 'Making dir: ' . $subdir . PHP_EOL;
        mkdir($subdir, 0777, true);
    }
    
    // if its not a .php file, just copy it
    if (!preg_match('#\.php$#', $fs_item->getRealPath())) {
        echo "Copying file " . $fs_item->getRealPath() . " to $output/$relative_to_tests\n";
        copy($fs_item->getRealPath(), $output . '/' . $relative_to_tests);
    } else {
        echo "Recursively running command on  $relative_to_tests\n";
        $command = 'php ./test-namespacer.php ../tests ' . $relative_to_tests . ' > ' . $output . '/' . $relative_to_tests;
        system($command);
    }

}
