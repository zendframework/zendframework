<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Config;

use Zend\Config\Config;
use Zend\Config\Processor\Token as TokenProcessor;
use Zend\Config\Processor\Translator as TranslatorProcessor;
use Zend\Config\Processor\Filter as FilterProcessor;
use Zend\Config\Processor\Constant as ConstantProcessor;
use Zend\Config\Processor\Queue as Queue;
use Zend\I18n\Translator\Translator;
use Zend\I18n\Translator\Loader\PhpArray;
use Zend\Filter\StringToLower;
use Zend\Filter\StringToUpper;
use Zend\Filter\PregReplace;

/**
 * @group      Zend_Config
 */
class ProcessorTest extends \PHPUnit_Framework_TestCase
{
    protected $nested;
    protected $tokenBare, $tokenPrefix, $tokenSuffix, $tokenSurround, $tokenSurroundMixed;
    protected $translatorData, $translatorFile;
    protected $userConstants, $phpConstants;
    protected $filter;

    public function setUp()
    {
        // Arrays representing common config configurations
        $this->nested = array(
            'a' => 1,
            'b' => 2,
            'c' => array(
                'ca' => 3,
                'cb' => 4,
                'cc' => 5,
                'cd' => array(
                    'cda' => 6,
                    'cdb' => 7
                ),
            ),
            'd' => array(
                'da' => 8,
                'db' => 9
            ),
            'e' => 10
        );

        $this->tokenBare = array(
            'simple' => 'BARETOKEN',
            'inside' => 'some text with BARETOKEN inside',
            'nested' => array(
                'simple' => 'BARETOKEN',
                'inside' => 'some text with BARETOKEN inside',
            ),
        );

        $this->tokenPrefix = array(
            'simple' => '::TOKEN',
            'inside' => ':: some text with ::TOKEN inside ::',
            'nested' => array(
                'simple' => '::TOKEN',
                'inside' => ':: some text with ::TOKEN inside ::',
            ),
        );

        $this->tokenSuffix = array(
            'simple' => 'TOKEN::',
            'inside' => ':: some text with TOKEN:: inside ::',
            'nested' => array(
                'simple' => 'TOKEN::',
                'inside' => ':: some text with TOKEN:: inside ::',
            ),
        );

        $this->tokenSurround = array(
            'simple' => '##TOKEN##',
            'inside' => '## some text with ##TOKEN## inside ##',
            'nested' => array(
                'simple' => '##TOKEN##',
                'inside' => '## some text with ##TOKEN## inside ##',
            ),
        );

        $this->tokenSurroundMixed = array(
            'simple' => '##TOKEN##',
            'inside' => '## some text with ##TOKEN## inside ##',
            'nested' => array(
                'simple' => '@@TOKEN@@',
                'inside' => '@@ some text with @@TOKEN@@ inside @@',
            ),
        );

        $this->translatorData = array(
            'pages' => array(
                array(
                    'id' => 'oneDog',
                    'label' => 'one dog',
                    'route' => 'app-one-dog'
                ),
                array(
                    'id' => 'twoDogs',
                    'label' => 'two dogs',
                    'route' => 'app-two-dogs'
                ),
            )
        );

        $this->translatorFile = realpath(__DIR__ . '/_files/translations-de_DE.php');

        $this->filter = array(
            'simple' => 'some MixedCase VALue',
            'nested' => array(
                'simple' => 'OTHER mixed Case Value',
            ),
        );

        $this->userConstants = array(
            'simple' => 'SOME_USERLAND_CONSTANT',
            'inside' => 'some text with SOME_USERLAND_CONSTANT inside',
            'nested' => array(
                'simple' => 'SOME_USERLAND_CONSTANT',
                'inside' => 'some text with SOME_USERLAND_CONSTANT inside',
            ),
        );

        $this->phpConstants = array(
            'phpVersion' => 'PHP_VERSION',
            'phpVersionInside' => 'Current PHP version is: PHP_VERSION',
            'nested' => array(
                'phpVersion' => 'PHP_VERSION',
                'phpVersionInside' => 'Current PHP version is: PHP_VERSION',
            ),
        );
    }

    public function testProcessorsQueue()
    {
        $processor1 = new TokenProcessor();
        $processor2 = new TokenProcessor();
        $queue = new Queue();
        $queue->insert($processor1);
        $queue->insert($processor2);

        $this->assertInstanceOf('\Zend\Config\Processor\Queue', $queue);
        $this->assertEquals(2, $queue->count());
        $this->assertTrue($queue->contains($processor1));
        $this->assertTrue($queue->contains($processor2));
    }

