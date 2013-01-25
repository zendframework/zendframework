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
use Zend\Ldap\Filter;

/**
 * @category   Zend
 * @package    Zend_Ldap
 * @subpackage UnitTests
 * @group      Zend_Ldap
 */
class FilterTest extends \PHPUnit_Framework_TestCase
{
    public function testFilterEscapeBasicOperation()
    {
        $input    = 'a*b(b)d\e/f';
        $expected = 'a\2ab\28b\29d\5ce/f';
        $this->assertEquals($expected, Ldap\Filter::escapeValue($input));
    }

    public function testEscapeValues()
    {
        $expected  = 't\28e,s\29t\2av\5cal\1eue';
        $filterval = 't(e,s)t*v\\al' . chr(30) . 'ue';
        $this->assertEquals($expected, Ldap\Filter::escapeValue($filterval));
        $this->assertEquals($expected, Ldap\Filter::escapeValue(array($filterval)));
        $this->assertEquals(
            array($expected, $expected, $expected),
            Ldap\Filter::escapeValue(array($filterval, $filterval, $filterval))
        );
    }

    public function testUnescapeValues()
    {
        $expected  = 't(e,s)t*v\\al' . chr(30) . 'ue';
        $filterval = 't\28e,s\29t\2av\5cal\1eue';
        $this->assertEquals($expected, Ldap\Filter::unescapeValue($filterval));
        $this->assertEquals($expected, Ldap\Filter::unescapeValue(array($filterval)));
        $this->assertEquals(
            array($expected, $expected, $expected),
            Ldap\Filter::unescapeValue(array($filterval, $filterval, $filterval))
        );
    }

    public function testFilterValueUtf8()
    {
        $filter    = 'ÄÖÜäöüß€';
        $escaped   = Ldap\Filter::escapeValue($filter);
        $unescaped = Ldap\Filter::unescapeValue($escaped);
        $this->assertEquals($filter, $unescaped);
    }

    public function testFilterCreation()
    {
        $f1 = Ldap\Filter::equals('name', 'value');
        $this->assertEquals('(name=value)', $f1->toString());
        $f2 = Ldap\Filter::begins('name', 'value');
        $this->assertEquals('(name=value*)', $f2->toString());
        $f3 = Ldap\Filter::ends('name', 'value');
        $this->assertEquals('(name=*value)', $f3->toString());
        $f4 = Ldap\Filter::contains('name', 'value');
        $this->assertEquals('(name=*value*)', $f4->toString());
        $f5 = Ldap\Filter::greater('name', 'value');
        $this->assertEquals('(name>value)', $f5->toString());
        $f6 = Ldap\Filter::greaterOrEqual('name', 'value');
        $this->assertEquals('(name>=value)', $f6->toString());
        $f7 = Ldap\Filter::less('name', 'value');
        $this->assertEquals('(name<value)', $f7->toString());
        $f8 = Ldap\Filter::lessOrEqual('name', 'value');
        $this->assertEquals('(name<=value)', $f8->toString());
        $f9 = Ldap\Filter::approx('name', 'value');
        $this->assertEquals('(name~=value)', $f9->toString());
        $f10 = Ldap\Filter::any('name');
        $this->assertEquals('(name=*)', $f10->toString());
        $f11 = Ldap\Filter::string('name=*value*value*');
        $this->assertEquals('(name=*value*value*)', $f11->toString());
        $f12 = Ldap\Filter::mask('(&(objectClass=account)(uid=%s))', 'a*b(b)d\e/f');
        $this->assertEquals('(&(objectClass=account)(uid=a\2ab\28b\29d\5ce/f))', $f12->toString());
    }

    public function testToStringImplementation()
    {
        $f1 = Ldap\Filter::ends('name', 'value');
        $this->assertEquals($f1->toString(), (string)$f1);
    }

    public function testNegate()
    {
        $f1 = Ldap\Filter::ends('name', 'value');
        $this->assertEquals('(name=*value)', $f1->toString());
        $f1 = $f1->negate();
        $this->assertEquals('(!(name=*value))', $f1->toString());
        $f1 = $f1->negate();
        $this->assertEquals('(name=*value)', $f1->toString());
    }

