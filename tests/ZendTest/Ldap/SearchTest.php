<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Ldap
 */

namespace ZendTest\Ldap;

use Zend\Ldap;
use Zend\Ldap\Collection;
use Zend\Ldap\Exception;

/**
 * @category   Zend
 * @package    Zend_Ldap
 * @subpackage UnitTests
 * @group      Zend_Ldap
 */
class SearchTest extends AbstractOnlineTestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->prepareLDAPServer();
    }

    protected function tearDown()
    {
        $this->cleanupLDAPServer();
        parent::tearDown();
    }

    public function testGetSingleEntry()
    {
        $dn    = $this->createDn('ou=Test1,');
        $entry = $this->getLDAP()->getEntry($dn);
        $this->assertEquals($dn, $entry["dn"]);
        $this->assertArrayHasKey('ou', $entry);
        $this->assertContains('Test1', $entry['ou']);
        $this->assertEquals(1, count($entry['ou']));
    }

    public function testGetSingleIllegalEntry()
    {
        $dn    = $this->createDn('ou=Test99,');
        $entry = $this->getLDAP()->getEntry($dn);
        $this->assertNull($entry);
    }

    /**
     * @expectedException Zend\Ldap\Exception\LdapException
     */
    public function testGetSingleIllegalEntryWithException()
    {
        $dn    = $this->createDn('ou=Test99,');
        $entry = $this->getLDAP()->getEntry($dn, array(), true);
    }

    public function testCountBase()
    {
        $dn    = $this->createDn('ou=Node,');
        $count = $this->getLDAP()->count('(objectClass=*)', $dn, Ldap\Ldap::SEARCH_SCOPE_BASE);
        $this->assertEquals(1, $count);
    }

    public function testCountOne()
    {
        $dn1    = $this->createDn('ou=Node,');
        $count1 = $this->getLDAP()->count('(objectClass=*)', $dn1, Ldap\Ldap::SEARCH_SCOPE_ONE);
        $this->assertEquals(2, $count1);
        $dn2    = TESTS_ZEND_LDAP_WRITEABLE_SUBTREE;
        $count2 = $this->getLDAP()->count('(objectClass=*)', $dn2, Ldap\Ldap::SEARCH_SCOPE_ONE);
        $this->assertEquals(6, $count2);
    }

    public function testCountSub()
    {
        $dn1    = $this->createDn('ou=Node,');
        $count1 = $this->getLDAP()->count('(objectClass=*)', $dn1, Ldap\Ldap::SEARCH_SCOPE_SUB);
        $this->assertEquals(3, $count1);
        $dn2    = TESTS_ZEND_LDAP_WRITEABLE_SUBTREE;
        $count2 = $this->getLDAP()->count('(objectClass=*)', $dn2, Ldap\Ldap::SEARCH_SCOPE_SUB);
        $this->assertEquals(9, $count2);
    }

    public function testResultIteration()
    {
        $items = $this->getLDAP()->search('(objectClass=organizationalUnit)',
            TESTS_ZEND_LDAP_WRITEABLE_SUBTREE, Ldap\Ldap::SEARCH_SCOPE_SUB
        );
        $this->assertEquals(9, $items->count());
        $this->assertEquals(9, count($items));

        $i = 0;
        foreach ($items as $key => $item) {
            $this->assertEquals($i, $key);
            $i++;
        }
        $this->assertEquals(9, $i);
        $j = 0;
        foreach ($items as $item) {
            $j++;
        }
        $this->assertEquals($i, $j);
    }

    public function testSearchNoResult()
    {
        $items = $this->getLDAP()->search('(objectClass=account)', TESTS_ZEND_LDAP_WRITEABLE_SUBTREE,
            Ldap\Ldap::SEARCH_SCOPE_SUB
        );
        $this->assertEquals(0, $items->count());
    }

    public function testSearchEntriesShortcut()
    {
        $entries = $this->getLDAP()->searchEntries('(objectClass=organizationalUnit)',
            TESTS_ZEND_LDAP_WRITEABLE_SUBTREE, Ldap\Ldap::SEARCH_SCOPE_SUB
        );
        $this->assertInternalType("array", $entries);
        $this->assertEquals(9, count($entries));
    }

    /**
     * @expectedException Zend\Ldap\Exception\LdapException
     */
    public function testIllegalSearch()
    {
        $dn    = $this->createDn('ou=Node2,');
        $items = $this->getLDAP()->search('(objectClass=account)', $dn, Ldap\Ldap::SEARCH_SCOPE_SUB);
    }

    public function testSearchNothingGetFirst()
    {
        $entries = $this->getLDAP()->search('(objectClass=account)', TESTS_ZEND_LDAP_WRITEABLE_SUBTREE,
            Ldap\Ldap::SEARCH_SCOPE_SUB
        );
        $this->assertEquals(0, $entries->count());
        $this->assertNull($entries->getFirst());
    }

    public function testSorting()
    {
        $lSorted = array('a', 'b', 'c', 'd', 'e');
        $items   = $this->getLDAP()->search('(l=*)', TESTS_ZEND_LDAP_WRITEABLE_SUBTREE,
            Ldap\Ldap::SEARCH_SCOPE_SUB, array(), 'l'
        );
        $this->assertEquals(5, $items->count());
        foreach ($items as $key => $item) {
            $this->assertEquals($lSorted[$key], $item['l'][0]);
        }
    }

    public function testCountChildren()
    {
        $dn1    = $this->createDn('ou=Node,');
        $count1 = $this->getLDAP()->countChildren($dn1);
        $this->assertEquals(2, $count1);
        $dn2    = TESTS_ZEND_LDAP_WRITEABLE_SUBTREE;
        $count2 = $this->getLDAP()->countChildren($dn2);
        $this->assertEquals(6, $count2);
    }

    public function testExistsDn()
    {
        $dn1 = $this->createDn('ou=Test2,');
        $dn2 = $this->createDn('ou=Test99,');
        $this->assertTrue($this->getLDAP()->exists($dn1));
        $this->assertFalse($this->getLDAP()->exists($dn2));
    }

    public function testSearchWithDnObjectAndFilterObject()
    {
        $dn     = Ldap\Dn::fromString(TESTS_ZEND_LDAP_WRITEABLE_SUBTREE);
        $filter = Ldap\Filter::equals('objectClass', 'organizationalUnit');

        $items = $this->getLDAP()->search($filter, $dn, Ldap\Ldap::SEARCH_SCOPE_SUB);
        $this->assertEquals(9, $items->count());
    }

    public function testCountSubWithDnObjectAndFilterObject()
    {
        $dn1    = Ldap\Dn::fromString($this->createDn('ou=Node,'));
        $filter = Ldap\Filter::any('objectClass');

        $count1 = $this->getLDAP()->count($filter, $dn1, Ldap\Ldap::SEARCH_SCOPE_SUB);
        $this->assertEquals(3, $count1);

        $dn2    = Ldap\Dn::fromString(TESTS_ZEND_LDAP_WRITEABLE_SUBTREE);
        $count2 = $this->getLDAP()->count($filter, $dn2, Ldap\Ldap::SEARCH_SCOPE_SUB);
        $this->assertEquals(9, $count2);
    }

    public function testCountChildrenWithDnObject()
    {
        $dn1    = Ldap\Dn::fromString($this->createDn('ou=Node,'));
        $count1 = $this->getLDAP()->countChildren($dn1);
        $this->assertEquals(2, $count1);

        $dn2    = Ldap\Dn::fromString(TESTS_ZEND_LDAP_WRITEABLE_SUBTREE);
        $count2 = $this->getLDAP()->countChildren($dn2);
        $this->assertEquals(6, $count2);
    }

    public function testExistsDnWithDnObject()
    {
        $dn1 = Ldap\Dn::fromString($this->createDn('ou=Test2,'));
        $dn2 = Ldap\Dn::fromString($this->createDn('ou=Test99,'));

        $this->assertTrue($this->getLDAP()->exists($dn1));
        $this->assertFalse($this->getLDAP()->exists($dn2));
    }

    public function testSearchEntriesShortcutWithDnObjectAndFilterObject()
    {
        $dn     = Ldap\Dn::fromString(TESTS_ZEND_LDAP_WRITEABLE_SUBTREE);
        $filter = Ldap\Filter::equals('objectClass', 'organizationalUnit');

        $entries = $this->getLDAP()->searchEntries($filter, $dn, Ldap\Ldap::SEARCH_SCOPE_SUB);
        $this->assertInternalType("array", $entries);
        $this->assertEquals(9, count($entries));
    }

    public function testGetSingleEntryWithDnObject()
    {
        $dn    = Ldap\Dn::fromString($this->createDn('ou=Test1,'));
        $entry = $this->getLDAP()->getEntry($dn);
        $this->assertEquals($dn->toString(), $entry["dn"]);
    }

    public function testMultipleResultIteration()
    {
        $items   = $this->getLDAP()->search('(objectClass=organizationalUnit)',
            TESTS_ZEND_LDAP_WRITEABLE_SUBTREE, Ldap\Ldap::SEARCH_SCOPE_SUB
        );
        $isCount = 9;
        $this->assertEquals($isCount, $items->count());

        $i = 0;
        foreach ($items as $key => $item) {
            $this->assertEquals($i, $key);
            $i++;
        }
        $this->assertEquals($isCount, $i);
        $i = 0;
        foreach ($items as $key => $item) {
            $this->assertEquals($i, $key);
            $i++;
        }
        $this->assertEquals($isCount, $i);

        $items->close();
        $i = 0;
        foreach ($items as $key => $item) {
            $this->assertEquals($i, $key);
            $i++;
        }
        $this->assertEquals($isCount, $i);
        $i = 0;
        foreach ($items as $key => $item) {
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
        $items = $this->getLDAP()->search('(objectClass=organizationalUnit)',
            TESTS_ZEND_LDAP_WRITEABLE_SUBTREE, Ldap\Ldap::SEARCH_SCOPE_SUB
        );
        foreach ($items as $key => $item) {
            // do nothing - just iterate
        }
        $items->next();
    }

    public function testUnknownCollectionClassThrowsException()
    {
        try {
            $items = $this->getLDAP()->search('(objectClass=organizationalUnit)',
                TESTS_ZEND_LDAP_WRITEABLE_SUBTREE, Ldap\Ldap::SEARCH_SCOPE_SUB, array(), null,
                'This_Class_Does_Not_Exist'
            );
            $this->fail('Expected exception not thrown');
        } catch (Exception\LdapException $zle) {
            $this->assertContains("Class 'This_Class_Does_Not_Exist' can not be found",
                $zle->getMessage()
            );
        }
    }

    public function testCollectionClassNotSubclassingZendLDAPCollectionThrowsException()
    {
        try {
            $items = $this->getLDAP()->search(
                '(objectClass=organizationalUnit)',
                TESTS_ZEND_LDAP_WRITEABLE_SUBTREE,
                Ldap\Ldap::SEARCH_SCOPE_SUB,
                array(),
                null,
                'ZendTest\Ldap\CollectionClassNotSubclassingZendLDAPCollection'
            );
            $this->fail('Expected exception not thrown');
        } catch (Exception\LdapException $zle) {
            $this->assertContains(
                "Class 'ZendTest\\Ldap\\CollectionClassNotSubclassingZendLDAPCollection' must subclass 'Zend\\Ldap\\Collection'",
                $zle->getMessage()
            );
        }
    }

    /**
     * @group ZF-8233
     */
    public function testSearchWithOptionsArray()
    {
        $items = $this
            ->getLDAP()
            ->search(array(
                          'filter' => '(objectClass=organizationalUnit)',
                          'baseDn' => TESTS_ZEND_LDAP_WRITEABLE_SUBTREE,
                          'scope'  => Ldap\Ldap::SEARCH_SCOPE_SUB
                     )
        );
        $this->assertEquals(9, $items->count());
    }

    /**
     * @group ZF-8233
     */
    public function testSearchEntriesShortcutWithOptionsArray()
    {
        $items = $this
            ->getLDAP()
            ->searchEntries(array(
                                 'filter' => '(objectClass=organizationalUnit)',
                                 'baseDn' => TESTS_ZEND_LDAP_WRITEABLE_SUBTREE,
                                 'scope'  => Ldap\Ldap::SEARCH_SCOPE_SUB
                            )
        );
        $this->assertEquals(9, count($items));
    }

    /**
     * @group ZF-8233
     */
    public function testReverseSortingWithSearchEntriesShortcut()
    {
        $lSorted = array('e', 'd', 'c', 'b', 'a');
        $items   = $this->getLDAP()->searchEntries('(l=*)',
            TESTS_ZEND_LDAP_WRITEABLE_SUBTREE, Ldap\Ldap::SEARCH_SCOPE_SUB,
            array(), 'l', true
        );
        foreach ($items as $key => $item) {
            $this->assertEquals($lSorted[$key], $item['l'][0]);
        }
    }

    /**
     * @group ZF-8233
     */
    public function testReverseSortingWithSearchEntriesShortcutWithOptionsArray()
    {
        $lSorted = array('e', 'd', 'c', 'b', 'a');
        $items   = $this
            ->getLDAP()
            ->searchEntries(array(
                                 'filter'      => '(l=*)',
                                 'baseDn'      => TESTS_ZEND_LDAP_WRITEABLE_SUBTREE,
                                 'scope'       => Ldap\Ldap::SEARCH_SCOPE_SUB,
                                 'sort'        => 'l',
                                 'reverseSort' => true
                            )
        );
        foreach ($items as $key => $item) {
            $this->assertEquals($lSorted[$key], $item['l'][0]);
        }
    }

    public function testSearchNothingIteration()
    {
        $entries = $this->getLDAP()->search('(objectClass=account)',
            TESTS_ZEND_LDAP_WRITEABLE_SUBTREE, Ldap\Ldap::SEARCH_SCOPE_SUB,
            array(), 'uid'
        );
        $this->assertEquals(0, $entries->count());
        $i = 0;
        foreach ($entries as $key => $item) {
            $i++;
        }
        $this->assertEquals(0, $i);
    }

    public function testSearchNothingToArray()
    {
        $entries = $this->getLDAP()->search('(objectClass=account)',
            TESTS_ZEND_LDAP_WRITEABLE_SUBTREE, Ldap\Ldap::SEARCH_SCOPE_SUB,
            array(), 'uid'
        );
        $entries = $entries->toArray();
        $this->assertEquals(0, count($entries));
        $i = 0;
        foreach ($entries as $key => $item) {
            $i++;
        }
        $this->assertEquals(0, $i);
    }

    /**
     * @group ZF-8259
     */
    public function testUserIsAutomaticallyBoundOnOperationInDisconnectedState()
    {
        $ldap = $this->getLDAP();
        $ldap->disconnect();
        $dn    = $this->createDn('ou=Test1,');
        $entry = $ldap->getEntry($dn);
        $this->assertEquals($dn, $entry['dn']);
    }

    /**
     * @group ZF-8259
     */
    public function testUserIsAutomaticallyBoundOnOperationInUnboundState()
    {
        $ldap = $this->getLDAP();
        $ldap->disconnect();
        $ldap->connect();
        $dn    = $this->createDn('ou=Test1,');
        $entry = $ldap->getEntry($dn);
        $this->assertEquals($dn, $entry['dn']);
    }

    public function testInnerIteratorIsOfRequiredType()
    {
        $items = $this->getLDAP()->search('(objectClass=organizationalUnit)',
            TESTS_ZEND_LDAP_WRITEABLE_SUBTREE, Ldap\Ldap::SEARCH_SCOPE_SUB
        );
        $this->assertInstanceOf('\Zend\Ldap\Collection\DefaultIterator', $items->getInnerIterator());
    }

    /**
     * @group ZF-8262
     */
    public function testCallingCurrentOnIteratorReturnsFirstElement()
    {
        $items = $this->getLDAP()->search('(objectClass=organizationalUnit)',
            TESTS_ZEND_LDAP_WRITEABLE_SUBTREE, Ldap\Ldap::SEARCH_SCOPE_SUB
        );
        $this->assertEquals(TESTS_ZEND_LDAP_WRITEABLE_SUBTREE, $items->getInnerIterator()->key());
        $current = $items->getInnerIterator()->current();
        $this->assertInternalType('array', $current);
        $this->assertEquals(TESTS_ZEND_LDAP_WRITEABLE_SUBTREE, $current['dn']);
    }

    /**
     * @group ZF-8262
     */
    public function testCallingCurrentOnCollectionReturnsFirstElement()
    {
        $items = $this->getLDAP()->search('(objectClass=organizationalUnit)',
            TESTS_ZEND_LDAP_WRITEABLE_SUBTREE, Ldap\Ldap::SEARCH_SCOPE_SUB
        );
        $this->assertEquals(0, $items->key());
        $this->assertEquals(TESTS_ZEND_LDAP_WRITEABLE_SUBTREE, $items->dn());
        $current = $items->current();
        $this->assertInternalType('array', $current);
        $this->assertEquals(TESTS_ZEND_LDAP_WRITEABLE_SUBTREE, $current['dn']);
    }

    /**
     * @group ZF-8262
     */
    public function testCallingCurrentOnEmptyIteratorReturnsNull()
    {
        $items = $this->getLDAP()->search('(objectClass=account)',
            TESTS_ZEND_LDAP_WRITEABLE_SUBTREE, Ldap\Ldap::SEARCH_SCOPE_SUB
        );
        $this->assertNull($items->getInnerIterator()->key());
        $this->assertNull($items->getInnerIterator()->current());
    }

    /**
     * @group ZF-8262
     */
    public function testCallingCurrentOnEmptyCollectionReturnsNull()
    {
        $items = $this->getLDAP()->search('(objectClass=account)',
            TESTS_ZEND_LDAP_WRITEABLE_SUBTREE, Ldap\Ldap::SEARCH_SCOPE_SUB
        );
        $this->assertNull($items->key());
        $this->assertNull($items->dn());
        $this->assertNull($items->current());
    }

    /**
     * @group ZF-8262
     */
    public function testResultIterationAfterCallingCurrent()
    {
        $items = $this->getLDAP()->search('(objectClass=organizationalUnit)',
            TESTS_ZEND_LDAP_WRITEABLE_SUBTREE, Ldap\Ldap::SEARCH_SCOPE_SUB
        );
        $this->assertEquals(9, $items->count());
        $this->assertEquals(TESTS_ZEND_LDAP_WRITEABLE_SUBTREE, $items->getInnerIterator()->key());
        $current = $items->current();
        $this->assertInternalType('array', $current);
        $this->assertEquals(TESTS_ZEND_LDAP_WRITEABLE_SUBTREE, $current['dn']);

        $i = 0;
        foreach ($items as $key => $item) {
            $this->assertEquals($i, $key);
            $i++;
        }
        $this->assertEquals(9, $i);
        $j = 0;
        foreach ($items as $item) {
            $j++;
        }
        $this->assertEquals($i, $j);
    }

    /**
     * @group ZF-8263
     */
    public function testAttributeNameTreatmentToLower()
    {
        $dn   = $this->createDn('ou=Node,');
        $list = $this->getLDAP()->search('objectClass=*', $dn, Ldap\Ldap::SEARCH_SCOPE_BASE);
        $list->getInnerIterator()->setAttributeNameTreatment(Collection\DefaultIterator::ATTRIBUTE_TO_LOWER);
        $this->assertArrayHasKey('postalcode', $list->current());
    }

    /**
     * @group ZF-8263
     */
    public function testAttributeNameTreatmentToUpper()
    {
        $dn   = $this->createDn('ou=Node,');
        $list = $this->getLDAP()->search('objectClass=*', $dn, Ldap\Ldap::SEARCH_SCOPE_BASE);
        $list->getInnerIterator()->setAttributeNameTreatment(Collection\DefaultIterator::ATTRIBUTE_TO_UPPER);
        $this->assertArrayHasKey('POSTALCODE', $list->current());
    }

    /**
     * @group ZF-8263
     */
    public function testAttributeNameTreatmentNative()
    {
        $dn   = $this->createDn('ou=Node,');
        $list = $this->getLDAP()->search('objectClass=*', $dn, Ldap\Ldap::SEARCH_SCOPE_BASE);
        $list->getInnerIterator()->setAttributeNameTreatment(Collection\DefaultIterator::ATTRIBUTE_NATIVE);
        $this->assertArrayHasKey('postalCode', $list->current());
    }

    /**
     * @group ZF-8263
     */
    public function testAttributeNameTreatmentCustomFunction()
    {
        $dn   = $this->createDn('ou=Node,');
        $list = $this->getLDAP()->search('objectClass=*', $dn, Ldap\Ldap::SEARCH_SCOPE_BASE);
        $list->getInnerIterator()->setAttributeNameTreatment('ZendTest\Ldap\customNaming');
        $this->assertArrayHasKey('EDOCLATSOP', $list->current());
    }

    /**
     * @group ZF-8263
     */
    public function testAttributeNameTreatmentCustomStaticMethod()
    {
        $dn   = $this->createDn('ou=Node,');
        $list = $this->getLDAP()->search('objectClass=*', $dn, Ldap\Ldap::SEARCH_SCOPE_BASE);
        $list->getInnerIterator()->setAttributeNameTreatment(array(__NAMESPACE__ . '\CustomNaming', 'name1'));
        $this->assertArrayHasKey('edoclatsop', $list->current());
    }

    /**
     * @group ZF-8263
     */
    public function testAttributeNameTreatmentCustomInstanceMethod()
    {
        $dn    = $this->createDn('ou=Node,');
        $list  = $this->getLDAP()->search('objectClass=*', $dn, Ldap\Ldap::SEARCH_SCOPE_BASE);
        $namer = new CustomNaming();
        $list->getInnerIterator()->setAttributeNameTreatment(array($namer, 'name2'));
        $this->assertArrayHasKey('edoClatsop', $list->current());
    }
}

function customNaming($attrib)
{
    return strtoupper(strrev($attrib));
}

class CustomNaming
{
    public static function name1($attrib)
    {
        return strtolower(strrev($attrib));
    }

    public function name2($attrib)
    {
        return strrev($attrib);
    }
}

class CollectionClassNotSubclassingZendLDAPCollection
{
}
