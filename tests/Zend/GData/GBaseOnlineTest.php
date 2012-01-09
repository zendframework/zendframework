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
 * @package    Zend_GData_GBase
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\GData;
use Zend\GData\GBase;

/**
 * @category   Zend
 * @package    Zend_GData_GBase
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_GData
 * @group      Zend_GData_GBase
 */
class GBaseOnlineTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        if (!constant('TESTS_ZEND_GDATA_ONLINE_ENABLED')) {
            $this->markTestSkipped('Zend_GData online tests are not enabled');
        }
        $user = constant('TESTS_ZEND_GDATA_CLIENTLOGIN_EMAIL');
        $pass = constant('TESTS_ZEND_GDATA_CLIENTLOGIN_PASSWORD');
        $service = GBase::AUTH_SERVICE_NAME;
        $client = \Zend\GData\ClientLogin::getHttpClient($user, $pass, $service);
        $this->gdata = new GBase($client);
    }

    public function testGetGBaseItemFeed()
    {
        $feed = $this->gdata->getGBaseItemFeed();
        $this->assertTrue($feed instanceof GBase\ItemFeed);
        foreach ($feed->entries as $entry) {
            $this->assertTrue($entry instanceof GBase\ItemEntry);
            $this->assertEquals($entry->getHttpClient(), $feed->getHttpClient());
        }

        $query = new GBase\ItemQuery();
        $feed = $this->gdata->getGBaseItemFeed($query);
        $this->assertTrue($feed instanceof GBase\ItemFeed);
        foreach ($feed->entries as $entry) {
            $this->assertTrue($entry instanceof GBase\ItemEntry);
            $this->assertEquals($entry->getHttpClient(), $feed->getHttpClient());
        }

        $uri = $query->getQueryUrl();
        $feed = $this->gdata->getGBaseItemFeed($uri);
        $this->assertTrue($feed instanceof GBase\ItemFeed);
        foreach ($feed->entries as $entry) {
            $this->assertTrue($entry instanceof GBase\ItemEntry);
            $this->assertEquals($entry->getHttpClient(), $feed->getHttpClient());
        }
    }

    public function testGetGBaseItemEntry()
    {
        $newEntry = $this->gdata->newItemEntry();

        $title = 'PHP Developer Handbook';
        $newEntry->title = $this->gdata->newTitle(trim($title));

        $desc = 'This is a test item';
        $newEntry->content = $this->gdata->newContent($desc);
        $newEntry->content->type = 'text';

        $itemType = 'Products';
        $newEntry->itemType = $itemType;
        $newEntry->itemType->type = 'text';

        $newEntry->addGBaseAttribute('product_type', 'book', 'text');
        $newEntry->addGBaseAttribute('price', '12.99 usd', 'floatUnit');
        $newEntry->addGBaseAttribute('quantity', '10', 'int');

        $createdEntry = $this->gdata->insertGBaseItem($newEntry, false);
        $itemId = $createdEntry->id->text;

        $entry = $this->gdata->getGBaseItemEntry($itemId);
        $this->assertTrue($entry instanceof GBase\ItemEntry);
    }

    public function testInsertGBaseItem()
    {
        $newEntry = $this->gdata->newItemEntry();

        $title = 'PHP Developer Handbook';
        $newEntry->title = $this->gdata->newTitle(trim($title));

        $desc = 'Essential handbook for PHP developers.';
        $newEntry->content = $this->gdata->newContent($desc);
        $newEntry->content->type = 'text';

        $itemType = 'Products';
        $newEntry->itemType = $itemType;
        $newEntry->itemType->type = 'text';

        $newEntry->addGBaseAttribute('product_type', 'book', 'text');
        $newEntry->addGBaseAttribute('price', '12.99 usd', 'floatUnit');
        $newEntry->addGBaseAttribute('quantity', '10', 'int');

        $createdEntry = $this->gdata->insertGBaseItem($newEntry, true);

        $this->assertEquals($title, $createdEntry->title->text);
        $this->assertEquals($desc, $createdEntry->content->text);
        $this->assertEquals($itemType, $createdEntry->itemType->text);

        $baseAttribute = $createdEntry->getGBaseAttribute('product_type');
        $this->assertEquals('product_type', $baseAttribute[0]->name);
        $this->assertEquals('book', $baseAttribute[0]->text);
        $this->assertEquals('text', $baseAttribute[0]->type);

        $baseAttribute = $createdEntry->getGBaseAttribute('price');
        $this->assertEquals('price', $baseAttribute[0]->name);
        $this->assertEquals('12.99 usd', $baseAttribute[0]->text);
        $this->assertEquals('floatUnit', $baseAttribute[0]->type);

        $baseAttribute = $createdEntry->getGBaseAttribute('quantity');
        $this->assertEquals('quantity', $baseAttribute[0]->name);
        $this->assertEquals('10', $baseAttribute[0]->text);
        $this->assertEquals('int', $baseAttribute[0]->type);
    }

    public function testGetGBaseSnippetFeed()
    {
        $feed = $this->gdata->getGBaseSnippetFeed();
        $this->assertTrue($feed instanceof GBase\SnippetFeed);
        foreach ($feed->entries as $entry) {
            $this->assertTrue($entry instanceof GBase\SnippetEntry);
            $this->assertEquals($entry->getHttpClient(), $feed->getHttpClient());
        }

        $query = new GBase\SnippetQuery();
        $feed = $this->gdata->getGBaseSnippetFeed($query);
        $this->assertTrue($feed instanceof GBase\SnippetFeed);
        foreach ($feed->entries as $entry) {
            $this->assertTrue($entry instanceof GBase\SnippetEntry);
            $this->assertEquals($entry->getHttpClient(), $feed->getHttpClient());
        }

        $uri = $query->getQueryUrl();
        $feed = $this->gdata->getGBaseSnippetFeed($uri);
        $this->assertTrue($feed instanceof GBase\SnippetFeed);
        foreach ($feed->entries as $entry) {
            $this->assertTrue($entry instanceof GBase\SnippetEntry);
            $this->assertEquals($entry->getHttpClient(), $feed->getHttpClient());
        }
    }

}
