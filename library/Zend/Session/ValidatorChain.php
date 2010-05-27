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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Session;

use Zend\SignalSlot\Signals;

/**
 * Zend_Session_Validator_Interface
 *
 * @category   Zend
 * @package    Zend_Session
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ValidatorChain extends Signals
{
    /**
     * @var Storage
     */
    protected $_storage;

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
        $this->_storage = $storage;

        $validators = $storage->getMetadata('_VALID');
        if ($validators) {
            foreach ($validators as $validator => $data) {
                $this->connect('session.validate', new $validator($data), 'isValid');
            }
        }
    }

    /**
     * Attach a handler to the session validator chain
     * 
     * @param  string $topic 
     * @param  string|object|Closure $context 
     * @param  null|string $handler 
     * @return Zend\Stdlib\SignalHandler
     */
    public function connect($topic, $context, $handler = null)
    {
        if ($context instanceof Validator) {
            $data = $context->getData();
            $name = $context->getName();
            $this->getStorage()->setMetadata('_VALID', array($name => $data));
        }

        $handle = parent::connect($topic, $context, $handler);
        return $handle;
    }

    /**
     * Retrieve session storage object
     * 
     * @return Storage
     */
    public function getStorage()
    {
        return $this->_storage;
    }
}
