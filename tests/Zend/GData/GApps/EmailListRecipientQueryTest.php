<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_GData
 */

namespace ZendTest\GData\GApps;

/**
 * @category   Zend
 * @package    Zend_GData_GApps
 * @subpackage UnitTests
 * @group      Zend_GData
 * @group      Zend_GData_GApps
 */
class EmailListRecipientQueryTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->query = new \Zend\GData\GApps\EmailListRecipientQuery();
    }

    // Test to make sure that the domain accessor methods work and propagate
    // to the query URI.
    public function testCanSetQueryDomain()
    {
        $this->query->setEmailListName("something");
        $this->query->setDomain("my.domain.com");
        $this->assertEquals("my.domain.com", $this->query->getDomain());
        $this->assertEquals("https://apps-apis.google.com/a/feeds/my.domain.com/emailList/2.0/something/recipient/",
                $this->query->getQueryUrl());

        $this->query->setDomain("hello.world.baz");
        $this->assertEquals("hello.world.baz", $this->query->getDomain());
        $this->assertEquals("https://apps-apis.google.com/a/feeds/hello.world.baz/emailList/2.0/something/recipient/",
                $this->query->getQueryUrl());
    }

    // Test to make sure that the emailListName accessor methods work and propagate
    // to the query URI.
    public function testCanSetEmailListNameProperty()
    {
        $this->query->setDomain("my.domain.com");
        $this->query->setEmailListName("foo");
        $this->assertEquals("foo", $this->query->getEmailListName());
        $this->assertEquals("https://apps-apis.google.com/a/feeds/my.domain.com/emailList/2.0/foo/recipient/",
                $this->query->getQueryUrl());

        $this->query->setEmailListName("bar");
        $this->assertEquals("bar", $this->query->getEmailListName());
        $this->assertEquals("https://apps-apis.google.com/a/feeds/my.domain.com/emailList/2.0/bar/recipient/",
                $this->query->getQueryUrl());
    }

    public function testCanSetStartRecipientProperty()
    {
        $this->query->setDomain("my.domain.com");
        $this->query->setEmailListName("foo");
        $this->query->setStartRecipient("bar");
        $this->assertEquals("bar", $this->query->getStartRecipient());
        $this->assertEquals("https://apps-apis.google.com/a/feeds/my.domain.com/emailList/2.0/foo/recipient/?startRecipient=bar",
                $this->query->getQueryUrl());

        $this->query->setStartRecipient(null);
        $this->assertEquals(null, $this->query->getStartRecipient());
        $this->assertEquals("https://apps-apis.google.com/a/feeds/my.domain.com/emailList/2.0/foo/recipient/",
                $this->query->getQueryUrl());
    }

}
