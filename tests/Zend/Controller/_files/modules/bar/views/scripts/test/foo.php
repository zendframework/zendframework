<?php
require_once 'PHPUnit/Util/Filter.php';
PHPUnit_Util_Filter::addFileToFilter(__FILE__);
echo 'Rendered test/foo.php';
