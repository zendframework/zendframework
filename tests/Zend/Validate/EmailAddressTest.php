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

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Validate_EmailAddressTest::main');
}

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../TestHelper.php';

/**
 * @see Zend_Validate_EmailAddress
 */
require_once 'Zend/Validate/EmailAddress.php';

/**
 * @category   Zend
 * @package    Zend_Validate
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Validate_EmailAddressTest extends PHPUnit_Framework_TestCase
{
    /**
     * Default instance created for all test methods
     *
     * @var Zend_Validate_EmailAddress
     */
    protected $_validator;

    /**
     * Runs this test suite
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite(__CLASS__);
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Creates a new Zend_Validate_EmailAddress object for each test method
     *
     * @return void
     */
    public function setUp()
    {
        $this->_validator = new Zend_Validate_EmailAddress();
    }

    /**
     * Ensures that a basic valid e-mail address passes validation
     *
     * @return void
     */
    public function testBasic()
    {
        $this->assertTrue($this->_validator->isValid('username@example.com'));
    }

    /**
     * Ensures that localhost address is valid
     *
     * @return void
     */
    public function testLocalhostAllowed()
    {
        $validator = new Zend_Validate_EmailAddress(Zend_Validate_Hostname::ALLOW_ALL);
        $this->assertTrue($validator->isValid('username@localhost'));
    }

    /**
     * Ensures that local domain names are valid
     *
     * @return void
     */
    public function testLocaldomainAllowed()
    {
        $validator = new Zend_Validate_EmailAddress(Zend_Validate_Hostname::ALLOW_ALL);
        $this->assertTrue($validator->isValid('username@localhost.localdomain'));
    }

    /**
     * Ensures that IP hostnames are valid
     *
     * @return void
     */
    public function testIPAllowed()
    {
        $validator = new Zend_Validate_EmailAddress(Zend_Validate_Hostname::ALLOW_DNS | Zend_Validate_Hostname::ALLOW_IP);
        $valuesExpected = array(
            array(Zend_Validate_Hostname::ALLOW_DNS, true, array('bob@212.212.20.4')),
            array(Zend_Validate_Hostname::ALLOW_DNS, false, array('bob@localhost'))
            );
        foreach ($valuesExpected as $element) {
            foreach ($element[2] as $input) {
                $this->assertEquals($element[1], $validator->isValid($input), implode("\n", $validator->getMessages()));
            }
        }
    }

    /**
     * Ensures that validation fails when the local part is missing
     *
     * @return void
     */
    public function testLocalPartMissing()
    {
        $this->assertFalse($this->_validator->isValid('@example.com'));
        $messages = $this->_validator->getMessages();
        $this->assertEquals(1, count($messages));
        $this->assertContains('local-part@hostname', current($messages));
    }

    /**
     * Ensures that validation fails and produces the expected messages when the local part is invalid
     *
     * @return void
     */
    public function testLocalPartInvalid()
    {
        $this->assertFalse($this->_validator->isValid('Some User@example.com'));

        $messages = $this->_validator->getMessages();

        $this->assertEquals(3, count($messages));

        $this->assertContains('Some User', current($messages));
        $this->assertContains('dot-atom', current($messages));

        $this->assertContains('Some User', next($messages));
        $this->assertContains('quoted-string', current($messages));

        $this->assertContains('Some User', next($messages));
        $this->assertContains('not a valid local part', current($messages));
    }

    /**
     * Ensures that no validation failure message is produced when the local part follows the quoted-string format
     *
     * @return void
     */
    public function testLocalPartQuotedString()
    {
        $this->assertTrue($this->_validator->isValid('"Some User"@example.com'));

        $messages = $this->_validator->getMessages();

        $this->assertType('array', $messages);
        $this->assertEquals(0, count($messages));
    }

    /**
     * Ensures that validation fails when the hostname is invalid
     *
     * @return void
     */
    public function testHostnameInvalid()
    {
        $this->assertFalse($this->_validator->isValid('username@ example . com'));
        $messages = $this->_validator->getMessages();
        $this->assertThat(count($messages), $this->greaterThanOrEqual(1));
        $this->assertContains('not a valid hostname', current($messages));
    }

    /**
     * Ensures that quoted-string local part is considered valid
     *
     * @return void
     */
    public function testQuotedString()
    {
        $emailAddresses = array(
            '"username"@example.com',
            '"bob%jones"@domain.com',
            '"bob jones"@domain.com',
            '"bob@jones"@domain.com',
            '"[[ bob ]]"@domain.com',
            '"jones"@domain.com'
            );
        foreach ($emailAddresses as $input) {
            $this->assertTrue($this->_validator->isValid($input), "$input failed to pass validation:\n"
                            . implode("\n", $this->_validator->getMessages()));
        }
    }

    /**
     * Ensures that validation fails when the e-mail is given as for display,
     * with angle brackets around the actual address
     *
     * @return void
     */
    public function testEmailDisplay()
    {
        $this->assertFalse($this->_validator->isValid('User Name <username@example.com>'));
        $messages = $this->_validator->getMessages();
        $this->assertThat(count($messages), $this->greaterThanOrEqual(3));
        $this->assertContains('not a valid hostname', current($messages));
        $this->assertContains('cannot match TLD', next($messages));
        $this->assertContains('does not appear to be a valid local network name', next($messages));
    }

    /**
     * Ensures that the validator follows expected behavior for valid email addresses
     *
     * @return void
     */
    public function testBasicValid()
    {
        $emailAddresses = array(
            'bob@domain.com',
            'bob.jones@domain.co.uk',
            'bob.jones.smythe@domain.co.uk',
            'BoB@domain.museum',
            'bobjones@domain.info',
            "B.O'Callaghan@domain.com",
            'bob+jones@domain.us',
            'bob+jones@domain.co.uk',
            'bob@some.domain.uk.com',
            'bob@verylongdomainsupercalifragilisticexpialidociousspoonfulofsugar.com'
            );
        foreach ($emailAddresses as $input) {
            $this->assertTrue($this->_validator->isValid($input), "$input failed to pass validation:\n"
                            . implode("\n", $this->_validator->getMessages()));
        }
    }

    /**
     * Ensures that the validator follows expected behavior for invalid email addresses
     *
     * @return void
     */
    public function testBasicInvalid()
    {
        $emailAddresses = array(
            '',
            'bob

            @domain.com',
            'bob jones@domain.com',
            '.bobJones@studio24.com',
            'bobJones.@studio24.com',
            'bob.Jones.@studio24.com',
            '"bob%jones@domain.com',
            'bob@verylongdomainsupercalifragilisticexpialidociousaspoonfulofsugar.com',
            'bob+domain.com',
            'bob.domain.com',
            'bob @domain.com',
            'bob@ domain.com',
            'bob @ domain.com',
            'Abc..123@example.com'
            );
        foreach ($emailAddresses as $input) {
            $this->assertFalse($this->_validator->isValid($input), implode("\n", $this->_validator->getMessages()) . $input);
        }
    }

   /**
     * Ensures that the validator follows expected behavior for valid email addresses with complex local parts
     *
     * @return void
     */
    public function testComplexLocalValid()
    {
        $emailAddresses = array(
            'Bob.Jones@domain.com',
            'Bob.Jones!@domain.com',
            'Bob&Jones@domain.com',
            '/Bob.Jones@domain.com',
            '#Bob.Jones@domain.com',
            'Bob.Jones?@domain.com',
            'Bob~Jones@domain.com'
            );
        foreach ($emailAddresses as $input) {
            $this->assertTrue($this->_validator->isValid($input));
        }
    }


   /**
     * Ensures that the validator follows expected behavior for checking MX records
     *
     * @return void
     */
    public function testMXRecords()
    {
        if (!defined('TESTS_ZEND_VALIDATE_ONLINE_ENABLED')
            || !constant('TESTS_ZEND_VALIDATE_ONLINE_ENABLED')
        ) {
            $this->markTestSkipped('Testing MX records only works when a valid internet connection is available');
            return;
        }

        $validator = new Zend_Validate_EmailAddress(Zend_Validate_Hostname::ALLOW_DNS, true);

        // Are MX checks supported by this system?
        if (!$validator->validateMxSupported()) {
            return true;
        }

        $valuesExpected = array(
            array(true, array('Bob.Jones@zend.com', 'Bob.Jones@studio24.net')),
            array(false, array('Bob.Jones@madeupdomain242424a.com', 'Bob.Jones@madeupdomain242424b.net'))
            );
        foreach ($valuesExpected as $element) {
            foreach ($element[1] as $input) {
                $this->assertEquals($element[0], $validator->isValid($input), implode("\n", $validator->getMessages()));
            }
        }

        // Try a check via setting the option via a method
        unset($validator);
        $validator = new Zend_Validate_EmailAddress();
        $validator->setValidateMx(true);
        foreach ($valuesExpected as $element) {
            foreach ($element[1] as $input) {
                $this->assertEquals($element[0], $validator->isValid($input), implode("\n", $validator->getMessages()));
            }
        }
    }

   /**
     * Test changing hostname settings via EmailAddress object
     *
     * @return void
     */
    public function testHostnameSettings()
    {
        $validator = new Zend_Validate_EmailAddress();

        // Check no IDN matching
        $validator->getHostnameValidator()->setValidateIdn(false);
        $valuesExpected = array(
            array(false, array('name@b�rger.de', 'name@h�llo.de', 'name@h�llo.se'))
            );
        foreach ($valuesExpected as $element) {
            foreach ($element[1] as $input) {
                $this->assertEquals($element[0], $validator->isValid($input), implode("\n", $validator->getMessages()));
            }
        }

        // Check no TLD matching
        $validator->getHostnameValidator()->setValidateTld(false);
        $valuesExpected = array(
            array(true, array('name@domain.xx', 'name@domain.zz', 'name@domain.madeup'))
            );
        foreach ($valuesExpected as $element) {
            foreach ($element[1] as $input) {
                $this->assertEquals($element[0], $validator->isValid($input), implode("\n", $validator->getMessages()));
            }
        }
    }

    /**
     * Ensures that getMessages() returns expected default value (an empty array)
     *
     * @return void
     */
    public function testGetMessages()
    {
        $this->assertEquals(array(), $this->_validator->getMessages());
    }

    /**
     * @see ZF-2861
     */
    public function testHostnameValidatorMessagesShouldBeTranslated()
    {
        require_once 'Zend/Validate/Hostname.php';
        $hostnameValidator = new Zend_Validate_Hostname();
        require_once 'Zend/Translate.php';
        $translations = array(
            'hostnameIpAddressNotAllowed' => 'hostnameIpAddressNotAllowed translation',
            'hostnameUnknownTld' => 'hostnameUnknownTld translation',
            'hostnameDashCharacter' => 'hostnameDashCharacter translation',
            'hostnameInvalidHostnameSchema' => 'hostnameInvalidHostnameSchema translation',
            'hostnameUndecipherableTld' => 'hostnameUndecipherableTld translation',
            'hostnameInvalidHostname' => 'hostnameInvalidHostname translation',
            'hostnameInvalidLocalName' => 'hostnameInvalidLocalName translation',
            'hostnameLocalNameNotAllowed' => 'hostnameLocalNameNotAllowed translation',
        );
        $translator = new Zend_Translate('array', $translations);
        $this->_validator->setTranslator($translator)->setHostnameValidator($hostnameValidator);

        $this->_validator->isValid('_XX.!!3xx@0.239,512.777');
        $messages = $hostnameValidator->getMessages();
        $found = false;
        foreach ($messages as $code => $message) {
            if (array_key_exists($code, $translations)) {
                $this->assertEquals($translations[$code], $message);
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
    }

    /**
     * @see ZF-4888
     */
    public function testEmailsExceedingLength()
    {
        $emailAddresses = array(
            'thislocalpathoftheemailadressislongerthantheallowedsizeof64characters@domain.com',
            'bob@verylongdomainsupercalifragilisticexpialidociousspoonfulofsugarverylongdomainsupercalifragilisticexpialidociousspoonfulofsugarverylongdomainsupercalifragilisticexpialidociousspoonfulofsugarverylongdomainsupercalifragilisticexpialidociousspoonfulofsugarexpialidociousspoonfulofsugar.com',
            );
        foreach ($emailAddresses as $input) {
            $this->assertFalse($this->_validator->isValid($input));
        }
    }

    /**
     * @ZF-4352
     */
    public function testNonStringValidation()
    {
        $this->assertFalse($this->_validator->isValid(array(1 => 1)));
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Validate_EmailAddressTest::main') {
    Zend_Validate_EmailAddressTest::main();
}
