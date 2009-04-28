<?php
// Call Zend_Dom_Query_Css2XpathTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Dom_Query_Css2XpathTest::main");
}

require_once dirname(__FILE__) . '/../../../TestHelper.php';

/** Zend_Dom_Query_Result */
require_once 'Zend/Dom/Query/Result.php';

class Zend_Dom_Query_ResultTest extends PHPUnit_Framework_TestCase
{
    /**
     * @group ZF-4631
     */
    public function testEmptyResultDoesNotReturnIteratorValidTrue()
    {
        $dom = new DOMDocument();
        $emptyNodeList = $dom->getElementsByTagName("a");
        $result = new Zend_Dom_Query_Result("", "", $dom, $emptyNodeList);

        $this->assertFalse($result->valid());
    }
}

// Call Zend_Dom_Query_Css2XpathTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Dom_Query_Css2XpathTest::main") {
    Zend_Dom_Query_Css2XpathTest::main();
}