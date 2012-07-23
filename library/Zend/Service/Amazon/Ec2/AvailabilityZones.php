<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

namespace Zend\Service\Amazon\Ec2;

use Zend\Service\Amazon;
use Zend\Service\Amazon\Ec2\Exception;

/**
 * An Amazon EC2 interface to query which Availibity Zones your account has access to.
 *
 * @category   Zend
 * @package    Zend_Service_Amazon
 * @subpackage Ec2
 */
class AvailabilityZones extends AbstractEc2
{
    /**
     * Describes availability zones that are currently available to the account
     * and their states.
     *
     * @param string|array $zoneName            Name of an availability zone.
     * @return array                            An array that contains all the return items.  Keys: zoneName and zoneState.
     */
    public function describe($zoneName = null)
    {
        $params = array();
        $params['Action'] = 'DescribeAvailabilityZones';

        if(is_array($zoneName) && !empty($zoneName)) {
            foreach($zoneName as $k=>$name) {
                $params['ZoneName.' . ($k+1)] = $name;
            }
        } elseif($zoneName) {
            $params['ZoneName.1'] = $zoneName;
        }

        $response = $this->sendRequest($params);

        $xpath  = $response->getXPath();
        $nodes  = $xpath->query('//ec2:item');

        $return = array();
        foreach ($nodes as $k => $node) {
            $item = array();
            $item['zoneName']   = $xpath->evaluate('string(ec2:zoneName/text())', $node);
            $item['zoneState']  = $xpath->evaluate('string(ec2:zoneState/text())', $node);

            $return[] = $item;
            unset($item);
        }

        return $return;
    }
}
