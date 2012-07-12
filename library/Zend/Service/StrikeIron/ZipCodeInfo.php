<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

namespace Zend\Service\StrikeIron;

/**
 * @category   Zend
 * @package    Zend_Service
 * @subpackage StrikeIron
 */
class ZipCodeInfo extends Base
{
    /**
     * Configuration options
     * @param array
     */
    protected $options = array(
        'username' => null,
        'password' => null,
        'client'   => null,
        'options'  => null,
        'headers'  => null,
        'wsdl'     => 'http://sdpws.strikeiron.com/zf1.StrikeIron/sdpZIPCodeInfo?WSDL',
    );
}
