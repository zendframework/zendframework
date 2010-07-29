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
 * @package    Zend_Service_Audioscrobbler
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */




/**
 * @category   Zend
 * @package    Zend_Service_Audioscrobbler
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Service
 * @group      Zend_Service_Audioscrobbler
 */
class Zend_Service_Audioscrobbler_AudioscrobblerTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @var Zend_Http_Client
     */
    private $_httpClient = null;

    /**
     * @var Zend_Http_Client_Adapter_Test
     */
    private $_httpTestAdapter = null;

    /**
     * @var Zend_Service_Audioscrobbler
     */
    private $_asService = null;

    public function setUp()
    {
        $this->_httpTestAdapter = new Zend_Http_Client_Adapter_Test();
        $this->_httpClient = new Zend_Http_Client();
        $this->_httpClient->setConfig(array('adapter' => $this->_httpTestAdapter));
        $this->_asService = new Zend_Service_Audioscrobbler();
        $this->_asService->setHttpClient($this->_httpClient);
    }

    /**
     * @param string $responseMessage
     */
    protected function setAudioscrobblerResponse($responseMessage)
    {
        $this->_httpTestAdapter->setResponse($responseMessage);
    }

    /**
     * @return Zend_Service_Audioscrobbler
     */
    protected function getAudioscrobblerService()
    {
        return $this->_asService;
    }
}
