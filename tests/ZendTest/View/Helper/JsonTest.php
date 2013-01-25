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

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Http\Response;
use Zend\Json\Json as JsonFormatter;
use Zend\View\Helper\Json as JsonHelper;

/**
 * Test class for Zend_View_Helper_Json
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
class JsonTest extends TestCase
{

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $this->response = new Response();
        $this->helper   = new JsonHelper();
        $this->helper->setResponse($this->response);
    }

    public function verifyJsonHeader()
    {
        $headers = $this->response->getHeaders();
        $this->assertTrue($headers->has('Content-Type'));
        $header = $headers->get('Content-Type');
        $this->assertEquals('application/json', $header->getFieldValue());
    }

    public function testJsonHelperSetsResponseHeader()
    {
        $json = $this->helper->__invoke('foobar');
        $this->verifyJsonHeader();
    }

    public function testJsonHelperReturnsJsonEncodedString()
    {
        $data = $this->helper->__invoke('foobar');
        $this->assertTrue(is_string($data));
        $this->assertEquals('foobar', JsonFormatter::decode($data));
    }
}
