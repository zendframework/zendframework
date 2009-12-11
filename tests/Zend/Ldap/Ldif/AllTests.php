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

require_once dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'TestHelper.php';

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Ldap_Ldif_AllTests::main');
}

require_once 'Zend/Ldap/Ldif/SimpleEncoderTest.php';
require_once 'Zend/Ldap/Ldif/SimpleDecoderTest.php';

/**
 * @category   Zend
 * @package    Zend_Ldap
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Ldap
 * @group      Zend_Ldap_Ldif
 */
class Zend_Ldap_Ldif_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Ldap_Ldif');

        $suite->addTestSuite('Zend_Ldap_Ldif_SimpleEncoderTest');
        $suite->addTestSuite('Zend_Ldap_Ldif_SimpleDecoderTest');

        if (defined('TESTS_ZEND_LDAP_ONLINE_ENABLED')
                && constant('TESTS_ZEND_LDAP_ONLINE_ENABLED')) {

        } else {
            $suite->addTest(new Zend_Ldap_Ldif_SkipOnlineTests());
        }

        return $suite;
    }
}

class Zend_Ldap_Ldif_SkipOnlineTests extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->markTestSkipped('Zend_Ldap_Ldif online tests not enabled in TestConfiguration.php');
    }

    public function testNothing()
    {
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Ldap_Ldif_AllTests::main') {
    Zend_Ldap_Ldif_AllTests::main();
}
