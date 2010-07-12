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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id $
 */

require_once 'Zend/Gdata/Gapps.php';
require_once 'Zend/Gdata/Gapps/MemberQuery.php';

/**
 * @category   Zend
 * @package    Zend_Gdata_Gapps
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Gdata
 * @group      Zend_Gdata_Gapps
 */
class Zend_Gdata_Gapps_MemberQueryTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->query = new Zend_Gdata_Gapps_MemberQuery();
    }

    // Test to make sure that the domain accessor methods work and propagate
    // to the query URI.
    public function testCanSetQueryDomain()
    {
        $this->query->setGroupId("something");
        $this->query->setDomain("my.domain.com");
        $this->assertEquals("my.domain.com", $this->query->getDomain());
        $this->assertEquals("https://apps-apis.google.com/a/feeds/group/2.0/my.domain.com/something/member",
                $this->query->getQueryUrl());

        $this->query->setDomain("hello.world.baz");
        $this->assertEquals("hello.world.baz", $this->query->getDomain());
        $this->assertEquals("https://apps-apis.google.com/a/feeds/group/2.0/hello.world.baz/something/member",
                $this->query->getQueryUrl());
    }

    // Test to make sure that the groupId accessor methods work and propagate
    // to the query URI.
    public function testCanSetGroupIdProperty()
    {
        $this->query->setDomain("my.domain.com");
        $this->query->setGroupId("foo");
        $this->assertEquals("foo", $this->query->getGroupId());
        $this->assertEquals("https://apps-apis.google.com/a/feeds/group/2.0/my.domain.com/foo/member",
                $this->query->getQueryUrl());

        $this->query->setGroupId("bar");
        $this->assertEquals("bar", $this->query->getGroupId());
        $this->assertEquals("https://apps-apis.google.com/a/feeds/group/2.0/my.domain.com/bar/member",
                $this->query->getQueryUrl());
    }

    // Test to make sure that the memberId accessor methods work and propagate
    // to the query URI.
    public function testCanSetMemberIdProperty()
    {
        $this->query->setDomain("my.domain.com");
        $this->query->setGroupId("foo");
        $this->query->setMemberId("bar");
        $this->assertEquals("bar", $this->query->getMemberId());
        $this->assertEquals("https://apps-apis.google.com/a/feeds/group/2.0/my.domain.com/foo/member/bar",
                $this->query->getQueryUrl());

        $this->query->setGroupId("baz");
        $this->query->setMemberId(null);
        $this->assertEquals(null, $this->query->getMemberId());
        $this->assertEquals("https://apps-apis.google.com/a/feeds/group/2.0/my.domain.com/baz/member",
                $this->query->getQueryUrl());
    }

    public function testCanSetStartMemberIdProperty()
    {
        $this->query->setDomain("my.domain.com");
        $this->query->setGroupId("foo");
        $this->query->setStartMemberId("bar");
        $this->assertEquals("bar", $this->query->getStartMemberId());
        $this->assertEquals("https://apps-apis.google.com/a/feeds/group/2.0/my.domain.com/foo/member?start=bar",
                $this->query->getQueryUrl());

        $this->query->setStartMemberId(null);
        $this->assertEquals(null, $this->query->getStartMemberId());
        $this->assertEquals("https://apps-apis.google.com/a/feeds/group/2.0/my.domain.com/foo/member",
                $this->query->getQueryUrl());
    }

}

