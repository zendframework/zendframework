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
 * @package    UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

// Call Zend_Validate_IdenticalTest::main() if this source file is executed directly.
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Validate_IdenticalTest::main');
}

require_once dirname(__FILE__) . '/../../TestHelper.php';

/** Zend_Validate_Identical */
require_once 'Zend/Validate/Identical.php';

/**
 * Zend_Validate_Identical
 *
 * @category   Zend
 * @package    UnitTests
 * @uses       Zend_Validate_Identical
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */
class Zend_Validate_IdenticalTest extends PHPUnit_Framework_TestCase
{
    public static function main()
    {
        require_once "PHPUnit/TextUI/TestRunner.php";
        $suite  = new PHPUnit_Framework_TestSuite('Zend_Validate_IdenticalTest');
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function setUp()
    {
        $this->validator = new Zend_Validate_Identical;
    }

    public function testTokenInitiallyNull()
    {
        $this->assertNull($this->validator->getToken());
    }

    public function testCanSetToken()
    {
        $this->testTokenInitiallyNull();
        $this->validator->setToken('foo');
        $this->assertEquals('foo', $this->validator->getToken());
    }

    public function testCanSetTokenViaConstructor()
    {
        $validator = new Zend_Validate_Identical('foo');
        $this->assertEquals('foo', $validator->getToken());
    }

    public function testValidatingWhenTokenNullReturnsFalse()
    {
        $this->assertFalse($this->validator->isValid('foo'));
    }

    public function testValidatingWhenTokenNullSetsMissingTokenMessage()
    {
        $this->testValidatingWhenTokenNullReturnsFalse();
        $messages = $this->validator->getMessages();
        $this->assertTrue(array_key_exists('missingToken', $messages));
    }

    public function testValidatingAgainstTokenWithNonMatchingValueReturnsFalse()
    {
        $this->validator->setToken('foo');
        $this->assertFalse($this->validator->isValid('bar'));
    }

    public function testValidatingAgainstTokenWithNonMatchingValueSetsNotSameMessage()
    {
        $this->testValidatingAgainstTokenWithNonMatchingValueReturnsFalse();
        $messages = $this->validator->getMessages();
        $this->assertTrue(array_key_exists('notSame', $messages));
    }

    public function testValidatingAgainstTokenWithMatchingValueReturnsTrue()
    {
        $this->validator->setToken('foo');
        $this->assertTrue($this->validator->isValid('foo'));
    }

    /**
     * @see ZF-6511
     */
    public function testNotSameMessageContainsTokenAndValue()
    {
        $this->testValidatingAgainstTokenWithNonMatchingValueReturnsFalse();
        $messages = $this->validator->getMessages();
        $this->assertNotContains('foo', $messages['notSame']);
        $this->assertContains('bar', $messages['notSame']);
        $this->assertContains('does not match', $messages['notSame']);
    }

    /**
     * @see ZF-6953
     */
    public function testValidatingAgainstEmptyToken()
    {
        $this->validator->setToken('');
        $this->assertTrue($this->validator->isValid(''));
    }

    /**
     * @see ZF-7128
     */
    public function testValidatingAgainstNonStrings()
    {
        $this->validator->setToken(true);
        $this->assertTrue($this->validator->isValid(true));
        $this->assertFalse($this->validator->isValid(1));

        $this->validator->setToken(array('one' => 'two', 'three'));
        $this->assertTrue($this->validator->isValid(array('one' => 'two', 'three')));
        $this->assertFalse($this->validator->isValid(array()));
    }
}

// Call Zend_Validate_IdenticalTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == 'Zend_Validate_IdenticalTest::main') {
    Zend_Validate_IdenticalTest::main();
}
