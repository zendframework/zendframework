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

use Zend\Stdlib\Exception;
use Zend\Stdlib\StringWrapper\MbString;

class MbStringTest extends CommonStringWrapperTest
{

    public function setUp()
    {
        if (!extension_loaded('mbstring')) {
            try {
                new MbString('utf-8');
                $this->fail('Missing expected Zend\Stdlib\Exception\ExtensionNotLoadedException');
            } catch (Exception\ExtensionNotLoadedException $e) {
                $this->markTestSkipped('Missing ext/mbstring');
            }
        }

        parent::setUp();
    }

    protected function getWrapper($encoding = null, $convertEncoding = null)
    {
        if ($encoding === null) {
            $supportedEncodings = MbString::getSupportedEncodings();
            $encoding = array_shift($supportedEncodings);
        }

        if (!MbString::isSupported($encoding, $convertEncoding)) {
            return false;
        }

        $wrapper = new MbString();
        $wrapper->setEncoding($encoding, $convertEncoding);
        return $wrapper;
    }
}
