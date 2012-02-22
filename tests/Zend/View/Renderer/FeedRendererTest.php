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
 * @package    Zend_View
 * @subpackage UnitTest
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\View\Renderer;

use PHPUnit_Framework_TestCase as TestCase,
    Zend\Feed\Writer\Factory as FeedFactory,
    Zend\View\Model,
    Zend\View\Renderer\FeedRenderer;

/**
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTest
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class FeedRendererTest extends TestCase
{
    public function setUp()
    {
        $this->renderer = new FeedRenderer();
    }

    protected function getFeedData($type)
    {
        return array(
            'copyright' => date('Y'),
            'date_created' => time(),
            'date_modified' => time(),
            'last_build_date' => time(),
            'description' => __CLASS__,
            'id' => 'http://framework.zend.com/',
            'language' => 'en_US',
            'feed_link' => array(
                'link' => 'http://framework.zend.com/feed.xml',
                'type' => $type,
            ),
            'link' => 'http://framework.zend.com/feed.xml',
            'title' => 'Testing',
            'encoding' => 'UTF-8',
            'base_url' => 'http://framework.zend.com/',
            'entries' => array(
                array(
                    'content' => 'test content',
                    'date_created' => time(),
                    'date_modified' => time(),
                    'description' => __CLASS__,
                    'id' => 'http://framework.zend.com/1',
                    'link' => 'http://framework.zend.com/1',
                    'title' => 'Test 1',
                ),
                array(
                    'content' => 'test content',
                    'date_created' => time(),
                    'date_modified' => time(),
                    'description' => __CLASS__,
                    'id' => 'http://framework.zend.com/2',
                    'link' => 'http://framework.zend.com/2',
                    'title' => 'Test 2',
                ),
            ),
        );
    }

    public function testRendersFeedModelAccordingToTypeProvidedInModel()
    {
        $model = new Model\FeedModel($this->getFeedData('atom'));
        $model->setOption('feed_type', 'atom');
        $xml = $this->renderer->render($model);
        $this->assertContains('<' . '?xml', $xml);
        $this->assertContains('atom', $xml);
    }

    public function testRendersFeedModelAccordingToRenderTypeIfNoTypeProvidedInModel()
    {
        $this->renderer->setFeedType('atom');
        $model = new Model\FeedModel($this->getFeedData('atom'));
        $xml = $this->renderer->render($model);
        $this->assertContains('<' . '?xml', $xml);
        $this->assertContains('atom', $xml);
    }

    public function testCastsViewModelToFeedModelUsingFeedTypeOptionProvided()
    {
        $model = new Model\ViewModel($this->getFeedData('atom'));
        $model->setOption('feed_type', 'atom');
        $xml = $this->renderer->render($model);
        $this->assertContains('<' . '?xml', $xml);
        $this->assertContains('atom', $xml);
    }

    public function testCastsViewModelToFeedModelUsingRendererFeedTypeIfNoFeedTypeOptionInModel()
    {
        $this->renderer->setFeedType('atom');
        $model = new Model\ViewModel($this->getFeedData('atom'));
        $xml = $this->renderer->render($model);
        $this->assertContains('<' . '?xml', $xml);
        $this->assertContains('atom', $xml);
    }

    public function testStringModelWithValuesProvidedCastsToFeed()
    {
        $this->renderer->setFeedType('atom');
        $xml = $this->renderer->render('layout', $this->getFeedData('atom'));
        $this->assertContains('<' . '?xml', $xml);
        $this->assertContains('atom', $xml);
    }

    public function testNonStringNonModelArgumentRaisesException()
    {
        $this->setExpectedException('Zend\View\Exception\InvalidArgumentException', 'expects');
        $this->renderer->render(array('foo'));
    }

    public function testSettingUnacceptableFeedTypeRaisesException()
    {
        $this->setExpectedException('Zend\View\Exception\InvalidArgumentException', 'expects a string of either "rss" or "atom"');
        $this->renderer->setFeedType('foobar');
    }
}
