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
 * @package    Zend_OAuth
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id:$
 */

namespace ZendTest\OAuth\Signature;

use Zend\OAuth\Signature;

/**
 * @category   Zend
 * @package    Zend_OAuth
 * @subpackage UnitTests
 * @group      Zend_OAuth
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class HMACTest extends \PHPUnit_Framework_TestCase
{

    public function testSignatureWithoutAccessSecretIsHashedWithConsumerSecret()
    {
        $params = array(
            'oauth_version' => '1.0',
            'oauth_consumer_key' => 'dpf43f3p2l4k3l03',
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp' => '1191242090',
            'oauth_nonce' => 'hsu94j3884jdopsl',
            'oauth_version' => '1.0'
        );
        $signature = new Signature\Hmac('1234567890', null, 'SHA1');
        $this->assertEquals('XYkaERjLVjp2yP/klDCGQ+hZ2So=', $signature->sign($params));
    }

    public function testSignatureWithAccessSecretIsHashedWithConsumerAndAccessSecret()
    {
        $params = array(
            'oauth_version' => '1.0',
            'oauth_consumer_key' => 'dpf43f3p2l4k3l03',
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp' => '1191242090',
            'oauth_nonce' => 'hsu94j3884jdopsl',
            'oauth_version' => '1.0'
        );
        $signature = new Signature\Hmac('1234567890', '0987654321', 'SHA1');
        $this->assertEquals('b0J6H0jCEo+tvzVJy2G615sM6/M=', $signature->sign($params));
    }

}
