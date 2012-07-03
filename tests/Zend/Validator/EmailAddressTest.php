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
 * @package    Zend_Validator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Validator;

use Zend\I18n\Translator\Translator;
use Zend\Validator\EmailAddress;
use Zend\Validator\Hostname;

/**
 * @category   Zend
 * @package    Zend_Validator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Validator
 */
class EmailAddressTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EmailAddress
     */
    protected $validator;

    /** @var boolean */
    public $multipleOptionsDetected;

    public function setUp()
    {
        $this->validator = new EmailAddress();
    }

    /**
     * Ensures that a basic valid e-mail address passes validation
     *
     * @return void
     */
    public function testBasic()
    {
        $this->assertTrue($this->validator->isValid('username@example.com'));
    }

    /**
     * Ensures that localhost address is valid
     *
     * @return void
     */
    public function testLocalhostAllowed()
    {
        $validator = new EmailAddress(Hostname::ALLOW_ALL);
        $this->assertTrue($validator->isValid('username@localhost'));
    }

    /**
     * Ensures that local domain names are valid
     *
     * @return void
     */
    public function testLocaldomainAllowed()
    {
        $validator = new EmailAddress(Hostname::ALLOW_ALL);
        $this->assertTrue($validator->isValid('username@localhost.localdomain'));
    }

    /**
     * Ensures that IP hostnames are valid
     *
     * @return void
     */
    public function testIPAllowed()
    {
        $validator = new EmailAddress(Hostname::ALLOW_DNS | Hostname::ALLOW_IP);
        $valuesExpected = array(
            array(Hostname::ALLOW_DNS, true, array('bob@212.212.20.4')),
            array(Hostname::ALLOW_DNS, false, array('bob@localhost'))
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
        $this->assertFalse($this->validator->isValid('@example.com'));
        $messages = $this->validator->getMessages();
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
        $this->assertFalse($this->validator->isValid('Some User@example.com'));

        $messages = $this->validator->getMessages();

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
        $this->assertTrue($this->validator->isValid('"Some User"@example.com'));

        $messages = $this->validator->getMessages();

        $this->assertInternalType('array', $messages);
        $this->assertEquals(0, count($messages));
    }

    /**
     * Ensures that validation fails when the hostname is invalid
     *
     * @return void
     */
    public function testHostnameInvalid()
    {
        $this->assertFalse($this->validator->isValid('username@ example . com'));
        $messages = $this->validator->getMessages();
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
            $this->assertTrue($this->validator->isValid($input), "$input failed to pass validation:\n"
                            . implode("\n", $this->validator->getMessages()));
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
        $this->assertFalse($this->validator->isValid('User Name <username@example.com>'));
        $messages = $this->validator->getMessages();
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
            $this->assertTrue($this->validator->isValid($input), "$input failed to pass validation:\n"
                            . implode("\n", $this->validator->getMessages()));
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
            $this->assertFalse($this->validator->isValid($input), implode("\n", $this->validator->getMessages()) . $input);
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
            $this->assertTrue($this->validator->isValid($input));
        }
    }


   /**
     * Ensures that the validator follows expected behavior for checking MX records
     *
     * @return void
     */
    public function testMXRecords()
    {
        if (!constant('TESTS_ZEND_VALIDATOR_ONLINE_ENABLED')) {
            $this->markTestSkipped('Testing MX records has been disabled');
        }

        $validator = new EmailAddress(Hostname::ALLOW_DNS, true);

        // Are MX checks supported by this system?
        if (!$validator->isMxSupported()) {
            $this->markTestSkipped('Testing MX records is not supported with this configuration');
            return;
        }

        $valuesExpected = array(
            array(true, array('Bob.Jones@zend.com', 'Bob.Jones@php.net')),
            array(false, array('Bob.Jones@bad.example.com', 'Bob.Jones@anotherbad.example.com'))
            );
        foreach ($valuesExpected as $element) {
            foreach ($element[1] as $input) {
                $this->assertEquals($element[0], $validator->isValid($input), implode("\n", $validator->getMessages()));
            }
        }

        // Try a check via setting the option via a method
        unset($validator);
        $validator = new EmailAddress();
        $validator->useMxCheck(true);
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
        $validator = new EmailAddress();

        // Check no IDN matching
        $validator->getHostnameValidator()->useIdnCheck(false);
        $valuesExpected = array(
            array(false, array('name@b�rger.de', 'name@h�llo.de', 'name@h�llo.se'))
            );
        foreach ($valuesExpected as $element) {
            foreach ($element[1] as $input) {
                $this->assertEquals($element[0], $validator->isValid($input), implode("\n", $validator->getMessages()));
            }
        }

        // Check no TLD matching
        $validator->getHostnameValidator()->useTldCheck(false);
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
        $this->assertEquals(array(), $this->validator->getMessages());
    }

    /**
     * @group ZF-2861
     */
    public function testHostnameValidatorMessagesShouldBeTranslated()
    {
        $hostnameValidator = new Hostname();
        $translations = array(
            'hostnameIpAddressNotAllowed'   => 'hostnameIpAddressNotAllowed translation',
            'hostnameUnknownTld'            => 'hostnameUnknownTld translation',
            'hostnameDashCharacter'         => 'hostnameDashCharacter translation',
            'hostnameInvalidHostnameSchema' => 'hostnameInvalidHostnameSchema translation',
            'hostnameUndecipherableTld'     => 'hostnameUndecipherableTld translation',
            'hostnameInvalidHostname'       => 'hostnameInvalidHostname translation',
            'hostnameInvalidLocalName'      => 'hostnameInvalidLocalName translation',
            'hostnameLocalNameNotAllowed'   => 'hostnameLocalNameNotAllowed translation',
        );
        $loader = new TestAsset\ArrayTranslator();
        $loader->translations = $translations;
        $translator = new Translator();
        $translator->getPluginManager()->setService('test', $loader);
        $translator->addTranslationFile('test', null);

        $this->validator->setTranslator($translator)->setHostnameValidator($hostnameValidator);

        $this->validator->isValid('_XX.!!3xx@0.239,512.777');
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
     * @group ZF-4888
     */
    public function testEmailsExceedingLength()
    {
        $emailAddresses = array(
            'thislocalpathoftheemailadressislongerthantheallowedsizeof64characters@domain.com',
            'bob@verylongdomainsupercalifragilisticexpialidociousspoonfulofsugarverylongdomainsupercalifragilisticexpialidociousspoonfulofsugarverylongdomainsupercalifragilisticexpialidociousspoonfulofsugarverylongdomainsupercalifragilisticexpialidociousspoonfulofsugarexpialidociousspoonfulofsugar.com',
            );
        foreach ($emailAddresses as $input) {
            $this->assertFalse($this->validator->isValid($input));
        }
    }

    /**
     * @group ZF-4352
     */
    public function testNonStringValidation()
    {
        $this->assertFalse($this->validator->isValid(array(1 => 1)));
    }

    /**
     * @group ZF-7490
     */
    public function testSettingHostnameMessagesThroughEmailValidator()
    {
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

        $this->validator->setMessages($translations);
        $this->validator->isValid('_XX.!!3xx@0.239,512.777');
        $messages = $this->validator->getMessages();
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
     * Testing initializing with several options
     */
    public function testInstanceWithOldOptions()
    {
        $handler = set_error_handler(array($this, 'errorHandler'), E_USER_NOTICE);
        $validator = new EmailAddress();
        $options   = $validator->getOptions();

        $this->assertEquals(Hostname::ALLOW_DNS, $options['allow']);
        $this->assertFalse($options['useMxCheck']);

        try {
            $validator = new EmailAddress(Hostname::ALLOW_ALL, true, new Hostname(Hostname::ALLOW_ALL));
            $options   = $validator->getOptions();

            $this->assertEquals(Hostname::ALLOW_ALL, $options['allow']);
            $this->assertTrue($options['useMxCheck']);
            set_error_handler($handler);
        } catch (\Zend\Validator\Exception\InvalidArgumentException $e) {
            $this->markTestSkipped('MX not available on this system');
        }
    }

    /**
     * Testing setOptions
     */
    public function testSetOptions()
    {
        $this->validator->setOptions(array('messages' => array(EmailAddress::INVALID => 'TestMessage')));
        $messages = $this->validator->getMessageTemplates();
        $this->assertEquals('TestMessage', $messages[EmailAddress::INVALID]);

        $oldHostname = $this->validator->getHostnameValidator();
        $this->validator->setOptions(array('hostnameValidator' => new Hostname(Hostname::ALLOW_ALL)));
        $hostname = $this->validator->getHostnameValidator();
        $this->assertNotEquals($oldHostname, $hostname);
    }

    /**
     * Testing setMessage
     */
    public function testSetSingleMessage()
    {
        $messages = $this->validator->getMessageTemplates();
        $this->assertNotEquals('TestMessage', $messages[EmailAddress::INVALID]);
        $this->validator->setMessage('TestMessage', EmailAddress::INVALID);
        $messages = $this->validator->getMessageTemplates();
        $this->assertEquals('TestMessage', $messages[EmailAddress::INVALID]);
    }

    /**
     * Testing getValidateMx
     */
    public function testGetValidateMx()
    {
        $this->assertFalse($this->validator->getMxCheck());
    }

    /**
     * Testing getDeepMxCheck
     */
    public function testGetDeepMxCheck()
    {
        $this->assertFalse($this->validator->getDeepMxCheck());
    }

    /**
     * Testing setMessage for all messages
     *
     * @group ZF-10690
     */
    public function testSetMultipleMessages()
    {
        $messages = $this->validator->getMessageTemplates();
        $this->assertNotEquals('TestMessage', $messages[EmailAddress::INVALID]);
        $this->validator->setMessage('TestMessage');
        $messages = $this->validator->getMessageTemplates();
        $this->assertEquals('TestMessage', $messages[EmailAddress::INVALID]);
    }

    /**
     * Testing getDomainCheck
     */
    public function testGetDomainCheck()
    {
        $this->assertTrue($this->validator->getDomainCheck());
    }

    public function errorHandler($errno, $errstr)
    {
        if (strstr($errstr, 'deprecated')) {
            $this->multipleOptionsDetected = true;
        }
    }

    /**
     * @group ZF-11222
     * @group ZF-11451
     */
    public function testEmailAddressesWithTrailingDotInHostPartAreRejected()
    {
        $this->assertFalse($this->validator->isValid('example@gmail.com.'));
        $this->assertFalse($this->validator->isValid('test@test.co.'));
        $this->assertFalse($this->validator->isValid('test@test.co.za.'));
    }

    /**
     * @group ZF-11239
     */
    public function testNotSetHostnameValidator()
    {
        $hostname = $this->validator->getHostnameValidator();
        $this->assertTrue($hostname instanceof Hostname);
    }

    /**
     * Test getMXRecord
     */
    public function testGetMXRecord()
    {
        if (!constant('TESTS_ZEND_VALIDATOR_ONLINE_ENABLED')) {
            $this->markTestSkipped('Testing MX records has been disabled');
        }

        $validator = new EmailAddress(array('useMxCheck' => true, 'allow' => Hostname::ALLOW_ALL));

        if (!$validator->isMxSupported()) {
            $this->markTestSkipped('Testing MX records is not supported with this configuration');
            return;
        }

        $this->assertTrue($validator->isValid('john.doe@gmail.com'));
        $result = $validator->getMXRecord();
        $this->assertTrue(!empty($result));
    }

    public function testEqualsMessageTemplates()
    {
        $validator = $this->validator;
        $this->assertAttributeEquals($validator->getOption('messageTemplates'),
                                     'messageTemplates', $validator);
    }

    public function testEqualsMessageVariables()
    {
        $validator = $this->validator;
        $this->assertAttributeEquals($validator->getOption('messageVariables'),
                                     'messageVariables', $validator);
    }

    /**
     * @group ZF2-130
     */
    public function testUseMxCheckBasicValid()
    {
        if (!constant('TESTS_ZEND_VALIDATOR_ONLINE_ENABLED')) {
            $this->markTestSkipped('Testing MX records has been disabled');
        }
        $validator = new EmailAddress(array(
            'useMxCheck'        => true,
            'useDeepMxCheck'    => true
        ));

        $emailAddresses = array(
            'bob@gmail.com',
            'bob.jones@bbc.co.uk',
            'bob.jones.smythe@bbc.co.uk',
            'BoB@aol.com',
            'bobjones@nist.gov',
            "B.O'Callaghan@usmc.mil",
            'bob+jones@nic.us',
            'bob+jones@dailymail.co.uk',
            'bob@teaparty.uk.com',
            'bob@thelongestdomainnameintheworldandthensomeandthensomemoreandmore.com'
        );

        foreach ($emailAddresses as $input) {
            $this->assertTrue($validator->isValid($input), "$input failed to pass validation:\n"
                            . implode("\n", $validator->getMessages()));
        }
    }

    /**
     * @group ZF2-130
     */
    public function testUseMxRecordsBasicInvalid()
    {
        $validator = new EmailAddress(array(
            'useMxCheck'        => true,
            'useDeepMxCheck'    => true
        ));

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
            $this->assertFalse($validator->isValid($input), implode("\n", $this->validator->getMessages()) . $input);
        }
    }
}
