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
 * @package    Zend_Tool
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Tool\Framework;

/**
 * @category   Zend
 * @package    Zend_Tool
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface Registry
{


    /**
     * setClient()
     *
     * @param \Zend\Tool\Framework\Client\AbstractClient $client
     * @return \Zend\Tool\Framework\Registry
     */
    public function setClient(\Zend\Tool\Framework\Client\AbstractClient $client);

    /**
     * getClient() return the client in the registry
     *
     * @return \Zend\Tool\Framework\Client\AbstractClient
     */
    public function getClient();

    /**
     * setLoader()
     *
     * @param \Zend\Tool\Framework\Loader $loader
     * @return \Zend\Tool\Framework\Registry
     */
    public function setLoader(\Zend\Tool\Framework\Loader $loader);

    /**
     * getLoader()
     *
     * @return \Zend\Tool\Framework\Loader\AbstractLoader
     */
    public function getLoader();

    /**
     * setActionRepository()
     *
     * @param \Zend\Tool\Framework\Action\Repository $actionRepository
     * @return \Zend\Tool\Framework\Registry
     */
    public function setActionRepository(\Zend\Tool\Framework\Action\Repository $actionRepository);

    /**
     * getActionRepository()
     *
     * @return \Zend\Tool\Framework\Action\Repository
     */
    public function getActionRepository();

    /**
     * setProviderRepository()
     *
     * @param \Zend\Tool\Framework\Provider\Repository $providerRepository
     * @return \Zend\Tool\Framework\Registry
     */
    public function setProviderRepository(\Zend\Tool\Framework\Provider\Repository $providerRepository);

    /**
     * getProviderRepository()
     *
     * @return \Zend\Tool\Framework\Provider\Repository
     */
    public function getProviderRepository();

    /**
     * setManifestRepository()
     *
     * @param \Zend\Tool\Framework\Manifest\Repository $manifestRepository
     * @return \Zend\Tool\Framework\Registry
     */
    public function setManifestRepository(\Zend\Tool\Framework\Manifest\Repository $manifestRepository);

    /**
     * getManifestRepository()
     *
     * @return \Zend\Tool\Framework\Manifest\Repository
     */
    public function getManifestRepository();

    /**
     * setRequest()
     *
     * @param \Zend\Tool\Framework\Client\Request $request
     * @return \Zend\Tool\Framework\Registry
     */
    public function setRequest(\Zend\Tool\Framework\Client\Request $request);

    /**
     * getRequest()
     *
     * @return \Zend\Tool\Framework\Client\Request
     */
    public function getRequest();

    /**
     * setResponse()
     *
     * @param \Zend\Tool\Framework\Client\Response $response
     * @return \Zend\Tool\Framework\Registry
     */
    public function setResponse(\Zend\Tool\Framework\Client\Response $response);

    /**
     * getResponse()
     *
     * @return \Zend\Tool\Framework\Client\Response
     */
    public function getResponse();

}
