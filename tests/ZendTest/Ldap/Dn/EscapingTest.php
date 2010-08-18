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
 * @package    Zend_LDAP
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace ZendTest\Ldap\Dn;
use Zend\Ldap;

/**
 * Test helper
 */
/**
 * Zend_LDAP_Dn
 */

/**
 * @category   Zend
 * @package    Zend_LDAP
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_LDAP
 * @group      Zend_LDAP_Dn
 */
class EscapingTest extends \PHPUnit_Framework_TestCase
{
    public function testEscapeValues()
    {
        $dnval='  '.chr(22).' t,e+s"t,\\v<a>l;u#e=!    ';
        $expected='\20\20\16 t\,e\+s\"t\,\\\\v\<a\>l\;u\#e\=!\20\20\20\20';
        $this->assertEquals($expected, Ldap\Dn::escapeValue($dnval));
        $this->assertEquals($expected, Ldap\Dn::escapeValue(array($dnval)));
        $this->assertEquals(array($expected, $expected, $expected),
            Ldap\Dn::escapeValue(array($dnval, $dnval, $dnval)));
    }

    public function testUnescapeValues()
    {
        $dnval='\\20\\20\\16\\20t\\,e\\+s \\"t\\,\\\\v\\<a\\>l\\;u\\#e\\=!\\20\\20\\20\\20';
        $expected='  '.chr(22).' t,e+s "t,\\v<a>l;u#e=!    ';
        $this->assertEquals($expected, Ldap\Dn::unescapeValue($dnval));
        $this->assertEquals($expected, Ldap\Dn::unescapeValue(array($dnval)));
        $this->assertEquals(array($expected, $expected, $expected),
            Ldap\Dn::unescapeValue(array($dnval,$dnval,$dnval)));
    }
}
