<?php
if (version_compare(PHP_VERSION, '5.3.4', 'lt')) {
    if (!class_exists('Zend\Stdlib\ArrayObject', false)
        && file_exists(__DIR__ . '/../../Stdlib/compatibility/autoload.php')
    ) {
        require __DIR__ . '/../../Stdlib/compatibility/autoload.php';
    }

    require_once __DIR__ . '/Container.php';
    require_once __DIR__ . '/Storage/SessionArrayStorage.php';
}
