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

use Zend\GData\GApps\OwnerQuery;

/**
 * @category   Zend
 * @package    Zend_Gdata_Gapps
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Gdata
 * @group      Zend_Gdata_Gapps
 */
class OwnerQueryTest extends \PHPUnit_Framework_TestCase
{

    /** @var OwnerQuery */
    public $query;

    public function setUp()
    {
        $this->query = new OwnerQuery();
    }

    // Test to make sure that the domain accessor methods work and propagate
    // to the query URI.
    public function testCanSetQueryDomain()
    {
        $this->query->setGroupId("something");
        $this->query->setDomain("my.domain.com");
        $this->assertEquals("my.domain.com", $this->query->getDomain());
        $this->assertEquals("https://apps-apis.google.com/a/feeds/group/2.0/my.domain.com/something/owner",
                $this->query->getQueryUrl());

        $this->query->setDomain("hello.world.baz");
        $this->assertEquals("hello.world.baz", $this->query->getDomain());
        $this->assertEquals("https://apps-apis.google.com/a/feeds/group/2.0/hello.world.baz/something/owner",
                $this->query->getQueryUrl());
    }

    // Test to make sure that the groupId accessor methods work and propagate
    // to the query URI.
    public function testCanSetGroupIdProperty()
    {
        $this->query->setDomain("my.domain.com");
        $this->query->setGroupId("foo");
        $this->assertEquals("foo", $this->query->getGroupId());
        $this->assertEquals("https://apps-apis.google.com/a/feeds/group/2.0/my.domain.com/foo/owner",
                $this->query->getQueryUrl());

        $this->query->setGroupId("bar");
        $this->assertEquals("bar", $this->query->getGroupId());
        $this->assertEquals("https://apps-apis.google.com/a/feeds/group/2.0/my.domain.com/bar/owner",
                $this->query->getQueryUrl());
    }

    public function testCanSetOwnerEmailProperty()
    {
        $this->query->setDomain("my.domain.com");
        $this->query->setGroupId("foo");
        $this->query->setOwnerEmail("bar@blah.com");
        $this->assertEquals("bar@blah.com", $this->query->getOwnerEmail());
        $this->assertEquals("https://apps-apis.google.com/a/feeds/group/2.0/my.domain.com/foo/owner/bar@blah.com",
                $this->query->getQueryUrl());

        $this->query->setOwnerEmail('baz@blah.com');
        $this->assertEquals('baz@blah.com', $this->query->getOwnerEmail());
        $this->assertEquals("https://apps-apis.google.com/a/feeds/group/2.0/my.domain.com/foo/owner/baz@blah.com",
                $this->query->getQueryUrl());
    }

}

