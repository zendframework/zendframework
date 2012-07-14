<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

namespace ZendTest\Service\Delicious;

use Zend\Service\Delicious;

/**
 * @category   Zend_Service
 * @package    Zend_Service_Delicious
 * @subpackage UnitTests
 * @group      Zend_Service
 * @group      Zend_Service_Delicious
 */
class SimplePostTest extends \PHPUnit_Framework_TestCase
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
            $simplePost = new Delicious\SimplePost($post);
            $this->fail('Expected Zend_Service_Delicious_Exception not thrown');
        } catch (Delicious\Exception $e) {
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
            $simplePost = new Delicious\SimplePost($post);
            $this->fail('Expected Zend_Service_Delicious_Exception not thrown');
        } catch (Delicious\Exception $e) {
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
        $simplePost = new Delicious\SimplePost($post);
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
        $simplePost = new Delicious\SimplePost($post);
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
        $simplePost = new Delicious\SimplePost($post);
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
        $simplePost = new Delicious\SimplePost($post);
        $this->assertEquals(
            $tags,
            $result = $simplePost->getTags(),
            "Expected getTags() to return '$tags'; got '$result' instead"
            );
    }
}
