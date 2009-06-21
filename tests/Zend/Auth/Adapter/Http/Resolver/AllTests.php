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
 * @package    Zend_Auth
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Auth_Adapter_Http_Resolver_AllTests::main');
}


/**
 * PHPUnit_Framework_TestSuite
 */
require_once 'PHPUnit/Framework/TestSuite.php';


/**
 * PHPUnit_TextUI_TestRunner
 */
require_once 'PHPUnit/TextUI/TestRunner.php';


/**
 * @see Zend_Auth_Adapter_Http_Resolver_FileTest
 */
require_once 'Zend/Auth/Adapter/Http/Resolver/FileTest.php';


/**
 * @category   Zend
 * @package    Zend_Auth
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Auth_Adapter_Http_Resolver_AllTests
{
    /**
     * Runs this test suite
     *
     * @return void
     */
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    /**
     * Creates and returns this test suite
     *
     * @return PHPUnit_Framework_TestSuite
     */
    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Auth_Adapter_Http Resolvers');

        $suite->addTestSuite('Zend_Auth_Adapter_Http_Resolver_FileTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Auth_Adapter_Http_Resolver_AllTests::main') {
    Zend_Auth_Adapter_Http_Resolver_AllTests::main();
}
