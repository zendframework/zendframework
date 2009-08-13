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
 * @package    Zend_OpenId
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_OpenId_AllTests::main');
}

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../TestHelper.php';

require_once 'Zend/OpenId/ConsumerTest.php';
require_once 'Zend/OpenId/Consumer/Storage/FileTest.php';
require_once 'Zend/OpenId/ProviderTest.php';
require_once 'Zend/OpenId/Provider/Storage/FileTest.php';
require_once 'Zend/OpenId/Provider/User/SessionTest.php';
require_once 'Zend/OpenId/ExtensionTest.php';
require_once 'Zend/OpenId/Extension/SregTest.php';

/**
 * @category   Zend
 * @package    Zend_OpenId
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_OpenId
 */
class Zend_OpenId_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_OpenId');

        $suite->addTestSuite('Zend_OpenId_ConsumerTest');
        $suite->addTestSuite('Zend_OpenId_Consumer_Storage_FileTest');
        $suite->addTestSuite('Zend_OpenId_ProviderTest');
        $suite->addTestSuite('Zend_OpenId_Provider_Storage_FileTest');
        $suite->addTestSuite('Zend_OpenId_Provider_User_SessionTest');
        $suite->addTestSuite('Zend_OpenId_ExtensionTest');
        $suite->addTestSuite('Zend_OpenId_Extension_SregTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_OpenId_AllTests::main') {
    Zend_OpenId_AllTests::main();
}
