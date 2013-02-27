<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Console
 */

namespace ZendTest\Console;

use Zend\Console\Request;

/**
 * @category   Zend
 * @package    Zend_Console
 * @subpackage UnitTests
 * @group      Zend_Console
 */
class RequestTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (ini_get('register_argc_argv') == false) {
            $this->markTestSkipped("Cannot Test Zend\\Console\\Getopt without 'register_argc_argv' ini option true.");
        }
    }

    public function testCanConstructRequestAndGetParams()
    {
        $_SERVER['argv'] = array('foo.php', 'foo' => 'baz', 'bar');
        $_ENV["FOO_VAR"] = "bar";

        $request = new Request();
        $params = $request->getParams();

        $this->assertEquals(2, count($params));
        $this->assertEquals($params->toArray(), array('foo' => 'baz', 'bar'));
        $this->assertEquals($request->getParam('foo'), 'baz');
        $this->assertEquals($request->getScriptName(), 'foo.php');
        $this->assertEquals(1, count($request->env()));
        $this->assertEquals($request->env()->get('FOO_VAR'), 'bar');
        $this->assertEquals($request->getEnv('FOO_VAR'), 'bar');
    }
}
