<?php

namespace Zend\Session\Handler;

use Zend\Session\Configuration,
    Zend\Session\Exception as SessionException,
    Zend\Session\Handler as HandlerDefinition,
    Zend\Session\Storage,
    Zend\Session\ValidatorChain,
    Zend\Validator\Alnum as AlnumValidator,
    Zend\Messenger;

class SessionHandler implements HandlerDefinition
{
    protected $_config;
    protected $_defaultDestroyOptions = array(
        'send_expire_cookie' => true,
        'clear_storage'      => false,
    );
    protected $_destroyed;
    protected $_name;
    protected $_rememberMe = 1209600; // 2 weeks
    protected $_sessionStarted;
    protected $_storage;
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
            throw new SessionException('Session failed validation');
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

    public function stop()
    {
    }

    public function writeClose()
    {
        $storage  = $this->getStorage();
        $_SESSION = (array) $storage;
        session_write_close();
        $storage->fromArray($_SESSION)
                ->markImmutable();
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
            throw new SessionException('Cannot set session name after a session has already started');
        }

        $validator = new AlnumValidator();
        if (!$validator->isValid($name)) {
            throw new SessionException('Name provided contains invalid characters; must be alphanumeric only');
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
            $ttl = $this->_rememberMe;
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
        $config = $this->getConfiguration();
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

    public function setConfiguration(Configuration $config)
    {
        $this->_config = $config;
        return $this;
    }

    public function getConfiguration()
    {
        if (null === $this->_config) {
            $this->setConfiguration(new Configuration\SessionConfiguration());
        }
        return $this->_config;
    }

    public function setStorage(Storage $storage)
    {
        $this->_storage = $storage;
        return $this;
    }

    public function getStorage()
    {
        if (null === $this->_storage) {
            $this->setStorage(new Storage\SessionStorage());
        }
        return $this->_storage;
    }

    /**
     * @param  int $ttl 
     * @return void
     */
    protected function _setSessionCookieLifetime($ttl)
    {
        $config = $this->getConfiguration();
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
