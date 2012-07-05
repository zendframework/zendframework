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

namespace ZendTest\Service\Twitter;

use Zend\Service\Twitter;
use Zend\Config;

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
    /**
     * @var \Zend\Service\Twitter\Search $twitter
     */
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
        } catch (\Exception $e) {
            // ok
        }
    }

    public function testValidResponseTypeShouldNotThrowException()
    {
        $this->twitter->setResponseType('atom');
    }

    public function testSetOptionsWithArray()
    {
        $this->twitter->setOptions(array(
            'lang'        => 'fr',
            'result_type' => 'mixed',
            'show_user'   => true
        ));
        $this->assertEquals('Zend\Service\Twitter\SearchOptions', get_class($this->twitter->getOptions()));
        $this->assertEquals('fr', $this->twitter->getOptions()->getLanguage());
        $this->assertEquals('mixed', $this->twitter->getOptions()->getResultType());
    }

    public function testSetOptionsWithArrayLongName()
    {
        $this->twitter->setOptions(array(
            'language'         => 'fr',
            'results_per_page' => '10',
            'result_type'      => 'mixed',
            'show_user'        => true
        ));
        $this->assertEquals('Zend\Service\Twitter\SearchOptions', get_class($this->twitter->getOptions()));
        $this->assertEquals('fr', $this->twitter->getOptions()->getLanguage());
        $this->assertEquals('mixed', $this->twitter->getOptions()->getResultType());
    }

    public function testSetOptionsWithConfig()
    {
        $this->twitter->setOptions(new Config\Config(array(
            'lang'        => 'fr',
            'result_type' => 'mixed',
            'show_user'   => true
        )));
        $this->assertEquals('Zend\Service\Twitter\SearchOptions', get_class($this->twitter->getOptions()));
        $this->assertEquals('fr', $this->twitter->getOptions()->getLanguage());
        $this->assertEquals('mixed', $this->twitter->getOptions()->getResultType());
    }

    public function testWithQueryInConfig()
    {
        $this->twitter->setResponseType('json');
        $this->twitter->setOptions(new Config\Config(array(
            'q'           => 'zend',
            'lang'        => 'fr',
            'result_type' => 'mixed',
            'show_user'   => true
        )));
        $response = $this->twitter->execute();
        $this->assertEquals('zend', $this->twitter->getOptions()->getQuery());
        $this->assertEquals('fr', $this->twitter->getOptions()->getLanguage());
        $this->assertEquals('mixed', $this->twitter->getOptions()->getResultType());
        $this->assertInternalType('array', $response);
        $this->assertTrue((isset($response['results'][0]) && $response['results'][0]['iso_language_code'] == "fr"));
    }

    public function testWithQueryAliasInConfig()
    {
        $this->twitter->setResponseType('json');
        $this->twitter->setOptions(new Config\Config(array(
            'query'       => 'zend',
            'lang'        => 'fr',
            'result_type' => 'mixed',
            'show_user'   => true
        )));
        $response = $this->twitter->execute();
        $this->assertEquals('zend', $this->twitter->getOptions()->getQuery());
        $this->assertEquals('fr', $this->twitter->getOptions()->getLanguage());
        $this->assertEquals('mixed', $this->twitter->getOptions()->getResultType());
        $this->assertInternalType('array', $response);
        $this->assertTrue((isset($response['results'][0]) && $response['results'][0]['iso_language_code'] == "fr"));
    }

    public function testWithNotQueryAndConfigOnExecute()
    {
        $this->twitter->setResponseType('json');
        $response = $this->twitter->execute(null, new Config\Config(array(
            'q'                => 'zend',
            'lang'             => 'fr',
            'result_type'      => 'mixed',
            'show_user'        => true,
            'include_entities' => true
        )));
        $this->assertNotEquals('zend', $this->twitter->getOptions()->getQuery());
        $this->assertNotEquals('fr', $this->twitter->getOptions()->getLanguage());
        $this->assertInternalType('array', $response);
        $this->assertTrue((isset($response['results'][0]) && $response['results'][0]['iso_language_code'] == "fr"));
        $this->assertTrue((isset($response['results'][0]) && isset($response['results'][0]['entities'])));
    }

    public function testWithNotQueryAndConfigOnExecuteWithLongName()
    {
        $this->twitter->setResponseType('json');
        $response = $this->twitter->execute(null, new Config\Config(array(
            'query'            => 'zend',
            'language'         => 'fr',
            'results_per_page' => 10,
            'result_type'      => 'mixed',
            'show_user'        => true,
            'include_entities' => true
        )));
        $this->assertNotEquals('zend', $this->twitter->getOptions()->getQuery());
        $this->assertNotEquals('fr', $this->twitter->getOptions()->getLanguage());
        $this->assertInternalType('array', $response);
        $this->assertTrue((isset($response['results'][0]) && $response['results'][0]['iso_language_code'] == "fr"));
        $this->assertTrue((isset($response['results'][0]) && isset($response['results'][0]['entities'])));
    }

    public function testWithQueryAndConfigOnExecute()
    {
        $this->twitter->setResponseType('json');
        $response = $this->twitter->execute('zend', new Config\Config(array(
            'lang'        => 'fr',
            'result_type' => 'mixed',
            'show_user'   => true
        )));
        $this->assertNotEquals('zend', $this->twitter->getOptions()->getQuery());
        $this->assertNotEquals('fr', $this->twitter->getOptions()->getLanguage());
        $this->assertInternalType('array', $response);
        $this->assertTrue((isset($response['results'][0]) && $response['results'][0]['iso_language_code'] == "fr"));
    }

    public function testSetOptionsWithSearchOptions()
    {
        $this->twitter->setOptions(new Twitter\SearchOptions(array(
            'lang'             => 'fr',
            'result_type'      => 'mixed',
            'show_user'        => true,
            'include_entities' => false
        )));
        $this->assertEquals('fr', $this->twitter->getOptions()->getLanguage());
        $this->assertEquals('mixed', $this->twitter->getOptions()->getResultType());
        $response = $this->twitter->execute('zend');
        $this->assertTrue((isset($response['results'][0]) && !isset($response['results'][0]['entities'])));
    }

    public function testSetOptionsWithSearchOptionsByGetter()
    {
        $searchOptions = new Twitter\SearchOptions();
        $searchOptions->setLanguage('en');
        $searchOptions->setResultType('mixed');
        $searchOptions->setResultsPerPage(10);
        $searchOptions->setShowUser(true);
        $searchOptions->setIncludeEntities(false);
        $this->twitter->setOptions($searchOptions);
        $this->assertEquals('en', $this->twitter->getOptions()->getLanguage());
        $this->assertEquals('mixed', $this->twitter->getOptions()->getResultType());
        $response = $this->twitter->execute('zend');
        $this->assertTrue((isset($response['results'][0]) && !isset($response['results'][0]['entities'])));
    }

    public function testSetOptionsWithNoEntities()
    {
        $this->twitter->setOptions(new Twitter\SearchOptions(array(
            'lang'             => 'en',
            'result_type'      => 'mixed',
            'show_user'        => true,
            'include_entities' => false
        )));
        $response = $this->twitter->execute('zend');
        $this->assertNotEquals('zend', $this->twitter->getOptions()->getQuery());
        $this->assertTrue((isset($response['results'][0]) && !isset($response['results'][0]['entities'])));
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

    public function testJsonSearchWithArrayOptions()
    {
        $this->twitter->setResponseType('json');
        $response = $this->twitter->execute('zend', array(
            'lang'        => 'fr',
            'result_type' => 'recent',
            'show_user'   => true
        ));
        $this->assertNotEquals('fr', $this->twitter->getOptions()->getLanguage());
        $this->assertNotEquals('recent', $this->twitter->getOptions()->getResultType());
        $this->assertInternalType('array', $response);
        $this->assertTrue((isset($response['results'][0]) && $response['results'][0]['iso_language_code'] == "fr"));
    }

    public function testAtomSearchRestrictsLanguageReturnsObject()
    {
        $this->markTestIncomplete('Problem with missing link method.');

        $this->twitter->setResponseType('atom');
        $response = $this->twitter->execute('zend', array('lang' => 'de'));
        $this->assertInstanceOf('Zend\Feed\Reader\Feed\Atom', $response);
        $this->assertTrue((strpos($response->link('self'), 'lang=de') !== false));
    }

    public function testJsonSearchReturnTwentyResultsReturnsArray()
    {
        $this->twitter->setResponseType('json');
        $response = $this->twitter->execute('php', array(
            'rpp'              => '20',
            'lang'             => 'en',
            'result_type'      => 'recent',
            'include_entities' => false
        ));
        $this->assertNotEquals(20, $this->twitter->getOptions()->getResultsPerPage());
        $this->assertInternalType('array', $response);
        $this->assertEquals(count($response['results']), 20);
    }

    public function testAtomSearchReturnTwentyResultsReturnsObject()
    {
        $this->twitter->setResponseType('atom');
        $response = $this->twitter->execute('php', array(
            'rpp'              => 20,
            'lang'             => 'en',
            'result_type'      => 'recent',
            'include_entities' => false
        ));
        $this->assertInstanceOf('Zend\Feed\Reader\Feed\Atom', $response);
        $this->assertTrue(($response->count() == 20));
    }

    public function testAtomSearchShowUserReturnsObject()
    {
        $this->twitter->setResponseType('atom');
        $response = $this->twitter->execute('zend', array('show_user' => 'true'));
        $this->assertInstanceOf('Zend\Feed\Reader\Feed\Atom', $response);
    }
}
