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
 * @package    Zend_Gdata
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

require_once 'Zend/Gdata/Gbase.php';
require_once 'Zend/Http/Client.php';
require_once 'Zend/Gdata/ClientLogin.php';

/**
 * @package Zend_Gdata
 * @subpackage UnitTests
 */
class Zend_Gdata_GbaseOnlineTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $user = constant('TESTS_ZEND_GDATA_CLIENTLOGIN_EMAIL');
        $pass = constant('TESTS_ZEND_GDATA_CLIENTLOGIN_PASSWORD');
        $service = Zend_Gdata_Gbase::AUTH_SERVICE_NAME;
        $client = Zend_Gdata_ClientLogin::getHttpClient($user, $pass, $service);
        $this->gdata = new Zend_Gdata_Gbase($client);
    }

    public function testGetGbaseItemFeed() 
    {
        $feed = $this->gdata->getGbaseItemFeed();
        $this->assertTrue($feed instanceof Zend_Gdata_Gbase_ItemFeed);
        foreach ($feed->entries as $entry) {
            $this->assertTrue($entry instanceof Zend_Gdata_Gbase_ItemEntry);
            $this->assertEquals($entry->getHttpClient(), $feed->getHttpClient());
        }
        
        $query = new Zend_Gdata_Gbase_ItemQuery();
        $feed = $this->gdata->getGbaseItemFeed($query);
        $this->assertTrue($feed instanceof Zend_Gdata_Gbase_ItemFeed);
        foreach ($feed->entries as $entry) {
            $this->assertTrue($entry instanceof Zend_Gdata_Gbase_ItemEntry);
            $this->assertEquals($entry->getHttpClient(), $feed->getHttpClient());
        }

        $uri = $query->getQueryUrl();
        $feed = $this->gdata->getGbaseItemFeed($uri);
        $this->assertTrue($feed instanceof Zend_Gdata_Gbase_ItemFeed);
        foreach ($feed->entries as $entry) {
            $this->assertTrue($entry instanceof Zend_Gdata_Gbase_ItemEntry);
            $this->assertEquals($entry->getHttpClient(), $feed->getHttpClient());
        }
    }

    public function testGetGbaseItemEntry() 
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

        $newEntry->addGbaseAttribute('product_type', 'book', 'text');
        $newEntry->addGbaseAttribute('price', '12.99 usd', 'floatUnit');
        $newEntry->addGbaseAttribute('quantity', '10', 'int');

        $createdEntry = $this->gdata->insertGbaseItem($newEntry, false);
        $itemId = $createdEntry->id->text;

        $entry = $this->gdata->getGbaseItemEntry($itemId);
        $this->assertTrue($entry instanceof Zend_Gdata_Gbase_ItemEntry);
    }

    public function testInsertGbaseItem() 
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

        $newEntry->addGbaseAttribute('product_type', 'book', 'text');
        $newEntry->addGbaseAttribute('price', '12.99 usd', 'floatUnit');
        $newEntry->addGbaseAttribute('quantity', '10', 'int');

        $createdEntry = $this->gdata->insertGbaseItem($newEntry, true);

        $this->assertEquals($title, $createdEntry->title->text);
        $this->assertEquals($desc, $createdEntry->content->text);
        $this->assertEquals($itemType, $createdEntry->itemType->text);

        $baseAttribute = $createdEntry->getGbaseAttribute('product_type');
        $this->assertEquals('product_type', $baseAttribute[0]->name);
        $this->assertEquals('book', $baseAttribute[0]->text);
        $this->assertEquals('text', $baseAttribute[0]->type);

        $baseAttribute = $createdEntry->getGbaseAttribute('price');
        $this->assertEquals('price', $baseAttribute[0]->name);
        $this->assertEquals('12.99 usd', $baseAttribute[0]->text);
        $this->assertEquals('floatUnit', $baseAttribute[0]->type);

        $baseAttribute = $createdEntry->getGbaseAttribute('quantity');
        $this->assertEquals('quantity', $baseAttribute[0]->name);
        $this->assertEquals('10', $baseAttribute[0]->text);
        $this->assertEquals('int', $baseAttribute[0]->type);
    }

    public function testGetGbaseSnippetFeed() 
    {
        $feed = $this->gdata->getGbaseSnippetFeed();
        $this->assertTrue($feed instanceof Zend_Gdata_Gbase_SnippetFeed);
        foreach ($feed->entries as $entry) {
            $this->assertTrue($entry instanceof Zend_Gdata_Gbase_SnippetEntry);
            $this->assertEquals($entry->getHttpClient(), $feed->getHttpClient());
        }
        
        $query = new Zend_Gdata_Gbase_SnippetQuery();
        $feed = $this->gdata->getGbaseSnippetFeed($query);
        $this->assertTrue($feed instanceof Zend_Gdata_Gbase_SnippetFeed);
        foreach ($feed->entries as $entry) {
            $this->assertTrue($entry instanceof Zend_Gdata_Gbase_SnippetEntry);
            $this->assertEquals($entry->getHttpClient(), $feed->getHttpClient());
        }

        $uri = $query->getQueryUrl();
        $feed = $this->gdata->getGbaseSnippetFeed($uri);
        $this->assertTrue($feed instanceof Zend_Gdata_Gbase_SnippetFeed);
        foreach ($feed->entries as $entry) {
            $this->assertTrue($entry instanceof Zend_Gdata_Gbase_SnippetEntry);
            $this->assertEquals($entry->getHttpClient(), $feed->getHttpClient());
        }
    }

}
