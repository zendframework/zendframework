<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Authentication\Adapter;

use Zend\Authentication\Exception\InvalidArgumentException;
use Zend\Authentication\Exception\RuntimeException;
use Zend\Authentication\Result;

/**
 * Authentication Adapter authenticates using callback function.
 * The Callback function must return the identity on authentication success
 * and false on authentication failure.
 */
class Callback extends AbstractAdapter
{
    /**
     * @var callable
     */
    protected $callback;

    /**
     * Class constructor
     *
     * @param callable $callback the autentication callback
     */
    public function __construct($callback = null)
    {
        if (!is_null($callback)) {
            $this->setCallback($callback);
        }
    }

    /**
     * Authenticate
     * @return \Zend\Authentication\Result the authentication result
     * @throws RuntimeException
     */
    public function authenticate()
    {
        $callback = $this->getCallback();
        if (is_null($callback)) {
            throw new RuntimeException('No callback provided');
        }
        try {
            $identity = call_user_func($callback, $this->getIdentity(), $this->getCredential());
            if (!$identity) {
                return new Result(Result::FAILURE, null, array('Authentication failure'));
            }
        } catch (\Exception $e) {
            return new Result(Result::FAILURE_UNCATEGORIZED, null, array($e->getMessage()));
        }

        return new Result(Result::SUCCESS, $identity, array('Authentication success'));
    }

    /**
     * Gets the value of callback.
     *
     * @return mixed
     */
    public function getCallback()
    {
        return $this->callback;
    }

    /**
     * Sets the value of callback.
     *
     * @param  mixed $callback the callback
     * @throws InvalidArgumentException
     *
     * @return self
     */
    public function setCallback($callback)
    {
        if (!is_callable($callback)) {
            throw new InvalidArgumentException('Invalid callback provided');
        }

        $this->callback = $callback;

        return $this;
    }
}
