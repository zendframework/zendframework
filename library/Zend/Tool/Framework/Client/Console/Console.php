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
namespace Zend\Tool\Framework\Client\Console;

/**
 * Zend_Tool_Framework_Client_Console - the CLI Client implementation for Zend_Tool_Framework
 *
 * @uses       \Zend\Filter\FilterChain
 * @uses       \Zend\Filter\StringToLower
 * @uses       \Zend\Filter\Word\CamelCaseToDash
 * @uses       \Zend\Filter\Word\DashToCamelCase
 * @uses       \Zend\Tool\Framework\Client\AbstractClient
 * @uses       \Zend\Tool\Framework\Client\Console\ArgumentParser
 * @uses       \Zend\Tool\Framework\Client\Console\HelpSystem
 * @uses       \Zend\Tool\Framework\Client\Console\ResponseDecorator\AlignCenter
 * @uses       \Zend\Tool\Framework\Client\Console\ResponseDecorator\Blockize
 * @uses       \Zend\Tool\Framework\Client\Console\ResponseDecorator\Colorizer
 * @uses       \Zend\Tool\Framework\Client\Console\ResponseDecorator\Indention
 * @uses       \Zend\Tool\Framework\Client\Interactive\InteractiveInput
 * @uses       \Zend\Tool\Framework\Client\Interactive\OutputInterface
 * @uses       \Zend\Tool\Framework\Client\Response\ContentDecorator\Separator
 * @uses       \Zend\Tool\Framework\Client\Storage\Directory
 * @uses       \Zend\Tool\Framework\Loader\BasicLoader
 * @category   Zend
 * @package    Zend_Tool
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Console
    extends \Zend\Tool\Framework\Client\AbstractClient
    implements \Zend\Tool\Framework\Client\Interactive\InteractiveInput,
               \Zend\Tool\Framework\Client\Interactive\InteractiveOutput
{

    /**
     * @var array
     */
    protected $_configOptions = null;

    /**
     * @var array
     */
    protected $_storageOptions = null;

    /**
     * @var \Zend\Filter\Word\CamelCaseToDash
     */
    protected $_filterToClientNaming = null;

    /**
     * @var \Zend\Filter\Word\DashToCamelCase
     */
    protected $_filterFromClientNaming = null;

    /**
     * @var array
     */
    protected $_classesToLoad = array();
    
    /**
     * main() - This is typically called from zf.php. This method is a
     * self contained main() function.
     *
     */
    public static function main($options = array())
    {
        $cliClient = new self($options);
        $cliClient->dispatch();
    }

    /**
     * getName() - return the name of the client, in this case 'console'
     *
     * @return string
     */
    public function getName()
    {
        return 'console';
    }
    
    /**
     * setConfigOptions()
     * 
     * @param $configOptions
     */
    public function setConfigOptions($configOptions)
    {
        $this->_configOptions = $configOptions;
        return $this;
    }

    /**
     * setStorageOptions()
     * 
     * @param $storageOptions
     */
    public function setStorageOptions($storageOptions)
    {
        $this->_storageOptions = $storageOptions;
        return $this;
    }
    
    public function setClassesToLoad($classesToLoad)
    {
    	$this->_classesToLoad = $classesToLoad;
    	return $this;
    }

    /**
     * _init() - Tasks processed before the constructor, generally setting up objects to use
     *
     */
    protected function _preInit()
    {
        $config = $this->_registry->getConfig();

        if ($this->_configOptions != null) {
            $config->setOptions($this->_configOptions);
        }

        $storage = $this->_registry->getStorage();

        if ($this->_storageOptions != null && isset($this->_storageOptions['directory'])) {
            $storage->setAdapter(
                new \Zend\Tool\Framework\Client\Storage\Directory($this->_storageOptions['directory'])
                );
        }

        // which classes are essential to initializing Zend\Tool\Framework\Client\Console
        $classesToLoad = array(
            'Zend\Tool\Framework\Client\Console\Manifest',    
            'Zend\Tool\Framework\System\Manifest'
            );
            
        if ($this->_classesToLoad) {
        	if (is_string($this->_classesToLoad)) {
        		$classesToLoad[] = $this->_classesToLoad;
        	} elseif (is_array($this->_classesToLoad)) {
        		$classesToLoad = array_merge($classesToLoad, $this->_classesToLoad);
        	}
        }
        
        // add classes to the basic loader from the config file basicloader.classes.1 ..
        if (isset($config->basicloader) && isset($config->basicloader->classes)) {
            foreach ($config->basicloader->classes as $classKey => $className) {
                array_push($classesToLoad, $className);
            }
        }

        $this->_registry->setLoader(
            new \Zend\Tool\Framework\Loader\BasicLoader(array('classesToLoad' => $classesToLoad))
            );

        return;
    }

    /**
     * _preDispatch() - Tasks handed after initialization but before dispatching
     *
     */
    protected function _preDispatch()
    {
        $response = $this->_registry->getResponse();

        $response->addContentDecorator(new ResponseDecorator\AlignCenter());
        $response->addContentDecorator(new ResponseDecorator\Indention());
        $response->addContentDecorator(new ResponseDecorator\Blockize());

        if (function_exists('posix_isatty')) {
            $response->addContentDecorator(new ResponseDecorator\Colorizer());
        }
        
        $response->addContentDecorator(new \Zend\Tool\Framework\Client\Response\ContentDecorator\Separator())
            ->setDefaultDecoratorOptions(array('separator' => true));

        $optParser = new ArgumentParser();
        $optParser->setArguments($_SERVER['argv'])
            ->setRegistry($this->_registry)
            ->parse();

        return;
    }

    /**
     * _postDispatch() - Tasks handled after dispatching
     *
     */
    protected function _postDispatch()
    {
        $request = $this->_registry->getRequest();
        $response = $this->_registry->getResponse();

        if ($response->isException()) {
            $helpSystem = new HelpSystem();
            $helpSystem->setRegistry($this->_registry)
                ->respondWithErrorMessage($response->getException()->getMessage(), $response->getException())
                ->respondWithSpecialtyAndParamHelp(
                    $request->getProviderName(),
                    $request->getActionName()
                    );
        }

        echo PHP_EOL;
        return;
    }

    /**
     * handleInteractiveInputRequest() is required by the InteractiveInput interface
     *
     *
     * @param \Zend\Tool\Framework\Client\Interactive\InputRequest $inputRequest
     * @return string
     */
    public function handleInteractiveInputRequest(\Zend\Tool\Framework\Client\Interactive\InputRequest $inputRequest)
    {
        fwrite(STDOUT, $inputRequest->getContent() . PHP_EOL . 'zf> ');
        $inputContent = fgets(STDIN);
        return rtrim($inputContent); // remove the return from the end of the string
    }

    /**
     * handleInteractiveOutput() is required by the InteractiveOutput interface
     *
     * This allows us to display output immediately from providers, rather
     * than displaying it after the provider is done.
     *
     * @param string $output
     */
    public function handleInteractiveOutput($output)
    {
        echo $output;
    }

    /**
     * getMissingParameterPromptString()
     *
     * @param \Zend\Tool\Framework\Provider $provider
     * @param \Zend\Tool\Framework\Action $actionInterface
     * @param string $missingParameterName
     * @return string
     */
    public function getMissingParameterPromptString(\Zend\Tool\Framework\Provider $provider, \Zend\Tool\Framework\Action $actionInterface, $missingParameterName)
    {
        return 'Please provide a value for $' . $missingParameterName;
    }


    /**
     * convertToClientNaming()
     *
     * Convert words to client specific naming, in this case is lower, dash separated
     *
     * Filters are lazy-loaded.
     *
     * @param string $string
     * @return string
     */
    public function convertToClientNaming($string)
    {
        if (!$this->_filterToClientNaming) {
            $filter = new \Zend\Filter\FilterChain();
            $filter->attach(new \Zend\Filter\Word\CamelCaseToDash());
            $filter->attach(new \Zend\Filter\StringToLower());

            $this->_filterToClientNaming = $filter;
        }

        return $this->_filterToClientNaming->filter($string);
    }

    /**
     * convertFromClientNaming()
     *
     * Convert words from client specific naming to code naming - camelcased
     *
     * Filters are lazy-loaded.
     *
     * @param string $string
     * @return string
     */
    public function convertFromClientNaming($string)
    {
        if (!$this->_filterFromClientNaming) {
            $this->_filterFromClientNaming = new \Zend\Filter\Word\DashToCamelCase();
        }

        return $this->_filterFromClientNaming->filter($string);
    }

}
