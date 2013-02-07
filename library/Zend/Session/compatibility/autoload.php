<?php
if (version_compare(PHP_VERSION, '5.3.4', 'lt')) {
    require_once __DIR__ . '/Container.php';
    require_once __DIR__ . '/Storage/SessionArrayStorage.php';
}
