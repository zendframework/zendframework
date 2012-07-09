<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Session
 */

namespace Zend\Session;

use Zend\EventManager\EventManagerInterface;
use Zend\Session\Configuration\ConfigurationInterface as Configuration;
use Zend\Session\SaveHandler\SaveHandlerInterface as SaveHandler;
use Zend\Session\Storage\StorageInterface as Storage;

/**
 * Session manager interface
 *
 * @category   Zend
 * @package    Zend_Session
 */
interface ManagerInterface
{
    public function __construct(Configuration $config = null, Storage $storage = null, SaveHandler $saveHandler = null);

    public function getConfig();
    public function getStorage();
    public function getSaveHandler();
    
    public function sessionExists();
    public function start();
    public function destroy();
    public function writeClose();

    public function getName();
    public function setName($name);
    public function getId();
    public function setId($id);
    public function regenerateId();

    public function rememberMe($ttl = null);
    public function forgetMe();
    public function expireSessionCookie();

    public function setValidatorChain(EventManagerInterface $chain);
    public function getValidatorChain();
    public function isValid();
}
