<?php
require_once 'PHPUnit/Util/Filter.php';
PHPUnit_Util_Filter::addFileToFilter(__FILE__);

$config = array(
    'foo' => 'bar',
);
return $config;
