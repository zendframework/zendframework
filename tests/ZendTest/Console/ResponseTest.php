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

use Zend\Console\Response;

/**
 * @category   Zend
 * @package    Zend_Console
 * @subpackage UnitTests
 * @group      Zend_Console
 */
class ResponseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Response
     */
    protected $response;

    public function setUp()
    {
        $this->response = new Response();
    }

    public function testInitialisation()
    {
        $this->assertEquals(false, $this->response->contentSent());
        $this->assertEquals(0, $this->response->getErrorLevel());
    }

    public function testSetContent()
    {
        $this->response->setContent('foo, bar');
        $this->assertEquals(false, $this->response->contentSent());
        ob_start();
        $this->response->sendContent();
        $content = ob_get_clean();
        $this->assertEquals('foo, bar', $content);
        $this->assertEquals(true, $this->response->contentSent());
        $this->assertEquals($this->response, $this->response->sendContent());
    }

    /*
    public function testSetContentWithExit()
    {
        if (!function_exists('set_exit_overload')) {
            $this->markTestSkipped("Install ext/test_helpers to test method with exit : https://github.com/sebastianbergmann/php-test-helpers.");
        }

        $self = $this;
        set_exit_overload(
            function($param = null) use ($self) {
                if ($param) {
                    $self->assertEquals($param, 1);
                }

                return false;
            }
        );
        $this->response->setErrorLevel(1);
        $this->response->setContent('foo, bar');
        ob_start();
        $this->response->send();
        $content = ob_get_clean();
        $this->assertEquals('foo, bar', $content);

        unset_exit_overload();
    }
    */
}
