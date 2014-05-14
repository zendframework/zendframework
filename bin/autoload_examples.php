<?php
require_once __DIR__ . '/../library/Zend/.classmap.php';
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
