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
 * @package    Zend_Crypt
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Crypt\Key\Derivation;

use Zend\Crypt\Key\Derivation\SaltedS2k;

/**
 * @category   Zend
 * @package    Zend_Crypt
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
        $password = SaltedS2k::calc('sha256', 'test', $this->salt, 32);
        $this->assertEquals(32, strlen($password));
        $this->assertEquals(base64_encode($password), 'qzQISUBUSP1iqYtwe/druhdOVqluc/Y2TetdSHSbaw8=');
    }

    public function testCalcWithWrongHash()
    {
        $this->setExpectedException('Zend\Crypt\Key\Derivation\Exception\InvalidArgumentException',
                                    'The hash algorihtm wrong is not supported by Zend\Crypt\Key\Derivation\SaltedS2k');
        $password = SaltedS2k::calc('wrong', 'test', $this->salt, 32);
    }

    public function testCalcWithWrongSalt()
    {
        $this->setExpectedException('Zend\Crypt\Key\Derivation\Exception\InvalidArgumentException',
                                    'The salt size must be at least of 8 bytes');
        $password = SaltedS2k::calc('sha256', 'test', substr($this->salt,-1), 32);
    }

}
