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

/**
 * @category   Zend
 * @package    Zend_Service_Twitter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
