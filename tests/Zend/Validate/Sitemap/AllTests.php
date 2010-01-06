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
 * @package    Zend_Validate
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

require_once dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'TestHelper.php';

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Validate_Sitemap_AllTests::main');
}

require_once 'Zend/Validate/Sitemap/ChangefreqTest.php';
require_once 'Zend/Validate/Sitemap/LastmodTest.php';
require_once 'Zend/Validate/Sitemap/LocTest.php';
require_once 'Zend/Validate/Sitemap/PriorityTest.php';

/**
 * @category   Zend
 * @package    Zend_Validate
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Validate
 * @group      Zend_Validate_Sitemap
 */
class Zend_Validate_Sitemap_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Validate_Sitemap');

        $suite->addTestSuite('Zend_Validate_Sitemap_ChangefreqTest');
        $suite->addTestSuite('Zend_Validate_Sitemap_LastmodTest');
        $suite->addTestSuite('Zend_Validate_Sitemap_LocTest');
        $suite->addTestSuite('Zend_Validate_Sitemap_PriorityTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Validate_Sitemap_AllTests::main') {
    Zend_Validate_Sitemap_AllTests::main();
}
