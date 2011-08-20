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
 * @package    Zend_Tool
 * @subpackage Framework
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Tool\Framework\Client;
use Zend\Log,
    Zend\Tool\Framework\Registry,
    Zend\Tool\Framework\RegistryEnabled;

/**
 * @category   Zend
 * @package    Zend_Tool
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractClient implements RegistryEnabled
{

    /**
     * @var \Zend\Tool\Framework\Registry
     */
    protected $_registry = null;

    /**
     * @var callback|null
     */
    protected $_interactiveCallback = null;

    /**
     * @var bool
     */
    protected $_isInitialized = false;

    /**
     * @var Log\Logger
     */
    protected $_debugLogger = null;

    public function __construct($options = array())
    {
        // require autoloader 
        $loader = new \Zend\Loader\StandardAutoloader();
        $loader->register();

        // this might look goofy, but this is setting up the
        // registry for dependency injection into the client
        $registry = new \Zend\Tool\Framework\Registry\FrameworkRegistry();
        $registry->setClient($this);

        // NOTE: at this moment, $this->_registry should contain the registry object
        
        if ($options) {
            $this->setOptions($options);
        }
    }

    public function setOptions(Array $options)
    {
        foreach ($options as $optionName => $optionValue) {
            $setMethodName = 'set' . $optionName;
            if (method_exists($this, $setMethodName)) {
                $this->{$setMethodName}($optionValue);
            }
        }
    }

    /**
     * getName() - Return the client name which can be used to
     * query the manifest if need be.
     *
     * @return string The client name
     */
    abstract public function getName();

    /**
     * initialized() - This will initialize the client for use
     *
     */
    public function initialize()
    {
        // if its already initialized, no need to initialize again
        if ($this->_isInitialized) {
            return;
        }

        // run any preInit
        $this->_preInit();

        $manifest = $this->_registry->getManifestRepository();
        $manifest->addManifest(new Manifest());
        
        // setup the debug log
        if (!$this->_debugLogger instanceof Log\Logger) {
            $this->_debugLogger = new Log\Logger(new Log\Writer\Null());
        }

        // let the loader load, then the repositories process whats been loaded
        $this->_registry->getLoader()->load();

        // process the action repository
        $this->_registry->getActionRepository()->process();

        // process the provider repository
        $this->_registry->getProviderRepository()->process();

        // process the manifest repository
        $this->_registry->getManifestRepository()->process();

        if ($this instanceof Interactive\InteractiveOutput) {
            $this->_registry->getResponse()->setContentCallback(array($this, 'handleInteractiveOutput'));
        }

    }


    /**
     * This method should be implemented by the client implementation to
     * construct and set custom inflectors, request and response objects.
     */
    protected function _preInit()
    {
    }

    /**
     * This method *must* be implemented by the client implementation to
     * parse out and setup the request objects action, provider and parameter
     * information.
     */
    abstract protected function _preDispatch();

    /**
     * This method should be implemented by the client implementation to
     * take the output of the response object and return it (in an client
     * specific way) back to the Tooling Client.
     */
    protected function _postDispatch()
    {
    }

    /**
     * setRegistry() - Required by the Zend\Tool\Framework\RegistryEnabled
     * interface which ensures proper registry dependency resolution
     *
     * @param \Zend\Tool\Framework\Registry $registry
     * @return \Zend\Tool\Framework\Client\AbstractClient
     */
    public function setRegistry(Registry $registry)
    {
        $this->_registry = $registry;
        return $this;
    }
    
    /**
     * getRegistry();
     * 
     * @return \Zend\Tool\Framework\Registry
     */
    public function getRegistry()
    {
    	return $this->_registry;
    }

    /**
     * hasInteractiveInput() - Convienence method for determining if this
     * client can handle interactive input, and thus be able to run the
     * promptInteractiveInput
     *
     * @return bool
     */
    public function hasInteractiveInput()
    {
        return ($this instanceof Interactive\InteractiveInput);
    }

    public function promptInteractiveInput($inputRequest)
    {
        if (!$this->hasInteractiveInput()) {
            throw new Exception\RuntimeException('promptInteractive() cannot be called on a non-interactive client.');
        }

        $inputHandler = new Interactive\InputHandler();
        $inputHandler->setClient($this);
        $inputHandler->setInputRequest($inputRequest);
        return $inputHandler->handle();

    }

    /**
     * This method should be called in order to "handle" a Tooling Client
     * request that has come to the client that has been implemented.
     */
    public function dispatch()
    {
        $this->initialize();

        try {

            $this->_preDispatch();

            if ($this->_registry->getRequest()->isDispatchable()) {

                if ($this->_registry->getRequest()->getActionName() == null) {
                    throw new Exception\RuntimeException('Client failed to setup the action name.');
                }

                if ($this->_registry->getRequest()->getProviderName() == null) {
                    throw new Exception\RuntimeException('Client failed to setup the provider name.');
                }

                $this->_handleDispatch();

            }

        } catch (\Exception $exception) {
            $this->_registry->getResponse()->setException($exception);
        }

        $this->_postDispatch();
    }

    public function convertToClientNaming($string)
    {
        return $string;
    }

    public function convertFromClientNaming($string)
    {
        return $string;
    }

    protected function _handleDispatch()
    {
        // get the provider repository
        $providerRepository = $this->_registry->getProviderRepository();

        $request = $this->_registry->getRequest();

        // get the dispatchable provider signature
        $providerSignature = $providerRepository->getProviderSignature($request->getProviderName());

        // get the actual provider
        $provider = $providerSignature->getProvider();

        // ensure that we can pretend if this is a pretend request
        if ($request->isPretend() && (!$provider instanceof \Zend\Tool\Framework\Provider\Pretendable)) {
            throw new Exception\RuntimeException('Dispatcher error - provider does not support pretend');
        }

        // get the action name
        $actionName = $this->_registry->getRequest()->getActionName();
        $specialtyName = $this->_registry->getRequest()->getSpecialtyName();

        if (!$actionableMethod = $providerSignature->getActionableMethodByActionName($actionName, $specialtyName)) {
            throw new Exception\RuntimeException('Dispatcher error - actionable method not found');
        }

        // get the actual method and param information
        $methodName       = $actionableMethod['methodName'];
        $methodParameters = $actionableMethod['parameterInfo'];

        // get the provider params
        $requestParameters = $this->_registry->getRequest()->getProviderParameters();

        // @todo This seems hackish, determine if there is a better way
        $callParameters = array();
        foreach ($methodParameters as $methodParameterName => $methodParameterValue) {
            if (!array_key_exists($methodParameterName, $requestParameters) && $methodParameterValue['optional'] == false) {
                if ($this instanceof Interactive\InteractiveInput) {
                    $promptSting = $this->getMissingParameterPromptString($provider, $actionableMethod['action'], $methodParameterValue['name']);
                    $parameterPromptValue = $this->promptInteractiveInput($promptSting)->getContent();
                    if ($parameterPromptValue == null) {
                        throw new Exception\RuntimeException('Value supplied for required parameter "' . $methodParameterValue['name'] . '" is empty');
                    }
                    $callParameters[] = $parameterPromptValue;
                } else {
                    throw new Exception\RuntimeException('A required parameter "' . $methodParameterValue['name'] . '" was not supplied.');
                }
            } else {
                $callParameters[] = (array_key_exists($methodParameterName, $requestParameters)) ? $requestParameters[$methodParameterName] : $methodParameterValue['default'];
            }
        }

        $this->_handleDispatchExecution($provider, $methodName, $callParameters);
    }
    
    protected function _handleDispatchExecution($class, $methodName, $callParameters)
    {
        if (method_exists($class, $methodName)) {
            call_user_func_array(array($class, $methodName), $callParameters);
        } elseif (method_exists($class, $methodName . 'Action')) {
            call_user_func_array(array($class, $methodName . 'Action'), $callParameters);
        } else {
            throw new Exception\RuntimeException('Not a supported method.');
        }
    }

}
