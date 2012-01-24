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
 * @package    Zend_Config
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Config;

use Zend\Config\Config,
Zend\Config\Processor\Token as TokenProcessor,
Zend\Config\Processor\Translator as TranslatorProcessor,
Zend\Config\Processor\Filter as FilterProcessor,
Zend\Config\Processor\Constant as ConstantProcessor,
Zend\Config\Processor\Queue as Queue,
Zend\Translator\Translator,
Zend\Translator\Adapter\ArrayAdapter,
Zend\Filter\StringToLower,
Zend\Filter\StringToUpper,
Zend\Filter\PregReplace;

/**
 * @category   Zend
 * @package    Zend_Config
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Config
 */
class ProcessorTest extends \PHPUnit_Framework_TestCase
{
    protected $_nested;
    protected $_tokenBare, $_tokenPrefix, $_tokenSuffix, $_tokenSurround, $_tokenSurroundMixed;
    protected $_translator, $_translatorStrings;
    protected $_userConstants, $_phpConstants;
    protected $_filter;

    public function setUp()
    {
        // Arrays representing common config configurations
        $this->_nested = array(
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

        $this->_tokenBare = array(
            'simple' => 'BARETOKEN',
            'inside' => 'some text with BARETOKEN inside',
            'nested' => array(
                'simple' => 'BARETOKEN',
                'inside' => 'some text with BARETOKEN inside',
            ),
        );

        $this->_tokenPrefix = array(
            'simple' => '::TOKEN',
            'inside' => ':: some text with ::TOKEN inside ::',
            'nested' => array(
                'simple' => '::TOKEN',
                'inside' => ':: some text with ::TOKEN inside ::',
            ),
        );

        $this->_tokenSuffix = array(
            'simple' => 'TOKEN::',
            'inside' => ':: some text with TOKEN:: inside ::',
            'nested' => array(
                'simple' => 'TOKEN::',
                'inside' => ':: some text with TOKEN:: inside ::',
            ),
        );

        $this->_tokenSurround = array(
            'simple' => '##TOKEN##',
            'inside' => '## some text with ##TOKEN## inside ##',
            'nested' => array(
                'simple' => '##TOKEN##',
                'inside' => '## some text with ##TOKEN## inside ##',
            ),
        );

        $this->_tokenSurroundMixed = array(
            'simple' => '##TOKEN##',
            'inside' => '## some text with ##TOKEN## inside ##',
            'nested' => array(
                'simple' => '@@TOKEN@@',
                'inside' => '@@ some text with @@TOKEN@@ inside @@',
            ),
        );

        $this->_translator = array(
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

        $this->_translatorStrings = array(
            'one dog' => 'ein Hund',
            'two dogs' => 'zwei Hunde'
        );

        $this->_filter = array(
            'simple' => 'some MixedCase VALue',
            'nested' => array(
                'simple' => 'OTHER mixed Case Value',
            ),
        );

        if (ArrayAdapter::hasCache()) {
            ArrayAdapter::clearCache();
            ArrayAdapter::removeCache();
        }

        $this->_userConstants = array(
            'simple' => 'SOME_USERLAND_CONSTANT',
            'inside' => 'some text with SOME_USERLAND_CONSTANT inside',
            'nested' => array(
                'simple' => 'SOME_USERLAND_CONSTANT',
                'inside' => 'some text with SOME_USERLAND_CONSTANT inside',
            ),
        );

        $this->_phpConstants = array(
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
        $config = new Config($this->_tokenBare, true);
        $processor = new TokenProcessor();
        $processor->addToken('BARETOKEN', 'some replaced value');
        $processor->process($config);

        $this->assertEquals('some replaced value', $config->simple);
        $this->assertEquals('some text with some replaced value inside', $config->inside);
        $this->assertEquals('some replaced value', $config->nested->simple);
        $this->assertEquals('some text with some replaced value inside', $config->nested->inside);
    }

    public function testTokenPrefix()
    {
        $config = new Config($this->_tokenPrefix, true);
        $processor = new TokenProcessor(array('TOKEN' => 'some replaced value'), '::');
        $processor->process($config);

        $this->assertEquals('some replaced value', $config->simple);
        $this->assertEquals(':: some text with some replaced value inside ::', $config->inside);
        $this->assertEquals('some replaced value', $config->nested->simple);
        $this->assertEquals(':: some text with some replaced value inside ::', $config->nested->inside);
    }

    public function testTokenSuffix()
    {
        $config = new Config($this->_tokenSuffix, true);
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
        $config = new Config($this->_tokenSurround, true);
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
        $config = new Config($this->_tokenSurroundMixed, true);
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
     * @depends testTokenSurround
     */
    public function testUserConstants()
    {
        define('SOME_USERLAND_CONSTANT', 'some constant value');

        $config = new Config($this->_userConstants, true);
        $processor = new ConstantProcessor();
        $processor->process($config);

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
        $config = new Config($this->_phpConstants, true);
        $processor = new ConstantProcessor(false);
        $processor->process($config);

        $this->assertEquals(PHP_VERSION, $config->phpVersion);
        $this->assertEquals('Current PHP version is: ' . PHP_VERSION, $config->phpVersionInside);
        $this->assertEquals(PHP_VERSION, $config->nested->phpVersion);
        $this->assertEquals('Current PHP version is: ' . PHP_VERSION, $config->nested->phpVersionInside);
    }

    public function testTranslator()
    {
        $config = new Config($this->_translator, true);
        $translator = new Translator(Translator::AN_ARRAY, $this->_translatorStrings, 'de_DE');
        $processor = new TranslatorProcessor($translator);

        $processor->process($config);

        $this->assertEquals('oneDog', $config->pages[0]->id);
        $this->assertEquals('ein Hund', $config->pages[0]->label);

        $this->assertEquals('twoDogs', $config->pages[1]->id);
        $this->assertEquals('zwei Hunde', $config->pages[1]->label);
    }

    public function testFilter()
    {
        $config = new Config($this->_filter, true);
        $filter = new StringToLower();
        $processor = new FilterProcessor($filter);
        $processor->process($config);

        $this->assertEquals('some mixedcase value', $config->simple);
        $this->assertEquals('other mixed case value', $config->nested->simple);
    }

    /**
     * @depends testFilter
     */
    public function testProcessorsQueueFIFO()
    {
        $config = new Config($this->_filter, true);
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

    /**
     * @depends testProcessorsQueueFIFO
     */
    public function testProcessorsQueuePriorities()
    {
        $config = new Config($this->_filter, 1);
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

