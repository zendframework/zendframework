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
 * @package    Zend_Cloud_DocumentService
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Cloud\Infrastructure\Adapter;

use ZendTest\Cloud\Infrastructure\Adapter\Ec2Test,
    ZendTest\Cloud\Infrastructure\Adapter\RackspaceTest;

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'ZendTest\Cloud\Infrastructure\Adapter\AllTests::main');
}

/**
 * @category   Zend
 * @package    Zend\Cloud\Infrastructure\Adapter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class AllTests
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
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend\Cloud');

        $suite->addTestSuite('ZendTest\Cloud\Infrastructure\Adapter\Ec2Test');
        $suite->addTestSuite('ZendTest\Cloud\Infrastructure\Adapter\RackspaceTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'ZendTest\Cloud\Infrastructure\Adapter\AllTests::main') {
    AllTests::main();
}
