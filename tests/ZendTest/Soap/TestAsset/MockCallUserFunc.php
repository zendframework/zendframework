<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Soap
 */

namespace ZendTest\Soap\TestAsset;

/**
 * Allows mocking of call_user_func.
 */
class MockCallUserFunc
{
    /**
     * Whether to mock the call_user_func function.
     *
     * @var boolean
     */
    public static $mock = false;

    /**
     * Passed parameters.
     *
     * @var array
     */
    public static $params = array();
}
