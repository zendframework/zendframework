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
require_once 'Zend/Gdata/Gapps/EmailListRecipientQuery.php';

/**
 * @category   Zend
 * @package    Zend_Gdata_Gapps
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Gdata
 * @group      Zend_Gdata_Gapps
 */
class Zend_Gdata_Gapps_EmailListRecipientQueryTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->query = new Zend_Gdata_Gapps_EmailListRecipientQuery();
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
