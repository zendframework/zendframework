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
 * @package    Zend_Service_Technorati
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Service\Technorati;

use DateTime;
use Zend\Service\Technorati;

/**
 * Test helper
 */

/**
 * @see Technorati\Utils
 */


/**
 * @category   Zend
 * @package    Zend_Service_Technorati
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Service
 * @group      Zend_Service_Technorati
 */
class UtilsTest extends TestCase
{
    /**
     * @return void
     */
    public function testSetUriHttpInputNullReturnsNull()
    {
        $this->assertNull(Technorati\Utils::normalizeUriHttp(null));
    }

    /**
     * @return void
     */
    public function testSetUriHttpInputInvalidSchemeFtpThrowsException()
    {
        $scheme             = 'ftp';
        $inputInvalidScheme = "$scheme://example.com";
        try {
            Technorati\Utils::normalizeUriHttp($inputInvalidScheme);
            $this->fail('Expected Zend\Service\Technorati\Exception not thrown');
        } catch (Technorati\Exception\RuntimeException $e) {
            $this->assertContains($scheme, $e->getMessage());
        }
    }

    /**
     * @return void
     */
    public function testSetDateInputNullReturnsNull()
    {
        $this->assertNull(Technorati\Utils::normalizeDate(null));
    }

    /**
     * @return void
     */
    public function testSetDateInputDateInstanceReturnsInstance()
    {
        $date   = new DateTime('2007-11-11 08:47:26 GMT');
        $result = Technorati\Utils::normalizeDate($date);

        $this->assertInstanceOf('DateTime', $result);
        $this->assertEquals($date, $result);
    }

    /**
     * @return void
     */
    public function testSetDateInputInvalidThrowsException()
    {
        $inputInvalid = "2007foo";
        try {
            Technorati\Utils::normalizeDate($inputInvalid);
            $this->fail('Expected Zend\Service\Technorati\Exception\RuntimeException not thrown');
        } catch (\Exception $e) {
            $this->assertContains($inputInvalid, $e->getMessage());
        }
    }
}
