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
 * @package    Zend_Validate
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../TestHelper.php';

/**
 * @see Zend_Validate_Isbn
 */
require_once 'Zend/Validate/Isbn.php';


/**
 * @category   Zend
 * @package    Zend_Validate
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Validate_IsbnTest extends PHPUnit_Framework_TestCase
{
    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testBasic()
    {
        $validator = new Zend_Validate_Isbn();

        // Brave New World by Aldous Huxley
        $this->assertTrue($validator->isValid('0060929871'));
        $this->assertFalse($validator->isValid('006092987X'));

        // Time Rations by Benjamin Friedlander
        $this->assertTrue($validator->isValid('188202205X'));
        $this->assertFalse($validator->isValid('1882022059'));

        // Towards The Primeval Lighting Field by Will Alexander
        $this->assertTrue($validator->isValid('1882022300'));
        $this->assertFalse($validator->isValid('1882022301'));

        //  ISBN-13 for dummies by Zoë Wykes
        $this->assertTrue($validator->isValid('9780555023402'));
        $this->assertFalse($validator->isValid('97805550234029'));

        // Change Your Brain, Change Your Life Daniel G. Amen
        $this->assertTrue($validator->isValid('9780812929980'));
        $this->assertFalse($validator->isValid('9780812929981'));
    }

    /**
     * Ensures that setSeparator() works as expected
     *
     * @return void
     */
    public function testType()
    {
        $validator = new Zend_Validate_Isbn();

        try {
            $validator->setType(Zend_Validate_Isbn::AUTO);
            $this->assertTrue($validator->getType() == Zend_Validate_Isbn::AUTO);
        } catch (Exception $e) {
            $this->fail("Should accept type 'auto'");
        }

        try {
            $validator->setType(Zend_Validate_Isbn::ISBN10);
            $this->assertTrue($validator->getType() == Zend_Validate_Isbn::ISBN10);
        } catch (Exception $e) {
            $this->fail("Should accept type 'ISBN-10'");
        }

        try {
            $validator->setType(Zend_Validate_Isbn::ISBN13);
            $this->assertTrue($validator->getType() == Zend_Validate_Isbn::ISBN13);
        } catch (Exception $e) {
            $this->fail("Should accept type 'ISBN-13'");
        }

        try {
            $validator->setType('X');
            $this->fail("Should not accept type 'X'");
        } catch (Exception $e) {
            // success
        }
    }

    /**
     * Ensures that setSeparator() works as expected
     *
     * @return void
     */
    public function testSeparator()
    {
        $validator = new Zend_Validate_Isbn();

        try {
            $validator->setSeparator('-');
            $this->assertTrue($validator->getSeparator() == '-');
        } catch (Exception $e) {
            $this->fail("Should accept separator '-'");
        }

        try {
            $validator->setSeparator(' ');
            $this->assertTrue($validator->getSeparator() == ' ');
        } catch (Exception $e) {
            $this->fail("Should accept separator ' '");
        }

        try {
            $validator->setSeparator('');
            $this->assertTrue($validator->getSeparator() == '');
        } catch (Exception $e) {
            $this->fail("Should accept empty separator");
        }

        try {
            $validator->setSeparator('X');
            $this->fail("Should not accept separator 'X'");
        } catch (Exception $e) {
            // success
        }
    }

    /**
     * Ensures that __construct() works as expected
     *
     * @return void
     */
    public function testInitialization()
    {
        $options = array('type'      => Zend_Validate_Isbn::AUTO,
                         'separator' => ' ');
        $validator = new Zend_Validate_Isbn($options);
        $this->assertTrue($validator->getType() == Zend_Validate_Isbn::AUTO);
        $this->assertTrue($validator->getSeparator() == ' ');

        $options = array('type'      => Zend_Validate_Isbn::ISBN10,
                         'separator' => '-');
        $validator = new Zend_Validate_Isbn($options);
        $this->assertTrue($validator->getType() == Zend_Validate_Isbn::ISBN10);
        $this->assertTrue($validator->getSeparator() == '-');

        $options = array('type'      => Zend_Validate_Isbn::ISBN13,
                         'separator' => '');
        $validator = new Zend_Validate_Isbn($options);
        $this->assertTrue($validator->getType() == Zend_Validate_Isbn::ISBN13);
        $this->assertTrue($validator->getSeparator() == '');
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testTypeAuto()
    {
        $validator = new Zend_Validate_Isbn();

        $this->assertTrue($validator->isValid('0060929871'));
        $this->assertFalse($validator->isValid('0-06-092987-1'));
        $this->assertFalse($validator->isValid('0 06 092987 1'));

        $this->assertTrue($validator->isValid('9780555023402'));
        $this->assertFalse($validator->isValid('978-0-555023-40-2'));
        $this->assertFalse($validator->isValid('978 0 555023 40 2'));

        $validator->setSeparator('-');

        $this->assertFalse($validator->isValid('0060929871'));
        $this->assertTrue($validator->isValid('0-06-092987-1'));
        $this->assertFalse($validator->isValid('0 06 092987 1'));

        $this->assertFalse($validator->isValid('9780555023402'));
        $this->assertTrue($validator->isValid('978-0-555023-40-2'));
        $this->assertFalse($validator->isValid('978 0 555023 40 2'));

        $validator->setSeparator(' ');

        $this->assertFalse($validator->isValid('0060929871'));
        $this->assertFalse($validator->isValid('0-06-092987-1'));
        $this->assertTrue($validator->isValid('0 06 092987 1'));

        $this->assertFalse($validator->isValid('9780555023402'));
        $this->assertFalse($validator->isValid('978-0-555023-40-2'));
        $this->assertTrue($validator->isValid('978 0 555023 40 2'));
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testType10()
    {
        $validator = new Zend_Validate_Isbn();
        $validator->setType(Zend_Validate_Isbn::ISBN10);

        $this->assertTrue($validator->isValid('0060929871'));
        $this->assertFalse($validator->isValid('9780555023402'));

        $validator->setSeparator('-');

        $this->assertTrue($validator->isValid('0-06-092987-1'));
        $this->assertFalse($validator->isValid('978-0-555023-40-2'));

        $validator->setSeparator(' ');

        $this->assertTrue($validator->isValid('0 06 092987 1'));
        $this->assertFalse($validator->isValid('978 0 555023 40 2'));
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testType13()
    {
        $validator = new Zend_Validate_Isbn();
        $validator->setType(Zend_Validate_Isbn::ISBN13);

        $this->assertFalse($validator->isValid('0060929871'));
        $this->assertTrue($validator->isValid('9780555023402'));

        $validator->setSeparator('-');

        $this->assertFalse($validator->isValid('0-06-092987-1'));
        $this->assertTrue($validator->isValid('978-0-555023-40-2'));

        $validator->setSeparator(' ');

        $this->assertFalse($validator->isValid('0 06 092987 1'));
        $this->assertTrue($validator->isValid('978 0 555023 40 2'));
    }
}
