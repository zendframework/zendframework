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
 * @package    Zend_Service_Twitter_Search
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Service\Twitter;

use Zend\Service\Twitter;

/**
 * @category   Zend
 * @package    Zend_Service_Twitter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Service
 * @group      Zend_Service_Twitter
 */
class SearchTest extends \PHPUnit_Framework_TestCase
{

    /* @var Zend\Service\Twitter\Search $twitter */
    protected $twitter;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    protected function setUp()
    {
        if (!defined('TESTS_ZEND_SERVICE_TWITTER_ONLINE_ENABLED')
            || !constant('TESTS_ZEND_SERVICE_TWITTER_ONLINE_ENABLED')
        ) {
            $this->markTestSkipped('Twitter tests are not enabled');
            return;
        }

        $this->twitter = new Twitter\Search();
    }

    public function testSetResponseTypeToJson()
    {
        $this->twitter->setResponseType('json');
        $this->assertEquals('json', $this->twitter->getResponseType());
    }

    public function testSetResponseTypeToAtom()
    {
        $this->twitter->setResponseType('atom');
        $this->assertEquals('atom', $this->twitter->getResponseType());
    }

    public function testInvalidResponseTypeShouldThrowException()
    {
        try {
            $this->twitter->setResponseType('xml');
            $this->fail('Setting an invalid response type should throw an exception');
        } catch(\Exception $e) {
        }
    }

    public function testValidResponseTypeShouldNotThrowException()
    {
        try {
            $this->twitter->setResponseType('atom');
        } catch(\Exception $e) {
            $this->fail('Setting a valid response type should not throw an exception');
        }
    }

    public function testJsonSearchContainsWordReturnsArray()
    {
        $this->twitter->setResponseType('json');
        $response = $this->twitter->execute('zend');
        $this->assertInternalType('array', $response);

    }

    public function testAtomSearchContainsWordReturnsObject()
    {
        $this->twitter->setResponseType('atom');
        $response = $this->twitter->execute('zend');

        $this->assertInstanceOf('Zend\Feed\Reader\Feed\Atom', $response);
    }

    public function testJsonSearchRestrictsLanguageReturnsArray()
    {
        $this->twitter->setResponseType('json');
        $response = $this->twitter->execute('zend', array('lang' => 'de'));
        $this->assertInternalType('array', $response);
        $this->assertTrue((isset($response['results'][0]) && $response['results'][0]['iso_language_code'] == "de"));
    }

    public function testAtomSearchRestrictsLanguageReturnsObject()
    {
        $this->markTestIncomplete('Problem with missing link method.');

        $this->twitter->setResponseType('atom');
        $response = $this->twitter->execute('zend', array('lang' => 'de'));

        $this->assertInstanceOf('Zend\Feed\Reader\Feed\Atom', $response);
        var_dump($response);
        $this->assertTrue((strpos($response->link('self'), 'lang=de') !== false));

    }

    public function testJsonSearchReturnThirtyResultsReturnsArray()
    {
        $this->twitter->setResponseType('json');
        $response = $this->twitter->execute('zend', array('rpp' => '30'));
        $this->assertInternalType('array', $response);
        $this->assertTrue((count($response['results']) == 30));
    }

    public function testAtomSearchReturnThirtyResultsReturnsObject()
    {
        $this->twitter->setResponseType('atom');
        $response = $this->twitter->execute('zend', array('rpp' => '30'));

        $this->assertInstanceOf('Zend\Feed\Reader\Feed\Atom', $response);
        $this->assertTrue(($response->count() == 30));

    }

    public function testAtomSearchShowUserReturnsObject()
    {
        $this->twitter->setResponseType('atom');
        $response = $this->twitter->execute('zend', array('show_user' => 'true'));

        $this->assertInstanceOf('Zend\Feed\Reader\Feed\Atom', $response);
    }
}
