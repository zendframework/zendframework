<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

namespace ZendTest\Service\Nirvanix;

/**
 * @category   Zend
 * @package    Zend_Service_Nirvanix
 * @subpackage UnitTests
 * @group      Zend_Service
 * @group      Zend_Service_Nirvanix
 */
class NirvanixTest extends FunctionalTestCase
{
    // getService()

    public function testFactoryReturnsBaseWhenNoSubclassAvailable()
    {
        $base = $this->nirvanix->getService('Foo');
        $this->assertInstanceOf('Zend\Service\Nirvanix\Context\Base', $base);
    }

    public function testFactoryReturnsImfsSubclassForImfsNamespace()
    {
        $imfs = $this->nirvanix->getService('IMFS');
        $this->assertInstanceOf('Zend\Service\Nirvanix\Context\Imfs', $imfs);
    }

    public function testFactoryPassesHttpClientInstanceWithOptions()
    {
        $nirvanixOptions = $this->nirvanix->getOptions();
        $this->assertSame($this->httpClient, $nirvanixOptions['httpClient']);

        $foo = $this->nirvanix->getService('Foo');
        $fooOptions = $foo->getOptions();
        $this->assertSame($this->httpClient, $nirvanixOptions['httpClient']);
    }

    // getOptions()

    public function testGetOptionsReturnsOptions()
    {
        $options = $this->nirvanix->getOptions();
        $this->assertSame($this->httpClient, $options['httpClient']);
    }

}