    /**
     * @expectedException Zend\Ldap\Filter\Exception\FilterException
     */
    public function testIllegalGroupingFilter()
    {
        $data = array('a', 'b', 5);
        $f    = new Filter\AndFilter($data);
    }

    public function testGroupingFilter()
    {
        $f1 = Ldap\Filter::equals('name', 'value');
        $f2 = Ldap\Filter::begins('name', 'value');
        $f3 = Ldap\Filter::ends('name', 'value');

        $f4 = Ldap\Filter::andFilter($f1, $f2, $f3);
        $f5 = Ldap\Filter::orFilter($f1, $f2, $f3);

        $this->assertEquals('(&(name=value)(name=value*)(name=*value))', $f4->toString());
        $this->assertEquals('(|(name=value)(name=value*)(name=*value))', $f5->toString());

        $f4 = $f4->addFilter($f1);
        $this->assertEquals('(&(name=value)(name=value*)(name=*value)(name=value))', $f4->toString());
    }

    public function testComplexFilter()
    {
        $f1 = Ldap\Filter::equals('name1', 'value1');
        $f2 = Ldap\Filter::equals('name1', 'value2');

        $f3 = Ldap\Filter::equals('name2', 'value1');
        $f4 = Ldap\Filter::equals('name2', 'value2');

        $f5 = Ldap\Filter::orFilter($f1, $f2);
        $f6 = Ldap\Filter::orFilter($f3, $f4);

        $f7 = Ldap\Filter::andFilter($f5, $f6);

        $this->assertEquals(
            '(&(|(name1=value1)(name1=value2))(|(name2=value1)(name2=value2)))',
            $f7->toString()
        );
    }

    public function testChaining()
    {
        $f = Ldap\Filter::equals('a1', 'v1')
            ->addAnd(Ldap\Filter::approx('a2', 'v2'));
        $this->assertEquals('(&(a1=v1)(a2~=v2))', $f->toString());
        $f = Ldap\Filter::equals('a1', 'v1')
            ->addOr(Ldap\Filter::approx('a2', 'v2'));
        $this->assertEquals('(|(a1=v1)(a2~=v2))', $f->toString());
        $f = Ldap\Filter::equals('a1', 'v1')
            ->negate()
            ->addOr(Ldap\Filter::approx('a2', 'v2'));
        $this->assertEquals('(|(!(a1=v1))(a2~=v2))', $f->toString());
        $f = Ldap\Filter::equals('a1', 'v1')
            ->addAnd(Ldap\Filter::approx('a2', 'v2')->negate());
        $this->assertEquals('(&(a1=v1)(!(a2~=v2)))', $f->toString());
        $f = Ldap\Filter::equals('a1', 'v1')
            ->negate()
            ->addAnd(Ldap\Filter::approx('a2', 'v2')->negate());
        $this->assertEquals('(&(!(a1=v1))(!(a2~=v2)))', $f->toString());
        $f = Ldap\Filter::equals('a1', 'v1')
            ->negate()
            ->addAnd(Ldap\Filter::approx('a2', 'v2')->negate());
        $this->assertEquals('(&(!(a1=v1))(!(a2~=v2)))', $f->toString());
        $f = Ldap\Filter::equals('a1', 'v1')
            ->negate()
            ->addAnd(Ldap\Filter::approx('a2', 'v2')->negate())
            ->negate();
        $this->assertEquals('(!(&(!(a1=v1))(!(a2~=v2))))', $f->toString());
    }

    public function testRealFilterString()
    {
        $f1 = Ldap\Filter::orFilter(
            Ldap\Filter::equals('sn', 'Gehrig'),
            Ldap\Filter::equals('sn', 'Goerke')
        );
        $f2 = Ldap\Filter::orFilter(
            Ldap\Filter::equals('givenName', 'Stefan'),
            Ldap\Filter::equals('givenName', 'Ingo')
        );

        $f = Ldap\Filter::andFilter($f1, $f2);

        $this->assertEquals(
            '(&(|(sn=Gehrig)(sn=Goerke))(|(givenName=Stefan)(givenName=Ingo)))',
            $f->toString()
        );
    }
}
