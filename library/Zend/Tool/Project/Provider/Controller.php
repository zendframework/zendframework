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
    Zend\Tool\Project\Profile\Resource\Resource;

/**
 * @uses       \Zend\Tool\Framework\Provider\Pretendable
 * @uses       \Zend\Tool\Project\Provider\AbstractProvider
 * @uses       \Zend\Tool\Project\Provider\Action
 * @uses       \Zend\Tool\Project\Provider\Exception
 * @uses       \Zend\Tool\Project\Provider\Test
 * @uses       \Zend\Tool\Project\Provider\View
 * @category   Zend
 * @package    Zend_Tool
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Controller
    extends AbstractProvider
    implements \Zend\Tool\Framework\Provider\Pretendable
{

    /**
     * createResource will create the controllerFile resource at the appropriate location in the
     * profile.  NOTE: it is your job to execute the create() method on the resource, as well as
     * store the profile when done.
     *
     * @param \Zend\Tool\Project\Profile\Profile $profile
     * @param string $controllerName
     * @param string $moduleName
     * @return \Zend\Tool\Project\Profile\Resource\Resource
     */
    public static function createResource(ProjectProfile $profile, $controllerName, $moduleName = null)
    {
        if (!is_string($controllerName)) {
            throw new Exception\RuntimeException('Zend\\Tool\\Project\\Provider\\Controller::createResource() expects \"controllerName\" is the name of a controller resource to create.');
        }

        if (!($controllersDirectory = self::_getControllersDirectoryResource($profile, $moduleName))) {
            if ($moduleName) {
                $exceptionMessage = 'A controller directory for module "' . $moduleName . '" was not found.';
            } else {
                $exceptionMessage = 'A controller directory was not found.';
            }
            throw new Exception\RuntimeException($exceptionMessage);
        }

        $newController = $controllersDirectory->createResource(
            'controllerFile', 
            array('controllerName' => $controllerName, 'moduleName' => $moduleName)
            );

        return $newController;
    }

    /**
     * hasResource()
     *
     * @param \Zend\Tool\Project\Profile\Profile $profile
     * @param string $controllerName
     * @param string $moduleName
     * @return \Zend\Tool\Project\Profile\Resource\Resource
     */
    public static function hasResource(ProjectProfile $profile, $controllerName, $moduleName = null)
    {
        if (!is_string($controllerName)) {
            throw new Exception\RuntimeException('Zend\\Tool\\Project\\Provider\\Controller::createResource() expects \"controllerName\" is the name of a controller resource to create.');
        }

        $controllersDirectory = self::_getControllersDirectoryResource($profile, $moduleName);
        return (($controllersDirectory->search(array('controllerFile' => array('controllerName' => $controllerName)))) instanceof Resource);
    }

    /**
     * _getControllersDirectoryResource()
     *
     * @param \Zend\Tool\Project\Profile\Profile $profile
     * @param string $moduleName
     * @return \Zend\Tool\Project\Profile\Resource\Resource
     */
    protected static function _getControllersDirectoryResource(ProjectProfile $profile, $moduleName = null)
    {
        $profileSearchParams = array();

        if ($moduleName != null && is_string($moduleName)) {
            $profileSearchParams = array('modulesDirectory', 'moduleDirectory' => array('moduleName' => $moduleName));
        }

        $profileSearchParams[] = 'controllersDirectory';

        return $profile->search($profileSearchParams);
    }

    /**
     * Create a new controller
     *
     * @param string $name The name of the controller to create, in camelCase.
     * @param bool $indexActionIncluded Whether or not to create the index action.
     */
    public function create($name, $indexActionIncluded = true, $module = null)
    {
        $this->_loadProfile(self::NO_PROFILE_THROW_EXCEPTION);

        // determine if testing is enabled in the project
        $testingEnabled = Test::isTestingEnabled($this->_loadedProfile);

        if (self::hasResource($this->_loadedProfile, $name, $module)) {
            throw new Exception\RuntimeException('This project already has a controller named ' . $name);
        }

        // Check that there is not a dash or underscore, return if doesnt match regex
        if (preg_match('#[_-]#', $name)) {
            throw new Exception\RuntimeException('Controller names should be camel cased.');
        }
        
        $originalName = $name;
        $name = ucfirst($name);
        
        // get request & response
        $request = $this->_registry->getRequest();
        $response = $this->_registry->getResponse();
        
        try {
            $controllerResource = self::createResource($this->_loadedProfile, $name, $module);
            if ($indexActionIncluded) {
                $indexActionResource = Action::createResource($this->_loadedProfile, 'index', $name, $module);
                $indexActionViewResource = View::createResource($this->_loadedProfile, 'index', $name, $module);
            }
            if ($testingEnabled) {
                $testControllerResource = Test::createApplicationResource($this->_loadedProfile, $name, 'index', $module);
            }

        } catch (\Exception $e) {
            $response->setException($e);
            return;
        }

        // determime if we need to note to the user about the name
        if (($name !== $originalName)) {
            $tense = (($request->isPretend()) ? 'would be' : 'is');
            $response->appendContent(
                'Note: The canonical controller name that ' . $tense
                    . ' used with other providers is "' . $name . '";'
                    . ' not "' . $originalName . '" as supplied',
                array('color' => array('yellow'))
                );
            unset($tense);
        }
        
        // do the creation
        if ($request->isPretend()) {
            
            $response->appendContent('Would create a controller at '  . $controllerResource->getContext()->getPath());

            if (isset($indexActionResource)) {
                $response->appendContent('Would create an index action method in controller ' . $name);
                $response->appendContent('Would create a view script for the index action method at ' . $indexActionViewResource->getContext()->getPath());
            }
            
            if ($testControllerResource) {
                $response->appendContent('Would create a controller test file at ' . $testControllerResource->getContext()->getPath());
            }

        } else {

            $response->appendContent('Creating a controller at ' . $controllerResource->getContext()->getPath());
            $controllerResource->create();

            if (isset($indexActionResource)) {
                $response->appendContent('Creating an index action method in controller ' . $name);
                $indexActionResource->create();
                $response->appendContent('Creating a view script for the index action method at ' . $indexActionViewResource->getContext()->getPath());
                $indexActionViewResource->create();
            }

            if ($testControllerResource) {
                $response->appendContent('Creating a controller test file at ' . $testControllerResource->getContext()->getPath());
                $testControllerResource->create();
            }

            $this->_storeProfile();
        }

    }



}