    public function testBareTokenPost()
    {
        $config = new Config($this->tokenBare, true);
        $processor = new TokenProcessor();
        $processor->addToken('BARETOKEN', 'some replaced value');
        $processor->process($config);

        $this->assertEquals(array('BARETOKEN' => 'some replaced value'), $processor->getTokens());
        $this->assertEquals('some replaced value', $config->simple);
        $this->assertEquals('some text with some replaced value inside', $config->inside);
        $this->assertEquals('some replaced value', $config->nested->simple);
        $this->assertEquals('some text with some replaced value inside', $config->nested->inside);
    }

    public function testAddInvalidToken()
    {
        $processor = new TokenProcessor();
        $this->setExpectedException('Zend\Config\Exception\InvalidArgumentException',
                                    'Cannot use ' . gettype(array()) . ' as token name.');
        $processor->addToken(array(), 'bar');
    }

    public function testSingleValueToken()
    {
        $processor = new TokenProcessor();
        $processor->addToken('BARETOKEN', 'test');
        $data = 'BARETOKEN';
        $out = $processor->processValue($data);
        $this->assertEquals($out, 'test');
    }

    public function testTokenReadOnly()
    {
        $config = new Config($this->tokenBare, false);
        $processor = new TokenProcessor();
        $processor->addToken('BARETOKEN', 'some replaced value');

        $this->setExpectedException('Zend\Config\Exception\InvalidArgumentException',
                                    'Cannot process config because it is read-only');
        $processor->process($config);
    }

    public function testTokenPrefix()
    {
        $config = new Config($this->tokenPrefix, true);
        $processor = new TokenProcessor(array('TOKEN' => 'some replaced value'), '::');
        $processor->process($config);

        $this->assertEquals('some replaced value', $config->simple);
        $this->assertEquals(':: some text with some replaced value inside ::', $config->inside);
        $this->assertEquals('some replaced value', $config->nested->simple);
        $this->assertEquals(':: some text with some replaced value inside ::', $config->nested->inside);
    }

    public function testTokenSuffix()
    {
        $config = new Config($this->tokenSuffix, true);
        $processor = new TokenProcessor(array('TOKEN' => 'some replaced value'), '', '::');
        $processor->process($config);

        $this->assertEquals('some replaced value', $config->simple);
        $this->assertEquals(':: some text with some replaced value inside ::', $config->inside);
        $this->assertEquals('some replaced value', $config->nested->simple);
        $this->assertEquals(':: some text with some replaced value inside ::', $config->nested->inside);
    }

    /**
     * @depends testTokenSuffix
     * @depends testTokenPrefix
     */
    public function testTokenSurround()
    {
        $config = new Config($this->tokenSurround, true);
        $processor = new TokenProcessor(array('TOKEN' => 'some replaced value'), '##', '##');
        $processor->process($config);

        $this->assertEquals('some replaced value', $config->simple);
        $this->assertEquals('## some text with some replaced value inside ##', $config->inside);
        $this->assertEquals('some replaced value', $config->nested->simple);
        $this->assertEquals('## some text with some replaced value inside ##', $config->nested->inside);
    }

    /**
     * @depends testTokenSurround
     */
    public function testTokenChangeParams()
    {
        $config = new Config($this->tokenSurroundMixed, true);
        $processor = new TokenProcessor(array('TOKEN' => 'some replaced value'), '##', '##');
        $processor->process($config);
        $this->assertEquals('some replaced value', $config->simple);
        $this->assertEquals('## some text with some replaced value inside ##', $config->inside);
        $this->assertEquals('@@TOKEN@@', $config->nested->simple);
        $this->assertEquals('@@ some text with @@TOKEN@@ inside @@', $config->nested->inside);

        /**
         * Now change prefix and suffix on the processor
         */
        $processor->setPrefix('@@');
        $processor->setSuffix('@@');

        /**
         * Parse the config again
         */
        $processor->process($config);

        $this->assertEquals('some replaced value', $config->simple);
        $this->assertEquals('## some text with some replaced value inside ##', $config->inside);
        $this->assertEquals('some replaced value', $config->nested->simple);
        $this->assertEquals('@@ some text with some replaced value inside @@', $config->nested->inside);
    }

    /**
     * @group ZF2-5772
     */
    public function testTokenChangeParamsRetainsType()
    {
        $config = new Config(
            array(
                'trueBoolKey' => true,
                'falseBoolKey' => false,
                'intKey' => 123,
                'floatKey' => (float) 123.456,
                'doubleKey' => (double) 456.789,
            ),
            true
        );

        $processor = new TokenProcessor();

        $processor->process($config);

        $this->assertSame(true, $config['trueBoolKey']);
        $this->assertSame(false, $config['falseBoolKey']);
        $this->assertSame(123, $config['intKey']);
        $this->assertSame((float) 123.456, $config['floatKey']);
        $this->assertSame((double) 456.789, $config['doubleKey']);
    }

