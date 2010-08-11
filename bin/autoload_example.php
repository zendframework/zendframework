<?php
$classMap = include __DIR__ . '/../library/Zend/Controller/_autoload.php';
spl_autoload_register(function($class) use ($classMap) {
    if (array_key_exists($class, $classMap)) {
        require_once $classMap[$class];
    }
});
if (!class_exists('Zend\Controller\Action')) {
    echo "Could not find action class?\n";
} else {
    echo "Found action class!\n";
}
if (!class_exists('Zend\Version')) {
    echo "Could not find version class!\n";
} else {
    echo "Found version class?\n";
}
