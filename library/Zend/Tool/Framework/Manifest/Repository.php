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
namespace Zend\Tool\Framework\Manifest;

use Zend\Tool\Framework\Manifest,
    Zend\Tool\Framework\Registry,
    Zend\Tool\Framework\RegistryEnabled,
    Zend\Tool\Framework\Metadata,
    Zend\Tool\Framework\Manifest\Exception;

/**
 * @uses       ArrayIterator
 * @uses       Countable
 * @uses       IteratorAggregate
 * @uses       \Zend\Tool\Framework\Action\Base
 * @uses       \Zend\Tool\Framework\Manifest\Exception
 * @uses       \Zend\Tool\Framework\Metadata\Dynamic
 * @uses       \Zend\Tool\Framework\RegistryEnabled
 * @category   Zend
 * @package    Zend_Tool
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Repository implements RegistryEnabled, \IteratorAggregate, \Countable
{

    /**
     * @var Zend\Tool\Framework\Provider\Registry
     */
    protected $_registry = null;

    /**
     * @var array
     */
    protected $_manifests = array();

    /**
     * @var array Array of \Zend\Tool\Framework\Metadata
     */
    protected $_metadatas = array();

    /**
     * setRegistry()
     *
     * @param \Zend\Tool\Framework\Registry $registry
     * @return unknown
     */
    public function setRegistry(Registry $registry)
    {
        $this->_registry = $registry;
        return $this;
    }

    /**
     * addManifest() - Add a manifest for later processing
     *
     * @param \Zend\Tool\Framework\Manifest $manifest
     * @return \Zend\Tool\Framework\Manifest\Repository
     */
    public function addManifest(Manifest $manifest)
    {
        // we need to get an index number so that manifests with
        // higher indexes have priority over others
        $index = count($this->_manifests);

        if ($manifest instanceof RegistryEnabled) {
            $manifest->setRegistry($this->_registry);
        }

        // if the manifest supplies a getIndex() method, use it
        if ($manifest instanceof Indexable) {
            $index = $manifest->getIndex();
        }

        // get the required objects from the framework registry
        $actionRepository   = $this->_registry->getActionRepository();
        $providerRepository = $this->_registry->getProviderRepository();

        // load providers if interface supports that method
        if ($manifest instanceof ProviderManifestable) {
            $providers = $manifest->getProviders();
            if (!is_array($providers)) {
                $providers = array($providers);
            }

            foreach ($providers as $provider) {

                // if provider is a string, try and load it as an object
                if (is_string($provider)) {
                    $provider = new $provider();
                }
                
                if (!$provider instanceof \Zend\Tool\Framework\Provider) {
                    throw new Exception\InvalidArgumentException(
                        'A provider provided by the ' . get_class($manifest)
                        . ' does not implement Zend\\Tool\\Framework\\Provider\\Interface'
                        );
                }
                if (!$providerRepository->hasProvider($provider, false)) {
                    $providerRepository->addProvider($provider);
                }
            }

        }

        // load actions if interface supports that method
        if ($manifest instanceof ActionManifestable) {
            $actions = $manifest->getActions();
            if (!is_array($actions)) {
                $actions = array($actions);
            }

            foreach ($actions as $action) {
                if (is_string($action)) {
                    $action = new \Zend\Tool\Framework\Action\Base($action);
                }
                $actionRepository->addAction($action);
            }
        }

        // should we detect collisions here? does it even matter?
        $this->_manifests[$index] = $manifest;
        ksort($this->_manifests);

        return $this;
    }

    /**
     * getManifests()
     *
     * @return \Zend\Tool\Framework\Manifest\Manifest[]
     */
    public function getManifests()
    {
        return $this->_manifests;
    }

    /**
     * addMetadata() - add a metadata peice by peice
     *
     * @param Zend\Tool\Framework\Manifest\Metadata $metadata
     * @return \Zend\Tool\Framework\Manifest\Repository
     */
    public function addMetadata(Metadata $metadata)
    {
        $this->_metadatas[] = $metadata;
        return $this;
    }

    /**
     * process() - Process is expected to be called at the end of client construction time.
     * By this time, the loader has run and loaded any found manifests into the repository
     * for loading
     *
     * @return \Zend\Tool\Framework\Manifest\Repository
     */
    public function process()
    {

        foreach ($this->_manifests as $manifest) {
            if ($manifest instanceof MetadataManifestable) {
                $metadatas = $manifest->getMetadata();
                if (!is_array($metadatas)) {
                    $metadatas = array($metadatas);
                }

                foreach ($metadatas as $metadata) {
                    if (is_array($metadata)) {
                        $metadata = new Metadata\Dynamic($metadata);
                    }
                    
                    if (!$metadata instanceof Metadata) {
                        throw new Exception\RuntimeException(
                            'A Zend\\Tool\\Framework\\Metadata\\Interface object was not found in manifest ' . get_class($manifest)
                            );
                    }

                    $this->addMetadata($metadata);
                }

            }
        }

        return $this;
    }

    /**
     * getMetadatas() - This is the main search function for the repository.
     *
     * example: This will retrieve all metadata that matches the following criteria
     *      $manifestRepo->getMetadatas(array(
     *          'providerName' => 'Version',
     *          'actionName' => 'show'
     *          ));
     *
     * @param array $searchProperties
     * @param bool $includeNonExistentProperties
     * @return Zend\Tool\Framework\Manifest\Metadata[]
     */
    public function getMetadatas(Array $searchProperties = array(), $includeNonExistentProperties = true)
    {

        $returnMetadatas = array();

        // loop through the metadatas so that we can search each individual one
        foreach ($this->_metadatas as $metadata) {

            // each value will be retrieved from the metadata, each metadata should
            // implement a getter method to retrieve the value
            foreach ($searchProperties as $searchPropertyName => $searchPropertyValue) {
                if (method_exists($metadata, 'get' . $searchPropertyName)) {
                    if ($metadata->{'get' . $searchPropertyName}() != $searchPropertyValue) {
                        // if the metadata supports a specific property but the value does not
                        // match, move on
                        continue 2;
                    }
                } elseif (!$includeNonExistentProperties) {
                    // if the option $includeNonExitentProperties is false, then move on as
                    // we dont want to include this metadata if non existent
                    // search properties are not inside the target (current) metadata
                    continue 2;
                }
            }

            // all searching has been accounted for, if we reach this point, then the metadata
            // is good and we can return it
            $returnMetadatas[] = $metadata;

        }

        return $returnMetadatas;
    }

    /**
     * getMetadata() - This will proxy to getMetadatas(), but will only return a single metadata.  This method
     * should be used in situations where the search criteria is known to only find a single metadata object
     *
     * @param array $searchProperties
     * @param bool $includeNonExistentProperties
     * @return Zend\Tool\Framework\Manifest\Metadata
     */
    public function getMetadata(Array $searchProperties = array(), $includeNonExistentProperties = true)
    {
        $metadatas = $this->getMetadatas($searchProperties, $includeNonExistentProperties);
        return array_shift($metadatas);
    }

    /**
     * __toString() - cast to string
     *
     * @return string
     */
    public function __toString()
    {
        $metadatasByType = array();

        foreach ($this->_metadatas as $metadata) {
            if (!array_key_exists($metadata->getType(), $metadatasByType)) {
                $metadatasByType[$metadata->getType()] = array();
            }
            $metadatasByType[$metadata->getType()][] = $metadata;
        }

        $string = '';
        foreach ($metadatasByType as $type => $metadatas) {
            $string .= $type . PHP_EOL;
            foreach ($metadatas as $metadata) {
                $metadataString = '    ' . $metadata->__toString() . PHP_EOL;
                //$metadataString = str_replace(PHP_EOL, PHP_EOL . '    ', $metadataString);
                $string .= $metadataString;
            }
        }

        return $string;
    }

    /**
     * count() - required by the Countable Interface
     *
     * @return int
     */
    public function count()
    {
        return count($this->_metadatas);
    }

    /**
     * getIterator() - required by the IteratorAggregate interface
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->_metadatas);
    }

}
