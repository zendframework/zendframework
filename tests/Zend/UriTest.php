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
 * @package    Zend_Uri
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_UriTest::main');
}

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../TestHelper.php';

/**
 * Zend_Uri
 */
require_once 'Zend/Uri.php';

/**
 * @category   Zend
 * @package    Zend_Uri
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_UriTest extends PHPUnit_Framework_TestCase
{
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_UriTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function setUp()
    {
        $this->notices = array();
        $this->errorReporting = error_reporting();
        $this->displayErrors  = ini_get('display_errors');
    }

    public function tearDown()
    {
        error_reporting($this->errorReporting);
        ini_set('display_errors', $this->displayErrors);
    }

    public function testSchemeEmpty()
    {
        $this->_testInvalidUri('', '/empty/i');
        $this->_testInvalidUri('://www.zend.com', '/empty/i');
    }

    public function testSchemeUnsupported()
    {
        $this->_testInvalidUri('unsupported', '/unsupported/i');
        $this->_testInvalidUri('unsupported://zend.com', '/unsupported/i');
    }

    public function testSchemeIllegal()
    {
        $this->_testInvalidUri('!@#$%^&*()', '/illegal/i');
    }

    public function testSchemeHttp()
    {
    	$this->_testValidUri('http');
    }

    public function testSchemeHttps()
    {
    	$this->_testValidUri('https');
    }

    public function testSchemeMailto()
    {
        $this->markTestIncomplete('Zend_Uri_Mailto is not implemented yet');
    	$this->_testValidUri('mailto');
    }

    /**
     * Tests that an invalid $uri throws an exception and that the
     * message of that exception matches $regex.
     *
     * @param string $uri
     * @param string $regex
     */
    protected function _testInvalidUri($uri, $regex)
    {
        $e = null;
        try {
            $uri = Zend_Uri::factory($uri);
        } catch (Zend_Uri_Exception $e) {
            $this->assertRegExp($regex, $e->getMessage());
            return;
        }
        $this->fail('Zend_Uri_Exception was expected but not thrown');
    }

    /**
     * Tests that a valid $uri returns a Zend_Uri object.
     *
     * @param string $uri
     */
    protected function _testValidUri($uri)
    {
        $uri = Zend_Uri::factory($uri);
        $this->assertTrue($uri instanceof Zend_Uri, 'Zend_Uri object not returned.');
    }

}

// Call Zend_UriTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_UriTest::main") {
    Zend_UriTest::main();
}
