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
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../TestHelper.php';

/**
 * @see Zend_Validate_Hostname
 */
require_once 'Zend/Validate/Hostname.php';


/**
 * @category   Zend
 * @package    Zend_Validate
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Validate_HostnameTest extends PHPUnit_Framework_TestCase
{
    /**
     * Default instance created for all test methods
     *
     * @var Zend_Validate_Hostname
     */
    protected $_validator;

    /**
     * Creates a new Zend_Validate_Hostname object for each test method
     *
     * @return void
     */
    public function setUp()
    {
        $this->_origEncoding = iconv_get_encoding('internal_encoding');
        $this->_validator = new Zend_Validate_Hostname();
    }

    /**
     * Reset iconv
     */
    public function tearDown()
    {
        iconv_set_encoding('internal_encoding', $this->_origEncoding);
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testBasic()
    {
        $valuesExpected = array(
            array(Zend_Validate_Hostname::ALLOW_IP, true, array('1.2.3.4', '10.0.0.1', '255.255.255.255')),
            array(Zend_Validate_Hostname::ALLOW_IP, false, array('1.2.3.4.5', '0.0.0.256')),
            array(Zend_Validate_Hostname::ALLOW_DNS, true, array('example.com', 'example.museum', 'd.hatena.ne.jp')),
            array(Zend_Validate_Hostname::ALLOW_DNS, false, array('localhost', 'localhost.localdomain', '1.2.3.4', 'domain.invalid')),
            array(Zend_Validate_Hostname::ALLOW_LOCAL, true, array('localhost', 'localhost.localdomain', 'example.com')),
            array(Zend_Validate_Hostname::ALLOW_ALL, true, array('localhost', 'example.com', '1.2.3.4')),
            array(Zend_Validate_Hostname::ALLOW_LOCAL, false, array('local host', 'example,com', 'exam_ple.com'))
        );
        foreach ($valuesExpected as $element) {
            $validator = new Zend_Validate_Hostname($element[0]);
            foreach ($element[2] as $input) {
                $this->assertEquals($element[1], $validator->isValid($input), implode("\n", $validator->getMessages()) . $input);
            }
        }
    }

    public function testCombination()
    {
        $valuesExpected = array(
            array(Zend_Validate_Hostname::ALLOW_DNS | Zend_Validate_Hostname::ALLOW_LOCAL, true, array('domain.com', 'localhost', 'local.localhost')),
            array(Zend_Validate_Hostname::ALLOW_DNS | Zend_Validate_Hostname::ALLOW_LOCAL, false, array('1.2.3.4', '255.255.255.255')),
            array(Zend_Validate_Hostname::ALLOW_DNS | Zend_Validate_Hostname::ALLOW_IP, true, array('1.2.3.4', '255.255.255.255')),
            array(Zend_Validate_Hostname::ALLOW_DNS | Zend_Validate_Hostname::ALLOW_IP, false, array('localhost', 'local.localhost'))
            );
        foreach ($valuesExpected as $element) {
            $validator = new Zend_Validate_Hostname($element[0]);
            foreach ($element[2] as $input) {
                $this->assertEquals($element[1], $validator->isValid($input), implode("\n", $validator->getMessages()) . $input);
            }
        }
    }

    /**
     * Ensure the dash character tests work as expected
     *
     */
    public function testDashes()
    {
        $valuesExpected = array(
            array(Zend_Validate_Hostname::ALLOW_DNS, true, array('domain.com', 'doma-in.com')),
            array(Zend_Validate_Hostname::ALLOW_DNS, false, array('-domain.com', 'domain-.com', 'do--main.com'))
            );
        foreach ($valuesExpected as $element) {
            $validator = new Zend_Validate_Hostname($element[0]);
            foreach ($element[2] as $input) {
                $this->assertEquals($element[1], $validator->isValid($input), implode("\n", $validator->getMessages()) . $input);
            }
        }
    }

    /**
     * Ensures that getMessages() returns expected default value
     *
     * @return void
     */
    public function testGetMessages()
    {
        $this->assertEquals(array(), $this->_validator->getMessages());
    }

    /**
     * Ensure the IDN check works as expected
     *
     */
    public function testIDN()
    {
        $validator = new Zend_Validate_Hostname();

        // Check IDN matching
        $valuesExpected = array(
            array(true, array('bürger.de', 'hãllo.de', 'hållo.se')),
            array(true, array('bÜrger.de', 'hÃllo.de', 'hÅllo.se')),
            array(false, array('hãllo.se', 'bürger.lt', 'hãllo.uk'))
            );
        foreach ($valuesExpected as $element) {
            foreach ($element[1] as $input) {
                $this->assertEquals($element[0], $validator->isValid($input), implode("\n", $validator->getMessages()) . $input);
            }
        }

        // Check no IDN matching
        $validator->setValidateIdn(false);
        $valuesExpected = array(
            array(false, array('bürger.de', 'hãllo.de', 'hållo.se'))
            );
        foreach ($valuesExpected as $element) {
            foreach ($element[1] as $input) {
                $this->assertEquals($element[0], $validator->isValid($input), implode("\n", $validator->getMessages()) . $input);
            }
        }

        // Check setting no IDN matching via constructor
        unset($validator);
        $validator = new Zend_Validate_Hostname(Zend_Validate_Hostname::ALLOW_DNS, false);
        $valuesExpected = array(
            array(false, array('bürger.de', 'hãllo.de', 'hållo.se'))
            );
        foreach ($valuesExpected as $element) {
            foreach ($element[1] as $input) {
                $this->assertEquals($element[0], $validator->isValid($input), implode("\n", $validator->getMessages()) . $input);
            }
        }
    }

    /**
     * Ensure the IDN check works on ressource files as expected
     *
     */
    public function testRessourceIDN()
    {
        $validator = new Zend_Validate_Hostname();

        // Check IDN matching
        $valuesExpected = array(
            array(true, array('bürger.com', 'hãllo.com', 'hållo.com')),
            array(true, array('bÜrger.com', 'hÃllo.com', 'hÅllo.com')),
            array(false, array('hãllo.lt', 'bürger.lt', 'hãllo.lt'))
            );
        foreach ($valuesExpected as $element) {
            foreach ($element[1] as $input) {
                $this->assertEquals($element[0], $validator->isValid($input), implode("\n", $validator->getMessages()) . $input);
            }
        }

        // Check no IDN matching
        $validator->setValidateIdn(false);
        $valuesExpected = array(
            array(false, array('bürger.com', 'hãllo.com', 'hållo.com'))
            );
        foreach ($valuesExpected as $element) {
            foreach ($element[1] as $input) {
                $this->assertEquals($element[0], $validator->isValid($input), implode("\n", $validator->getMessages()) . $input);
            }
        }

        // Check setting no IDN matching via constructor
        unset($validator);
        $validator = new Zend_Validate_Hostname(Zend_Validate_Hostname::ALLOW_DNS, false);
        $valuesExpected = array(
            array(false, array('bürger.com', 'hãllo.com', 'hållo.com'))
            );
        foreach ($valuesExpected as $element) {
            foreach ($element[1] as $input) {
                $this->assertEquals($element[0], $validator->isValid($input), implode("\n", $validator->getMessages()) . $input);
            }
        }
    }

    /**
     * Ensure the TLD check works as expected
     *
     */
    public function testTLD()
    {
        $validator = new Zend_Validate_Hostname();

        // Check TLD matching
        $valuesExpected = array(
            array(true, array('domain.co.uk', 'domain.uk.com', 'domain.tl', 'domain.zw')),
            array(false, array('domain.xx', 'domain.zz', 'domain.madeup'))
            );
        foreach ($valuesExpected as $element) {
            foreach ($element[1] as $input) {
                $this->assertEquals($element[0], $validator->isValid($input), implode("\n", $validator->getMessages()) . $input);
            }
        }

        // Check no TLD matching
        $validator->setValidateTld(false);
        $valuesExpected = array(
            array(true, array('domain.xx', 'domain.zz', 'domain.madeup'))
            );
        foreach ($valuesExpected as $element) {
            foreach ($element[1] as $input) {
                $this->assertEquals($element[0], $validator->isValid($input), implode("\n", $validator->getMessages()) . $input);
            }
        }

        // Check setting no TLD matching via constructor
        unset($validator);
        $validator = new Zend_Validate_Hostname(Zend_Validate_Hostname::ALLOW_DNS, true, false);
        $valuesExpected = array(
            array(true, array('domain.xx', 'domain.zz', 'domain.madeup'))
            );
        foreach ($valuesExpected as $element) {
            foreach ($element[1] as $input) {
                $this->assertEquals($element[0], $validator->isValid($input), implode("\n", $validator->getMessages()) . $input);
            }
        }
    }

    /**
     * Ensures that getAllow() returns expected default value
     *
     * @return void
     */
    public function testGetAllow()
    {
        $this->assertEquals(Zend_Validate_Hostname::ALLOW_DNS, $this->_validator->getAllow());
    }

    /**
     * Test changed with ZF-6676, as IP check is only involved when IP patterns match
     *
     * @see ZF-2861
     * @see ZF-6676
     */
    public function testValidatorMessagesShouldBeTranslated()
    {
        require_once 'Zend/Translate.php';
        $translations = array(
            'hostnameInvalidLocalName' => 'this is the IP error message',
        );
        $translator = new Zend_Translate('array', $translations);
        $this->_validator->setTranslator($translator);

        $this->_validator->isValid('0.239,512.777');
        $messages = $this->_validator->getMessages();
        $found = false;
        foreach ($messages as $code => $message) {
            if (array_key_exists($code, $translations)) {
                $found = true;
                break;
            }
        }

        $this->assertTrue($found);
        $this->assertEquals($translations[$code], $message);
    }

    /**
     * @see ZF-6033
     */
    public function testNumberNames()
    {
        $validator = new Zend_Validate_Hostname();

        // Check TLD matching
        $valuesExpected = array(
            array(true, array('www.danger1.com', 'danger.com', 'www.danger.com')),
            array(false, array('www.danger1com', 'dangercom', 'www.dangercom'))
            );
        foreach ($valuesExpected as $element) {
            foreach ($element[1] as $input) {
                $this->assertEquals($element[0], $validator->isValid($input), implode("\n", $validator->getMessages()) . $input);
            }
        }
    }

    /**
     * @see ZF-6133
     */
    public function testPunycodeDecoding()
    {
        $validator = new Zend_Validate_Hostname();

        // Check TLD matching
        $valuesExpected = array(
            array(true, array('xn--brger-kva.com')),
            array(false, array('xn--brger-x45d2va.com', 'xn--bürger.com', 'xn--'))
            );
        foreach ($valuesExpected as $element) {
            foreach ($element[1] as $input) {
                $this->assertEquals($element[0], $validator->isValid($input), implode("\n", $validator->getMessages()) . $input);
            }
        }
    }

    /**
     * @ZF-4352
     */
    public function testNonStringValidation()
    {
        $this->assertFalse($this->_validator->isValid(array(1 => 1)));
    }

    /**
     * @ZF-7323
     */
    public function testLatinSpecialChars()
    {
        $this->assertFalse($this->_validator->isValid('place@yah&oo.com'));
        $this->assertFalse($this->_validator->isValid('place@y*ahoo.com'));
        $this->assertFalse($this->_validator->isValid('ya#hoo'));
    }

    /**
     * @see ZF-7277
     */
    public function testDifferentIconvEncoding()
    {
        iconv_set_encoding('internal_encoding', 'ISO8859-1');
        $validator = new Zend_Validate_Hostname();

        $valuesExpected = array(
            array(true, array('bürger.com', 'hãllo.com', 'hållo.com')),
            array(true, array('bÜrger.com', 'hÃllo.com', 'hÅllo.com')),
            array(false, array('hãllo.lt', 'bürger.lt', 'hãllo.lt'))
            );
        foreach ($valuesExpected as $element) {
            foreach ($element[1] as $input) {
                $this->assertEquals($element[0], $validator->isValid($input), implode("\n", $validator->getMessages()) . $input);
            }
        }
    }
}
