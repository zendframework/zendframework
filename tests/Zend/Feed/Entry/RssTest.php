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
 * @package    Zend_Feed
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../../TestHelper.php';

/**
 * @see Zend_Feed
 */
require_once 'Zend/Feed.php';

/**
 * @category   Zend
 * @package    Zend_Feed
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Feed
 */
class Zend_Feed_Entry_RssTest extends PHPUnit_Framework_TestCase
{

    public function testContentEncodedSupport()
    {
        $feed = Zend_Feed::importFile(dirname(__FILE__) . '/../_files/TestFeedEntryRssContentEncoded.xml');
        $this->assertType('Zend_Feed_Rss', $feed);

        $item = $feed->current();
        $this->assertType('Zend_Feed_Entry_Rss', $item);

        $this->assertTrue(isset($item->content));
        $this->assertContains(
            'http://framework.zend.com/fisheye/changelog/Zend_Framework/?cs=7757',
            $item->content->__toString()
            );
        $this->assertContains(
            'http://framework.zend.com/fisheye/changelog/Zend_Framework/?cs=7757',
            $item->content()
            );
        $item->content = 'foo';
        $this->assertEquals('foo', $item->content->__toString());
    }

    public function testContentEncodedNullIfEmpty()
    {
        $feed = Zend_Feed::importFile(dirname(__FILE__) . '/../_files/TestFeedEntryRssContentEncoded.xml');
        $this->assertType('Zend_Feed_Rss', $feed);

        $feed->next();
        $item =  $feed->current();
        $this->assertType('Zend_Feed_Entry_Rss', $item);
        $this->assertFalse(isset($item->content));
        $this->assertNull($item->content());
        // $this->assertNull($item->content); // always return DOMElement Object
    }

}
