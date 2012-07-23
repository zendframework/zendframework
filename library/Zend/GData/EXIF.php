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
 * Service class for interacting with the services which use the EXIF extensions
 * @link http://code.google.com/apis/picasaweb/reference.html#exif_reference
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage Exif
 */
class EXIF extends GData
{

    /**
     * Namespaces used for Zend_Gdata_Exif
     *
     * @var array
     */
    public static $namespaces = array(
        array('exif', 'http://schemas.google.com/photos/exif/2007', 1, 0)
    );

    /**
     * Create Zend_Gdata_Exif object
     *
     * @param \Zend\Http\Client $client (optional) The HTTP client to use when
     *          when communicating with the Google servers.
     * @param string $applicationId The identity of the app in the form of Company-AppName-Version
     */
    public function __construct($client = null, $applicationId = 'MyCompany-MyApp-1.0')
    {
        $this->registerPackage('Zend\GData\EXIF');
        $this->registerPackage('Zend\GData\EXIF\Extension');
        parent::__construct($client, $applicationId);
    }

}
