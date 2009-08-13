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
 * @see Zend_Feed_Entry_Atom
 */
require_once 'Zend/Feed/Entry/Atom.php';

/**
 * @see Zend_Http_Client_File
 */
require_once 'Zend/Http/Client.php';

/**
 * @category   Zend
 * @package    Zend_Feed
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Feed
 */
class Zend_Feed_AtomPublishingTest extends PHPUnit_Framework_TestCase
{
    protected $_uri;

    public function setUp()
    {
        $this->_uri = 'http://fubar.com/myFeed';
    }

    public function tearDown()
    {
        Zend_Feed::setHttpClient(new Zend_Http_Client());
    }

    public function testPost()
    {
        Zend_Feed::setHttpClient(new TestClient());

        $entry = new Zend_Feed_Entry_Atom();

        /* Give the entry its initial values. */
        $entry->title = 'Entry 1';
        $entry->content = '1.1';
        $entry->content['type'] = 'text';

        /* Do the initial post. The base feed URI is the same as the
         * POST URI, so just supply save() with that. */
        $entry->save($this->_uri);

        /* $entry will be filled in with any elements returned by the
         * server (id, updated, link rel="edit", etc). */
        $this->assertEquals('1', $entry->id(), 'Expected id to be 1');
        $this->assertEquals('Entry 1', $entry->title(), 'Expected title to be "Entry 1"');
        $this->assertEquals('1.1', $entry->content(), 'Expected content to be "1.1"');
        $this->assertEquals('text', $entry->content['type'], 'Expected content/type to be "text"');
        $this->assertEquals('2005-05-23T16:26:00-08:00', $entry->updated(), 'Expected updated date of 2005-05-23T16:26:00-08:00');
        $this->assertEquals('http://fubar.com/myFeed/1/1/', $entry->link('edit'), 'Expected edit URI of http://fubar.com/myFeed/1/1/');
    }

    public function testEdit()
    {
        Zend_Feed::setHttpClient(new TestClient());
        $contents = file_get_contents(dirname(__FILE__) .  '/_files/AtomPublishingTest-before-update.xml');

        /* The base feed URI is the same as the POST URI, so just supply the
         * Zend_Feed_Entry_Atom object with that. */
        $entry = new Zend_Feed_Entry_Atom($this->_uri, $contents);

        /* Initial state. */
        $this->assertEquals('2005-05-23T16:26:00-08:00', $entry->updated(), 'Initial state of updated timestamp does not match');
        $this->assertEquals('http://fubar.com/myFeed/1/1/', $entry->link('edit'), 'Initial state of edit link does not match');

        /* Just change the entry's properties directly. */
        $entry->content = '1.2';

        /* Then save the changes. */
        $entry->save();

        /* New state. */
        $this->assertEquals('1.2', $entry->content(), 'Content change did not stick');
        $this->assertEquals('2005-05-23T16:27:00-08:00', $entry->updated(), 'New updated link is not correct');
        $this->assertEquals('http://fubar.com/myFeed/1/2/', $entry->link('edit'), 'New edit link is not correct');
    }
}

/**
 * A test wrapper around Zend_Http_Client, not actually performing
 * the request.
 *
 */
class TestClient extends Zend_Http_Client
{
    public function request($method = null)
    {
        $code = 400;
        $body = '';

        switch ($method) {
            case self::POST:
                $code = 201;
                $body = file_get_contents(dirname(__FILE__) . '/_files/AtomPublishingTest-created-entry.xml');
                break;

            case self::PUT:
                $doc1 = new DOMDocument();
                $doc1->load(dirname(__FILE__) . '/_files/AtomPublishingTest-expected-update.xml');
                $doc2 = new DOMDocument();
                $doc2->loadXML($this->raw_post_data);
                if ($doc1->saveXml() == $doc2->saveXml()) {
                    $code = 200;
                    $body = file_get_contents(dirname(__FILE__) . '/_files/AtomPublishingTest-updated-entry.xml');
                }
                break;

            default:
                break;
        }

        return new Zend_Http_Response($code, array(), $body);
    }
}
