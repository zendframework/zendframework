<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

/**
 * Zend_Service_DeveloperGarden test case
 *
 * @category   Zend
 * @package    Zend_Service_DeveloperGarden
 * @subpackage UnitTests
 * @group      Zend_Service
 * @group      Zend_Service_DeveloperGarden
 */
class Zend_Service_DeveloperGarden_LocalSearchTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Zend_Service_DeveloperGarden_LocalSearch_Mock
     */
    protected $_service = null;

    public function setUp()
    {
        if (!defined('TESTS_ZEND_SERVICE_DEVELOPERGARDEN_ONLINE_ENABLED') ||
            TESTS_ZEND_SERVICE_DEVELOPERGARDEN_ONLINE_ENABLED !== true) {
            $this->markTestSkipped('TESTS_ZEND_SERVICE_DEVELOPERGARDEN_ONLINE_ENABLED is not enabled');
        }
        if (!defined('TESTS_ZEND_SERVICE_DEVELOPERGARDEN_ONLINE_LOGIN')) {
            define('TESTS_ZEND_SERVICE_DEVELOPERGARDEN_ONLINE_LOGIN', 'Unknown');
        }
        if (!defined('TESTS_ZEND_SERVICE_DEVELOPERGARDEN_ONLINE_PASSWORD')) {
            define('TESTS_ZEND_SERVICE_DEVELOPERGARDEN_ONLINE_PASSWORD', 'Unknown');
        }
        $config = array(
            'username' => TESTS_ZEND_SERVICE_DEVELOPERGARDEN_ONLINE_LOGIN,
            'password' => TESTS_ZEND_SERVICE_DEVELOPERGARDEN_ONLINE_PASSWORD,
        );
        $this->service = new Zend_Service_DeveloperGarden_LocalSearch_Mock($config);
    }

    public function testLocalSearchValid()
    {
        $searchParameters = new Zend_Service_DeveloperGarden_LocalSearch_SearchParameters();
        $searchParameters->setWhere('Berlin')
                         ->setWhat('Pizza')
                         ->setHits(3);
        try {
            $result = $this->service->localSearch($searchParameters);
            $this->assertInstanceOf('Zend_Service_DeveloperGarden_Response_LocalSearch_LocalSearchResponseType',
                              $result->getSearchResult());
            $this->assertEquals('0000', $result->getErrorCode());
        } catch (Exception $e) {
            if ($e->getMessage() != 'quotas have exceeded') {
                throw $e;
            } else {
                $this->markTestSkipped('Quota exceeded.');
            }
        }
    }

    /**
     * @expectedException Zend_Service_DeveloperGarden_Response_Exception
     */
    public function testLocalSearchInValid()
    {
        $searchParameters = new Zend_Service_DeveloperGarden_LocalSearch_SearchParameters();
        $searchParameters->setWhere('Berlin');
        $result = $this->service->localSearch($searchParameters);
    }
}

class Zend_Service_DeveloperGarden_LocalSearch_Mock
    extends Zend_Service_DeveloperGarden_LocalSearch
{

}
