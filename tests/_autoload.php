<?php
/**
 * Setup autoloading
 */

// Setup autoloading for ZendTest assets
require_once __DIR__ . '/../library/Zend/Loader/StandardAutoloader.php';
$loader = new Zend\Loader\StandardAutoloader(
    array(
            Zend\Loader\StandardAutoloader::LOAD_NS => array(
                'ZendTest' => __DIR__ . '/ZendTest',
            ),
    ));
$loader->register();

if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    include_once __DIR__ . '/../vendor/autoload.php';
} else {
    // if composer autoloader is missing, explicitly add the ZF library path
    $loader->registerNamespace('Zend', __DIR__ . '/../library/Zend');
}
