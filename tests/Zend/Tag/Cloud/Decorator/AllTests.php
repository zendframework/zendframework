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
 * @package    Zend_Tag
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) . DIRECTORY_SEPARATOR . 'TestHelper.php';

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Tag_Cloud_Decorator_AllTests::main');
}

require_once 'Zend/Tag/Cloud/Decorator/HtmlCloudTest.php';
require_once 'Zend/Tag/Cloud/Decorator/HtmlTagTest.php';

/**
 * @category   Zend
 * @package    Zend_Tag
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Tag
 * @group      Zend_Tag_Cloud
 */
class Zend_Tag_Cloud_Decorator_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Tag_Cloud_Decorator');

        $suite->addTestSuite('Zend_Tag_Cloud_Decorator_HtmlCloudTest');
        $suite->addTestSuite('Zend_Tag_Cloud_Decorator_HtmlTagTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Tag_Cloud_Decorator_AllTests::main') {
    Zend_Tag_Cloud_Decorator_AllTests::main();
}
