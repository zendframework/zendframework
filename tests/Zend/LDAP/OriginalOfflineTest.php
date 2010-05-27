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
namespace ZendTest\LDAP;
use Zend\LDAP;

/**
 * @category   Zend
 * @package    Zend_LDAP
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_LDAP
 */
class OriginalOfflineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Zend_LDAP instance
     *
     * @var Zend_LDAP
     */
    protected $_ldap = null;

    /**
     * Setup operations run prior to each test method:
     *
     * * Creates an instance of Zend_LDAP
     *
     * @return void
     */
    public function setUp()
    {
        if (!constant('TESTS_ZEND_LDAP_ONLINE_ENABLED')) {
            $this->markTestSkipped("Zend_LDAP online tests are not enabled");
        }

        $this->_ldap = new LDAP\LDAP();
    }

    public function testFilterEscapeBasicOperation()
    {
        $input = 'a*b(b)d\e/f';
        $expected = 'a\2ab\28b\29d\5ce\2ff';
        $this->assertEquals($expected, LDAP\LDAP::filterEscape($input));
    }

    public function testInvalidOptionResultsInException()
    {
        $optionName = 'invalid';
        try {
            $this->_ldap->setOptions(array($optionName => 'irrelevant'));
            $this->fail('Expected Zend_LDAP_Exception not thrown');
        } catch (LDAP\Exception $e) {
            $this->assertEquals("Unknown Zend_LDAP option: $optionName", $e->getMessage());
        }
    }

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
            $ret = LDAP\LDAP::explodeDn($dn);
            $this->assertTrue($ret === $expected);
        }
    }
}
