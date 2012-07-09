<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

namespace ZendTest\Service\Twitter;

use Zend\Service\Twitter;

/**
 * @category   Zend
 * @package    Zend_Service_Twitter
 * @subpackage UnitTests
 * @group      Zend_Service
 * @group      Zend_Service_Twitter
 */
class SearchOptionsTest extends \PHPUnit_Framework_TestCase
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
        $this->twitter = new Twitter\Search();
    }

    public function testImmutableOption()
    {
        $expectedLang = 'fr';
        $this->twitter->setOptions(new Twitter\SearchOptions(array(
            'language' => $expectedLang,
        )));
        $options = $this->twitter->getOptions();
        $options->setLanguage('en');
        $actualOptions = $this->twitter->getOptions();

        $this->assertEquals($expectedLang, $actualOptions->getLanguage());
    }
}
