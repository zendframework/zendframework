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
 * @package    Zend_Soap
 * @subpackage Client
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Soap\Client;

use Zend\Soap\Client as SOAPClient,
    Zend\Soap\Exception;

/**
 * .NET SOAP client
 *
 * Class is intended to be used with .Net Web Services.
 *
 * Important! Class is at experimental stage now.
 * Please leave your notes, compatiblity issues reports or
 * suggestions in fw-webservices@lists.zend.com or fw-general@lists.com
 *
 * @uses       \Zend\Soap\Client
 * @uses       \Zend\Soap\ClientException
 * @category   Zend
 * @package    Zend_Soap
 * @subpackage Client
 */
class DotNet extends SOAPClient
{
    /**
     * Constructor
     *
     * @param string $wsdl
     * @param array $options
     */
    public function __construct($wsdl = null, $options = null)
    {
        // Use SOAP 1.1 as default
        $this->setSoapVersion(SOAP_1_1);

        parent::__construct($wsdl, $options);
    }


    /**
     * Perform arguments pre-processing
     *
     * My be overridden in descendant classes
     *
     * @param array $arguments
     * @throws \Zend\Soap\ClientException
     */
    protected function _preProcessArguments($arguments)
    {
        if (count($arguments) > 1  ||
            (count($arguments) == 1  &&  !is_array(reset($arguments)))
           ) {
            throw new Exception\RuntimeException('.Net webservice arguments have to be grouped into array: array(\'a\' => $a, \'b\' => $b, ...).');
        }

        // Do nothing
        return $arguments;
    }

    /**
     * Perform result pre-processing
     *
     * My be overridden in descendant classes
     *
     * @param array $arguments
     */
    protected function _preProcessResult($result)
    {
        $resultProperty = $this->getLastMethod() . 'Result';

        return $result->$resultProperty;
    }

}
