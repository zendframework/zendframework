<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Mail
 */

namespace ZendTest\Mail;

use Zend\Mail\Address;
use Zend\Mail\AddressList;

/**
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage UnitTests
 * @group      Zend_Mail
 */
class AddressListTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->list = new AddressList();
    }

    public function testImplementsCountable()
    {
        $this->assertInstanceOf('Countable', $this->list);
    }

    public function testIsEmptyByDefault()
    {
        $this->assertEquals(0, count($this->list));
    }

    public function testAddingEmailsIncreasesCount()
    {
        $this->list->add('zf-devteam@zend.com');
        $this->assertEquals(1, count($this->list));
    }

    public function testImplementsTraversable()
    {
        $this->assertInstanceOf('Traversable', $this->list);
    }

    public function testHasReturnsFalseWhenAddressNotInList()
    {
        $this->assertFalse($this->list->has('foo@example.com'));
    }

    public function testHasReturnsTrueWhenAddressInList()
    {
        $this->list->add('zf-devteam@zend.com');
        $this->assertTrue($this->list->has('zf-devteam@zend.com'));
    }

    public function testGetReturnsFalseWhenEmailNotFound()
    {
        $this->assertFalse($this->list->get('foo@example.com'));
    }

    public function testGetReturnsAddressObjectWhenEmailFound()
    {
        $this->list->add('zf-devteam@zend.com');
        $address = $this->list->get('zf-devteam@zend.com');
        $this->assertInstanceOf('Zend\Mail\Address', $address);
        $this->assertEquals('zf-devteam@zend.com', $address->getEmail());
    }

    public function testCanAddAddressWithName()
    {
        $this->list->add('zf-devteam@zend.com', 'ZF DevTeam');
        $address = $this->list->get('zf-devteam@zend.com');
        $this->assertInstanceOf('Zend\Mail\Address', $address);
        $this->assertEquals('zf-devteam@zend.com', $address->getEmail());
        $this->assertEquals('ZF DevTeam', $address->getName());
    }

    public function testCanAddManyAddressesAtOnce()
    {
        $addresses = array(
            'zf-devteam@zend.com',
            'zf-contributors@lists.zend.com' => 'ZF Contributors List',
            new Address('fw-announce@lists.zend.com', 'ZF Announce List'),
        );
        $this->list->addMany($addresses);
        $this->assertEquals(3, count($this->list));
        $this->assertTrue($this->list->has('zf-devteam@zend.com'));
        $this->assertTrue($this->list->has('zf-contributors@lists.zend.com'));
        $this->assertTrue($this->list->has('fw-announce@lists.zend.com'));
    }

    public function testDoesNotStoreDuplicatesAndFirstWins()
    {
        $addresses = array(
            'zf-devteam@zend.com',
            new Address('zf-devteam@zend.com', 'ZF DevTeam'),
        );
        $this->list->addMany($addresses);
        $this->assertEquals(1, count($this->list));
        $this->assertTrue($this->list->has('zf-devteam@zend.com'));
        $address = $this->list->get('zf-devteam@zend.com');
        $this->assertNull($address->getName());
    }
}
