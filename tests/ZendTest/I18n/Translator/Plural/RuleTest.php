<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_I18n
 */

namespace ZendTest\I18n\Translator\Plural;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\I18n\Translator\Plural\Rule;

class RuleTest extends TestCase
{
    public static function parseRuleProvider()
    {
        return array(
            // Basic calculations
            'addition'         => array('2 + 3', 5),
            'substraction'     => array('3 - 2', 1),
            'multiplication'   => array('2 * 3', 6),
            'division'         => array('6 / 3', 2),
            'integer-division' => array('7 / 4', 1),
            'modulo'           => array('7 % 4', 3),

            // Boolean NOT
            'boolean-not-0'  => array('!0', 1),
            'boolean-not-1'  => array('!1', 0),
            'boolean-not-15' => array('!1', 0),

            // Equal operators
            'equal-true'      => array('5 == 5', 1),
            'equal-false'     => array('5 == 4', 0),
            'not-equal-true'  => array('5 != 5', 0),
            'not-equal-false' => array('5 != 4', 1),

            // Compare operators
            'less-than-true'         => array('5 > 4', 1),
            'less-than-false'        => array('5 > 5', 0),
            'less-or-equal-true'     => array('5 >= 5', 1),
            'less-or-equal-false'    => array('5 >= 6', 0),
            'greater-than-true'      => array('5 < 6', 1),
            'greater-than-false'     => array('5 < 5', 0),
            'greater-or-equal-true'  => array('5 <= 5', 1),
            'greater-or-equal-false' => array('5 <= 4', 0),

            // Boolean operators
            'boolean-and-true'  => array('1 && 1', 1),
            'boolean-and-false' => array('1 && 0', 0),
            'boolean-or-true'   => array('1 || 0', 1),
            'boolean-or-false'  => array('0 || 0', 0),

            // Variable injection
            'variable-injection' => array('n', 0)
        );
    }

    /**
     * @dataProvider parseRuleProvider
     */
    public function testParseRules($rule, $expectedValue)
    {
        $this->assertEquals(
            $expectedValue,
            Rule::fromString('nplurals=9; plural=' . $rule)->evaluate(0)
        );
    }

    public static function completeRuleProvider()
    {
        // Taken from original gettext tests
        return array(
            array(
                'n != 1',
                '10111111111111111111111111111111111111111111111111111111111111'
                . '111111111111111111111111111111111111111111111111111111111111'
                . '111111111111111111111111111111111111111111111111111111111111'
                . '111111111111111111'
            ),
            array(
                'n>1',
                '00111111111111111111111111111111111111111111111111111111111111'
                . '111111111111111111111111111111111111111111111111111111111111'
                . '111111111111111111111111111111111111111111111111111111111111'
                . '111111111111111111'
            ),
            array(
                'n==1 ? 0 : n==2 ? 1 : 2',
                '20122222222222222222222222222222222222222222222222222222222222'
                . '222222222222222222222222222222222222222222222222222222222222'
                . '222222222222222222222222222222222222222222222222222222222222'
                . '222222222222222222'
            ),
            array(
                'n==1 ? 0 : (n==0 || (n%100 > 0 && n%100 < 20)) ? 1 : 2',
                '10111111111111111111222222222222222222222222222222222222222222'
                . '222222222222222222222222222222222222222111111111111111111122'
                . '222222222222222222222222222222222222222222222222222222222222'
                . '222222222222222222'
            ),
            array(
                'n%10==1 && n%100!=11 ? 0 : n%10>=2 && (n%100<10 || n%100>=20) ? 1 : 2',
                '20111111112222222222201111111120111111112011111111201111111120'
                . '111111112011111111201111111120111111112011111111222222222220'
                . '111111112011111111201111111120111111112011111111201111111120'
                . '111111112011111111'
            ),
            array(
                'n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2',
                '20111222222222222222201112222220111222222011122222201112222220'
                . '111222222011122222201112222220111222222011122222222222222220'
                . '111222222011122222201112222220111222222011122222201112222220'
                . '111222222011122222'
            ),
            array(
                'n%100/10==1 ? 2 : n%10==1 ? 0 : (n+9)%10>3 ? 2 : 1',
                '20111222222222222222201112222220111222222011122222201112222220'
                . '111222222011122222201112222220111222222011122222222222222220'
                . '111222222011122222201112222220111222222011122222201112222220'
                . '111222222011122222'
            ),
            array(
                'n==1 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2',
                '20111222222222222222221112222222111222222211122222221112222222'
                . '111222222211122222221112222222111222222211122222222222222222'
                . '111222222211122222221112222222111222222211122222221112222222'
                . '111222222211122222'
            ),
            array(
                'n%100==1 ? 0 : n%100==2 ? 1 : n%100==3 || n%100==4 ? 2 : 3',
                '30122333333333333333333333333333333333333333333333333333333333'
                . '333333333333333333333333333333333333333012233333333333333333'
                . '333333333333333333333333333333333333333333333333333333333333'
                . '333333333333333333'
            ),
        );
    }

    /**
     * @dataProvider completeRuleProvider
     */
    public function testCompleteRules($rule, $expectedValues)
    {
        $rule = Rule::fromString('nplurals=9; plural=' . $rule);

        for ($i = 0; $i < 200; $i++) {
            $this->assertEquals((int) $expectedValues[$i], $rule->evaluate($i));
        }
    }
}
