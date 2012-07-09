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

/**
 * An Amazon EC2 interface to query which Regions your account has access to.
 *
 * @category   Zend
 * @package    Zend_Service_Amazon
 * @subpackage Ec2
 */
class Region extends AbstractEc2
{

    /**
     * Describes availability zones that are currently available to the account
     * and their states.
     *
     * @param string|array $region              Name of an region.
     * @return array                            An array that contains all the return items.  Keys: regionName and regionUrl.
     */
    public function describe($region = null)
    {
        $params = array();
        $params['Action'] = 'DescribeRegions';

        if(is_array($region) && !empty($region)) {
            foreach($region as $k=>$name) {
                $params['Region.' . ($k+1)] = $name;
            }
        } elseif($region) {
            $params['Region.1'] = $region;
        }

        $response = $this->sendRequest($params);

        $xpath  = $response->getXPath();
        $nodes  = $xpath->query('//ec2:item');

        $return = array();
        foreach ($nodes as $k => $node) {
            $item = array();
            $item['regionName']   = $xpath->evaluate('string(ec2:regionName/text())', $node);
            $item['regionUrl']  = $xpath->evaluate('string(ec2:regionUrl/text())', $node);

            $return[] = $item;
            unset($item);
        }

        return $return;
    }
}
