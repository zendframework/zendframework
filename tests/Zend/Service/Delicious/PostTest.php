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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Service\Delicious;
use \Zend\Service\Delicious\Delicious as DeliciousClient,
    \Zend\Service\Delicious,
    \Zend\Service\Delicious\Post,
    \Zend\Date\Date;

/**
 * Test helper
 */

/**
 * @see Zend_Service_Delicious
 */

/**
 * @see Zend_Service_Delicious_Post
 */


/**
 * @category   Zend_Service
 * @package    Zend_Service_Delicious
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Service
 * @group      Zend_Service_Delicious
 */
class PostTest extends \PHPUnit_Framework_TestCase
{
    const UNAME = 'zfTestUser';
    const PASS  = 'zfuser';

    /**
     * Service consumer object
     *
     * @var Zend_Service_Delicious
     */
    protected $_delicious;

    /**
     * Post object
     *
     * @var Zend_Service_Delicious_Post
     */
    protected $_post;

    /**
     * Creates an instance of Zend_Service_Delicious for each test method
     *
     * @return void
     */
    public function setUp()
    {
        $this->_delicious = new DeliciousClient(self::UNAME, self::PASS);

        $values = array(
            'title' => 'anything',
            'url'   => 'anything'
            );
        $this->_post = new Post($this->_delicious, $values);
    }

    /**
     * Ensures that the constructor throws an exception when the title is missing from the values
     *
     * @return void
     */
    public function testConstructExceptionValuesTitleMissing()
    {
        try {
            $post = new Post($this->_delicious, array('url' => 'anything'));
            $this->fail('Expected \Zend\Service\Delicious\Exception not thrown');
        } catch (Delicious\Exception $e) {
            $this->assertContains("'url' and 'title'", $e->getMessage());
        }
    }

    /**
     * Ensures that the constructor throws an exception when the URL is missing from the values
     *
     * @return void
     */
    public function testConstructExceptionValuesUrlMissing()
    {
        try {
            $post = new Post($this->_delicious, array('title' => 'anything'));
            $this->fail('Expected \Zend\Service\Delicious\Exception not thrown');
        } catch (Delicious\Exception $e) {
            $this->assertContains("'url' and 'title'", $e->getMessage());
        }
    }

    /**
     * Ensures that the constructor throws an exception when the date value is not an instance of Zend_Date
     *
     * @return void
     */
    public function testConstructExceptionValuesDateInvalid()
    {
        $values = array(
            'title' => 'anything',
            'url'   => 'anything',
            'date'  => 'invalid'
            );
        try {
            $post = new Post($this->_delicious, $values);
            $this->fail('Expected \Zend\Service\Delicious\Exception not thrown');
        } catch (Delicious\Exception $e) {
            $this->assertContains('instance of \Zend\Date\Date', $e->getMessage());
        }
    }

    /**
     * Ensures that setTitle() provides a fluent interface
     *
     * @return void
     */
    public function testSetTitleFluent()
    {
        $this->assertSame($this->_post, $this->_post->setTitle('something'));
    }

    /**
     * Ensures that setNotes() provides a fluent interface
     *
     * @return void
     */
    public function testSetNotesFluent()
    {
        $this->assertSame($this->_post, $this->_post->setNotes('something'));
    }

    /**
     * Ensures that setTags() provides a fluent interface
     *
     * @return void
     */
    public function testSetTagsFluent()
    {
        $this->assertSame($this->_post, $this->_post->setTags(array('something')));
    }

    /**
     * Ensures that addTag() provides a fluent interface
     *
     * @return void
     */
    public function testAddTagFluent()
    {
        $this->assertSame($this->_post, $this->_post->addTag('another'));
    }

    /**
     * Ensures that removeTag() provides a fluent interface
     *
     * @return void
     */
    public function testRemoveTagFluent()
    {
        $this->assertSame($this->_post, $this->_post->removeTag('missing'));
    }

    /**
     * Ensures that getDate() provides expected behavior
     *
     * @return void
     */
    public function testGetDate()
    {
        $this->assertNull($this->_post->getDate());
    }

    /**
     * Ensures that getOthers() provides expected behavior
     *
     * @return void
     */
    public function testGetOthers()
    {
        $this->assertNull($this->_post->getOthers());
    }

    /**
     * Ensures that getHash() provides expected behavior
     *
     * @return void
     */
    public function testGetHash()
    {
        $this->assertNull($this->_post->getHash());
    }

    /**
     * Ensures that getShared() provides expected behavior
     *
     * @return void
     */
    public function testGetShared()
    {
        $this->assertTrue($this->_post->getShared());
    }

    /**
     * Ensures that setShared() provides a fluent interface
     *
     * @return void
     */
    public function testSetSharedFluent()
    {
        $this->assertSame($this->_post, $this->_post->setShared(true));
    }
}
