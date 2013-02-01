<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Paginator
 */

namespace ZendTest\Paginator;

use Zend\Paginator;
use Zend\Paginator\Adapter;
use ZendTest\Paginator\TestAsset\TestArrayAggregate;

/**
 * @category   Zend
 * @package    Zend_Paginator
 * @subpackage UnitTests
 * @group      Zend_Paginator
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $mockSelect;

    protected $mockAdapter;

    protected function setUp()
    {
        $this->mockSelect = $this->getMock('Zend\Db\Sql\Select');

        $mockStatement = $this->getMock('Zend\Db\Adapter\Driver\StatementInterface');
        $mockResult = $this->getMock('Zend\Db\Adapter\Driver\ResultInterface');

        $mockDriver = $this->getMock('Zend\Db\Adapter\Driver\DriverInterface');
        $mockDriver->expects($this->any())->method('createStatement')->will($this->returnValue($mockStatement));
        $mockStatement->expects($this->any())->method('execute')->will($this->returnValue($mockResult));
        $mockPlatform = $this->getMock('Zend\Db\Adapter\Platform\PlatformInterface');
        $mockPlatform->expects($this->any())->method('getName')->will($this->returnValue('platform'));

        $this->mockAdapter = $this->getMockForAbstractClass(
            'Zend\Db\Adapter\Adapter',
            array($mockDriver, $mockPlatform)
        );
    }

    public function testCanFactoryPaginatorWithStringAdapterObject()
    {
        $datas = array(1, 2, 3);
        $paginator = Paginator\Factory::factory($datas, new Adapter\ArrayAdapter($datas));
        $this->assertInstanceOf('Zend\Paginator\Adapter\ArrayAdapter', $paginator->getAdapter());
        $this->assertEquals(count($datas), $paginator->getCurrentItemCount());
    }

    public function testCanFactoryPaginatorWithStringAdapterName()
    {
        $datas = array(1, 2, 3);
        $paginator = Paginator\Factory::factory($datas, 'array');
        $this->assertInstanceOf('Zend\Paginator\Adapter\ArrayAdapter', $paginator->getAdapter());
        $this->assertEquals(count($datas), $paginator->getCurrentItemCount());
    }

    public function testCanFactoryPaginatorWithStringAdapterAggregate()
    {
        $paginator = Paginator\Factory::factory(null, new TestArrayAggregate);
        $this->assertInstanceOf('Zend\Paginator\Adapter\ArrayAdapter', $paginator->getAdapter());
    }

    public function testCanFactoryPaginatorWithDbSelect()
    {
        $paginator = Paginator\Factory::factory(array($this->mockSelect, $this->mockAdapter), 'dbselect');
        $this->assertInstanceOf('Zend\Paginator\Adapter\DbSelect', $paginator->getAdapter());
    }

    public function testCanFactoryPaginatorWithOneParameterWithArrayAdapter()
    {
        $datas = array(
            'items' => array(1, 2, 3),
            'adapter' => 'array',
        );
        $paginator = Paginator\Factory::factory($datas);
        $this->assertInstanceOf('Zend\Paginator\Adapter\ArrayAdapter', $paginator->getAdapter());
        $this->assertEquals(count($datas['items']), $paginator->getCurrentItemCount());
    }

    public function testCanFactoryPaginatorWithOneParameterWithDbAdapter()
    {
        $datas = array(
            'items' => array($this->mockSelect, $this->mockAdapter),
            'adapter' => 'dbselect',
        );
        $paginator = Paginator\Factory::factory($datas);
        $this->assertInstanceOf('Zend\Paginator\Adapter\DbSelect', $paginator->getAdapter());
    }

    public function testCanFactoryPaginatorWithOneBadParameter()
    {
        $datas = array(
            array(1, 2, 3),
            'array',
        );
        $this->setExpectedException('Zend\Paginator\Exception\InvalidArgumentException');
        $paginator = Paginator\Factory::factory($datas);
    }
}
