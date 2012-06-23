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
 * @package    Zend_Session
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Session;

use Zend\EventManager\EventManager,
    Zend\Session\Storage\StorageInterface as Storage,
    Zend\Session\Validator\ValidatorInterface as Validator;

/**
 * Validator chain for validating sessions
 *
 * @category   Zend
 * @package    Zend_Session
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ValidatorChain extends EventManager
{
    /**
     * @var Storage
     */
    protected $storage;

    /**
     * Construct the validation chain
     *
     * Retrieves validators from session storage and attaches them.
     * 
     * @param  Storage $storage 
     * @return void
     */
    public function __construct(Storage $storage)
    {
        $this->storage = $storage;

        $validators = $storage->getMetadata('_VALID');
        if ($validators) {
            foreach ($validators as $validator => $data) {
                $this->attach('session.validate', new $validator($data), 'isValid');
            }
        }
    }

    /**
     * Attach a listener to the session validator chain
     * 
     * @param  string $event
     * @param  callback $callback
     * @param  int $priority 
     * @return \Zend\Stdlib\CallbackHandler
     */
    public function attach($event, $callback = null, $priority = 1)
    {
        $context = null;
        if ($callback instanceof Validator) {
            $context = $callback;
        } elseif (is_array($callback)) {
            $test = array_shift($callback);
            if ($test instanceof Validator) {
                $context = $test;
            }
            array_unshift($callback, $test);
        }
        if ($context instanceof Validator) {
            $data = $context->getData();
            $name = $context->getName();
            $this->getStorage()->setMetadata('_VALID', array($name => $data));
        }

        $listener = parent::attach($event, $callback, $priority);
        return $listener;
    }

    /**
     * Retrieve session storage object
     * 
     * @return Storage
     */
    public function getStorage()
    {
        return $this->storage;
    }
}
