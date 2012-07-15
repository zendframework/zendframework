<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

namespace ZendTest\Service\Amazon\Ec2;

use Zend\Service\Amazon\Ec2;

/**
 * Zend\Service\Amazon\Ec2 test case.
 *
 * @category   Zend
 * @package    Zend_Service_Amazon
 * @subpackage UnitTests
 * @group      Zend_Service
 * @group      Zend_Service_Amazon
 * @group      Zend_Service_Amazon_Ec2
 */
class Ec2Test extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Zend\Service\Amazon\Ec2
     */
    private $ec2Instance;

    public function testFactoryReturnsKeyPairObject()
    {
        $object = Ec2\Ec2::factory('keypair', 'access_key', 'secret_access_key');
        $this->assertInstanceOf('Zend\Service\Amazon\Ec2\Keypair', $object);
    }

    public function testFactoryReturnsElasticIpObject()
    {
        $object = Ec2\Ec2::factory('elasticip', 'access_key', 'secret_access_key');
        $this->assertInstanceOf('Zend\Service\Amazon\Ec2\Elasticip', $object);
    }

    public function testFactoryReturnsEbsObject()
    {
        $object = Ec2\Ec2::factory('ebs', 'access_key', 'secret_access_key');
        $this->assertInstanceOf('Zend\Service\Amazon\Ec2\Ebs', $object);
    }

    public function testFactoryReturnsAvailabilityZonesObject()
    {
        $object = Ec2\Ec2::factory('availabilityzones', 'access_key', 'secret_access_key');
        $this->assertInstanceOf('Zend\Service\Amazon\Ec2\AvailabilityZones', $object);
    }

    public function testFactoryReturnImageObject()
    {
        $object = Ec2\Ec2::factory('image', 'access_key', 'secret_access_key');
        $this->assertInstanceOf('Zend\Service\Amazon\Ec2\Image', $object);
    }

    public function testFactoryReturnsInstanceObject()
    {
        $object = Ec2\Ec2::factory('instance', 'access_key', 'secret_access_key');
        $this->assertInstanceOf('Zend\Service\Amazon\Ec2\Instance', $object);
    }

    public function testFactoryReturnsSecurityGroupsObject()
    {
        $object = Ec2\Ec2::factory('security', 'access_key', 'secret_access_key');
        $this->assertInstanceOf('Zend\Service\Amazon\Ec2\Securitygroups', $object);
    }

    public function testFactoryWillFailInvalidSection()
    {
        try {
            $object = Ec2\Ec2::factory('avaavaavailabilityzones', 'access_key', 'secret_access_key');
            $this->fail('RuntimeException was expected but not thrown');
        } catch (Ec2\Exception\RuntimeException $e) {
        }
    }
}

