<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

namespace ZendTest\Service\Amazon\Authentication;

use Zend\Service\Amazon\Authentication;
use Zend\Service\Amazon\Authentication\Exception;

/**
 * Amazon V1 authentication test case
 *
 * @category   Zend
 * @package    Zend_Service_Amazon_Authentication
 * @subpackage UnitTests
 */
class V1Test extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Authentication\V1
     */
    private $_amazon;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        $this->_amazon = new Authentication\V1('0PN5J17HBGZHT7JJ3X82', 'uV3F3YluFJax1cknvbcGwgjvx4QpvB+leU8dUj2o', '2007-12-01');
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->_amazon = null;
    }

    /**
     * Tests Authentication\V1::generateSignature()
     */
    public function testGenerateDevPaySignature()
    {
        $url = "https://ls.amazonaws.com/";
        $params = array();
        $params['Action'] = "ActivateHostedProduct";
        $params['Timestamp'] = "2009-11-11T13:52:38Z";

        $ret = $this->_amazon->generateSignature($url, $params);

        $this->assertEquals('31Q2YlgABM5X3GkYQpGErcL10Xc=', $params['Signature']);
        $this->assertEquals("ActionActivateHostedProductAWSAccessKeyId0PN5J17HBGZHT7JJ3X82SignatureVersion1Timestamp2009-11-11T13:52:38ZVersion2007-12-01", $ret);
    }

}

