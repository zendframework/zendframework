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
 * @package    Zend_Service_Amazon
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace ZendTest\Service\Amazon\Ec2;
use Zend\Service\Amazon\Ec2;

/**
 * Zend_Service_Amazon_Ec2 test case.
 *
 * @category   Zend
 * @package    Zend_Service_Amazon
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Service
 * @group      Zend_Service_Amazon
 * @group      Zend_Service_Amazon_Ec2
 */
class Ec2Test extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Zend_Service_Amazon_Ec2
     */
    private $Zend_Service_Amazon_Ec2;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        parent::tearDown();
    }

    public function testFactoryReturnsKeyPairObject()
    {
        $object = Ec2\Ec2::factory('keypair', 'access_key', 'secret_access_key');
        $this->assertType('\Zend\Service\Amazon\Ec2\Keypair', $object);
    }

    public function testFactoryReturnsElasticIpObject()
    {
        $object = Ec2\Ec2::factory('elasticip', 'access_key', 'secret_access_key');
        $this->assertType('\Zend\Service\Amazon\Ec2\Elasticip', $object);
    }


    public function testFactoryReturnsEbsObject()
    {
        $object = Ec2\Ec2::factory('ebs', 'access_key', 'secret_access_key');
        $this->assertType('\Zend\Service\Amazon\Ec2\Ebs', $object);
    }

    public function testFactoryReturnImageObject()
    {
        $object = Ec2\Ec2::factory('image', 'access_key', 'secret_access_key');
        $this->assertType('\Zend\Service\Amazon\Ec2\Image', $object);
    }

    public function testFactoryReturnsInstanceObject()
    {
        $object = Ec2\Ec2::factory('instance', 'access_key', 'secret_access_key');
        $this->assertType('\Zend\Service\Amazon\Ec2\Instance', $object);
    }

    public function testFactoryReturnsSecurityGroupsObject()
    {
        $object = Ec2\Ec2::factory('security', 'access_key', 'secret_access_key');
        $this->assertType('\Zend\Service\Amazon\Ec2\Securitygroups', $object);
    }

}

