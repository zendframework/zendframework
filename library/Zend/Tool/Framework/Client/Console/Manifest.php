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
use Zend\Tool\Framework\Metadata,
    Zend\Tool\Framework\Manifest\MetadataManifestable,
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
    public function setRegistry(\Zend\Tool\Framework\Registry $registry)
    {
        $this->_registry = $registry;
        return $this;
    }

    /**
     * getMetadata() is required by the Manifest Interface.
     *
     * These are the following metadatas that will be setup:
     *
     * actionName
     *   - metadata for actions
     *   - value will be a dashed name for the action named in 'actionName'
     * providerName
     *   - metadata for providers
     *   - value will be a dashed-name for the provider named in 'providerName'
     * providerSpecialtyNames
     *   - metadata for providers
     * actionableMethodLongParameters
     *   - metadata for providers
     * actionableMethodShortParameters
     *   - metadata for providers
     *
     * @return array Array of Metadatas
     */
    public function getMetadata()
    {
        $metadatas = array();

        // setup the camelCase to dashed filter to use since cli expects dashed named
        $ccToDashedFilter = new \Zend\Filter\FilterChain();
        $ccToDashedFilter
            ->attach(new \Zend\Filter\StringToLower())
            ->attach(new \Zend\Filter\Word\CamelCaseToDash());

        // get the registry to get the action and provider repository
        $actionRepository   = $this->_registry->getActionRepository();
        $providerRepository = $this->_registry->getProviderRepository();

        // loop through all actions and create a metadata for each
        foreach ($actionRepository->getActions() as $action) {
            // each action metadata will be called
            $metadatas[] = new Metadata\Tool(array(
                'name'            => 'actionName',
                'value'           => $ccToDashedFilter->filter($action->getName()),
                'reference'       => $action,
                'actionName'      => $action->getName(),
                'clientName'      => 'console',
                'clientReference' => $this->_registry->getClient()
                ));
        }

        foreach ($providerRepository->getProviderSignatures() as $providerSignature) {

            // create the metadata for the provider's cliProviderName
            $metadatas[] = new Metadata\Tool(array(
                'name'            => 'providerName',
                'value'           => $ccToDashedFilter->filter($providerSignature->getName()),
                'reference'       => $providerSignature,
                'clientName'      => 'console',
                'providerName'    => $providerSignature->getName(),
                'clientReference' => $this->_registry->getClient()
                ));

            // create the metadatas for the per provider specialites in providerSpecaltyNames
            foreach ($providerSignature->getSpecialties() as $specialty) {

                $metadatas[] = new Metadata\Tool(array(
                    'name'            => 'specialtyName',
                    'value'           =>  $ccToDashedFilter->filter($specialty),
                    'reference'       => $providerSignature,
                    'clientName'      => 'console',
                    'providerName'    => $providerSignature->getName(),
                    'specialtyName'   => $specialty,
                    'clientReference' => $this->_registry->getClient()
                    ));

            }

            // $actionableMethod is keyed by the methodName (but not used)
            foreach ($providerSignature->getActionableMethods() as $actionableMethodData) {

                $methodLongParams  = array();
                $methodShortParams = array();

                // $actionableMethodData get both the long and short names
                foreach ($actionableMethodData['parameterInfo'] as $parameterInfoData) {

                    // filter to dashed
                    $methodLongParams[$parameterInfoData['name']] = $ccToDashedFilter->filter($parameterInfoData['name']);

                    // simply lower the character, (its only 1 char after all)
                    $methodShortParams[$parameterInfoData['name']] = strtolower($parameterInfoData['name'][0]);

                }

                // create metadata for the long name cliActionableMethodLongParameters
                $metadatas[] = new Metadata\Tool(array(
                    'name'            => 'actionableMethodLongParams',
                    'value'           => $methodLongParams,
                    'clientName'      => 'console',
                    'providerName'    => $providerSignature->getName(),
                    'specialtyName'   => $actionableMethodData['specialty'],
                    'actionName'      => $actionableMethodData['actionName'],
                    'reference'       => &$actionableMethodData,
                    'clientReference' => $this->_registry->getClient()
                    ));

                // create metadata for the short name cliActionableMethodShortParameters
                $metadatas[] = new Metadata\Tool(array(
                    'name'            => 'actionableMethodShortParams',
                    'value'           => $methodShortParams,
                    'clientName'      => 'console',
                    'providerName'    => $providerSignature->getName(),
                    'specialtyName'   => $actionableMethodData['specialty'],
                    'actionName'      => $actionableMethodData['actionName'],
                    'reference'       => &$actionableMethodData,
                    'clientReference' => $this->_registry->getClient()
                    ));

            }

        }

        return $metadatas;
    }

    public function getIndex()
    {
        return 10000;
    }

}
