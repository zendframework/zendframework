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
use Zend\Tool\Framework\Metadata,
    Zend\Tool\Framework\Manifest\MetadataManifestable,
    Zend\Tool\Framework\Registry,
    Zend\Tool\Framework\RegistryEnabled;

/**
 * Zend_Tool_Framework_Client_ConsoleClient_Manifest
 *
 * @uses       \Zend\Filter\FilterChain
 * @uses       \Zend\Filter\StringToLower
 * @uses       \Zend\Filter\Word\CamelCaseToDash
 * @uses       \Zend\Tool\Framework\Manifest\MetadataManifestable
 * @uses       \Zend\Tool\Framework\Metadata\Tool
 * @uses       \Zend\Tool\Framework\RegistryEnabled
 * @category   Zend
 * @package    Zend_Tool
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Manifest implements RegistryEnabled, MetadataManifestable
{

    /**
     * @var \Zend\Tool\Framework\Registry
     */
    protected $_registry = null;

    /**
     * setRegistry() - Required for the Zend\Tool\Framework\RegistryEnabled interface
     *
     * @param \Zend\Tool\Framework\Registry $registry
     * @return \Zend\Tool\Framework\Client\Console\Manifest
     */
    public function setRegistry(Registry $registry)
    {
        $this->_registry = $registry;
        return $this;
    }

    /**
     * getMetadata() is required by the Manifest Interface.
     *
     * These are the following metadatas that will be setup:
     *
     * normalizedActionName
     *   - metadata for actions
     *   - value will be a dashed name for the action named in 'actionName'
     * normalizedProviderName
     *   - metadata for providers
     *   - value will be a dashed-name for the provider named in 'providerName'
     * normalizedProviderSpecialtyNames
     *   - metadata for providers
     * normalizedActionableMethodLongParameters
     *   - metadata for providers
     * normalizedActionableMethodShortParameters
     *   - metadata for providers
     *
     * @return array Array of Metadatas
     */
    public function getMetadata()
    {
        $metadatas = array();

        // setup the camelCase to dashed filter to use since cli expects dashed named
        $lowerFilter = new \Zend\Filter\FilterChain();
        $lowerFilter->attach(new \Zend\Filter\StringToLower());

        // get the registry to get the action and provider repository
        $actionRepository   = $this->_registry->getActionRepository();
        $providerRepository = $this->_registry->getProviderRepository();

        // loop through all actions and create a metadata for each
        foreach ($actionRepository->getActions() as $action) {
            // each action metadata will be called
            $metadatas[] = new Metadata\Tool(array(
                'name'            => 'normalizedActionName',
                'value'           => $lowerFilter->filter($action->getName()),
                'reference'       => $action,
                'actionName'      => $action->getName(),
                'clientName'      => 'all'
                ));
        }

        foreach ($providerRepository->getProviderSignatures() as $providerSignature) {

            // create the metadata for the provider's cliProviderName
            $metadatas[] = new Metadata\Tool(array(
                'name'            => 'normalizedProviderName',
                'value'           => $lowerFilter->filter($providerSignature->getName()),
                'reference'       => $providerSignature,
                'clientName'      => 'all',
                'providerName'    => $providerSignature->getName()
                ));

            // create the metadatas for the per provider specialites in providerSpecaltyNames
            foreach ($providerSignature->getSpecialties() as $specialty) {

                if ($specialty == '_Global') {
                    continue;
                }
                
                $metadatas[] = new Metadata\Tool(array(
                    'name'            => 'normalizedSpecialtyName',
                    'value'           => $lowerFilter->filter($specialty),
                    'reference'       => $providerSignature,
                    'clientName'      => 'all',
                    'providerName'    => $providerSignature->getName(),
                    'specialtyName'   => $specialty
                    ));

            }

            // $actionableMethod is keyed by the methodName (but not used)
            foreach ($providerSignature->getActionableMethods() as $actionableMethodData) {

                $methodLongParams  = array();
                $methodShortParams = array();

                // $actionableMethodData get both the long and short names
                foreach ($actionableMethodData['parameterInfo'] as $parameterInfoData) {

                    // filter to dashed
                    $methodLongParams[$parameterInfoData['name']] = $lowerFilter->filter($parameterInfoData['name']);

                    // simply lower the character, (its only 1 char after all)
                    $methodShortParams[$parameterInfoData['name']] = strtolower($parameterInfoData['name'][0]);

                }

                // create metadata for the long name cliActionableMethodLongParameters
                $metadatas[] = new Metadata\Tool(array(
                    'name'            => 'normalizedActionableMethodLongParams',
                    'value'           => $methodLongParams,
                    'clientName'      => 'console',
                    'providerName'    => $providerSignature->getName(),
                    'specialtyName'   => $actionableMethodData['specialty'],
                    'actionName'      => $actionableMethodData['actionName'],
                    'reference'       => &$actionableMethodData
                    ));

                // create metadata for the short name cliActionableMethodShortParameters
                $metadatas[] = new Metadata\Tool(array(
                    'name'            => 'normalizedActionableMethodShortParams',
                    'value'           => $methodShortParams,
                    'clientName'      => 'console',
                    'providerName'    => $providerSignature->getName(),
                    'specialtyName'   => $actionableMethodData['specialty'],
                    'actionName'      => $actionableMethodData['actionName'],
                    'reference'       => &$actionableMethodData
                    ));

            }

        }

        return $metadatas;
    }

    public function getIndex()
    {
        return 100000;
    }

}
