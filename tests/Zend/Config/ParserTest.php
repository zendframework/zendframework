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
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Config;

use Zend\Config\Config,
Zend\Config\Parser\Token as TokenParser,
Zend\Config\Parser\Translator as TranslatorParser,
Zend\Config\Parser\Filter as FilterParser,
Zend\Config\Parser\Constant as ConstantParser,
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
class ParserTest extends \PHPUnit_Framework_TestCase
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

    public function testEmptyParsersCollection()
    {
        $config = new Config($this->_nested);
        $this->assertInstanceOf('\Zend\Config\Parser\Queue', $config->getParsers());
        $this->assertEquals($this->_nested, $config->toArray());
    }

    public function testParsersQueue()
    {
        $parser1 = new TokenParser();
        $parser2 = new TokenParser();
        $config = new Config(array(), true, array($parser1, $parser2));

        $this->assertInstanceOf('\Zend\Config\Parser\Queue', $config->getParsers());
        $this->assertEquals(2, $config->getParsers()->count());
        $this->assertTrue($config->getParsers()->contains($parser1));
        $this->assertTrue($config->getParsers()->contains($parser2));
    }

    public function testParsersCollectionPersistence()
    {
        $config = new Config($this->_nested);
        $this->assertInstanceOf('\Zend\Config\Parser\Queue', $config->getParsers());
        $this->assertInstanceOf('\Zend\Config\Parser\Queue', $config->c->getParsers());
        $this->assertInstanceOf('\Zend\Config\Parser\Queue', $config->c->cd->getParsers());
        $this->assertSame($config->getParsers(), $config->c->getParsers());
        $this->assertSame($config->c->getParsers(), $config->c->cd->getParsers());
    }

    public function testBareTokenPost()
    {
        $config = new Config($this->_tokenBare, true);
        $parser = new TokenParser();
        $parser->addToken('BARETOKEN', 'some replaced value');
        $parser->parse($config);

        $this->assertEquals('some replaced value', $config->simple);
        $this->assertEquals('some text with some replaced value inside', $config->inside);
        $this->assertEquals('some replaced value', $config->nested->simple);
        $this->assertEquals('some text with some replaced value inside', $config->nested->inside);
    }

    public function testBareTokenJIT()
    {
        $parser = new TokenParser(array('BARETOKEN' => 'some replaced value'));
        $config = new Config($this->_tokenBare, true, array($parser));

        $this->assertEquals('some replaced value', $config->simple);
        $this->assertEquals('some text with some replaced value inside', $config->inside);
        $this->assertEquals('some replaced value', $config->nested->simple);
        $this->assertEquals('some text with some replaced value inside', $config->nested->inside);
    }

    public function testTokenPrefix()
    {
        $parser = new TokenParser(array('TOKEN' => 'some replaced value'), '::');
        $config = new Config($this->_tokenPrefix, true, array($parser));

        $this->assertEquals('some replaced value', $config->simple);
        $this->assertEquals(':: some text with some replaced value inside ::', $config->inside);
        $this->assertEquals('some replaced value', $config->nested->simple);
        $this->assertEquals(':: some text with some replaced value inside ::', $config->nested->inside);
    }

    public function testTokenSuffix()
    {
        $parser = new TokenParser(array('TOKEN' => 'some replaced value'), '', '::');
        $config = new Config($this->_tokenSuffix, true, array($parser));

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
        $parser = new TokenParser(array('TOKEN' => 'some replaced value'), '##', '##');
        $config = new Config($this->_tokenSurround, true, array($parser));

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
        $parser = new TokenParser(array('TOKEN' => 'some replaced value'), '##', '##');
        $config = new Config($this->_tokenSurroundMixed, true);
        $parser->parse($config);
        $this->assertEquals('some replaced value', $config->simple);
        $this->assertEquals('## some text with some replaced value inside ##', $config->inside);
        $this->assertEquals('@@TOKEN@@', $config->nested->simple);
        $this->assertEquals('@@ some text with @@TOKEN@@ inside @@', $config->nested->inside);

        /**
         * Now change prefix and suffix on the parser
         */
        $parser->setPrefix('@@');
        $parser->setSuffix('@@');

        /**
         * Parse the config again
         */
        $parser->parse($config);

        $this->assertEquals('some replaced value', $config->simple);
        $this->assertEquals('## some text with some replaced value inside ##', $config->inside);
        $this->assertEquals('some replaced value', $config->nested->simple);
        $this->assertEquals('@@ some text with some replaced value inside @@', $config->nested->inside);
    }

    /**
     * @depends testTokenSurround
     */
    public function testJITToken()
    {
        $parser = new TokenParser(array('TOKEN' => 'some replaced value'), '##', '##');
        $config = new Config($this->_tokenSurround, true, $parser);

        $config->simple = 'Changed text with ##TOKEN## inside';
        $this->assertEquals('Changed text with some replaced value inside', $config->simple);

        $config->newKey = 'New text with ##TOKEN##';
        $this->assertEquals('New text with some replaced value', $config->newKey);
    }

    /**
     * @depends testJITToken
     */
    public function testJITNestedToken()
    {
        $parser = new TokenParser(array('TOKEN' => 'some replaced value'), '##', '##');
        $config = new Config($this->_tokenSurround, true, $parser);

        $config->nested->moreNested = array();
        $config->nested->moreNested->newKey = 'New text with ##TOKEN##';
        $this->assertEquals('New text with some replaced value', $config->nested->moreNested->newKey);
    }

    /**
     * @depends testTokenSurround
     */
    public function testUserConstants()
    {
        define('SOME_USERLAND_CONSTANT', 'some constant value');

        $parser = new ConstantParser();
        $config = new Config($this->_userConstants, true);
        $parser->parse($config);

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
        $parser = new ConstantParser(false);
        $config = new Config($this->_phpConstants, true);
        $parser->parse($config);

        $this->assertEquals(PHP_VERSION, $config->phpVersion);
        $this->assertEquals('Current PHP version is: ' . PHP_VERSION, $config->phpVersionInside);
        $this->assertEquals(PHP_VERSION, $config->nested->phpVersion);
        $this->assertEquals('Current PHP version is: ' . PHP_VERSION, $config->nested->phpVersionInside);
    }

    public function testTranslator()
    {
        $translator = new Translator(Translator::AN_ARRAY, $this->_translatorStrings, 'de_DE');
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        $parser = new TranslatorParser($translator);
        $config = new Config($this->_translator, true);

        $parser->parse($config);

        $this->assertEquals('oneDog', $config->pages[0]->id);
        $this->assertEquals('ein Hund', $config->pages[0]->label);

        $this->assertEquals('twoDogs', $config->pages[1]->id);
        $this->assertEquals('zwei Hunde', $config->pages[1]->label);
    }

    public function testJITTranslator()
    {
        $translator = new Translator(Translator::AN_ARRAY, $this->_translatorStrings, 'de_DE');
        $parser = new TranslatorParser($translator);
        $config = new Config(array(), true, $parser);

        $config->newValue = 'one dog';
        $this->assertEquals('ein Hund', $config->newValue);

        $config->newValue = 'two dogs';
        $this->assertEquals('zwei Hunde', $config->newValue);

        $config->unknownTranslation = 'three dogs';
        $this->assertEquals('three dogs', $config->unknownTranslation);
    }

    public function testFilter()
    {
        $filter = new StringToLower();
        $parser = new FilterParser($filter);
        $config = new Config($this->_filter, 1);

        $parser->parse($config);

        $this->assertEquals('some mixedcase value', $config->simple);
        $this->assertEquals('other mixed case value', $config->nested->simple);
    }

    public function testJITFilter()
    {
        $filter = new StringToLower();
        $parser = new FilterParser($filter);
        $config = new Config($this->_filter, 1, $parser);

        $this->assertEquals('some mixedcase value', $config->simple);
        $this->assertEquals('other mixed case value', $config->nested->simple);

        $config->newValue = 'THIRD mixed CASE value';
        $this->assertEquals('third mixed case value', $config->newValue);
    }

    /**
     * @depends testFilter
     */
    public function testParsersQueueFIFO()
    {
        $lower = new StringToLower();
        $upper = new StringToUpper();
        $lowerParser = new FilterParser($lower);
        $upperParser = new FilterParser($upper);

        /**
         * Default queue order (FIFO)
         */
        $config = new Config(
            $this->_filter,
            1,
            array(
                $upperParser,
                $lowerParser
            )
        );
        $this->assertEquals('some mixedcase value', $config->simple);
        $this->assertEquals('other mixed case value', $config->nested->simple);
    }

    /**
     * @depends testParsersQueueFIFO
     */
    public function testParsersQueuePriorities()
    {
        $lower = new StringToLower();
        $upper = new StringToUpper();
        $replace = new PregReplace('/[a-z]/', '');
        $lowerParser = new FilterParser($lower);
        $upperParser = new FilterParser($upper);
        $replaceParser = new FilterParser($replace);
        $config = new Config(array(), 1);

        /**
         * Insert lower case filter with higher priority
         */
        $config->getParsers()->insert($upperParser, 10);
        $config->getParsers()->insert($lowerParser, 1000);
        $config->simple = 'some MixedCase VALue';
        $this->assertEquals('SOME MIXEDCASE VALUE', $config->simple);

        /**
         * Add even higher priority replace parser that will remove all lowercase letters
         */
        $config->getParsers()->insert($replaceParser, 10000);
        $config->newValue = 'THIRD mixed CASE value';
        $this->assertEquals('THIRD  CASE ', $config->newValue);
    }

}

