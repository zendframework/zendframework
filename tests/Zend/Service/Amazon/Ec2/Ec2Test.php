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
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

require_once dirname(__FILE__) . '/../../../../TestHelper.php';
require_once 'Zend/Service/Amazon/Ec2.php';

/**
 * Zend_Service_Amazon_Ec2 test case.
 *
 * @category   Zend
 * @package    Zend_Service_Amazon
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Service
 * @group      Zend_Service_Amazon
 * @group      Zend_Service_Amazon_Ec2
 */
class Zend_Service_Amazon_Ec2_Ec2Test extends PHPUnit_Framework_TestCase
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
        $object = Zend_Service_Amazon_Ec2::factory('keypair', 'access_key', 'secret_access_key');
        $this->assertType('Zend_Service_Amazon_Ec2_Keypair', $object);
    }

    public function testFactoryReturnsElasticIpObject()
    {
        $object = Zend_Service_Amazon_Ec2::factory('elasticip', 'access_key', 'secret_access_key');
        $this->assertType('Zend_Service_Amazon_Ec2_Elasticip', $object);
    }


    public function testFactoryReturnsEbsObject()
    {
        $object = Zend_Service_Amazon_Ec2::factory('ebs', 'access_key', 'secret_access_key');
        $this->assertType('Zend_Service_Amazon_Ec2_Ebs', $object);
    }

    public function testFactoryReturnImageObject()
    {
        $object = Zend_Service_Amazon_Ec2::factory('image', 'access_key', 'secret_access_key');
        $this->assertType('Zend_Service_Amazon_Ec2_Image', $object);
    }

    public function testFactoryReturnsInstanceObject()
    {
        $object = Zend_Service_Amazon_Ec2::factory('instance', 'access_key', 'secret_access_key');
        $this->assertType('Zend_Service_Amazon_Ec2_Instance', $object);
    }

    public function testFactoryReturnsSecurityGroupsObject()
    {
        $object = Zend_Service_Amazon_Ec2::factory('security', 'access_key', 'secret_access_key');
        $this->assertType('Zend_Service_Amazon_Ec2_Securitygroups', $object);
    }

}

