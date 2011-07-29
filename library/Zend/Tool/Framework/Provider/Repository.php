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
namespace Zend\Tool\Framework\Provider;

use Countable,
    IteratorAggregate,
    Zend\Tool\Framework\Provider,
    Zend\Tool\Framework\Registry,
    Zend\Tool\Framework\RegistryEnabled;

/**
 * @category   Zend
 * @package    Zend_Tool
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Repository implements RegistryEnabled, IteratorAggregate, Countable
{

    /**
     * @var Registry
     */
    protected $_registry = null;

    /**
     * @var bool
     */
    protected $_processOnAdd = false;

    /**
     * @var Provider[]
     */
    protected $_unprocessedProviders = array();

    /**
     * @var Provider\Signature[]
     */
    protected $_providerSignatures = array();

    /**
     * @var array Array of Provider\Inteface
     */
    protected $_providers = array();

    /**
     * setRegistry()
     *
     * @param  Registry $registry
     * @return unknown
     */
    public function setRegistry(Registry $registry)
    {
        $this->_registry = $registry;
        return $this;
    }

    /**
     * Set the ProcessOnAdd flag
     *
     * @param unknown_type $processOnAdd
     * @return unknown
     */
    public function setProcessOnAdd($processOnAdd = true)
    {
        $this->_processOnAdd = (bool) $processOnAdd;
        return $this;
    }

    /**
     * Add a provider to the repository for processing
     *
     * @param  Provider $provider
     * @return Provider\Repository
     */
    public function addProvider(Provider $provider, $overwriteExistingProvider = false)
    {
        if ($provider instanceof RegistryEnabled) {
            $provider->setRegistry($this->_registry);
        }

        if (method_exists($provider, 'getName')) {
            $providerName = $provider->getName();
        } else {
            $providerName = $this->_parseName($provider);
        }

        // if a provider by the given name already exist, and its not set as overwritable, throw exception
        if (!$overwriteExistingProvider &&
            (array_key_exists($providerName, $this->_unprocessedProviders)
                || array_key_exists($providerName, $this->_providers)))
        {
            throw new Exception\InvalidArgumentException(sprintf(
                'A provider by the name "%s" is already registered and $overrideExistingProvider is set to false',
                $providerName
            ));
        }

        $this->_unprocessedProviders[$providerName] = $provider;

        // if process has already been called, process immediately.
        if ($this->_processOnAdd) {
            $this->process();
        }

        return $this;
    }

    public function hasProvider($providerOrClassName, $processedOnly = true)
    {
        if ($providerOrClassName instanceof Provider) {
            $targetProviderClassName = get_class($providerOrClassName);
        } else {
            $targetProviderClassName = (string) $providerOrClassName;
        }

        if (!$processedOnly) {
            foreach ($this->_unprocessedProviders as $unprocessedProvider) {
                if (get_class($unprocessedProvider) == $targetProviderClassName) {
                    return true;
                }
            }
        }

        foreach ($this->_providers as $processedProvider) {
            if (get_class($processedProvider) == $targetProviderClassName) {
                return true;
            }
        }

        return false;
    }

    /**
     * Process all of the unprocessed providers
     *
     */
    public function process()
    {
        // process all providers in the unprocessedProviders array
        //foreach ($this->_unprocessedProviders as $providerName => $provider) {
        reset($this->_unprocessedProviders);
        while ($this->_unprocessedProviders) {
            $providerName = key($this->_unprocessedProviders);
            $provider = array_shift($this->_unprocessedProviders);

            // create a signature for the provided provider
            $providerSignature = new Signature($provider);

            if ($providerSignature instanceof RegistryEnabled) {
                $providerSignature->setRegistry($this->_registry);
            }

            $providerSignature->process();

            // ensure the name is lowercased for easier searching
            $providerName = strtolower($providerName);

            // add to the appropraite place
            $this->_providerSignatures[$providerName] = $providerSignature;
            $this->_providers[$providerName]          = $providerSignature->getProvider();

            if ($provider instanceof Initializable) {
                $provider->initialize();
            }
        }

    }

    /**
     * getProviders() Get all the providers in the repository
     *
     * @return array
     */
    public function getProviders()
    {
        return $this->_providers;
    }

    /**
     * getProviderSignatures() Get all the provider signatures
     *
     * @return array
     */
    public function getProviderSignatures()
    {
        return $this->_providerSignatures;
    }

    /**
     * getProvider()
     *
     * @param  string $providerName
     * @return Provider
     */
    public function getProvider($providerName)
    {
        return $this->_providers[strtolower($providerName)];
    }

    /**
     * getProviderSignature()
     *
     * @param  string $providerName
     * @return Provider\Signature
     */
    public function getProviderSignature($providerName)
    {
        return $this->_providerSignatures[strtolower($providerName)];
    }

    /**
     * count() - return the number of providers
     *
     * @return int
     */
    public function count()
    {
        return count($this->_providers);
    }

    /**
     * getIterator() - Required by the IteratorAggregate Interface
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->getProviders());
    }

    /**
     * _parseName - internal method to determine the name of an action when one is not explicity provided.
     *
     * @param  Provider $action
     * @return string
     */
    protected function _parseName(Provider $provider)
    {
        $className = get_class($provider);
        $providerName = $className;
        if (strpos($providerName, '_') !== false) {
            $providerName = substr($providerName, strrpos($providerName, '_')+1);
        }
        $providerName = substr($className, strrpos($className, '\\')+1);
        if (substr($providerName, -8) == 'Provider') {
            $providerName = substr($providerName, 0, strlen($providerName)-8);
        }
        return $providerName;
    }

}
