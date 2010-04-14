<?php

namespace ZendTest\Session;

use Zend\Session\Handler\SessionHandler,
    Zend\Session\Handler\SessionHeader,
    Zend\Session\Handler,
    Zend\Registry;

class SessionHandlerTest extends \PHPUnit_Framework_TestCase
{
    public $error;

    public $cookieDateFormat = 'D, d-M-y H:i:s e';

    public function setUp()
    {
        $this->handler = new SessionHandler;
        $this->error   = false;
        Registry::_unsetInstance();
    }

    public function handleErrors($errno, $errstr)
    {
        $this->error = $errstr;
    }

    public function getTimestampFromCookie($cookie)
    {
        if (preg_match('/expires=([^;]+)/', $cookie, $matches)) {
            $ts = new \DateTime($matches[1]);
            return $ts;
        }
        return false;
    }

    /**
     * @runInSeparateProcess
     */
    public function testSessionExistsReturnsFalseWhenNoSessionStarted()
    {
        $this->assertFalse($this->handler->sessionExists());
    }

    /**
     * @runInSeparateProcess
     */
    public function testSessionExistsReturnsTrueWhenSessionStarted()
    {
        session_start();
        $this->assertTrue($this->handler->sessionExists());
    }

    /**
     * @runInSeparateProcess
     */
    public function testSessionExistsReturnsTrueWhenSessionStartedThenWritten()
    {
        session_start();
        session_write_close();
        $this->assertTrue($this->handler->sessionExists());
    }

    /**
     * @runInSeparateProcess
     */
    public function testSessionExistsReturnsFalseWhenSessionStartedThenDestroyed()
    {
        session_start();
        session_destroy();
        $this->assertFalse($this->handler->sessionExists());
    }

    /**
     * @runInSeparateProcess
     */
    public function testSessionIsStartedAfterCallingStart()
    {
        $this->assertFalse($this->handler->sessionExists());
        $this->handler->start();
        $this->assertTrue($this->handler->sessionExists());
    }

    /**
     * @runInSeparateProcess
     */
    public function testStartDoesNothingWhenCalledAfterWriteCloseOperation()
    {
        $this->handler->start();
        $id1 = session_id();
        session_write_close();
        $this->handler->start();
        $id2 = session_id();
        $this->assertTrue($this->handler->sessionExists());
        $this->assertEquals($id1, $id2);
    }

    /**
     * @runInSeparateProcess
     */
    public function testStartCreatesNewSessionIfPreviousSessionHasBeenDestroyed()
    {
        $this->handler->start();
        $id1 = session_id();
        session_destroy();
        $this->handler->start();
        $id2 = session_id();
        $this->assertTrue($this->handler->sessionExists());
        $this->assertNotEquals($id1, $id2);
    }

    /**
     * @outputBuffering disabled
     */
    public function testStartWillNotBlockHeaderSentNotices()
    {
        if ('cli' == PHP_SAPI) {
            $this->markTestSkipped('session_start() will not raise headers_sent warnings in CLI');
        }
        set_error_handler(array($this, 'handleErrors'), E_WARNING);
        echo ' ';
        $this->assertTrue(headers_sent());
        $this->handler->start();
        restore_error_handler();
        $this->assertTrue(is_string($this->error));
        $this->assertContains('already sent', $this->error);
    }

    /**
     * @runInSeparateProcess
     */
    public function testGetNameReturnsSessionName()
    {
        $ini = ini_get('session.name');
        $this->assertEquals($ini, $this->handler->getName());
    }

    /**
     * @runInSeparateProcess
     */
    public function testSetNameRaisesExceptionOnInvalidName()
    {
        $this->setExpectedException('Zend\\Session\\Exception', 'invalid characters');
        $this->handler->setName('foo bar!');
    }

    /**
     * @runInSeparateProcess
     */
    public function testSetNameSetsSessionNameOnSuccess()
    {
        $this->handler->setName('foobar');
        $this->assertEquals('foobar', $this->handler->getName());
        $this->assertEquals('foobar', session_name());
    }

    /**
     * @runInSeparateProcess
     */
    public function testCanSetNewSessionNameAfterSessionDestroyed()
    {
        $this->handler->start();
        session_destroy();
        $this->handler->setName('foobar');
        $this->assertEquals('foobar', $this->handler->getName());
        $this->assertEquals('foobar', session_name());
    }

