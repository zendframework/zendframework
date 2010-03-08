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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Service_TwitterSearchTest::main');
}

/**
 * Test helper
 */

/** Zend_Service_Twitter_Search */

/** Zend_Http_Client */

/** Zend_Http_Client_Adapter_Test */

/**
 * @category   Zend
 * @package    Zend_Service_Twitter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Service
 * @group      Zend_Service_Twitter
 */
class Zend_Service_TwitterSearchTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite(__CLASS__);
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

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

        $this->twitter = new Zend_Service_Twitter_Search();
    }

    public function testSetResponseTypeToJSON()
    {
        $this->twitter->setResponseType('json');
        $this->assertEquals('json', $this->twitter->getResponseType());
    }

    public function testSetResponseTypeToATOM()
    {
        $this->twitter->setResponseType('atom');
        $this->assertEquals('atom', $this->twitter->getResponseType());
    }

    public function testInvalidResponseTypeShouldThrowException()
    {
        try {
            $this->twitter->setResponseType('xml');
            $this->fail('Setting an invalid response type should throw an exception');
        } catch(Exception $e) {
        }
    }

    public function testValidResponseTypeShouldNotThrowException()
    {
        try {
            $this->twitter->setResponseType('atom');
        } catch(Exception $e) {
            $this->fail('Setting a valid response type should not throw an exception');
        }
    }

    public function testSearchTrendsReturnsArray()
    {
        $response = $this->twitter->trends();
        $this->assertType('array', $response);
    }

    public function testJsonSearchContainsWordReturnsArray()
    {
        $this->twitter->setResponseType('json');
        $response = $this->twitter->search('zend');
        $this->assertType('array', $response);

    }

    public function testAtomSearchContainsWordReturnsObject()
    {
        $this->twitter->setResponseType('atom');
        $response = $this->twitter->search('zend');

        $this->assertTrue($response instanceof Zend_Feed_Atom);

    }

    public function testJsonSearchRestrictsLanguageReturnsArray()
    {
        $this->twitter->setResponseType('json');
        $response = $this->twitter->search('zend', array('lang' => 'de'));
        $this->assertType('array', $response);
        $this->assertTrue((isset($response['results'][0]) && $response['results'][0]['iso_language_code'] == "de"));
    }

    public function testAtomSearchRestrictsLanguageReturnsObject()
    {
        $this->twitter->setResponseType('atom');
        /* @var $response Zend_Feed_Atom */
        $response = $this->twitter->search('zend', array('lang' => 'de'));

        $this->assertTrue($response instanceof Zend_Feed_Atom);
        $this->assertTrue((strpos($response->link('self'), 'lang=de') !== false));

    }

    public function testJsonSearchReturnThirtyResultsReturnsArray()
    {
        $this->twitter->setResponseType('json');
        $response = $this->twitter->search('zend', array('rpp' => '30'));
        $this->assertType('array', $response);
        $this->assertTrue((count($response['results']) == 30));
    }

    public function testAtomSearchReturnThirtyResultsReturnsObject()
    {
        $this->twitter->setResponseType('atom');
        /* @var $response Zend_Feed_Atom */
        $response = $this->twitter->search('zend', array('rpp' => '30'));

        $this->assertTrue($response instanceof Zend_Feed_Atom);
        $this->assertTrue(($response->count() == 30));

    }

    public function testAtomSearchShowUserReturnsObject()
    {
        $this->twitter->setResponseType('atom');
        /* @var $response Zend_Feed_Atom */
        $response = $this->twitter->search('zend', array('show_user' => 'true'));

        $this->assertTrue($response instanceof Zend_Feed_Atom);

    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Service_TwitterSearchTest::main') {
    Zend_Service_TwitterSearchTest::main();
}
