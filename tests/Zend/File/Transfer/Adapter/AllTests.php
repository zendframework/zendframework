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
 * @package    Zend_File
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../../../TestHelper.php';

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_File_Transfer_Adapter_AllTests::main');
}

require_once 'Zend/File/Transfer/Adapter/AbstractTest.php';
require_once 'Zend/File/Transfer/Adapter/HttpTest.php';

/**
 * @category   Zend
 * @package    Zend_File
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_File_Transfer_Adapter_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_File_Transfer_Adapter');

        $suite->addTestSuite('Zend_File_Transfer_Adapter_AbstractTest');
        $suite->addTestSuite('Zend_File_Transfer_Adapter_HttpTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_File_Transfer_Adapter_AllTests::main') {
    Zend_File_Transfer_Adapter_AllTests::main();
}
