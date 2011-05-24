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
 * @package    Zend_Service_LiveDocx
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * @namespace
 */
namespace ZendTest\Service;

namespace Zend\Service\LiveDocx;


class ConcreteLiveDocx extends AbstractLiveDocx { }


/**
 * @category   Zend
 * @package    Zend_Service_LiveDocx
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Service
 * @group      Zend_Service_LiveDocx
 */
class ConcreteLiveDocxTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (!constant('TESTS_ZEND_SERVICE_LIVEDOCX_USERNAME') ||
                !constant('TESTS_ZEND_SERVICE_LIVEDOCX_PASSWORD')) {
            $this->markTestSkipped('LiveDocx tests disabled');
            return true;
        }
    }
    
    public function tearDown() {}

    // -------------------------------------------------------------------------

    public function testGetFormat()
    {
        $_concreteLiveDocx = new ConcreteLiveDocx();
        
        $this->assertEquals('',    $_concreteLiveDocx->getFormat('document'));
        $this->assertEquals('doc', $_concreteLiveDocx->getFormat('document.doc'));
        $this->assertEquals('doc', $_concreteLiveDocx->getFormat('document-123.doc'));
        $this->assertEquals('doc', $_concreteLiveDocx->getFormat('document123.doc'));
        $this->assertEquals('doc', $_concreteLiveDocx->getFormat('document.123.doc'));

        unset($_concreteLiveDocx);
    }

    public function testGetVersion()
    {
        $_concreteLiveDocx = new ConcreteLiveDocx();

        $this->assertEquals('2.0', $_concreteLiveDocx->getVersion());

        unset($_concreteLiveDocx);
    }

    public function testSetWsdlGetWsdl()
    {
        $wsdl = 'http://example.com/somewhere.wsdl';

        $_concreteLiveDocx = new ConcreteLiveDocx();
        $_concreteLiveDocx->setUsername(TESTS_ZEND_SERVICE_LIVEDOCX_USERNAME)
                          ->setPassword(TESTS_ZEND_SERVICE_LIVEDOCX_PASSWORD)
                          ->setWsdl($wsdl);

        $this->assertTrue($wsdl === $_concreteLiveDocx->getWsdl());

        unset($_concreteLiveDocx);
    }

    public function testSetWsdlGetWsdlWithSoapClient()
    {
        $wsdl = 'http://example.com/somewhere.wsdl';

        $_concreteLiveDocx = new ConcreteLiveDocx();

        $soapClient = new \Zend\Soap\Client();
        $soapClient->setWsdl($wsdl);

        $_concreteLiveDocx->setSoapClient($soapClient);

        $this->assertTrue($wsdl === $_concreteLiveDocx->getWsdl());

        unset($_concreteLiveDocx);
    }

    // -------------------------------------------------------------------------

}
