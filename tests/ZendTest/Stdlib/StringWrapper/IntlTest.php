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
use Zend\Stdlib\StringWrapper\Intl;

class IntlTest extends CommonStringWrapperTest
{

    public function setUp()
    {
        if (!extension_loaded('intl')) {
            try {
                new Intl('utf-8');
                $this->fail('Missing expected Zend\Stdlib\Exception\ExtensionNotLoadedException');
            } catch (Exception\ExtensionNotLoadedException $e) {
                $this->markTestSkipped('Missing ext/intl');
            }
        }

        parent::setUp();
    }

    protected function getWrapper($encoding = null, $convertEncoding = null)
    {
        if ($encoding === null) {
            $supportedEncodings = Intl::getSupportedEncodings();
            $encoding = array_shift($supportedEncodings);
        }

        if (!Intl::isSupported($encoding, $convertEncoding)) {
            return false;
        }

        $wrapper = new Intl();
        $wrapper->setEncoding($encoding, $convertEncoding);
        return $wrapper;
    }
}
