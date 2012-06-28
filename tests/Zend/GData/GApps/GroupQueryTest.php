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
 * @package    Zend_Gdata_Gapps
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\GData\GApps;

use Zend\GData\GApps\GroupQuery;

/**
 * @category   Zend
 * @package    Zend_Gdata_Gapps
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Gdata
 * @group      Zend_Gdata_Gapps
 */
class GroupQueryTest extends \PHPUnit_Framework_TestCase
{

    /** @var GroupQuery */
    public $query;

    public function setUp()
    {
        $this->query = new GroupQuery();
    }

    // Test to make sure that URI generation works
    public function testDefaultQueryURIGeneration()
    {
        $this->query->setDomain("foo.bar.invalid");
        $this->assertEquals("https://apps-apis.google.com/a/feeds/group/2.0/foo.bar.invalid",
                $this->query->getQueryUrl());
    }

    // Test to make sure that the domain accessor methods work and propagate
    // to the query URI.
    public function testCanSetQueryDomain()
    {
        $this->query->setDomain("my.domain.com");
        $this->assertEquals("my.domain.com", $this->query->getDomain());
        $this->assertEquals("https://apps-apis.google.com/a/feeds/group/2.0/my.domain.com",
                $this->query->getQueryUrl());

        $this->query->setDomain("hello.world.baz");
        $this->assertEquals("hello.world.baz", $this->query->getDomain());
        $this->assertEquals("https://apps-apis.google.com/a/feeds/group/2.0/hello.world.baz",
                $this->query->getQueryUrl());
    }

    // Test to make sure that the groupId accessor methods work and propagate
    // to the query URI.
    public function testCanSetGroupIdProperty()
    {
        $this->query->setDomain("my.domain.com");
        $this->query->setGroupId("foo");
        $this->assertEquals("foo", $this->query->getGroupId());
        $this->assertEquals("https://apps-apis.google.com/a/feeds/group/2.0/my.domain.com/foo",
                $this->query->getQueryUrl());

        $this->query->setGroupId("bar");
        $this->assertEquals("bar", $this->query->getGroupId());
        $this->assertEquals("https://apps-apis.google.com/a/feeds/group/2.0/my.domain.com/bar",
                $this->query->getQueryUrl());
    }

    // Test to make sure that the member accessor methods work and propagate
    // to the query URI.
    public function testCanSetMemberProperty()
    {
        $this->query->setDomain("my.domain.com");
        $this->query->setMember("bar@qux.com");
        $this->assertEquals("bar@qux.com", $this->query->getMember());
        $this->assertEquals("https://apps-apis.google.com/a/feeds/group/2.0/my.domain.com/?member=bar%40qux.com",
                $this->query->getQueryUrl());

        $this->query->setMember(null);
        $this->assertEquals(null, $this->query->getMember());
        $this->assertEquals("https://apps-apis.google.com/a/feeds/group/2.0/my.domain.com",
                $this->query->getQueryUrl());
    }

    // Test to make sure that the startUsername accessor methods work and
    // propagate to the query URI.
    public function testCanSetStartGroupIdProperty()
    {
        $this->query->setDomain("my.domain.com");
        $this->query->setStartGroupId("foo");
        $this->assertEquals("foo", $this->query->getStartGroupId());
        $this->assertEquals("https://apps-apis.google.com/a/feeds/group/2.0/my.domain.com?start=foo",
                $this->query->getQueryUrl());

        $this->query->setStartGroupId(null);
        $this->assertEquals(null, $this->query->getStartGroupId());
        $this->assertEquals("https://apps-apis.google.com/a/feeds/group/2.0/my.domain.com",
                $this->query->getQueryUrl());
    }

    public function testCanSetDirectOnlyProperty()
    {
        $this->query->setDomain("my.domain.com");
        $this->query->setMember("bar@qux.com");
        $this->query->setDirectOnly(true);
        $this->assertEquals(true, $this->query->getDirectOnly());
        $expected_url  = "https://apps-apis.google.com/a/feeds/group/2.0/my.domain.com/";
        $expected_url .= "?member=bar%40qux.com&directOnly=true";
        $this->assertEquals($expected_url, $this->query->getQueryUrl());

        $this->query->setDirectOnly(false);
        $this->assertEquals(false, $this->query->getDirectOnly());
        $expected_url  = "https://apps-apis.google.com/a/feeds/group/2.0/my.domain.com/";
        $expected_url .= "?member=bar%40qux.com&directOnly=false";
        $this->assertEquals($expected_url, $this->query->getQueryUrl());

        $this->query->setDirectOnly(null);
        $this->assertEquals(null, $this->query->getDirectOnly());
        $expected_url  = "https://apps-apis.google.com/a/feeds/group/2.0/my.domain.com/";
        $expected_url .= "?member=bar%40qux.com";
        $this->assertEquals($expected_url, $this->query->getQueryUrl());
    }

    // Test to make sure that all parameters can be set simultaneously with no
    // ill effects.
    public function testCanSetAllParameters()
    {
        $this->query->setDomain("my.domain.com");
        $this->query->setGroupId("foo");
        $this->query->setMember("bar@qux.com");
        $this->query->setStartGroupId("wibble");
        $this->query->setDirectOnly(true);
        $this->assertEquals("foo", $this->query->getGroupId());
        $this->assertEquals("bar@qux.com", $this->query->getMember());
        $this->assertEquals("wibble", $this->query->getStartGroupId());
        $this->assertEquals(true, $this->query->getDirectOnly());
        $expected_url  = "https://apps-apis.google.com/a/feeds/group/2.0/my.domain.com/";
        $expected_url .= "foo/?member=bar%40qux.com&start=wibble&directOnly=true";
        $this->assertEquals($expected_url, $this->query->getQueryUrl());

        $this->query->setMember("baz@blah.com");
        $this->query->setGroupId("xyzzy");
        $this->query->setStartGroupId("woof");
        $this->query->setDirectOnly(false);
        $this->assertEquals("xyzzy", $this->query->getGroupId());
        $this->assertEquals("baz@blah.com", $this->query->getMember());
        $this->assertEquals("woof", $this->query->getStartGroupId());
        $this->assertEquals(false, $this->query->getDirectOnly());
        $expected_url  = "https://apps-apis.google.com/a/feeds/group/2.0/my.domain.com/";
        $expected_url .= "xyzzy/?member=baz%40blah.com&start=woof&directOnly=false";
        $this->assertEquals($expected_url, $this->query->getQueryUrl());
    }

}

