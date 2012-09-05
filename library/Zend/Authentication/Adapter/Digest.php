<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Authentication
 */

namespace Zend\Authentication\Adapter;

use Zend\Authentication\Result as AuthenticationResult;
use Zend\Stdlib\ErrorHandler;

/**
 * @category   Zend
 * @package    Zend_Authentication
 * @subpackage Adapter
 */
class Digest implements AdapterInterface
{
    /**
     * Filename against which authentication queries are performed
     *
     * @var string
     */
    protected $filename;

    /**
     * Digest authentication realm
     *
     * @var string
     */
    protected $realm;

    /**
     * Digest authentication user
     *
     * @var string
     */
    protected $username;

    /**
     * Password for the user of the realm
     *
     * @var string
     */
    protected $password;

    /**
     * Sets adapter options
     *
     * @param  mixed $filename
     * @param  mixed $realm
     * @param  mixed $username
     * @param  mixed $password
     */
    public function __construct($filename = null, $realm = null, $username = null, $password = null)
    {
        $options = array('filename', 'realm', 'username', 'password');
        foreach ($options as $option) {
            if (null !== $$option) {
                $methodName = 'set' . ucfirst($option);
                $this->$methodName($$option);
            }
        }
    }

    /**
     * Returns the filename option value or null if it has not yet been set
     *
     * @return string|null
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Sets the filename option value
     *
     * @param  mixed $filename
     * @return Digest Provides a fluent interface
     */
    public function setFilename($filename)
    {
        $this->filename = (string) $filename;
        return $this;
    }

    /**
     * Returns the realm option value or null if it has not yet been set
     *
     * @return string|null
     */
    public function getRealm()
    {
        return $this->realm;
    }

    /**
     * Sets the realm option value
     *
     * @param  mixed $realm
     * @return Digest Provides a fluent interface
     */
    public function setRealm($realm)
    {
        $this->realm = (string) $realm;
        return $this;
    }

    /**
     * Returns the username option value or null if it has not yet been set
     *
     * @return string|null
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Sets the username option value
     *
     * @param  mixed $username
     * @return Digest Provides a fluent interface
     */
    public function setUsername($username)
    {
        $this->username = (string) $username;
        return $this;
    }

    /**
     * Returns the password option value or null if it has not yet been set
     *
     * @return string|null
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Sets the password option value
     *
     * @param  mixed $password
     * @return Digest Provides a fluent interface
     */
    public function setPassword($password)
    {
        $this->password = (string) $password;
        return $this;
    }

    /**
     * Defined by Zend\Authentication\Adapter\AdapterInterface
     *
     * @throws Exception\ExceptionInterface
     * @return AuthenticationResult
     */
    public function authenticate()
    {
        $optionsRequired = array('filename', 'realm', 'username', 'password');
        foreach ($optionsRequired as $optionRequired) {
            if (null === $this->$optionRequired) {
                throw new Exception\RuntimeException("Option '$optionRequired' must be set before authentication");
            }
        }

        ErrorHandler::start(E_WARNING);
        $fileHandle = fopen($this->filename, 'r');
        $error      = ErrorHandler::stop();
        if (false === $fileHandle) {
            throw new Exception\UnexpectedValueException("Cannot open '$this->filename' for reading", 0, $error);
        }

        $id       = "$this->username:$this->realm";
        $idLength = strlen($id);

        $result = array(
            'code'  => AuthenticationResult::FAILURE,
            'identity' => array(
                'realm'    => $this->realm,
                'username' => $this->username,
                ),
            'messages' => array()
            );

        while ($line = trim(fgets($fileHandle))) {
            if (substr($line, 0, $idLength) === $id) {
                if ($this->_secureStringCompare(substr($line, -32), md5("$this->username:$this->realm:$this->password"))) {
                    $result['code'] = AuthenticationResult::SUCCESS;
                } else {
                    $result['code'] = AuthenticationResult::FAILURE_CREDENTIAL_INVALID;
                    $result['messages'][] = 'Password incorrect';
                }
                return new AuthenticationResult($result['code'], $result['identity'], $result['messages']);
            }
        }

        $result['code'] = AuthenticationResult::FAILURE_IDENTITY_NOT_FOUND;
        $result['messages'][] = "Username '$this->username' and realm '$this->realm' combination not found";
        return new AuthenticationResult($result['code'], $result['identity'], $result['messages']);
    }

    /**
     * Securely compare two strings for equality while avoided C level memcmp()
     * optimisations capable of leaking timing information useful to an attacker
     * attempting to iteratively guess the unknown string (e.g. password) being
     * compared against.
     *
     * @param string $a
     * @param string $b
     * @return bool
     */
    protected function _secureStringCompare($a, $b)
    {
        if (strlen($a) !== strlen($b)) {
            return false;
        }
        $result = 0;
        for ($i = 0; $i < strlen($a); $i++) {
            $result |= ord($a[$i]) ^ ord($b[$i]);
        }
        return $result == 0;
    }
}
