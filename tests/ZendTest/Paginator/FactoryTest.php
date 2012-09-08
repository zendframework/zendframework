<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Paginator
 */

namespace ZendTest\Paginator;

use Zend\Paginator\Factory;
use Zend\Paginator\Adapter\ArrayAdapter;

/**
 * @category   Zend
 * @package    Zend_Paginator
 * @subpackage UnitTests
 * @group      Zend_Paginator
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCanFactoryPaginatorWithStringAdapterObject()
    {
        $datas = array(1, 2, 3);
        $paginator = Factory::Factory($datas, new ArrayAdapter($datas));
        $this->assertInstanceOf('Zend\Paginator\Adapter\ArrayAdapter', $paginator->getAdapter());
        $this->assertEquals(count($datas), $paginator->getCurrentItemCount());
    }
    
    public function testCanFactoryPaginatorWithStringAdapterName()
    {
        $datas = array(1, 2, 3);
        $paginator = Factory::Factory($datas, 'array');
        $this->assertInstanceOf('Zend\Paginator\Adapter\ArrayAdapter', $paginator->getAdapter());
        $this->assertEquals(count($datas), $paginator->getCurrentItemCount());
    }
}