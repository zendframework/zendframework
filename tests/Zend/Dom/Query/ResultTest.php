<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Dom
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

// Call Zend_Dom_Query_Css2XpathTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Dom_Query_Css2XpathTest::main");
}

require_once dirname(__FILE__) . '/../../../TestHelper.php';

/** Zend_Dom_Query_Result */
require_once 'Zend/Dom/Query/Result.php';

/**
 * @category   Zend
 * @package    Zend_Dom
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Dom
 */
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
