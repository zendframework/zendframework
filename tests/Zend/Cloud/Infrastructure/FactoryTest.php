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
 * @package    Zend\Cloud
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Cloud\Infrastructure;

use Zend\Config\Config,
    Zend\Cloud\Infrastructure\Factory as CloudFactory,
    ZendTest\Cloud\Infrastructure\Adapter\Ec2OfflineTest,
    ZendTest\Cloud\Infrastructure\Adapter\RackspaceOfflineTest;

/**
 * Test class for Zend_Cloud_Infrastructure_Factory
 *
 * @category   Zend
 * @package    Zend_Cloud
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Cloud
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testGetInfrastructureAdapterKey()
    {
        $this->assertTrue(is_string(CloudFactory::INFRASTRUCTURE_ADAPTER_KEY));
    }

    public function testGetAdapterWithConfig() {
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
