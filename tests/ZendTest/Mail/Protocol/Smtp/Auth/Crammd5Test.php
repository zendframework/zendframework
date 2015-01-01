<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Mail\Protocol\Smtp\Auth;

use ReflectionClass;
use Zend\Mail\Protocol\Smtp\Auth\Crammd5;

/**
 * @group      Zend_Mail
 */
class Crammd5Test extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Crammd5
     */
    protected $auth;

    public function setUp()
    {
        $this->auth = new Crammd5();
    }

    public function testHmacMd5ReturnsExpectedHash()
    {
        $class = new ReflectionClass('Zend\Mail\Protocol\Smtp\Auth\Crammd5');
        $method = $class->getMethod('_hmacMd5');
        $method->setAccessible(true);

        $result = $method->invokeArgs(
            $this->auth,
            array('frodo', 'speakfriendandenter')
        );

        $this->assertEquals('be56fa81a5671e0c62e00134180aae2c', $result);
    }
}
