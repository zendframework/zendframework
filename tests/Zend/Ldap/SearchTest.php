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
 * @package    Zend_Ldap
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Zend_Ldap_OnlineTestCase
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'OnlineTestCase.php';

/**
 * @see Zend_Ldap_Dn
 */
require_once 'Zend/Ldap/Dn.php';
/**
 * @see Zend_Ldap_Filter
 */
require_once 'Zend/Ldap/Filter.php';

/**
 * @category   Zend
 * @package    Zend_Ldap
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Ldap
 */
class Zend_Ldap_SearchTest extends Zend_Ldap_OnlineTestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->_prepareLdapServer();
    }

    protected function tearDown()
    {
        $this->_cleanupLdapServer();
        parent::tearDown();
    }

    public function testGetSingleEntry()
    {
        $dn=$this->_createDn('ou=Test1,');
        $entry=$this->_getLdap()->getEntry($dn);
        $this->assertEquals($dn, $entry["dn"]);
        $this->assertArrayHasKey('ou', $entry);
        $this->assertContains('Test1', $entry['ou']);
        $this->assertEquals(1, count($entry['ou']));
    }

    public function testGetSingleIllegalEntry()
    {
        $dn=$this->_createDn('ou=Test99,');
        $entry=$this->_getLdap()->getEntry($dn);
        $this->assertNull($entry);
    }

    /**
     * @expectedException Zend_Ldap_Exception
     */
    public function testGetSingleIllegalEntryWithException()
    {
        $dn=$this->_createDn('ou=Test99,');
        $entry=$this->_getLdap()->getEntry($dn, array(), true);
    }

    public function testCountBase()
    {
        $dn=$this->_createDn('ou=Node,');
        $count=$this->_getLdap()->count('(objectClass=*)', $dn, Zend_Ldap::SEARCH_SCOPE_BASE);
        $this->assertEquals(1, $count);
    }

    public function testCountOne()
    {
        $dn1=$this->_createDn('ou=Node,');
        $count1=$this->_getLdap()->count('(objectClass=*)', $dn1, Zend_Ldap::SEARCH_SCOPE_ONE);
        $this->assertEquals(2, $count1);
        $dn2=TESTS_ZEND_LDAP_WRITEABLE_SUBTREE;
        $count2=$this->_getLdap()->count('(objectClass=*)', $dn2, Zend_Ldap::SEARCH_SCOPE_ONE);
        $this->assertEquals(6, $count2);
    }

    public function testCountSub()
    {
        $dn1=$this->_createDn('ou=Node,');
        $count1=$this->_getLdap()->count('(objectClass=*)', $dn1, Zend_Ldap::SEARCH_SCOPE_SUB);
        $this->assertEquals(3, $count1);
        $dn2=TESTS_ZEND_LDAP_WRITEABLE_SUBTREE;
        $count2=$this->_getLdap()->count('(objectClass=*)', $dn2, Zend_Ldap::SEARCH_SCOPE_SUB);
        $this->assertEquals(9, $count2);
    }

    public function testResultIteration()
    {
        $items=$this->_getLdap()->search('(objectClass=organizationalUnit)',
            TESTS_ZEND_LDAP_WRITEABLE_SUBTREE, Zend_Ldap::SEARCH_SCOPE_SUB);
        $this->assertEquals(9, $items->count());

        $i=0;
        foreach ($items as $key => $item)
        {
            $this->assertEquals($i, $key);
            $i++;
        }
        $this->assertEquals(9, $i);
        $j=0;
        foreach ($items as $item) { $j++; }
        $this->assertEquals($i, $j);
    }

    public function testSearchNoResult()
    {
        $items=$this->_getLdap()->search('(objectClass=account)', TESTS_ZEND_LDAP_WRITEABLE_SUBTREE,
            Zend_Ldap::SEARCH_SCOPE_SUB);
        $this->assertEquals(0, $items->count());
    }

    public function testSearchEntriesShortcut()
    {
        $entries=$this->_getLdap()->searchEntries('(objectClass=organizationalUnit)',
            TESTS_ZEND_LDAP_WRITEABLE_SUBTREE, Zend_Ldap::SEARCH_SCOPE_SUB);
        $this->assertType("array", $entries);
        $this->assertEquals(9, count($entries));
    }

    /**
     * @expectedException Zend_Ldap_Exception
     */
    public function testIllegalSearch()
    {
        $dn=$this->_createDn('ou=Node2,');
        $items=$this->_getLdap()->search('(objectClass=account)', $dn, Zend_Ldap::SEARCH_SCOPE_SUB);
    }

    public function testSearchNothingGetFirst()
    {
        $entries=$this->_getLdap()->search('(objectClass=account)', TESTS_ZEND_LDAP_WRITEABLE_SUBTREE,
            Zend_Ldap::SEARCH_SCOPE_SUB);
        $this->assertEquals(0, $entries->count());
        $this->assertNull($entries->getFirst());
    }

    public function testSorting()
    {
        $lSorted=array('a', 'b', 'c', 'd', 'e');
        $items=$this->_getLdap()->search('(l=*)', TESTS_ZEND_LDAP_WRITEABLE_SUBTREE,
            Zend_Ldap::SEARCH_SCOPE_SUB, array(), 'l');
        $this->assertEquals(5, $items->count());
        foreach ($items as $key => $item)
        {
            $this->assertEquals($lSorted[$key], $item['l'][0]);
        }
    }

    public function testCountChildren()
    {
        $dn1=$this->_createDn('ou=Node,');
        $count1=$this->_getLdap()->countChildren($dn1);
        $this->assertEquals(2, $count1);
        $dn2=TESTS_ZEND_LDAP_WRITEABLE_SUBTREE;
        $count2=$this->_getLdap()->countChildren($dn2);
        $this->assertEquals(6, $count2);
    }

    public function testExistsDn()
    {
        $dn1=$this->_createDn('ou=Test2,');
        $dn2=$this->_createDn('ou=Test99,');
        $this->assertTrue($this->_getLdap()->exists($dn1));
        $this->assertFalse($this->_getLdap()->exists($dn2));
    }

    public function testSearchWithDnObjectAndFilterObject()
    {
        $dn=Zend_Ldap_Dn::fromString(TESTS_ZEND_LDAP_WRITEABLE_SUBTREE);
        $filter=Zend_Ldap_Filter::equals('objectClass', 'organizationalUnit');

        $items=$this->_getLdap()->search($filter, $dn, Zend_Ldap::SEARCH_SCOPE_SUB);
        $this->assertEquals(9, $items->count());
    }

    public function testCountSubWithDnObjectAndFilterObject()
    {
        $dn1=Zend_Ldap_Dn::fromString($this->_createDn('ou=Node,'));
        $filter=Zend_Ldap_Filter::any('objectClass');

        $count1=$this->_getLdap()->count($filter, $dn1, Zend_Ldap::SEARCH_SCOPE_SUB);
        $this->assertEquals(3, $count1);

        $dn2=Zend_Ldap_Dn::fromString(TESTS_ZEND_LDAP_WRITEABLE_SUBTREE);
        $count2=$this->_getLdap()->count($filter, $dn2, Zend_Ldap::SEARCH_SCOPE_SUB);
        $this->assertEquals(9, $count2);
    }

    public function testCountChildrenWithDnObject()
    {
        $dn1=Zend_Ldap_Dn::fromString($this->_createDn('ou=Node,'));
        $count1=$this->_getLdap()->countChildren($dn1);
        $this->assertEquals(2, $count1);

        $dn2=Zend_Ldap_Dn::fromString(TESTS_ZEND_LDAP_WRITEABLE_SUBTREE);
        $count2=$this->_getLdap()->countChildren($dn2);
        $this->assertEquals(6, $count2);
    }

    public function testExistsDnWithDnObject()
    {
        $dn1=Zend_Ldap_Dn::fromString($this->_createDn('ou=Test2,'));
        $dn2=Zend_Ldap_Dn::fromString($this->_createDn('ou=Test99,'));

        $this->assertTrue($this->_getLdap()->exists($dn1));
        $this->assertFalse($this->_getLdap()->exists($dn2));
    }

    public function testSearchEntriesShortcutWithDnObjectAndFilterObject()
    {
        $dn=Zend_Ldap_Dn::fromString(TESTS_ZEND_LDAP_WRITEABLE_SUBTREE);
        $filter=Zend_Ldap_Filter::equals('objectClass', 'organizationalUnit');

        $entries=$this->_getLdap()->searchEntries($filter, $dn, Zend_Ldap::SEARCH_SCOPE_SUB);
        $this->assertType("array", $entries);
        $this->assertEquals(9, count($entries));
    }

    public function testGetSingleEntryWithDnObject()
    {
        $dn=Zend_Ldap_Dn::fromString($this->_createDn('ou=Test1,'));
        $entry=$this->_getLdap()->getEntry($dn);
        $this->assertEquals($dn->toString(), $entry["dn"]);
    }

    public function testMultipleResultIteration()
    {
        $items=$this->_getLdap()->search('(objectClass=organizationalUnit)',
            TESTS_ZEND_LDAP_WRITEABLE_SUBTREE, Zend_Ldap::SEARCH_SCOPE_SUB);
        $isCount = 9;
        $this->assertEquals($isCount, $items->count());

        $i=0;
        foreach ($items as $key => $item)
        {
            $this->assertEquals($i, $key);
            $i++;
        }
        $this->assertEquals($isCount, $i);
        $i=0;
        foreach ($items as $key => $item)
        {
            $this->assertEquals($i, $key);
            $i++;
        }
        $this->assertEquals($isCount, $i);

        $items->close();
        $i=0;
        foreach ($items as $key => $item)
        {
            $this->assertEquals($i, $key);
            $i++;
        }
        $this->assertEquals($isCount, $i);
        $i=0;
        foreach ($items as $key => $item)
        {
            $this->assertEquals($i, $key);
            $i++;
        }
        $this->assertEquals($isCount, $i);
    }

    /**
     * Test issue reported by Lance Hendrix on
     * http://framework.zend.com/wiki/display/ZFPROP/Zend_Ldap+-+Extended+support+-+Stefan+Gehrig?
     *      focusedCommentId=13107431#comment-13107431
     */
    public function testCallingNextAfterIterationShouldNotThrowException()
    {
        $items = $this->_getLdap()->search('(objectClass=organizationalUnit)',
            TESTS_ZEND_LDAP_WRITEABLE_SUBTREE, Zend_Ldap::SEARCH_SCOPE_SUB);
        foreach ($items as $key => $item) {
            // do nothing - just iterate
        }
        $items->next();
    }

    public function testUnknownCollectionClassThrowsException()
    {
        try {
            $items=$this->_getLdap()->search('(objectClass=organizationalUnit)',
                TESTS_ZEND_LDAP_WRITEABLE_SUBTREE, Zend_Ldap::SEARCH_SCOPE_SUB, array(), null,
                'This_Class_Does_Not_Exist');
            $this->fail('Expected exception not thrown');
        } catch (Zend_Ldap_Exception $zle) {
            $this->assertContains("Class 'This_Class_Does_Not_Exist' can not be found",
                $zle->getMessage());
        }
    }

    public function testCollectionClassNotSubclassingZendLdapCollectionThrowsException()
    {
        try {
            $items=$this->_getLdap()->search('(objectClass=organizationalUnit)',
                TESTS_ZEND_LDAP_WRITEABLE_SUBTREE, Zend_Ldap::SEARCH_SCOPE_SUB, array(), null,
                'Zend_Ldap_SearchTest_CollectionClassNotSubclassingZendLdapCollection');
            $this->fail('Expected exception not thrown');
        } catch (Zend_Ldap_Exception $zle) {
            $this->assertContains(
                "Class 'Zend_Ldap_SearchTest_CollectionClassNotSubclassingZendLdapCollection' must subclass 'Zend_Ldap_Collection'",
                $zle->getMessage());
        }
    }
}

class Zend_Ldap_SearchTest_CollectionClassNotSubclassingZendLdapCollection
{ }
