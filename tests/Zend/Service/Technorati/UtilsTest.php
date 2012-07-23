<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

namespace ZendTest\Service\Technorati;

use DateTime;
use Zend\Service\Technorati;

/**
 * @category   Zend
 * @package    Zend_Service_Technorati
 * @subpackage UnitTests
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
