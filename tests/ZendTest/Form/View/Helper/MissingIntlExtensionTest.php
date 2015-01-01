<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Form\View\Helper;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Form\View\Helper\FormDateSelect as FormDateSelectHelper;
use Zend\Form\View\Helper\FormDateTimeSelect as FormDateTimeSelectHelper;
use Zend\Form\View\Helper\FormMonthSelect as FormMonthSelectHelper;

class MissingIntlExtensionTest extends TestCase
{
    public function setUp()
    {
        if (extension_loaded('intl')) {
            $this->markTestSkipped('ext/intl enabled');
        }
    }

    public function testFormDateSelectHelper()
    {
        $this->setExpectedException(
            'Zend\Form\Exception\ExtensionNotLoadedException',
            'Zend\Form\View\Helper component requires the intl PHP extension'
        );

        $helper = new FormDateSelectHelper();
    }

    public function testFormDateTimeSelectHelper()
    {
        $this->setExpectedException(
            'Zend\Form\Exception\ExtensionNotLoadedException',
            'Zend\Form\View\Helper component requires the intl PHP extension'
        );

        $helper = new FormDateTimeSelectHelper();
    }

    public function testFormMonthSelectHelper()
    {
        $this->setExpectedException(
            'Zend\Form\Exception\ExtensionNotLoadedException',
            'Zend\Form\View\Helper component requires the intl PHP extension'
        );

        $helper = new FormMonthSelectHelper();
    }
}
