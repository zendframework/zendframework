<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Authentication
 */

namespace ZendTest\Authentication\Adapter;

use Zend\Authentication\Adapter;

class HttpTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Wrapper
     */
    protected $_wrapper;

    public function setUp()
    {
        $config = array(
            'accept_schemes' => 'basic',
            'realm'          => 'testing',
        );

        $this->_wrapper = new Wrapper($config);
    }

    public function tearDown()
    {
        unset($this->_wrapper);
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Deprecated
     */
    public function testProtectedMethodChallengeClientTriggersErrorDeprecated()
    {
        $this->_wrapper->_challengeClient();
    }
}

class Wrapper extends Adapter\Http
{
    public function __call($method, $args)
    {
        return call_user_func_array(array($this, $method), $args);
    }
}
