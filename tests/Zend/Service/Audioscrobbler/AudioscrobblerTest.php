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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Service\Audioscrobbler;

use Zend\Service\Audioscrobbler;

/**
 * @category   Zend
 * @package    Zend_Service_Audioscrobbler
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Service
 * @group      Zend_Service_Audioscrobbler
 */
class AudioscrobblerTest extends AudioscrobblerTestCase
{
    public function testRequestThrowsRuntimeExceptionWithNoUserError()
    {
        $this->setExpectedException('Zend\Service\Audioscrobbler\Exception\RuntimeException', 'No user exists with this name');

        $this->setAudioscrobblerResponse(self::readTestResponse('errorNoUserExists'));
        $as = $this->getAudioscrobblerService();
        $as->set('user', 'foobarfoo');

        $response = $as->userGetProfileInformation();
    }

    public function testRequestThrowsRuntimeExceptionWithoutSuccessfulResponse()
    {
        $this->setExpectedException('Zend\Service\Audioscrobbler\Exception\RuntimeException', '404');

        $this->setAudioscrobblerResponse(self::readTestResponse('errorResponseStatusError'));
        $as = $this->getAudioscrobblerService();
        $as->set('user', 'foobarfoo');

        $response = $as->userGetProfileInformation();
    }

    /**
     * @group ZF-4509
     */
    public function testSetViaCallIntercept()
    {
        $as = new Audioscrobbler\Audioscrobbler();
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
        $this->setExpectedException("Zend\Service\Audioscrobbler\Exception\BadMethodCallException", 'does not exist in class');

        $as = new Audioscrobbler\Audioscrobbler();
        $as->someInvalidMethod();
    }

    /**
     * @group ZF-6251
     */
    public function testCallInterceptMethodsRequireExactlyOneParameterAndThrowExceptionOtherwise()
    {
        $this->setExpectedException("Zend\Service\Audioscrobbler\Exception\InvalidArgumentException", 'A value is required for setting a parameter field');
        
        $as = new Audioscrobbler\Audioscrobbler();
        $as->setUser();
    }

    public static function readTestResponse($file)
    {
        return file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . $file);
    }
}
