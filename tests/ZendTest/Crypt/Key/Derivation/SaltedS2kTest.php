<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Crypt
 */

namespace ZendTest\Crypt\Key\Derivation;

use Zend\Crypt\Key\Derivation\SaltedS2k;

/**
 * @category   Zend
 * @package    Zend_Crypt
 * @subpackage UnitTests
 * @group      Zend_Crypt
 */
class SaltedS2kTest extends \PHPUnit_Framework_TestCase
{

    /** @var string */
    public $salt;

    public function setUp()
    {
        $this->salt = '12345678';
    }

    public function testCalc()
    {
        if (!extension_loaded('mhash')) {
            $this->markTestSkipped('The mhash extension is not available');
            return;
        }
        $password = SaltedS2k::calc('sha256', 'test', $this->salt, 32);
        $this->assertEquals(32, strlen($password));
        $this->assertEquals('qzQISUBUSP1iqYtwe/druhdOVqluc/Y2TetdSHSbaw8=', base64_encode($password));
    }

    public function testCalcWithWrongHash()
    {
        if (!extension_loaded('mhash')) {
            $this->markTestSkipped('The mhash extension is not available');
            return;
        }
        $this->setExpectedException('Zend\Crypt\Key\Derivation\Exception\InvalidArgumentException',
                                    'The hash algorihtm wrong is not supported by Zend\Crypt\Key\Derivation\SaltedS2k');
        $password = SaltedS2k::calc('wrong', 'test', $this->salt, 32);
    }

    public function testCalcWithWrongSalt()
    {
        if (!extension_loaded('mhash')) {
            $this->markTestSkipped('The mhash extension is not available');
            return;
        }
        $this->setExpectedException('Zend\Crypt\Key\Derivation\Exception\InvalidArgumentException',
                                    'The salt size must be at least of 8 bytes');
        $password = SaltedS2k::calc('sha256', 'test', substr($this->salt,-1), 32);
    }

}
