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
 * @package    Zend_Validator
 * @subpackage UnitTest
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Validator;

use Traversable;
use Zend\Stdlib\ArrayUtils;
use Zend\Session\Container as SessionContainer;

class Csrf extends AbstractValidator
{
    /**
     * Error codes
     * @const string
     */
    const NOT_SAME = 'notSame';

    /**
     * Error messages
     * @var array
     */
    protected $messageTemplates = array(
        self::NOT_SAME => "The form submitted did not originate from the expected site",
    );

    /**
     * Actual hash used.
     *
     * @var mixed
     */
    protected $hash;

    /**
     * Static cache of the session names to generated hashes
     *
     * @var array
     */
    protected static $hashCache;

    /**
     * Name of CSRF element (used to create non-colliding hashes)
     *
     * @var string
     */
    protected $name = 'csrf';

    /**
     * Salt for CSRF token
     * @var string
     */
    protected $salt = 'salt';

    /**
     * @var SessionContainer
     */
    protected $session;

    /**
     * TTL for CSRF token
     * @var int
     */
    protected $timeout = 300;

    /**
     * Constructor
     *
     * @param  array|Traversable $options
     */
    public function __construct($options = array())
    {
        parent::__construct();

        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        }

        if (!is_array($options)) {
            $options = (array) $options;
        }

        foreach ($options as $key => $value) {
            switch (strtolower($key)) {
                case 'name':
                    $this->setName($value);
                    break;
                case 'salt':
                    $this->setSalt($value);
                    break;
                case 'session':
                    $this->setSession($value);
                    break;
                case 'timeout':
                    $this->setTimeout($value);
                    break;
                default:
                    // ignore unknown options
                    break;
            }
        }
    }

    /**
     * Does the provided token match the one generated?
     *
     * @param  string $value
     * @param  mixed $context
     * @return bool
     */
    public function isValid($value, $context = null)
    {
        $this->setValue((string) $value);

        $hash = $this->getValidationToken();

        if ($value !== $hash) {
            $this->error(self::NOT_SAME);
            return false;
        }

        return true;
    }

    /**
     * Set CSRF name
     *
     * @param  string $name
     * @return Csrf
     */
    public function setName($name)
    {
        $this->name = (string) $name;
        return $this;
    }

    /**
     * Get CSRF name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set session container
     *
     * @param  SessionContainer $session
     * @return Csrf
     */
    public function setSession(SessionContainer $session)
    {
        $this->session = $session;
        if ($this->hash) {
            $this->initCsrfToken();
        }
        return $this;
    }

    /**
     * Get session container
     *
     * Instantiate session container if none currently exists
     *
     * @return SessionContainer
     */
    public function getSession()
    {
        if (null === $this->session) {
            $this->session = new SessionContainer($this->getSessionName());
        }
        return $this->session;
    }

    /**
     * Salt for CSRF token
     *
     * @param  string $salt
     * @return Csrf
     */
    public function setSalt($salt)
    {
        $this->salt = (string) $salt;
        return $this;
    }

    /**
     * Retrieve salt for CSRF token
     *
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Retrieve CSRF token
     *
     * If no CSRF token currently exists, or should be regenrated, 
     * generates one.
     *
     * @param  bool $regenerate    default false
     * @return string
     */
    public function getHash($regenerate = false)
    {
        if ((null === $this->hash) || $regenerate) {
            if ($regenerate) {
                $this->hash = null;
            } else {
                $this->hash = $this->getValidationToken();
            }
            if (null === $this->hash) {
                $this->generateHash();
            }
        }
        return $this->hash;
    }

    /**
     * Get session namespace for CSRF token
     *
     * Generates a session namespace based on salt, element name, and class.
     *
     * @return string
     */
    public function getSessionName()
    {
        return str_replace('\\', '_', __CLASS__) . '_' . $this->getSalt() . '_' . $this->getName();
    }

    /**
     * Set timeout for CSRF session token
     *
     * @param  int $ttl
     * @return Csrf
     */
    public function setTimeout($ttl)
    {
        $this->timeout = (int) $ttl;
        return $this;
    }

    /**
     * Get CSRF session token timeout
     *
     * @return int
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * Initialize CSRF token in session
     *
     * @return void
     */
    protected function initCsrfToken()
    {
        $session = $this->getSession();
        //$session->setExpirationHops(1, null, true);
        $session->setExpirationSeconds($this->getTimeout());
        $session->hash = $this->getHash();
    }

    /**
     * Generate CSRF token
     *
     * Generates CSRF token and stores both in {@link $hash} and element
     * value.
     *
     * @return void
     */
    protected function generateHash()
    {
        if (isset(static::$hashCache[$this->getSessionName()])) {
            $this->hash = static::$hashCache[$this->getSessionName()];
        } else {
            $this->hash = md5(
                mt_rand(1,1000000)
                .  $this->getSalt()
                .  $this->getName()
                .  mt_rand(1,1000000)
            );
            static::$hashCache[$this->getSessionName()] = $this->hash;
        }
        $this->setValue($this->hash);
        $this->initCsrfToken();
    }

    /**
     * Get validation token
     *
     * Retrieve token from session, if it exists.
     *
     * @return null|string
     */
    protected function getValidationToken()
    {
        $session = $this->getSession();
        if (isset($session->hash)) {
            return $session->hash;
        }
        return null;
    }
}
