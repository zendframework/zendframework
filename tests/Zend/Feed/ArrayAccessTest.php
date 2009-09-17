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
require_once dirname(__FILE__) . '/../../TestHelper.php';

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
class Zend_Feed_ArrayAccessTest extends PHPUnit_Framework_TestCase
{
    protected $_feed;
    protected $_nsfeed;

    public function setUp()
    {
        $this->_feed = Zend_Feed::importFile(dirname(__FILE__) . '/_files/TestAtomFeed.xml');
        $this->_nsfeed = Zend_Feed::importFile(dirname(__FILE__) . '/_files/TestAtomFeedNamespaced.xml');
    }

    public function testExists()
    {
        $this->assertFalse(isset($this->_feed[-1]), 'Negative array access should fail');
        $this->assertTrue(isset($this->_feed['version']), 'Feed version should be set');

        $this->assertFalse(isset($this->_nsfeed[-1]), 'Negative array access should fail');
        $this->assertTrue(isset($this->_nsfeed['version']), 'Feed version should be set');
    }

    public function testGet()
    {
        $this->assertEquals($this->_feed['version'], '1.0', 'Feed version should be 1.0');
        $this->assertEquals($this->_nsfeed['version'], '1.0', 'Feed version should be 1.0');
    }

    public function testSet()
    {
        $this->_feed['category'] = 'tests';
        $this->assertTrue(isset($this->_feed['category']), 'Feed category should be set');
        $this->assertEquals($this->_feed['category'], 'tests', 'Feed category should be tests');

        $this->_nsfeed['atom:category'] = 'tests';
        $this->assertTrue(isset($this->_nsfeed['atom:category']), 'Feed category should be set');
        $this->assertEquals($this->_nsfeed['atom:category'], 'tests', 'Feed category should be tests');

        // Changing an existing index.
        $oldEntry = $this->_feed['version'];
        $this->_feed['version'] = '1.1';
        $this->assertTrue($oldEntry != $this->_feed['version'], 'Version should have changed');
    }

    public function testUnset()
    {
        $feed = Zend_Feed::importFile(dirname(__FILE__) . '/_files/TestAtomFeed.xml');
        unset($feed['version']);
        $this->assertFalse(isset($feed['version']), 'Version should be unset');
        $this->assertEquals('', $feed['version'], 'Version should be equal to the empty string');

        $nsfeed = Zend_Feed::importFile(dirname(__FILE__) . '/_files/TestAtomFeedNamespaced.xml');
        unset($nsfeed['version']);
        $this->assertFalse(isset($nsfeed['version']), 'Version should be unset');
        $this->assertEquals('', $nsfeed['version'], 'Version should be equal to the empty string');
    }

    /**
     * @issue ZF-5354
     */
    public function testGetsLinkWithEmptyOrMissingRelAsAlternateRel()
    {
        $feed = Zend_Feed::importFile(dirname(__FILE__) . '/_files/AtomHOnline.xml');
        $entry = $feed->current();
        $this->assertEquals('http://www.h-online.com/security/Google-acquires-reCAPTCHA--/news/114266/from/rss', $entry->link('alternate'));
    }

}