    /**
     * @group ZF2-5772
     */
    public function testTokenChangeParamsReplacesInNumerics()
    {
        $config = new Config(
            array(
                'foo' => 'bar1',
                'trueBoolKey' => true,
                'falseBoolKey' => false,
                'intKey' => 123,
                'floatKey' => (float) 123.456,
                'doubleKey' => (double) 456.789,
            ),
            true
        );

        $processor = new TokenProcessor(array('1' => 'R', '9' => 'R'));

        $processor->process($config);

        $this->assertSame('R', $config['trueBoolKey']);
        $this->assertSame('barR', $config['foo']);
        $this->assertSame(false, $config['falseBoolKey']);
        $this->assertSame('R23', $config['intKey']);
        $this->assertSame('R23.456', $config['floatKey']);
        $this->assertSame('456.78R', $config['doubleKey']);
    }

    /**
     * @group ZF2-5772
     */
    public function testIgnoresEmptyStringReplacement()
    {
        $config    = new Config(array('foo' => 'bar'), true);
        $processor = new TokenProcessor(array('' => 'invalid'));

        $processor->process($config);

        $this->assertSame('bar', $config['foo']);
    }

    /**
     * @depends testTokenSurround
     */
    public function testUserConstants()
    {
        define('SOME_USERLAND_CONSTANT', 'some constant value');

        $config = new Config($this->userConstants, true);
        $processor = new ConstantProcessor(false);
        $processor->process($config);

        $tokens = $processor->getTokens();
        $this->assertTrue(is_array($tokens));
        $this->assertTrue(in_array('SOME_USERLAND_CONSTANT', $tokens));
        $this->assertTrue(!$processor->getUserOnly());

        $this->assertEquals('some constant value', $config->simple);
        $this->assertEquals('some text with some constant value inside', $config->inside);
        $this->assertEquals('some constant value', $config->nested->simple);
        $this->assertEquals('some text with some constant value inside', $config->nested->inside);
    }

    /**
     * @depends testUserConstants
     */
    public function testUserOnlyConstants()
    {
        $config = new Config($this->userConstants, true);
        $processor = new ConstantProcessor();
        $processor->process($config);

        $tokens = $processor->getTokens();

        $this->assertTrue(is_array($tokens));
        $this->assertTrue(in_array('SOME_USERLAND_CONSTANT', $tokens));
        $this->assertTrue($processor->getUserOnly());

        $this->assertEquals('some constant value', $config->simple);
        $this->assertEquals('some text with some constant value inside', $config->inside);
        $this->assertEquals('some constant value', $config->nested->simple);
        $this->assertEquals('some text with some constant value inside', $config->nested->inside);
    }

    /**
     * @depends testTokenSurround
     */
    public function testPHPConstants()
    {
        $config = new Config($this->phpConstants, true);
        $processor = new ConstantProcessor(false);
        $processor->process($config);

        $this->assertEquals(PHP_VERSION, $config->phpVersion);
        $this->assertEquals('Current PHP version is: ' . PHP_VERSION, $config->phpVersionInside);
        $this->assertEquals(PHP_VERSION, $config->nested->phpVersion);
        $this->assertEquals('Current PHP version is: ' . PHP_VERSION, $config->nested->phpVersionInside);
    }

    public function testTranslator()
    {
        if (!extension_loaded('intl')) {
            $this->markTestSkipped('ext/intl not enabled');
        }

        $config     = new Config($this->translatorData, true);
        $translator = new Translator();
        $translator->addTranslationFile('phparray', $this->translatorFile);
        $processor  = new TranslatorProcessor($translator);

        $processor->process($config);

        $this->assertEquals('oneDog', $config->pages[0]->id);
        $this->assertEquals('ein Hund', $config->pages[0]->label);
        $this->assertEquals('twoDogs', $config->pages[1]->id);
        $this->assertEquals('zwei Hunde', $config->pages[1]->label);
    }

    public function testTranslatorWithoutIntl()
    {
        if (extension_loaded('intl')) {
            $this->markTestSkipped('ext/intl enabled');
        }

        $this->setExpectedException('Zend\I18n\Exception\ExtensionNotLoadedException',
            'Zend\I18n\Translator component requires the intl PHP extension');

        $config     = new Config($this->translatorData, true);
        $translator = new Translator();
        $translator->addTranslationFile('phparray', $this->translatorFile);
        $processor  = new TranslatorProcessor($translator);

        $processor->process($config);
    }

