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
 * @package    Zend_Service_Technorati
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Service\Technorati;
use Zend\Service\Technorati;

/**
 * Test helper
 */

/**
 * @see Technorati\ResultSet
 */

/**
 * @see Technorati\SearchResultSet
 */


/**
 * @category   Zend
 * @package    Zend_Service_Technorati
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Service
 * @group      Zend_Service_Technorati
 */
class ResultSetTest extends TestCase
{
    /**
     * Even if Zend_Service_Technorati_ResultSet is an abstract class
     * it's useful to check whether it correctly implements
     * SeekableIterator interface as requested.
     *
     * Any *ResultSet class should be a child of ResultSet
     * thus it's safe to test basic methods on such child class.
     */
    public function setUp()
    {
        $this->ref = new \ReflectionClass('Zend\Service\Technorati\ResultSet');
        $this->dom = self::getTestFileContentAsDom('TestSearchResultSet.xml');
        $this->object = new Technorati\SearchResultSet($this->dom);
        $this->objectRef = new \ReflectionObject($this->object);
    }

    public function testResultSetIsAbstract()
    {
        $this->assertTrue($this->ref->isAbstract());
    }

    public function testResultSetImplementsSeekableIteratorInterface()
    {
        $this->assertTrue($this->ref->isIterateable());
    }

    /**
     * Security check
     */
    public function testResultSetIsParentOfThisObjectClass()
    {
        $this->assertTrue($this->objectRef->isSubclassOf($this->ref));
    }


    public function testResultSetSeek()
    {
        $this->assertEquals(0, $this->object->key());
        $this->object->seek(2);
        $this->assertEquals(2, $this->object->key());
    }

    public function testResultSetSeekThrowsOutOfBoundsExceptionWithInvalidIndex()
    {
        try {
            $this->object->seek(1000);
            $this->fail('Expected OutOfBoundsException not thrown');
        } catch (\OutOfBoundsException $e) {
            $this->assertContains('Illegal index', $e->getMessage());
        }
    }

    public function testResultSetKey()
    {
        $this->assertEquals(0, $this->object->key());
        $this->object->seek(2);
        $this->assertEquals(2, $this->object->key());
        // don't move forward
        $this->assertEquals(2, $this->object->key());
    }

    public function testResultSetNext()
    {
        $this->assertEquals(0, $this->object->key());
        $this->object->next();
        $this->assertEquals(1, $this->object->key());
    }

    public function testResultSetRewind()
    {
        $this->assertEquals(0, $this->object->key());
        $this->object->seek(2);
        $this->assertTrue($this->object->rewind());
        $this->assertEquals(0, $this->object->key());
    }

    public function testResultSetSerialization()
    {
        $this->_testResultSetSerialization($this->object);
    }
}
