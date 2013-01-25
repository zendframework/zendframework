<?php
$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->notName('TestSampleClass10.php')
    ->in(__DIR__);
return Symfony\CS\Config\Config::create()
    ->finder($finder);
