<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Soap
 */

namespace Zend\Soap;
use ZendTest\Soap\TestAsset\MockCallUserFunc;

/**
 * Function interceptor for call_user_func.
 *
 * @return mixed Return value.
 */
function call_user_func()
{
    if (!MockCallUserFunc::$mock) {
        return call_user_func_array('\call_user_func', func_get_args());
    }

    MockCallUserFunc::$params = func_get_args();

    $result = '<s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">'
        . '<s:Body>';

    $result .= '<TestMethodResponse xmlns="http://unit/test">'
        . '<TestMethodResult>'
        . '<TestMethodResult><dummy></dummy></TestMethodResult>'
        . '</TestMethodResult>'
        . '</TestMethodResponse>';

    $result .= '</s:Body>'
        . '</s:Envelope>';

    return $result;
}
