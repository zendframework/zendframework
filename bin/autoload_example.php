<?php
require_once __DIR__ . '/../library/Zend/Loader/ClassMapAutoloader.php';
$loader = new Zend\Loader\ClassMapAutoloader();
$loader->registerAutoloadMap(__DIR__ . '/../library/Zend/.classmap.php');
$loader->register();

if (!class_exists('Zend\Controller\Action')) {
    echo "Could not find action class?\n";
} else {
    echo "Found action class!\n";
}
if (!class_exists('Zend\Version')) {
    echo "Could not find version class!\n";
} else {
    echo "Found version class!\n";
}
