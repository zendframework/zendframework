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
    Zend\Config\Parser\Token as TokenParser;

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
    protected $_tokenBare,$_tokenPrefix, $_tokenSuffix, $_tokenSurround,$_tokenSurroundMixed;

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
            'simple2' => '@@TOKEN@@',
            'inside' => '## some text with ##TOKEN## inside ##',
        );
    }

    public function testEmptyParsersCollection()
    {
        $config = new Config($this->_nested);
        $this->assertInstanceOf('\Zend\Config\Parser\Queue', $config->getParsers());
        $this->assertEquals($this->_nested,$config->toArray());
    }

    public function testParsersQueue()
    {
        $parser1 = new TokenParser();
        $parser2 = new TokenParser();
        $config = new Config(array(),true,array($parser1,$parser2));

        $this->assertInstanceOf('\Zend\Config\Parser\Queue', $config->getParsers());
        $this->assertEquals(2,$config->getParsers()->count());
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
        $parser = new TokenParser(array('TOKEN' => 'some replaced value'),'::');
        $config = new Config($this->_tokenPrefix, true, array($parser));

        $this->assertEquals('some replaced value', $config->simple);
        $this->assertEquals(':: some text with some replaced value inside ::', $config->inside);
        $this->assertEquals('some replaced value', $config->nested->simple);
        $this->assertEquals(':: some text with some replaced value inside ::', $config->nested->inside);
    }

    public function testTokenSuffix()
    {
        $parser = new TokenParser(array('TOKEN' => 'some replaced value'),'','::');
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
        $parser = new TokenParser(array('TOKEN' => 'some replaced value'),'##','##');
        $config = new Config($this->_tokenSurround, true, array($parser));

        $this->assertEquals('some replaced value', $config->simple);
        $this->assertEquals('## some text with some replaced value inside ##', $config->inside);
        $this->assertEquals('some replaced value', $config->nested->simple);
        $this->assertEquals('## some text with some replaced value inside ##', $config->nested->inside);
    }

    /**
     * @depends testTokenSurround
     */
    public function testTokenChangeParams(){
        $parser = new TokenParser(array('TOKEN' => 'some replaced value'),'##','##');
        $config = new Config($this->_tokenSurround, true, array($parser));
        $config->nested['nested'][''];
        $this->assertEquals('some replaced value', $config->simple);
        $this->assertEquals('## some text with some replaced value inside ##', $config->inside);
        $this->assertEquals('some replaced value', $config->nested->simple);
        $this->assertEquals('## some text with some replaced value inside ##', $config->nested->inside);
    }
}

