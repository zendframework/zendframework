<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link       http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright  Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd New BSD License
 * @package    Zend_Stdlib
 * @subpackage StringWrapper
 */

namespace ZendTest\Stdlib\StringWrapper;

use Zend\Stdlib\StringWrapper\Native;

class NativeTest extends CommonStringWrapperTest
{

    protected function getWrapper($encoding = null, $convertEncoding = null)
    {
        if ($encoding === null) {
            $supportedEncodings = Native::getSupportedEncodings();
            $encoding = array_shift($supportedEncodings);
        }

        if (!Native::isSupported($encoding, $convertEncoding)) {
            return false;
        }

        $wrapper = new Native();
        $wrapper->setEncoding($encoding, $convertEncoding);
        return $wrapper;
    }
}
