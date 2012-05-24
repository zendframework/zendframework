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

use Zend\Crypt\Key\Derivation\Pbkdf2;

/**
 * @category   Zend
 * @package    Zend_Crypt
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Pbkdf2Test extends \PHPUnit_Framework_TestCase
{

    /** @var string */
    public $salt;

    public function setUp()
    {
        $this->salt = '12345678901234567890123456789012';
    }

    public function testCalc()
    {
        $password = Pbkdf2::calc('sha256', 'test', $this->salt, 5000, 32);
        $this->assertEquals(32, strlen($password));
        $this->assertEquals(base64_encode($password), '323tCTB8Z/KVrJYWPvMoKqbL34gMziymMdvYTfELpKI=');
    }

    public function testCalcWithWrongHash()
    {
        $this->setExpectedException('Zend\Crypt\Key\Derivation\Exception\InvalidArgumentException',
                                    'The hash algorihtm wrong is not supported by Zend\Crypt\Key\Derivation\Pbkdf2');
        $password = Pbkdf2::calc('wrong', 'test', $this->salt, 5000, 32);
    }

}
