<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_GData
 */

namespace ZendTest\GData\Books;

/**
 * @category   Zend
 * @package    Zend_GData_Books
 * @subpackage UnitTests
 * @group      Zend_GData
 * @group      Zend_GData_Books
 */
class CollectionFeedTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->gdata = new \Zend\GData\Books\CollectionFeed();
    }

    public function testCollectionFeed()
    {
        $this->assertTrue(true);
    }

}
