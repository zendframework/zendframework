<?php

namespace Zend\Session;

use Zend\Validator\Alnum as AlnumValidator,
    Zend\Messenger;

class SessionManager extends AbstractManager
{
    /**
     * @var Configuration
     */
    protected $_config;

    /**
     * @var Storage
     */
    protected $_storage;

    protected $_defaultDestroyOptions = array(
        'send_expire_cookie' => true,
        'clear_storage'      => false,
    );
    protected $_destroyed;
    protected $_name;
    protected $_sessionStarted;
    protected $_validatorChain;

    /**
     * Does a session exist and is it currently active?
     * 
     * @return bool
     */
    public function sessionExists()
    {
        $sid = defined('SID') ? constant('SID') : false;
        if ($sid && $this->getId()) {
            return true;
        }
        return false;
    }

    public function start()
    {
        if ($this->sessionExists()) {
            return;
        }
        session_start();
        if (!$this->isValid()) {
            throw new Exception('Session failed validation');
        }
    }

    public function destroy(array $options = null)
    {
        if (!$this->sessionExists()) {
            return;
        }

        if (null === $options) {
            $options = $this->_defaultDestroyOptions;
        } else {
            $options = array_merge($this->_defaultDestroyOptions, $options);
        }

        session_destroy();
        if ($options['send_expire_cookie']) {
            $this->expireSessionCookie();
        }

        if ($options['clear_storage']) {
            $this->getStorage()->clear();
        }
    }

    public function writeClose()
    {
        // The assumption is that we're using PHP's ext/session.
        // session_write_close() will actually overwrite $_SESSION with an 
        // empty array on completion -- which leads to a mismatch between what
        // is in the storage object and $_SESSION. To get around this, we
        // temporarily reset $_SESSION to an array, and then re-link it to 
        // the storage object.
        //
        // Additionally, while you _can_ write to $_SESSION following a 
        // session_write_close() operation, no changes made to it will be 
        // flushed to the session handler. As such, we now mark the storage 
        // object immutable.
        $storage  = $this->getStorage();
        $_SESSION = (array) $storage;
        session_write_close();
        $storage->fromArray($_SESSION);
        $storage->markImmutable();
    }

    public function getName()
    {
        if (null === $this->_name) {
            // If we're grabbing via session_name(), we don't need our 
            // validation routine; additionally, calling setName() after
            // session_start() can lead to issues, and often we just need the name
            // in order to do things such as setting cookies.
            $this->_name = session_name();
        }
        return $this->_name;
    }

    public function setName($name)
    {
        if ($this->sessionExists()) {
            throw new Exception('Cannot set session name after a session has already started');
        }

        $validator = new AlnumValidator();
        if (!$validator->isValid($name)) {
            throw new Exception('Name provided contains invalid characters; must be alphanumeric only');
        }

        $this->_name = $name;
        session_name($name);
        return $this;
    }

    public function getId()
    {
        return session_id();
    }

    public function setId($id)
    {
        if (!$this->sessionExists()) {
            session_id($id);
            return $this;
        }
        $this->destroy();
        session_id($id);
        $this->start();
        return $this;
    }

    public function regenerateId()
    {
        if (!$this->sessionExists()) {
            session_regenerate_id();
            return $this;
        }
        $this->destroy();
        session_regenerate_id();
        $this->start();
        return $this;
    }

    /**
     * @param  null|int $ttl 
     * @return SessionHandler
     */
    public function rememberMe($ttl = null)
    {
        if (null === $ttl) {
            $ttl = $this->getConfig()->getRememberMeSeconds();
        }
        $this->_setSessionCookieLifetime($ttl);
        return $this;
    }

    public function forgetMe()
    {
        $this->_setSessionCookieLifetime(0);
    }

    public function setValidatorChain(Messenger\Delivery $chain)
    {
        $this->_validatorChain = $chain;
        return $this;
    }

    public function getValidatorChain()
    {
        if (null === $this->_validatorChain) {
            $this->setValidatorChain(new ValidatorChain($this->getStorage()));
        }
        return $this->_validatorChain;
    }

    /**
     * Is this session valid?
     * 
     * @return bool
     */
    public function isValid()
    {
        $validator = $this->getValidatorChain();
        $return    = $validator->notifyUntil(function($test) {
            return !$test;
        }, 'session.validate');
        if (null === $return) {
            return true;
        }
        return (bool) $return;
    }

    public function expireSessionCookie()
    {
        $config = $this->getConfig();
        if (!$config->getUseCookies()) {
            return;
        }
        setcookie(
            $this->getName(),                 // session name
            '',                               // value
            $_SERVER['REQUEST_TIME'] - 42000, // TTL for cookie
            $config->getCookiePath(),
            $config->getCookieDomain(),
            $config->getCookieSecure(), 
            $config->getCookieHTTPOnly()
        );
    }

    /**
     * @param  int $ttl 
     * @return void
     */
    protected function _setSessionCookieLifetime($ttl)
    {
        $config = $this->getConfig();
        if (!$config->getUseCookies()) {
            return;
        }

        if ($this->sessionExists()) {
            $this->destroy(array('send_expire_cookie' => false));

            // Since a cookie was destroyed, we should regenerate the ID
            $this->regenerateId();
        }

        // Now simply set the cookie TTL
        $config->setCookieLifetime($ttl);

        if (!$this->sessionExists()) {
            // Restart session if necessary
            $this->start();
        }
    }
}
