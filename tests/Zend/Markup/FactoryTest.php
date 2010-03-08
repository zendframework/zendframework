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
 * @package    Zend_Markup
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Markup_FactoryTest::main");
}


/**
 * @category   Zend
 * @package    Zend_Markup
 * @subpackage UnitTests
 * @group      Zend_Markup
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Markup_FactoryTest extends PHPUnit_Framework_TestCase
{

    public function testFactory()
    {
        Zend_Markup::addParserPath('Zend_Markup_Test_Parser', 'Zend/Markup/Test/Parser');
        Zend_Markup::addRendererPath('Zend_Markup_Test_Renderer', 'Zend/Markup/Test/Renderer');

        Zend_Markup::factory('MockParser', 'MockRenderer');
    }

}

// Call Zend_Markup_BbcodeTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Markup_FactoryTest::main") {
    Zend_Markup_BbcodeTest::main();
}
