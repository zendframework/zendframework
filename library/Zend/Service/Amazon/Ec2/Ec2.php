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
 * @package    Zend_Service
 * @subpackage Amazon
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Service\Amazon\Ec2;
use Zend\Service\Amazon,
    Zend\Service\Amazon\Ec2\Exception;

/**
 * Amazon Ec2 Interface to allow easy creation of the Ec2 Components
 *
 * @uses       Zend_Loader
 * @uses       Zend\Service\Amazon\Ec2\Exception
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Amazon
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Ec2
{
    /**
     * Factory method to fetch what you want to work with.
     *
     * @param string $section           Create the method that you want to work with
     * @param string $key               Override the default aws key
     * @param string $secret_key        Override the default aws secretkey
     * @throws Zend\Service\Amazon\Ec2\Exception
     * @return object
     */
    public static function factory($section, $key = null, $secret_key = null)
    {
        switch(strtolower($section)) {
            case 'keypair':
                $class = '\Zend\Service\Amazon\Ec2\Keypair';
                break;
            case 'eip':
                // break left out
            case 'elasticip':
                $class = '\Zend\Service\Amazon\Ec2\ElasticIp';
                break;
            case 'ebs':
                $class = '\Zend\Service\Amazon\Ec2\Ebs';
                break;
            case 'availabilityzones':
                // break left out
            case 'zones':
                $class = '\Zend\Service\Amazon\Ec2\AvailabilityZones';
                break;
            case 'ami':
                // break left out
            case 'image':
                $class = '\Zend\Service\Amazon\Ec2\Image';
                break;
            case 'instance':
                $class = '\Zend\Service\Amazon\Ec2\Instance';
                break;
            case 'security':
                // break left out
            case 'securitygroups':
                $class = '\Zend\Service\Amazon\Ec2\SecurityGroups';
                break;
            default:
                throw new Exception\RuntimeException('Invalid Section: ' . $section);
                break;
        }

        return new $class($key, $secret_key);
    }
}

