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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Ldap;
use Zend\Ldap\Filter;

/**
 * @category   Zend
 * @package    Zend_Ldap
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Ldap
 */
class FilterTest extends \PHPUnit_Framework_TestCase
{
    public function testFilterEscapeBasicOperation()
    {
        $input = 'a*b(b)d\e/f';
        $expected = 'a\2ab\28b\29d\5ce/f';
        $this->assertEquals($expected, Filter::escapeValue($input));
    }

    public function testEscapeValues()
    {
        $expected='t\28e,s\29t\2av\5cal\1eue';
        $filterval='t(e,s)t*v\\al' . chr(30) . 'ue';
        $this->assertEquals($expected, Filter::escapeValue($filterval));
        $this->assertEquals($expected, Filter::escapeValue(array($filterval)));
        $this->assertEquals(array($expected, $expected, $expected),
            Filter::escapeValue(array($filterval, $filterval, $filterval)));
    }

    public function testUnescapeValues()
    {
        $expected='t(e,s)t*v\\al' . chr(30) . 'ue';
        $filterval='t\28e,s\29t\2av\5cal\1eue';
        $this->assertEquals($expected, Filter::unescapeValue($filterval));
        $this->assertEquals($expected, Filter::unescapeValue(array($filterval)));
        $this->assertEquals(array($expected, $expected, $expected),
            Filter::unescapeValue(array($filterval, $filterval, $filterval)));
    }

    public function testFilterValueUtf8()
    {
        $filter='ÄÖÜäöüß€';
        $escaped=Filter::escapeValue($filter);
        $unescaped=Filter::unescapeValue($escaped);
        $this->assertEquals($filter, $unescaped);
    }

    public function testFilterCreation()
    {
        $f1=Filter::equals('name', 'value');
        $this->assertEquals('(name=value)', $f1->toString());
        $f2=Filter::begins('name', 'value');
        $this->assertEquals('(name=value*)', $f2->toString());
        $f3=Filter::ends('name', 'value');
        $this->assertEquals('(name=*value)', $f3->toString());
        $f4=Filter::contains('name', 'value');
        $this->assertEquals('(name=*value*)', $f4->toString());
        $f5=Filter::greater('name', 'value');
        $this->assertEquals('(name>value)', $f5->toString());
        $f6=Filter::greaterOrEqual('name', 'value');
        $this->assertEquals('(name>=value)', $f6->toString());
        $f7=Filter::less('name', 'value');
        $this->assertEquals('(name<value)', $f7->toString());
        $f8=Filter::lessOrEqual('name', 'value');
        $this->assertEquals('(name<=value)', $f8->toString());
        $f9=Filter::approx('name', 'value');
        $this->assertEquals('(name~=value)', $f9->toString());
        $f10=Filter::any('name');
        $this->assertEquals('(name=*)', $f10->toString());
        $f11=Filter::string('name=*value*value*');
        $this->assertEquals('(name=*value*value*)', $f11->toString());
        $f12=Filter::mask('(&(objectClass=account)(uid=%s))', 'a*b(b)d\e/f');
        $this->assertEquals('(&(objectClass=account)(uid=a\2ab\28b\29d\5ce/f))', $f12->toString());
    }

    public function testToStringImplementation()
    {
        $f1=Filter::ends('name', 'value');
        $this->assertEquals($f1->toString(), (string)$f1);
    }

    public function testNegate()
    {
        $f1=Filter::ends('name', 'value');
        $this->assertEquals('(name=*value)', $f1->toString());
        $f1=$f1->negate();
        $this->assertEquals('(!(name=*value))', $f1->toString());
        $f1=$f1->negate();
        $this->assertEquals('(name=*value)', $f1->toString());
    }

    /**
     * @expectedException Zend\Ldap\Filter\Exception
     */
    public function testIllegalGroupingFilter()
    {
        $data=array('a', 'b', 5);
        $f=new Filter\AndFilter($data);
    }

    public function testGroupingFilter()
    {
        $f1=Filter::equals('name', 'value');
        $f2=Filter::begins('name', 'value');
        $f3=Filter::ends('name', 'value');

        $f4=Filter::andFilter($f1, $f2, $f3);
        $f5=Filter::orFilter($f1, $f2, $f3);

        $this->assertEquals('(&(name=value)(name=value*)(name=*value))', $f4->toString());
        $this->assertEquals('(|(name=value)(name=value*)(name=*value))', $f5->toString());

        $f4=$f4->addFilter($f1);
        $this->assertEquals('(&(name=value)(name=value*)(name=*value)(name=value))', $f4->toString());
    }

    public function testComplexFilter()
    {
        $f1=Filter::equals('name1', 'value1');
        $f2=Filter::equals('name1', 'value2');

        $f3=Filter::equals('name2', 'value1');
        $f4=Filter::equals('name2', 'value2');

        $f5=Filter::orFilter($f1, $f2);
        $f6=Filter::orFilter($f3, $f4);

        $f7=Filter::andFilter($f5, $f6);

        $this->assertEquals('(&(|(name1=value1)(name1=value2))(|(name2=value1)(name2=value2)))',
            $f7->toString());
    }

    public function testChaining()
    {
        $f=Filter::equals('a1', 'v1')
            ->addAnd(Filter::approx('a2', 'v2'));
        $this->assertEquals('(&(a1=v1)(a2~=v2))', $f->toString());
        $f=Filter::equals('a1', 'v1')
            ->addOr(Filter::approx('a2', 'v2'));
        $this->assertEquals('(|(a1=v1)(a2~=v2))', $f->toString());
        $f=Filter::equals('a1', 'v1')
            ->negate()
            ->addOr(Filter::approx('a2', 'v2'));
        $this->assertEquals('(|(!(a1=v1))(a2~=v2))', $f->toString());
        $f=Filter::equals('a1', 'v1')
            ->addAnd(Filter::approx('a2', 'v2')->negate());
        $this->assertEquals('(&(a1=v1)(!(a2~=v2)))', $f->toString());
        $f=Filter::equals('a1', 'v1')
            ->negate()
            ->addAnd(Filter::approx('a2', 'v2')->negate());
        $this->assertEquals('(&(!(a1=v1))(!(a2~=v2)))', $f->toString());
        $f=Filter::equals('a1', 'v1')
            ->negate()
            ->addAnd(Filter::approx('a2', 'v2')->negate());
        $this->assertEquals('(&(!(a1=v1))(!(a2~=v2)))', $f->toString());
        $f=Filter::equals('a1', 'v1')
            ->negate()
            ->addAnd(Filter::approx('a2', 'v2')->negate())
            ->negate();
        $this->assertEquals('(!(&(!(a1=v1))(!(a2~=v2))))', $f->toString());
    }

    public function testRealFilterString()
    {
        $f1=Filter::orFilter(
            Filter::equals('sn', 'Gehrig'),
            Filter::equals('sn', 'Goerke')
        );
        $f2=Filter::orFilter(
            Filter::equals('givenName', 'Stefan'),
            Filter::equals('givenName', 'Ingo')
        );

        $f=Filter::andFilter($f1, $f2);

        $this->assertEquals('(&(|(sn=Gehrig)(sn=Goerke))(|(givenName=Stefan)(givenName=Ingo)))',
            $f->toString());
    }
}

