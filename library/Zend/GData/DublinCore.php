<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_GData
 */

namespace Zend\GData;

/**
 * Service class for interacting with the services which use the
 * DublinCore extensions.
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage DublinCore
 */
class DublinCore extends GData
{

    /**
     * Namespaces used for Zend_Gdata_DublinCore
     *
     * @var array
     */
    public static $namespaces = array(
        array('dc', 'http://purl.org/dc/terms', 1, 0)
    );

    /**
     * Create Zend_Gdata_DublinCore object
     *
     * @param \Zend\Http\Client $client (optional) The HTTP client to use when
     *          when communicating with the Google servers.
     * @param string $applicationId The identity of the app in the form of Company-AppName-Version
     */
    public function __construct($client = null, $applicationId = 'MyCompany-MyApp-1.0')
    {
        $this->registerPackage('Zend\GData\DublinCore');
        $this->registerPackage('Zend\GData\DublinCore\Extension');
        parent::__construct($client, $applicationId);
    }

}
