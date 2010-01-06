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
 * @package    Zend_Service_Delicious
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../../TestHelper.php';

/**
 * @see Zend_Service_Delicious_SimplePost
 */
require_once 'Zend/Service/Delicious/SimplePost.php';


/**
 * @category   Zend_Service
 * @package    Zend_Service_Delicious
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Service
 * @group      Zend_Service_Delicious
 */
class Zend_Service_Delicious_SimplePostTest extends PHPUnit_Framework_TestCase
{
    /**
     * Ensures that the constructor throws an exception when the title is missing
     *
     * @return void
     */
    public function testConstructExceptionTitleMissing()
    {
        $post = array('u' => 'anything');
        try {
            $simplePost = new Zend_Service_Delicious_SimplePost($post);
            $this->fail('Expected Zend_Service_Delicious_Exception not thrown');
        } catch (Zend_Service_Delicious_Exception $e) {
            $this->assertContains('Title and URL', $e->getMessage());
        }
    }

    /**
     * Ensures that the constructor throws an exception when the URL is missing
     *
     * @return void
     */
    public function testConstructExceptionUrlMissing()
    {
        $post = array('d' => 'anything');
        try {
            $simplePost = new Zend_Service_Delicious_SimplePost($post);
            $this->fail('Expected Zend_Service_Delicious_Exception not thrown');
        } catch (Zend_Service_Delicious_Exception $e) {
            $this->assertContains('Title and URL', $e->getMessage());
        }
    }

    /**
     * Ensures that getUrl() behaves as expected
     *
     * @return void
     */
    public function testGetUrl()
    {
        $url  = 'something';
        $post = array(
            'd' => 'anything',
            'u' => $url
            );
        $simplePost = new Zend_Service_Delicious_SimplePost($post);
        $this->assertEquals(
            $url,
            $result = $simplePost->getUrl(),
            "Expected getUrl() to return '$url'; got '$result' instead"
            );
    }

    /**
     * Ensures that getTitle() behaves as expected
     *
     * @return void
     */
    public function testGetTitle()
    {
        $title  = 'something';
        $post   = array(
            'd' => $title,
            'u' => 'anything'
            );
        $simplePost = new Zend_Service_Delicious_SimplePost($post);
        $this->assertEquals(
            $title,
            $result = $simplePost->getTitle(),
            "Expected getTitle() to return '$title'; got '$result' instead"
            );
    }

    /**
     * Ensures that getNotes() behaves as expected
     *
     * @return void
     */
    public function testGetNotes()
    {
        $notes  = 'something';
        $post   = array(
            'd' => 'anything',
            'u' => 'anything',
            'n' => $notes
            );
        $simplePost = new Zend_Service_Delicious_SimplePost($post);
        $this->assertEquals(
            $notes,
            $result = $simplePost->getNotes(),
            "Expected getNotes() to return '$notes'; got '$result' instead"
            );
    }

    /**
     * Ensures that getTags() behaves as expected
     *
     * @return void
     */
    public function testGetTags()
    {
        $tags  = 'something';
        $post  = array(
            'd' => 'anything',
            'u' => 'anything',
            't' => $tags
            );
        $simplePost = new Zend_Service_Delicious_SimplePost($post);
        $this->assertEquals(
            $tags,
            $result = $simplePost->getTags(),
            "Expected getTags() to return '$tags'; got '$result' instead"
            );
    }
}
