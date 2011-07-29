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

use Zend\Tool\Project\Profile\Profile as ProjectProfile;

/**
 * @uses       \Zend\Tool\Project\Provider\AbstractProvider
 * @uses       \Zend\Tool\Project\Provider\Exception
 * @category   Zend
 * @package    Zend_Tool
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class View extends AbstractProvider
{

    /**
     * createResource()
     *
     * @param \Zend\Tool\Project\Profile\Profile $profile
     * @param string $actionName
     * @param string $controllerName
     * @param string $moduleName
     * @return \Zend\Tool\Project\Profile\Resource\Resource
     */
    public static function createResource(ProjectProfile $profile, $actionName, $controllerName, $moduleName = null)
    {
        if (!is_string($actionName)) {
            throw new Exception\RuntimeException('Zend\\Tool\\Project\\Provider\\View::createResource() expects \"actionName\" is the name of a controller resource to create.');
        }

        if (!is_string($controllerName)) {
            throw new Exception\RuntimeException('Zend\\Tool\\Project\\Provider\\View::createResource() expects \"controllerName\" is the name of a controller resource to create.');
        }

        $profileSearchParams = array();

        if ($moduleName) {
            $profileSearchParams = array('modulesDirectory', 'moduleDirectory' => array('moduleName' => $moduleName));
            $noModuleSearch = null;
        } else {
            $noModuleSearch = array('modulesDirectory');
        }

        $profileSearchParams[] = 'viewsDirectory';
        $profileSearchParams[] = 'viewScriptsDirectory';

        if (($viewScriptsDirectory = $profile->search($profileSearchParams, $noModuleSearch)) === false) {
            throw new Exception\RuntimeException('This project does not have a viewScriptsDirectory resource.');
        }

        $profileSearchParams['viewControllerScriptsDirectory'] = array('forControllerName' => $controllerName);

        // @todo check if below is failing b/c of above search params
        if (($viewControllerScriptsDirectory = $viewScriptsDirectory->search($profileSearchParams)) === false) {
            $viewControllerScriptsDirectory = $viewScriptsDirectory->createResource('viewControllerScriptsDirectory', array('forControllerName' => $controllerName));
        }

        $newViewScriptFile = $viewControllerScriptsDirectory->createResource('ViewScriptFile', array('forActionName' => $actionName));

        return $newViewScriptFile;
    }

    /**
     * create()
     *
     * @param string $controllerName
     * @param string $actionNameOrSimpleName
     */
    public function create($controllerName, $actionNameOrSimpleName)
    {

        if ($controllerName == '' || $actionNameOrSimpleName == '') {
            throw new Exception\RuntimeException('ControllerName and/or ActionName are empty.');
        }

        $profile = $this->_loadProfile();

        $view = self::createResource($profile, $actionNameOrSimpleName, $controllerName);

        if ($this->_registry->getRequest()->isPretend()) {
            $this->_registry->getResponse(
                'Would create a view script in location ' . $view->getContext()->getPath()
                );
        } else {
            $this->_registry->getResponse(
                'Creating a view script in location ' . $view->getContext()->getPath()
                );
            $view->create();
            $this->_storeProfile();
        }

    }
}
