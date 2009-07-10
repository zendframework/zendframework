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
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Test helper
 */
require_once dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'TestHelper.php';

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Ldap_Dn_AllTests::main');
}

/**
 * @category   Zend
 * @package    Zend_Ldap
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Ldap_Dn_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Ldap_Dn');

        /**
         * @see Zend_Ldap_Dn_EscapingTest
         */
        require_once 'Zend/Ldap/Dn/EscapingTest.php';
        $suite->addTestSuite('Zend_Ldap_Dn_EscapingTest');
        /**
         * @see Zend_Ldap_Dn_ExplodingTest
         */
        require_once 'Zend/Ldap/Dn/ExplodingTest.php';
        $suite->addTestSuite('Zend_Ldap_Dn_ExplodingTest');
        /**
         * @see Zend_Ldap_Dn_ImplodingTest
         */
        require_once 'Zend/Ldap/Dn/ImplodingTest.php';
        $suite->addTestSuite('Zend_Ldap_Dn_ImplodingTest');
        /**
         * @see Zend_Ldap_Dn_CreationTest
         */
        require_once 'Zend/Ldap/Dn/CreationTest.php';
        $suite->addTestSuite('Zend_Ldap_Dn_CreationTest');
        /**
         * @see Zend_Ldap_Dn_ModificationTest
         */
        require_once 'Zend/Ldap/Dn/ModificationTest.php';
        $suite->addTestSuite('Zend_Ldap_Dn_ModificationTest');
        /**
         * @see Zend_Ldap_Dn_MiscTest
         */
        require_once 'Zend/Ldap/Dn/MiscTest.php';
        $suite->addTestSuite('Zend_Ldap_Dn_MiscTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Ldap_Dn_AllTests::main') {
    Zend_Ldap_Dn_AllTests::main();
}
