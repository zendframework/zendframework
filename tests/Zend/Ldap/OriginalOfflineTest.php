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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Test helper
 */

/**
 * Zend_Ldap
 */

/**
 * @category   Zend
 * @package    Zend_Ldap
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Ldap
 */
class Zend_Ldap_OriginalOfflineTest extends PHPUnit_Framework_TestCase
{
    /**
     * Zend_Ldap instance
     *
     * @var Zend_Ldap
     */
    protected $_ldap = null;

    /**
     * Setup operations run prior to each test method:
     *
     * * Creates an instance of Zend_Ldap
     *
     * @return void
     */
    public function setUp()
    {
        $this->_ldap = new Zend_Ldap();
    }

    /**
     * @return void
     */
    public function testFilterEscapeBasicOperation()
    {
        $input = 'a*b(b)d\e/f';
        $expected = 'a\2ab\28b\29d\5ce\2ff';
        $this->assertEquals($expected, Zend_Ldap::filterEscape($input));
    }

    /**
     * @return void
     */
    public function testInvalidOptionResultsInException()
    {
        $optionName = 'invalid';
        try {
            $this->_ldap->setOptions(array($optionName => 'irrelevant'));
            $this->fail('Expected Zend_Ldap_Exception not thrown');
        } catch (Zend_Ldap_Exception $e) {
            $this->assertEquals("Unknown Zend_Ldap option: $optionName", $e->getMessage());
        }
    }

    /**
     * @return void
     */
    public function testExplodeDnOperation()
    {
        $inputs = array(
            'CN=Alice Baker,CN=Users,DC=example,DC=com' => true,
            'CN=Baker\\, Alice,CN=Users,DC=example,DC=com' => true,
            'OU=Sales,DC=local' => true,
            'OU=Sales;DC=local' => true,
            'OU=Sales ,DC=local' => true,
            'OU=Sales, dC=local' => true,
            'ou=Sales , DC=local' => true,
            'OU=Sales ; dc=local' => true,
            'DC=local' => true,
            ' DC=local' => true,
            'DC= local  ' => true,
            'username' => false,
            'username@example.com' => false,
            'EXAMPLE\\username' => false,
            'CN=,Alice Baker,CN=Users,DC=example,DC=com' => false,
            'CN=Users,DC==example,DC=com' => false,
            'O=ACME' => true,
            '' => false,
            '   ' => false,
        );

        foreach ($inputs as $dn => $expected) {
            $ret = Zend_Ldap::explodeDn($dn);
            $this->assertTrue($ret === $expected);
        }
    }
}
