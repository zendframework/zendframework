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
 * @package    Zend_Service_Nirvanix
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Service\Nirvanix;

/**
 * @see        FunctionalTestCase
 * @category   Zend
 * @package    Zend_Service_Nirvanix
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
