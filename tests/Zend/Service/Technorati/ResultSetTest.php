<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

namespace ZendTest\Service\Technorati;

/**
 * @category   Zend
 * @package    Zend_Service_Technorati
 * @subpackage UnitTests
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
     * Any *AbstractResultSet class should be a child of AbstractResultSet
     * thus it's safe to test basic methods on such child class.
     */
    public function setUp()
    {
        $this->ref = new \ReflectionClass('Zend\Service\Technorati\AbstractResultSet');
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
