<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Validator;

use Zend\Validator\AbstractValidator;
use Zend\Validator\Between;
use Zend\Validator\NotEmpty;
use Zend\Validator\StaticValidator;
use Zend\Validator\ValidatorChain;

/**
 * @group      Zend_Validator
 */
class ValidatorChainTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ValidatorChain
     */
    protected $validator;

    /**
     * Whether an error occurred
     *
     * @var bool
     */
    protected $errorOccurred = false;

    public function setUp()
    {
        AbstractValidator::setMessageLength(-1);
        $this->validator = new ValidatorChain();
    }

    public function tearDown()
    {
        AbstractValidator::setDefaultTranslator(null);
        AbstractValidator::setMessageLength(-1);
    }

    public function populateValidatorChain()
    {
        $this->validator->attach(new NotEmpty());
        $this->validator->attach(new Between(1, 5));
    }

    public function testValidatorChainIsEmptyByDefault()
    {
        $this->assertEquals(0, count($this->validator->getValidators()));
    }

    /**
     * Ensures expected results from empty validator chain
     *
     * @return void
     */
    public function testEmpty()
    {
        $this->assertEquals(array(), $this->validator->getMessages());
        $this->assertTrue($this->validator->isValid('something'));
    }

    /**
     * Ensures expected behavior from a validator known to succeed
     *
     * @return void
     */
    public function testTrue()
    {
        $this->validator->attach($this->getValidatorTrue());
        $this->assertTrue($this->validator->isValid(null));
        $this->assertEquals(array(), $this->validator->getMessages());
    }

    /**
     * Ensures expected behavior from a validator known to fail
     *
     * @return void
     */
    public function testFalse()
    {
        $this->validator->attach($this->getValidatorFalse());
        $this->assertFalse($this->validator->isValid(null));
        $this->assertEquals(array('error' => 'validation failed'), $this->validator->getMessages());
    }

    /**
     * Ensures that a validator may break the chain
     *
     * @return void
     */
    public function testBreakChainOnFailure()
    {
        $this->validator->attach($this->getValidatorFalse(), true)
            ->attach($this->getValidatorFalse());
        $this->assertFalse($this->validator->isValid(null));
        $this->assertEquals(array('error' => 'validation failed'), $this->validator->getMessages());
    }

    /**
     * Ensures that if we specify a validator class basename that doesn't
     * exist in the namespace, is() throws an exception.
     *
     * Refactored to conform with ZF-2724.
     *
     * @group  ZF-2724
     * @return void
     */
    public function testStaticFactoryClassNotFound()
    {
        $this->setExpectedException('Zend\ServiceManager\Exception\ServiceNotFoundException');
        StaticValidator::execute('1234', 'UnknownValidator');
    }

    public function testIsValidWithParameters()
    {
        $this->assertTrue(StaticValidator::execute(5, 'Between', array(1, 10)));
        $this->assertTrue(StaticValidator::execute(5, 'Between', array('min' => 1, 'max' => 10)));
    }

    public function testSetGetMessageLengthLimitation()
    {
        AbstractValidator::setMessageLength(5);
        $this->assertEquals(5, AbstractValidator::getMessageLength());

        $valid = new Between(1, 10);
        $this->assertFalse($valid->isValid(24));
        $message = current($valid->getMessages());
        $this->assertLessThanOrEqual(5, strlen($message));
    }

    public function testSetGetDefaultTranslator()
    {
        $translator = new TestAsset\Translator();
        AbstractValidator::setDefaultTranslator($translator);
        $this->assertSame($translator, AbstractValidator::getDefaultTranslator());
    }

    public function testAllowsPrependingValidators()
    {
        $this->validator->attach($this->getValidatorTrue())
            ->prependValidator($this->getValidatorFalse(), true);
        $this->assertFalse($this->validator->isValid(true));
        $messages = $this->validator->getMessages();
        $this->assertArrayHasKey('error', $messages);
    }

    public function testAllowsPrependingValidatorsByName()
    {
        $this->validator->attach($this->getValidatorTrue())
            ->prependByName('NotEmpty', array(), true);
        $this->assertFalse($this->validator->isValid(''));
        $messages = $this->validator->getMessages();
        $this->assertArrayHasKey('isEmpty', $messages);
    }

    /**
     * @group 6386
     * @group 6496
     */
    public function testValidatorsAreExecutedAccordingToPriority()
    {
        $this->validator->attach($this->getValidatorTrue(), false, 1000)
                        ->attach($this->getValidatorFalse(), true, 2000);
        $this->assertFalse($this->validator->isValid(true));
        $messages = $this->validator->getMessages();
        $this->assertArrayHasKey('error', $messages);
    }

    /**
     * @group 6386
     * @group 6496
     */
    public function testPrependValidatorsAreExecutedAccordingToPriority()
    {
        $this->validator->attach($this->getValidatorTrue(), false, 1000)
            ->prependValidator($this->getValidatorFalse(), true);
        $this->assertFalse($this->validator->isValid(true));
        $messages = $this->validator->getMessages();
        $this->assertArrayHasKey('error', $messages);
    }

    /**
     * @group 6386
     * @group 6496
     */
    public function testMergeValidatorChains()
    {
        $mergedValidatorChain = new ValidatorChain();

        $mergedValidatorChain->attach($this->getValidatorTrue());
        $this->validator->attach($this->getValidatorTrue());

        $this->validator->merge($mergedValidatorChain);

        $this->assertCount(2, $this->validator->getValidators());
    }

    /**
     * @group 6386
     * @group 6496
     */
    public function testValidatorChainIsCloneable()
    {
        $this->validator->attach(new NotEmpty());

        $this->assertCount(1, $this->validator->getValidators());

        $clonedValidatorChain = clone $this->validator;

        $this->assertCount(1, $clonedValidatorChain->getValidators());

        $clonedValidatorChain->attach(new NotEmpty());

        $this->assertCount(1, $this->validator->getValidators());
        $this->assertCount(2, $clonedValidatorChain->getValidators());
    }

    public function testCountGivesCountOfAttachedValidators()
    {
        $this->populateValidatorChain();
        $this->assertEquals(2, count($this->validator->getValidators()));
    }

    /**
     * Handle file not found errors
     *
     * @group  ZF-2724
     * @param  int    $errnum
     * @param  string $errstr
     * @return void
     */
    public function handleNotFoundError($errnum, $errstr)
    {
        if (strstr($errstr, 'No such file')) {
            $this->error = true;
        }
    }

    /**
     * Ignores a raised PHP error when in effect, but throws a flag to indicate an error occurred
     *
     * @param  integer $errno
     * @param  string  $errstr
     * @param  string  $errfile
     * @param  integer $errline
     * @param  array   $errcontext
     * @return void
     */
    public function errorHandlerIgnore($errno, $errstr, $errfile, $errline, array $errcontext)
    {
        $this->errorOccurred = true;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Zend\Validator\ValidatorInterface
     */
    public function getValidatorTrue()
    {
        $validator = $this->getMock('Zend\Validator\ValidatorInterface');
        $validator->expects($this->any())
            ->method('isValid')
            ->will($this->returnValue(true));
        return $validator;
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|\Zend\Validator\ValidatorInterface
     */
    public function getValidatorFalse()
    {
        $validator = $this->getMock('Zend\Validator\ValidatorInterface');
        $validator->expects($this->any())
            ->method('isValid')
            ->will($this->returnValue(false));
        $validator->expects($this->any())
            ->method('getMessages')
            ->will($this->returnValue(array('error' => 'validation failed')));
        return $validator;
    }

    /**
     * @group ZF-412
     */
    public function testCanAttachMultipleValidatorsOfTheSameTypeAsDiscreteInstances()
    {
        $this->validator->attachByName('Callback', array(
            'callback' => function ($value) {
                return true;
            },
            'messages' => array(
                'callbackValue' => 'This should not be seen in the messages',
            ),
        ));
        $this->validator->attachByName('Callback', array(
            'callback' => function ($value) {
                return false;
            },
            'messages' => array(
                'callbackValue' => 'Second callback trapped',
            ),
        ));

        $this->assertEquals(2, count($this->validator));
        $validators = $this->validator->getValidators();
        $compare = null;
        foreach ($validators as $validator) {
            $this->assertNotSame($compare, $validator);
            $compare = $validator;
        }

        $this->assertFalse($this->validator->isValid('foo'));
        $messages = $this->validator->getMessages();
        $found    = false;
        $test     = 'Second callback trapped';
        foreach ($messages as $messageSet) {
            if (is_string($messageSet) && $messageSet === $test) {
                $found = true;
                break;
            }
            if (is_array($messageSet) && in_array('Second callback trapped', $messageSet)) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
    }


    public function testCanSerializeValidatorChain()
    {
        $this->populateValidatorChain();
        $serialized = serialize($this->validator);

        $unserialized = unserialize($serialized);
        $this->assertInstanceOf('Zend\Validator\ValidatorChain', $unserialized);
        $this->assertEquals(2, count($unserialized));
        $this->assertFalse($unserialized->isValid(''));
    }

    public function breakChainFlags()
    {
        return array(
            'underscores' => array('break_chain_on_failure'),
            'no_underscores' => array('breakchainonfailure'),
        );
    }

    /**
     * @group zfcampus_zf-apigility-admin_89
     * @dataProvider breakChainFlags
     */
    public function testAttachByNameAllowsSpecifyingBreakChainOnFailureFlagViaOptions($option)
    {
        $this->validator->attachByName('GreaterThan', array(
            $option => true,
            'min' => 1,
        ));
        $this->assertEquals(1, count($this->validator));
        $validators = $this->validator->getValidators();
        $spec       = array_shift($validators);

        $this->assertInternalType('array', $spec);
        $this->assertArrayHasKey('instance', $spec);
        $validator = $spec['instance'];
        $this->assertInstanceOf('Zend\Validator\GreaterThan', $validator);
        $this->assertArrayHasKey('breakChainOnFailure', $spec);
        $this->assertTrue($spec['breakChainOnFailure']);
    }
}
