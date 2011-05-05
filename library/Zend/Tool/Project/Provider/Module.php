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
namespace Zend\Tool\Project\Provider;

use Zend\Tool\Project\Profile\Profile as ProjectProfile,
    Zend\Tool\Project\Profile\Iterator\ContextFilter,
    Zend\Tool\Project\Profile\Iterator\EnabledResourceFilter,
    Zend\Tool\Project\Profile\Resource\Resource;

/**
 * @uses       RecursiveIteratorIterator
 * @uses       \Zend\Tool\Framework\Provider\Pretendable
 * @uses       \Zend\Tool\Project\Profile\Iterator\ContextFilter
 * @uses       \Zend\Tool\Project\Profile\Iterator\EnabledResourceFilter
 * @uses       \Zend\Tool\Project\Provider\AbstractProvider
 * @uses       \Zend\Tool\Project\Provider\Exception
 * @category   Zend
 * @package    Zend_Tool
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Module
    extends AbstractProvider
    implements \Zend\Tool\Framework\Provider\Pretendable
{

    public static function createResources(ProjectProfile $profile, $moduleName, Resource $targetModuleResource = null)
    {

        // find the appliction directory, it will serve as our module skeleton
        if ($targetModuleResource == null) {
            $targetModuleResource = $profile->search('applicationDirectory');
            $targetModuleEnabledResources = array(
                'ControllersDirectory', 'ModelsDirectory', 'ViewsDirectory',
                'ViewScriptsDirectory', 'ViewHelpersDirectory', 'ViewFiltersDirectory'
                );
        }

        // find the actual modules directory we will use to house our module
        $modulesDirectory = $profile->search('modulesDirectory');

        // if there is a module directory already, except
        if ($modulesDirectory->search(array('moduleDirectory' => array('moduleName' => $moduleName)))) {
            throw new Exception\RuntimeException('A module named "' . $moduleName . '" already exists.');
        }

        // create the module directory
        $moduleDirectory = $modulesDirectory->createResource('moduleDirectory', array('moduleName' => $moduleName));

        // create a context filter so that we can pull out only what we need from the module skeleton
        $moduleContextFilterIterator = new ContextFilter(
            $targetModuleResource,
            array(
                'denyNames' => array('ModulesDirectory', 'ViewControllerScriptsDirectory'),
                'denyType'  => 'Zend\Tool\Project\Context\Filesystem\File'
                )
            );

        // the iterator for the module skeleton
        $targetIterator = new \RecursiveIteratorIterator($moduleContextFilterIterator, \RecursiveIteratorIterator::SELF_FIRST);

        // initialize some loop state information
        $currentDepth = 0;
        $parentResources = array();
        $currentResource = $moduleDirectory;
        $currentChildResource = null;

        // loop through the target module skeleton
        foreach ($targetIterator as $targetSubResource) {

            $depthDifference = $targetIterator->getDepth() - $currentDepth;
            $currentDepth = $targetIterator->getDepth();

            if ($depthDifference === 1) {
                // if we went down into a child, make note
                array_push($parentResources, $currentResource);
                // this will have always been set previously by another loop
                $currentResource = $currentChildResource;
            } elseif ($depthDifference < 0) {
                // if we went up to a parent, make note
                $i = $depthDifference;
                do {
                    // if we went out more than 1 parent, get to the correct parent
                    $currentResource = array_pop($parentResources);
                } while ($i-- > 0);
            }

            // get parameters for the newly created module resource
            $params = $targetSubResource->getAttributes();
            $currentChildResource = $currentResource->createResource($targetSubResource->getName(), $params);

            // based of the provided list (Currently up top), enable specific resources
            if (isset($targetModuleEnabledResources)) {
                $currentChildResource->setEnabled(in_array($targetSubResource->getName(), $targetModuleEnabledResources));
            } else {
                $currentChildResource->setEnabled($targetSubResource->isEnabled());
            }

        }

        return $moduleDirectory;
    }

    /**
     * create()
     *
     * @param string $name
     */
    public function create($name) //, $moduleProfile = null)
    {
        $this->_loadProfile(self::NO_PROFILE_THROW_EXCEPTION);

        $resources = self::createResources($this->_loadedProfile, $name);

        $response = $this->_registry->getResponse();

        if ($this->_registry->getRequest()->isPretend()) {
            $response->appendContent('I would create the following module and artifacts:');
            foreach (new \RecursiveIteratorIterator($resources, \RecursiveIteratorIterator::SELF_FIRST) as $resource) {
                if (is_callable(array($resource->getContext(), 'getPath'))) {
                    $response->appendContent($resource->getContext()->getPath());
                }
            }
        } else {
            $response->appendContent('Creating the following module and artifacts:');
            $enabledFilter = new EnabledResourceFilter($resources);
            foreach (new \RecursiveIteratorIterator($enabledFilter, \RecursiveIteratorIterator::SELF_FIRST) as $resource) {
                $response->appendContent($resource->getContext()->getPath());
                $resource->create();
            }
            
            if (strtolower($name) == 'default') {
                $response->appendContent('Added a key for the default module to the application.ini file');
                $appConfigFile = $this->_loadedProfile->search('ApplicationConfigFile');
                $appConfigFile->addStringItem('resources.frontController.params.prefixDefaultModule', '1', 'production');
                $appConfigFile->create();
            }

            // store changes to the profile
            $this->_storeProfile();
        }

    }

}

