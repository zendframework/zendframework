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
 * @package    Zend_Application
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Application\Resource;

use Zend\Loader\Autoloader,
    Zend\Application\Application,
    ZendTest\Application\TestAsset\ZfAppBootstrap;

require_once __DIR__ . '/../TestAsset/resources/Foo.php';

/**
 * @category   Zend
 * @package    Zend_Application
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Application
 */
class ResourceAbstractTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->application = new Application('testing');

        $this->bootstrap = new ZfAppBootstrap($this->application);
    }

    public function tearDown()
    {
    }

    public function testBootstrapIsNullByDefault()
    {
        $resource = new \ZendTest\Application\TestAsset\Resource\Foo();
        $this->assertNull($resource->getBootstrap());
    }

    public function testResourceShouldAllowSettingParentBootstrap()
    {
        $resource = new \ZendTest\Application\TestAsset\Resource\Foo();
        $resource->setBootstrap($this->bootstrap);
        $this->assertSame($this->bootstrap, $resource->getBootstrap());
    }

    public function testOptionsAreStoredVerbatim()
    {
        $resource = new \ZendTest\Application\TestAsset\Resource\Foo();
        $options  = array(
            'foo' => 'bar',
        );
        $resource->setOptions($options);
        $this->assertEquals($options, $resource->getOptions());
    }

    public function testCallingSetOptionsMultipleTimesMergesOptions()
    {
        $resource = new \ZendTest\Application\TestAsset\Resource\Foo();
        $options1  = array(
            'foo' => 'bar',
        );
        $options2  = array(
            'bar' => 'baz',
        );
        $options3  = array(
            'foo' => 'BAR',
        );
        $expected = $resource->mergeOptions($options1, $options2);
        $expected = $resource->mergeOptions($expected, $options3);
        $resource->setOptions($options1)
                 ->setOptions($options2)
                 ->setOptions($options3);
        $this->assertEquals($expected, $resource->getOptions());
    }

    public function testSetOptionsProxiesToLocalSetters()
    {
        $resource = new \ZendTest\Application\TestAsset\Resource\Foo();
        $options  = array(
            'someArbitraryKey' => 'test',
        );
        $resource->setOptions($options);
        $this->assertEquals('test', $resource->someArbitraryKey);
    }

    public function testConstructorAcceptsArrayConfiguration()
    {
        $options  = array(
            'foo' => 'bar',
        );
        $resource = new \ZendTest\Application\TestAsset\Resource\Foo($options);
        $this->assertEquals($options, $resource->getOptions());
    }

    public function testConstructorAcceptsZendConfigObject()
    {
        $options  = array(
            'foo' => 'bar',
        );
        $config = new \Zend\Config\Config($options);
        $resource = new \ZendTest\Application\TestAsset\Resource\Foo($config);
        $this->assertEquals($options, $resource->getOptions());
    }

    /**
     * @group ZF-6593
     */
    public function testSetOptionsShouldRemoveBootstrapOptionWhenPassed()
    {
        $resource = new \ZendTest\Application\TestAsset\Resource\Foo();
        $resource->setOptions(array(
            'bootstrap' => $this->bootstrap,
        ));
        $this->assertSame($this->bootstrap, $resource->getBootstrap());
        $options = $resource->getOptions();
        $this->assertNotContains('bootstrap', array_keys($options));
    }

    /**
     * @group ZF-8520
     */
    public function testFirstResourceOptionShouldNotBeDropped()
    {
        $options = array(
            array('someData'),
            array('someMoreData'),
        );

        $resource = new \ZendTest\Application\TestAsset\Resource\Foo($options);
        $stored   = $resource->getOptions();
        $this->assertSame($options, $stored);
    }
}
