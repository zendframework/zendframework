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
 * @package    Zend_Service_StrikeIron
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Service\StrikeIron\BaseTest;
use Zend\Service\StrikeIron;

/**
 * Test helper
 */

/**
 * @category   Zend
 * @package    Zend_Service_StrikeIron
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
