<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_View
 */

namespace ZendTest\View\Helper;

use Zend\View\Renderer\PhpRenderer as View;
use Zend\View\Helper\DeclareVars;

/**
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
class DeclareVarsTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $view = new View();
        $base = str_replace('/', DIRECTORY_SEPARATOR, '/../_templates');
        $view->resolver()->addPath(__DIR__ . $base);
        $view->vars()->setStrictVars(true);
        $this->view = $view;
    }

    public function tearDown()
    {
        unset($this->view);
    }

    protected function _declareVars()
    {
        $this->view->plugin('declareVars')->__invoke(
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

        $vars = $this->view->vars();
        $this->assertTrue(isset($vars->varName1));
        $this->assertTrue(isset($vars->varName2));
        $this->assertTrue(isset($vars->varName3));
        $this->assertTrue(isset($vars->varName4));

        $this->assertEquals('defaultValue', $vars->varName3);
        $this->assertEquals(array(), $vars->varName4);
    }

    public function testDeclareDeclaredVars()
    {
        $vars = $this->view->vars();
        $vars->varName2 = 'alreadySet';
        $vars->varName3 = 'myValue';
        $vars->varName5 = 'additionalValue';

        $this->_declareVars();

        $this->assertTrue(isset($vars->varName1));
        $this->assertTrue(isset($vars->varName2));
        $this->assertTrue(isset($vars->varName3));
        $this->assertTrue(isset($vars->varName4));
        $this->assertTrue(isset($vars->varName5));

        $this->assertEquals('alreadySet', $vars->varName2);
        $this->assertEquals('myValue', $vars->varName3);
        $this->assertEquals('additionalValue', $vars->varName5);
    }
}