    /**
     * @runInSeparateProcess
     */
    public function testSettingNameWhenAnActiveSessionExistsRaisesException()
    {
        $this->setExpectedException('Zend\\Session\\Exception', 'already started');
        $this->handler->start();
        $this->handler->setName('foobar');
    }

    /**
     * @runInSeparateProcess
     */
    public function testDestroyByDefaultSendsAnExpireCookie()
    {
        $config = $this->handler->getConfiguration();
        $config->setUseCookies(true);
        $this->handler->start();
        $this->handler->destroy();
        echo '';
        $headers = xdebug_get_headers();
        // $headers = Handler\headers_list();
        $found  = false;
        $sName  = $this->handler->getName();
        foreach ($headers as $header) {
            if (stristr($header, 'Set-Cookie:') && stristr($header, $sName)) {
                $found  = true;
            }
        }
        $this->assertTrue($found, 'No session cookie found: ' . var_export($headers, true));
    }

    /**
     * @runInSeparateProcess
     */
    public function testSendingFalseToSendExpireCookieWhenCallingDestroyShouldNotSendCookie()
    {
        $config = $this->handler->getConfiguration();
        $config->setUseCookies(true);
        $this->handler->start();
        $this->handler->destroy(array('send_expire_cookie' => false));
        echo '';
        $headers = xdebug_get_headers();
        // $headers = Handler\headers_list();
        $found  = false;
        $sName  = $this->handler->getName();
        foreach ($headers as $header) {
            if (stristr($header, 'Set-Cookie:') && stristr($header, $sName)) {
                $found  = true;
            }
        }
        if ($found) {
            $this->assertNotContains('expires=', $header);
        } else {
            $this->assertFalse($found, 'Unexpected session cookie found: ' . var_export($headers, true));
        }
    }

    public function testDestroyDoesNotClearSessionStorageByDefault()
    {
        $this->handler->start();
        $storage = $this->handler->getStorage();
        $storage['foo'] = 'bar';
        $this->handler->destroy();
        $this->handler->start();
        $this->assertEquals('bar', $storage['foo']);
    }

    public function testPassingClearStorageOptionWhenCallingDestroyClearsStorage()
    {
        $this->handler->start();
        $storage = $this->handler->getStorage();
        $storage['foo'] = 'bar';
        $this->handler->destroy(array('clear_storage' => true));
        $this->assertSame(array(), (array) $storage);
    }

    public function testCallingWriteCloseMarksStorageAsImmutable()
    {
        $this->handler->start();
        $storage = $this->handler->getStorage();
        $storage['foo'] = 'bar';
        $this->handler->writeClose();
        $this->assertTrue($storage->isImmutable());
    }

    public function testCallingWriteCloseShouldNotAlterSessionExistsStatus()
    {
        $this->handler->start();
        $this->handler->writeClose();
        $this->assertTrue($this->handler->sessionExists());
    }

    /**
     * @runInSeparateProcess
     */
    public function testIdShouldBeEmptyPriorToCallingStart()
    {
        $this->assertSame('', $this->handler->getId());
    }

    /**
     * @runInSeparateProcess
     */
    public function testIdShouldBeMutablePriorToCallingStart()
    {
        $this->handler->setId(__CLASS__);
        $this->assertSame(__CLASS__, $this->handler->getId());
        $this->assertSame(__CLASS__, session_id());
    }

    /**
     * @runInSeparateProcess
     */
    public function testIdShouldBeMutablePriorAfterSessionStarted()
    {
        $this->handler->start();
        $origId = $this->handler->getId();
        $this->handler->setId(__METHOD__);
        $this->assertNotSame($origId, $this->handler->getId());
        $this->assertSame(__METHOD__, $this->handler->getId());
        $this->assertSame(__METHOD__, session_id());
    }

    /**
     * @runInSeparateProcess
     */
    public function testSettingIdAfterSessionStartedShouldSendExpireCookie()
    {
        $config = $this->handler->getConfiguration();
        $config->setUseCookies(true);
        $this->handler->start();
        $origId = $this->handler->getId();
        $this->handler->setId(__METHOD__);
        $headers = xdebug_get_headers();
        // $headers = Handler\headers_list();
        $found  = false;
        $sName  = $this->handler->getName();
        foreach ($headers as $header) {
            if (stristr($header, 'Set-Cookie:') && stristr($header, $sName)) {
                $found  = true;
            }
        }
        $this->assertTrue($found, 'No session cookie found: ' . var_export($headers, true));
    }

