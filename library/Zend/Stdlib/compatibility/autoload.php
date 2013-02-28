<?php
if (version_compare(PHP_VERSION, '5.3.4', 'lt')
    && !class_exists('Zend\Stdlib\ArrayObject', false)
) {
    require_once __DIR__ . '/ArrayObject.php';
}
