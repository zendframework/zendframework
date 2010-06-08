<?php

require_once __DIR__ . DIRECTORY_SEPARATOR . 'Bootstrap.php';


exec('which php', $php);

if (!isset($php[0])) {
    exit('Cannot find PHP exec on your system.');
}

$php = $php[0];

$path = __DIR__ . DIRECTORY_SEPARATOR . 'MailMerge';
$cmds = array();

$it = new RecursiveDirectoryIterator($path);
foreach (new RecursiveIteratorIterator($it) as $file) {
    if ('php' === strtolower(substr(strrchr($file->getFilename(), '.'), 1))) {
        $directory = dirname($file->getPathname());
        $basename  = (string) $file->getFilename();
        $cmds[] = sprintf('cd %s && %s %s', $directory, $php, $basename);
    }
}

foreach ($cmds as $cmd) {
    print($cmd . PHP_EOL . PHP_EOL);
    system($cmd);
    print(PHP_EOL . '--------------------------------------------------------------------------------' . PHP_EOL . PHP_EOL);
}

exec('cd ' . __DIR__);