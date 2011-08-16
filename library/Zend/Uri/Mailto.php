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
 * @category  Zend
 * @package   Zend_Uri
 * @copyright Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Uri;

use Zend\Validator\Validator,
    Zend\Validator\EmailAddress as EmailValidator;

/**
 * "Mailto" URI handler
 *
 * The 'mailto:...' scheme is loosly defined in RFC-1738
 *
 * @category  Zend
 * @package   Zend_Uri
 * @copyright Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class Mailto extends Uri
{
    protected static $validSchemes = array('mailto');

    /**
     * Validator for use when validating email address
     * @var Validator
     */
    protected $emailValidator;

    /**
     * Check if the URI is a valid Mailto URI
     *
     * This applys additional specific validation rules beyond the ones
     * required by the generic URI syntax
     *
     * @return boolean
     * @see    Uri::isValid()
     */
    public function isValid()
    {
        if ($this->host || $this->userInfo || $this->port) {
            return false;
        }

        if (empty($this->path)) {
            return false;
        }

        if (0 === strpos($this->path, '/')) {
            return false;
        }

        $validator = $this->getValidator();
        return $validator->isValid($this->path);
    }

    /**
     * Set the email address
     *
     * This is infact equivalent to setPath() - but provides a more clear interface
     *
     * @param  string $email
     * @return Mailto
     */
    public function setEmail($email)
    {
        return $this->setPath($email);
    }

    /**
     * Get the email address
     *
     * This is infact equivalent to getPath() - but provides a more clear interface
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->getPath();
    }

    /**
     * Set validator to use when validating email address
     *
     * @param  Validator $validator
     * @return Mailto
     */
    public function setValidator(Validator $validator)
    {
        $this->emailValidator = $validator;
        return $this;
    }

    /**
     * Retrieve validator for use with validating email address
     *
     * If none is currently set, an EmailValidator instance with default options
     * will be used.
     *
     * @return Validator
     */
    public function getValidator()
    {
        if (null === $this->emailValidator) {
            $this->setValidator(new EmailValidator());
        }
        return $this->emailValidator;
    }
}
