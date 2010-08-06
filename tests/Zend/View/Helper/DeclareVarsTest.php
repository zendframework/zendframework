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
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace ZendTest\View\Helper;

// Call Zend_View_Helper_DeclareVarsTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_View_Helper_DeclareVarsTest::main");
}



/**
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
class DeclareVarsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main()
    {

        $suite  = new \PHPUnit_Framework_TestSuite("Zend_View_Helper_DeclareVarsTest");
        $result = \PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function setUp()
    {
        $view = new \Zend\View\View();
        $base = str_replace('/', DIRECTORY_SEPARATOR, '/../_templates');
        $view->setScriptPath(__DIR__ . $base);
        $view->strictVars(true);
        $this->view = $view;
    }

    public function tearDown()
    {
        unset($this->view);
    }

    protected function _declareVars()
    {
        $this->view->declareVars(
            'varName1',
            'varName2',
            array(
                'varName3' => 'defaultValue',
                'varName4' => array()
            )
        );
    }

    public function testDeclareUndeclaredVars()
    {
        $this->_declareVars();

        $this->assertTrue(isset($this->view->varName1));
        $this->assertTrue(isset($this->view->varName2));
        $this->assertTrue(isset($this->view->varName3));
        $this->assertTrue(isset($this->view->varName4));

        $this->assertEquals('defaultValue', $this->view->varName3);
        $this->assertEquals(array(), $this->view->varName4);
    }

    public function testDeclareDeclaredVars()
    {
        $this->view->varName2 = 'alreadySet';
        $this->view->varName3 = 'myValue';
        $this->view->varName5 = 'additionalValue';

        $this->_declareVars();

        $this->assertTrue(isset($this->view->varName1));
        $this->assertTrue(isset($this->view->varName2));
        $this->assertTrue(isset($this->view->varName3));
        $this->assertTrue(isset($this->view->varName4));
        $this->assertTrue(isset($this->view->varName5));

        $this->assertEquals('alreadySet', $this->view->varName2);
        $this->assertEquals('myValue', $this->view->varName3);
        $this->assertEquals('additionalValue', $this->view->varName5);
    }
}

// Call Zend_View_Helper_DeclareVarsTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_View_Helper_DeclareVarsTest::main") {
    \Zend_View_Helper_DeclareVarsTest::main();
}
