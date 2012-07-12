<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

namespace ZendTest\Service\StrikeIron\BaseTest;

use Zend\Service\StrikeIron;

/**
 * @category   Zend
 * @package    Zend_Service_StrikeIron
 * @subpackage UnitTests
 */
class MockSoapClient
{
    public static $outputHeaders = array('SubscriptionInfo' => array('RemainingHits' => 3));

    public $calls = array();

    public function __soapCall($method, $params, $options, $headers, &$outputHeaders)
    {
        $outputHeaders = self::$outputHeaders;

        $this->calls[] = array('method'  => $method,
                               'params'  => $params,
                               'options' => $options,
                               'headers' => $headers);

        if ($method == 'ReturnTheObject') {
            // testMethodResultWrappingAnyObject
            return new \stdclass();

        } else if ($method == 'WrapThis') {
            // testMethodResultWrappingAnObjectAndSelectingDefaultResultProperty
            return (object)array('WrapThisResult' => 'unwraped');

        } else if ($method == 'ThrowTheException') {
            // testMethodExceptionsAreWrapped
            throw new \Exception('foo', 43);

        } else if ($method == 'ReturnNoOutputHeaders') {
            // testGettingSubscriptionInfoThrowsWhenHeaderNotFound
            $outputHeaders = array();

        } else {
            return 42;
        }
    }
}
