<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Cloud
 */

namespace ZendTest\Cloud\Infrastructure;

use Zend\Config\Config;
use Zend\Cloud\Infrastructure\Factory as CloudFactory;
use ZendTest\Cloud\Infrastructure\Adapter\Ec2OfflineTest;
use ZendTest\Cloud\Infrastructure\Adapter\RackspaceOfflineTest;

/**
 * Test class for Zend_Cloud_Infrastructure_Factory
 *
 * @category   Zend
 * @package    Zend_Cloud
 * @subpackage UnitTests
 * @group      Zend_Cloud
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testGetInfrastructureAdapterKey()
    {
        $this->assertTrue(is_string(CloudFactory::INFRASTRUCTURE_ADAPTER_KEY));
    }

    public function testGetAdapterWithConfig()
    {
        // EC2 adapter
        $Ec2Adapter = CloudFactory::getAdapter(
                            new Config(Ec2OfflineTest::getConfigArray(),true)
                      );

        $this->assertEquals('Zend\Cloud\Infrastructure\Adapter\Ec2', get_class($Ec2Adapter));

        // Rackspace adapter
        $rackspaceAdapter = CloudFactory::getAdapter(
                                new Config(RackspaceOfflineTest::getConfigArray(),true)
                            );

        $this->assertEquals('Zend\Cloud\Infrastructure\Adapter\Rackspace', get_class($rackspaceAdapter));
    }
}
