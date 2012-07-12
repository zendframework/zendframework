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
class UserQueryTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->query = new \Zend\GData\GApps\UserQuery();
    }

    // Test to make sure that URI generation works
    public function testDefaultQueryURIGeneration()
    {
        $this->query->setDomain("foo.bar.invalid");
        $this->assertEquals("https://apps-apis.google.com/a/feeds/foo.bar.invalid/user/2.0",
                $this->query->getQueryUrl());
    }

    // Test to make sure that the domain accessor methods work and propagate
    // to the query URI.
    public function testCanSetQueryDomain()
    {
        $this->query->setDomain("my.domain.com");
        $this->assertEquals("my.domain.com", $this->query->getDomain());
        $this->assertEquals("https://apps-apis.google.com/a/feeds/my.domain.com/user/2.0",
                $this->query->getQueryUrl());

        $this->query->setDomain("hello.world.baz");
        $this->assertEquals("hello.world.baz", $this->query->getDomain());
        $this->assertEquals("https://apps-apis.google.com/a/feeds/hello.world.baz/user/2.0",
                $this->query->getQueryUrl());
    }

    // Test to make sure that the username accessor methods work and propagate
    // to the query URI.
    public function testCanSetUsernameProperty()
    {
        $this->query->setDomain("my.domain.com");
        $this->query->setUsername("foo");
        $this->assertEquals("foo", $this->query->getUsername());
        $this->assertEquals("https://apps-apis.google.com/a/feeds/my.domain.com/user/2.0/foo",
                $this->query->getQueryUrl());

        $this->query->setUsername("bar");
        $this->assertEquals("bar", $this->query->getUsername());
        $this->assertEquals("https://apps-apis.google.com/a/feeds/my.domain.com/user/2.0/bar",
                $this->query->getQueryUrl());
    }

    // Test to make sure that the startUsername accessor methods work and
    // propagate to the query URI.
    public function testCanSetStartUsernameProperty()
    {
        $this->query->setDomain("my.domain.com");
        $this->query->setStartUsername("foo");
        $this->assertEquals("foo", $this->query->getStartUsername());
        $this->assertEquals("https://apps-apis.google.com/a/feeds/my.domain.com/user/2.0?startUsername=foo",
                $this->query->getQueryUrl());

        $this->query->setStartUsername(null);
        $this->assertEquals(null, $this->query->getStartUsername());
        $this->assertEquals("https://apps-apis.google.com/a/feeds/my.domain.com/user/2.0",
                $this->query->getQueryUrl());
    }

}
