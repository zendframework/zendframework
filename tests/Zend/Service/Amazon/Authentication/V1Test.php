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
 * @package    Zend_Service_Amazon_Authentication
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: AllTests.php 11973 2008-10-15 16:00:56Z matthew $
 */


require_once dirname(__FILE__) . '/../../../../TestHelper.php';

require_once 'Zend/Service/Amazon/Authentication/V1.php';

/**
 * Amazon V1 authentication test case
 *
 * @category   Zend
 * @package    Zend_Service_Amazon_Authentication
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_Amazon_Authentication_V1Test extends PHPUnit_Framework_TestCase
{

    /**
     * @var Zend_Service_Amazon_Authentication_V2
     */
    private $Zend_Service_Amazon_Authentication_V2;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->Zend_Service_Amazon_Authentication_V1 = new Zend_Service_Amazon_Authentication_V1('0PN5J17HBGZHT7JJ3X82', 'uV3F3YluFJax1cknvbcGwgjvx4QpvB+leU8dUj2o', '2007-12-01');
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $this->Zend_Service_Amazon_Authentication_V1 = null;

        parent::tearDown();
    }

    /**
     * Tests Zend_Service_Amazon_Authentication_V2::generateSignature()
     */
    public function testGenerateDevPaySignature()
    {
        $url = "https://ls.amazonaws.com/";
        $params = array();
        $params['Action'] = "ActivateHostedProduct";
        $params['Timestamp'] = "2009-11-11T13:52:38Z";

        $ret = $this->Zend_Service_Amazon_Authentication_V1->generateSignature($url, $params);

        $this->assertEquals('31Q2YlgABM5X3GkYQpGErcL10Xc=', $params['Signature']);
        $this->assertEquals("ActionActivateHostedProductAWSAccessKeyId0PN5J17HBGZHT7JJ3X82SignatureVersion1Timestamp2009-11-11T13:52:38ZVersion2007-12-01", $ret);
    }

}

