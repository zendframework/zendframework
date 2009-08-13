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
 * @package    Zend_Captcha
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../TestHelper.php';

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Captcha_AllTests::main');
}

require_once 'Zend/Captcha/DumbTest.php';
require_once 'Zend/Captcha/FigletTest.php';
require_once 'Zend/Captcha/ImageTest.php';
require_once 'Zend/Captcha/ReCaptchaTest.php';

/**
 * @category   Zend
 * @package    Zend_Captcha
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Captcha
 */
class Zend_Captcha_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Captcha');

        $suite->addTestSuite('Zend_Captcha_DumbTest');
        $suite->addTestSuite('Zend_Captcha_FigletTest');
        $suite->addTestSuite('Zend_Captcha_ImageTest');
        $suite->addTestSuite('Zend_Captcha_ReCaptchaTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Captcha_AllTests::main') {
    Zend_Captcha_AllTests::main();
}
