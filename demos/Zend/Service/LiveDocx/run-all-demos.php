<?php

set_time_limit(0);

require_once __DIR__ . DIRECTORY_SEPARATOR . 'Bootstrap.php';


/**
 * Run all demonstration applications
 *
 * This script recursively runs all demonstration applications, which ship with
 * the Zend Framework LiveDocx component. The demonstration applications
 * do not necessarily output anything to stdout. Those that do not, generate a
 * file in the same directory as the PHP script.
 */

exec('which php', $php);

if (!isset($php[0])) {
    exit('Cannot find PHP exec on your system.');
}

$php = $php[0];

$path = __DIR__ . DIRECTORY_SEPARATOR . 'MailMerge';

$it = new \RecursiveDirectoryIterator($path);
foreach (new \RecursiveIteratorIterator($it) as $file) {
    if ('php' === substr($file->getFilename(), -3)) {
        $cmd = sprintf('cd %s && %s %s', dirname($file->getPathname()),
                $php, $file->getFilename());
        print($cmd . PHP_EOL);
        passthru($cmd);
    }
}

chdir(__DIR__);