    /**
     * @runInSeparateProcess
     */
    public function testRegenerateIdShouldWorkAfterSessionStarted()
    {
        $this->handler->start();
        $origId = $this->handler->getId();
        $this->handler->regenerateId();
        $this->assertNotSame($origId, $this->handler->getId());
    }

    /**
     * @runInSeparateProcess
     */
    public function testRegeneratingIdAfterSessionStartedShouldSendExpireCookie()
    {
        $config = $this->handler->getConfiguration();
        $config->setUseCookies(true);
        $this->handler->start();
        $origId = $this->handler->getId();
        $this->handler->regenerateId();
        $headers = xdebug_get_headers();
        // $headers = Handler\headers_list();
        $found  = false;
        $sName  = $this->handler->getName();
        foreach ($headers as $header) {
            if (stristr($header, 'Set-Cookie:') && stristr($header, $sName)) {
                $found  = true;
            }
        }
        $this->assertTrue($found, 'No session cookie found: ' . var_export($headers, true));
    }

    /**
     * @runInSeparateProcess
     */
    public function testRememberMeShouldSendNewSessionCookieWithUpdatedTimestamp()
    {
        $config = $this->handler->getConfiguration();
        $config->setUseCookies(true);
        $this->handler->start();
        $this->handler->rememberMe(18600);
        $headers = xdebug_get_headers();
        $found   = false;
        $sName   = $this->handler->getName();
        $cookie  = false;
        foreach ($headers as $header) {
            if (stristr($header, 'Set-Cookie:') && stristr($header, $sName) && !stristr($header, '=deleted')) {
                $found  = true;
                $cookie = $header;
            }
        }
        $this->assertTrue($found, 'No session cookie found: ' . var_export($headers, true));
        $ts = $this->getTimestampFromCookie($cookie);
        if (!$ts) {
            $this->fail('Cookie did not contain expiry? ' . var_export($headers, true));
        }
        $this->assertGreaterThan($_SERVER['REQUEST_TIME'], $ts->getTimestamp(), 'Session cookie: ' . var_export($headers, 1));
    }

    /**
     * @runInSeparateProcess
     */
    public function testRememberMeShouldSetTimestampTwoWeeksInFutureByDefault()
    {
        $config = $this->handler->getConfiguration();
        $config->setUseCookies(true);
        $this->handler->start();
        $this->handler->rememberMe();
        $headers = xdebug_get_headers();
        $found  = false;
        $sName  = $this->handler->getName();
        $cookie = false;
        foreach ($headers as $header) {
            if (stristr($header, 'Set-Cookie:') && stristr($header, $sName) && !stristr($header, '=deleted')) {
                $found  = true;
                $cookie = $header;
            }
        }
        $this->assertTrue($found, 'No session cookie found: ' . var_export($headers, true));
        $ts = $this->getTimestampFromCookie($cookie);
        if (!$ts) {
            $this->fail('Cookie did not contain expiry? ' . var_export($headers, true));
        }
        $compare = $_SERVER['REQUEST_TIME'] + 1208600;
        $this->assertGreaterThanOrEqual($compare, $ts->getTimestamp(), 'Session cookie: ' . var_export($headers, 1));
    }

    /**
     * @runInSeparateProcess
     */
    public function testForgetMeShouldSendCookieWithZeroTimestamp()
    {
        $config = $this->handler->getConfiguration();
        $config->setUseCookies(true);
        $this->handler->start();
        $this->handler->forgetMe();
        $headers = xdebug_get_headers();
        $found  = false;
        $sName  = $this->handler->getName();
        foreach ($headers as $header) {
            if (stristr($header, 'Set-Cookie:') && stristr($header, $sName) && !stristr($header, '=deleted')) {
                $found  = true;
            }
        }
        $this->assertTrue($found, 'No session cookie found: ' . var_export($headers, true));
        $this->assertNotContains('expires=', $header);
    }

    /**
     * @runInSeparateProcess
     */
    public function testStartingSessionThatFailsAValidatorShouldRaiseException()
    {
        $this->setExpectedException('Zend\\Session\\Exception', 'failed');
        $chain = $this->handler->getValidatorChain();
        $chain->attach('session.validate', function() {
             return false;
        });
        $this->handler->start();
    }
}
