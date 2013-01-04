<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_I18n
 */

namespace ZendTest\I18n\Translator;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\I18n\Translator\TextDomain;
use Zend\I18n\Translator\Plural\Rule as PluralRule;

class TextDomainTest extends TestCase
{
    public function testInstantiation()
    {
        $domain = new TextDomain(array('foo' => 'bar'));
        $this->assertEquals('bar', $domain['foo']);
    }

    public function testArrayAccess()
    {
        $domain = new TextDomain();
        $domain['foo'] = 'bar';
        $this->assertEquals('bar', $domain['foo']);
    }

    public function testPluralRuleSetter()
    {
        $domain = new TextDomain();
        $domain->setPluralRule(PluralRule::fromString('nplurals=3; plural=n'));
        $this->assertEquals(2, $domain->getPluralRule()->evaluate(2));
    }

    public function testPluralRuleDefault()
    {
        $domain = new TextDomain();
        $this->assertEquals(0, $domain->getPluralRule()->evaluate(0));
        $this->assertEquals(1, $domain->getPluralRule()->evaluate(1));
        $this->assertEquals(0, $domain->getPluralRule()->evaluate(2));
    }
}
