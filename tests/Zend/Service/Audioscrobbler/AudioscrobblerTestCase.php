<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

namespace ZendTest\Service\Audioscrobbler;

use Zend\Http;
use Zend\Service\Audioscrobbler;

/**
 * @category   Zend
 * @package    Zend_Service_Audioscrobbler
 * @subpackage UnitTests
 * @group      Zend_Service
 * @group      Zend_Service_Audioscrobbler
 */
class AudioscrobblerTestCase extends \PHPUnit_Framework_TestCase
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
        $this->_httpTestAdapter = new Http\Client\Adapter\Test();
        $this->_httpClient = new Http\Client();
        $this->_httpClient->setOptions(array('adapter' => $this->_httpTestAdapter));
        $this->_asService = new Audioscrobbler\Audioscrobbler();
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
