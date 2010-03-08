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
 * Test helper
 */

/**
 * @see Zend_Service_Audioscrobbler
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
class Zend_Service_Audioscrobbler_AudioscrobblerTest extends Zend_Service_Audioscrobbler_AudioscrobblerTestCase
{
    public function testRequestThrowsHttpClientExceptionWithNoUserError()
    {
        $this->setAudioscrobblerResponse(self::readTestResponse('errorNoUserExists'));
        $as = $this->getAudioscrobblerService();
        $as->set('user', 'foobarfoo');

        try {
            $response = $as->userGetProfileInformation();
            $this->fail('Expected Zend_Service_Technorati_Exception not thrown');
        } catch(Zend_Http_Client_Exception $e) {
            $this->assertContains("No user exists with this name", $e->getMessage());
        }
    }

    public function testRequestThrowsHttpClientExceptionWithoutSuccessfulResponse()
    {
        $this->setAudioscrobblerResponse(self::readTestResponse('errorResponseStatusError'));
        $as = $this->getAudioscrobblerService();
        $as->set('user', 'foobarfoo');

        try {
            $response = $as->userGetProfileInformation();
            $this->fail('Expected Zend_Service_Technorati_Exception not thrown');
        } catch(Zend_Http_Client_Exception $e) {
            $this->assertContains("404", $e->getMessage());
        }
    }

    /**
     * @group ZF-4509
     */
    public function testSetViaCallIntercept()
    {
        $as = new Zend_Service_Audioscrobbler();
        $as->setUser("foobar");
        $as->setAlbum("Baz");
        $this->assertEquals("foobar", $as->get("user"));
        $this->assertEquals("Baz",    $as->get("album"));
    }

    /**
     * @group ZF-6251
     */
    public function testUnknownMethodViaCallInterceptThrowsException()
    {
        $this->setExpectedException("Zend_Service_Exception");

        $as = new Zend_Service_Audioscrobbler();
        $as->someInvalidMethod();
    }

    /**
     * @group ZF-6251
     */
    public function testCallInterceptMethodsRequireExactlyOneParameterAndThrowExceptionOtherwise()
    {
        $this->setExpectedException("Zend_Service_Exception");

        $as = new Zend_Service_Audioscrobbler();
        $as->setUser();
    }

    public static function readTestResponse($file)
    {
        return file_get_contents(dirname(__FILE__) . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . $file);
    }
}
