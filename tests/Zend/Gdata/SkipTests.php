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
 * @package    Zend_Gdata
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @package    Zend_Gdata
 * @subpackage UnitTests
 */
class Zend_Gdata_SkipOnlineTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->markTestSkipped("Zend_Gdata online tests are not enabled in TestConfiguration.php");
    }

    public function testOnline()
    {
        // this is here only so we have at least one test
    }
}

class Zend_Gdata_SkipClientLoginTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->markTestSkipped("Zend_Gdata authenticated tests are not enabled in TestConfiguration.php");
    }

    public function testClientLogin()
    {
        // this is here only so we have at least one test
    }
}