    public function testTranslatorReadOnly()
    {
        $config     = new Config($this->translatorData, false);
        $translator = new Translator();
        $processor  = new TranslatorProcessor($translator);

        $this->setExpectedException('Zend\Config\Exception\InvalidArgumentException',
                                    'Cannot process config because it is read-only');
        $processor->process($config);
    }

    public function testTranslatorSingleValue()
    {
        if (!extension_loaded('intl')) {
            $this->markTestSkipped('ext/intl not enabled');
        }

        $translator = new Translator();
        $translator->addTranslationFile('phparray', $this->translatorFile);
        $processor  = new TranslatorProcessor($translator);

        $this->assertEquals('ein Hund', $processor->processValue('one dog'));
    }

    public function testTranslatorSingleValueWithoutIntl()
    {
        if (extension_loaded('intl')) {
            $this->markTestSkipped('ext/intl enabled');
        }

        $this->setExpectedException('Zend\I18n\Exception\ExtensionNotLoadedException',
            'Zend\I18n\Translator component requires the intl PHP extension');

        $translator = new Translator();
        $translator->addTranslationFile('phparray', $this->translatorFile);
        $processor  = new TranslatorProcessor($translator);

        $this->assertEquals('ein Hund', $processor->processValue('one dog'));
    }

    public function testFilter()
    {
        $config = new Config($this->filter, true);
        $filter = new StringToLower();
        $processor = new FilterProcessor($filter);

        $this->assertTrue($processor->getFilter() instanceof StringToLower);
        $processor->process($config);

        $this->assertEquals('some mixedcase value', $config->simple);
        $this->assertEquals('other mixed case value', $config->nested->simple);
    }

    public function testFilterReadOnly()
    {
        $config = new Config($this->filter, false);
        $filter = new StringToLower();
        $processor = new FilterProcessor($filter);

        $this->setExpectedException('Zend\Config\Exception\InvalidArgumentException',
                                    'Cannot process config because it is read-only');
        $processor->process($config);
    }

    public function testFilterValue()
    {
        $filter = new StringToLower();
        $processor = new FilterProcessor($filter);

        $value = 'TEST';
        $this->assertEquals('test', $processor->processValue($value));
    }

    /**
     * @depends testFilter
     */
    public function testQueueFIFO()
    {
        $config = new Config($this->filter, true);
        $lower = new StringToLower();
        $upper = new StringToUpper();
        $lowerProcessor = new FilterProcessor($lower);
        $upperProcessor = new FilterProcessor($upper);

        /**
         * Default queue order (FIFO)
         */
        $queue = new Queue();
        $queue->insert($upperProcessor);
        $queue->insert($lowerProcessor);
        $queue->process($config);

        $this->assertEquals('some mixedcase value', $config->simple);
        $this->assertEquals('other mixed case value', $config->nested->simple);
    }

    public function testQueueReadOnly()
    {
        $config = new Config($this->filter, false);
        $lower = new StringToLower();
        $lowerProcessor = new FilterProcessor($lower);

        /**
         * Default queue order (FIFO)
         */
        $queue = new Queue();
        $queue->insert($lowerProcessor);

        $this->setExpectedException('Zend\Config\Exception\InvalidArgumentException',
                                    'Cannot process config because it is read-only');
        $queue->process($config);
    }

    public function testQueueSingleValue()
    {
        $lower = new StringToLower();
        $upper = new StringToUpper();
        $lowerProcessor = new FilterProcessor($lower);
        $upperProcessor = new FilterProcessor($upper);

        /**
         * Default queue order (FIFO)
         */
        $queue = new Queue();
        $queue->insert($upperProcessor);
        $queue->insert($lowerProcessor);

        $data ='TeSt';
        $this->assertEquals('test', $queue->processValue($data));
    }

    /**
     * @depends testQueueFIFO
     */
    public function testQueuePriorities()
    {
        $config = new Config($this->filter, 1);
        $lower = new StringToLower();
        $upper = new StringToUpper();
        $replace = new PregReplace('/[a-z]/', '');
        $lowerProcessor = new FilterProcessor($lower);
        $upperProcessor = new FilterProcessor($upper);
        $replaceProcessor = new FilterProcessor($replace);
        $queue = new Queue();

        /**
         * Insert lower case filter with higher priority
         */
        $queue->insert($upperProcessor, 10);
        $queue->insert($lowerProcessor, 1000);

        $config->simple = 'some MixedCase VALue';
        $queue->process($config);
        $this->assertEquals('SOME MIXEDCASE VALUE', $config->simple);

        /**
         * Add even higher priority replace processor that will remove all lowercase letters
         */
        $queue->insert($replaceProcessor, 10000);
        $config->newValue = 'THIRD mixed CASE value';
        $queue->process($config);
        $this->assertEquals('THIRD  CASE ', $config->newValue);
    }
}
