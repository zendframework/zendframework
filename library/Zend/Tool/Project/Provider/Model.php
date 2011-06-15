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
 * @uses       \Zend\Tool\Project\Provider\AbstractProvider
 * @uses       \Zend\Tool\Project\Provider\Exception
 * @category   Zend
 * @package    Zend_Tool
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Model extends AbstractProvider
{

    public static function createResource(ProjectProfile $profile, $modelName, $moduleName = null)
    {
        if (!is_string($modelName)) {
            throw new Exception\RuntimeException('Zend\\Tool\\Project\\Provider\\Model::createResource() expects \"modelName\" is the name of a model resource to create.');
        }

        if (!($modelsDirectory = self::_getModelsDirectoryResource($profile, $moduleName))) {
            if ($moduleName) {
                $exceptionMessage = 'A model directory for module "' . $moduleName . '" was not found.';
            } else {
                $exceptionMessage = 'A model directory was not found.';
            }
            throw new Exception\RuntimeException($exceptionMessage);
        }

        $newModel = $modelsDirectory->createResource(
            'modelFile', 
            array('modelName' => $modelName, 'moduleName' => $moduleName)
            );

        return $newModel;
    }

    /**
     * hasResource()
     *
     * @param \Zend\Tool\Project\Profile\Profile $profile
     * @param string $modelName
     * @param string $moduleName
     * @return \Zend\Tool\Project\Profile\Resource\Resource
     */
    public static function hasResource(ProjectProfile $profile, $modelName, $moduleName = null)
    {
        if (!is_string($modelName)) {
            throw new Exception\RuntimeException('Zend\\Tool\\Project\\Provider\\Model::createResource() expects \"modelName\" is the name of a model resource to check for existence.');
        }

        $modelsDirectory = self::_getModelsDirectoryResource($profile, $moduleName);
        return (($modelsDirectory->search(array('modelFile' => array('modelName' => $modelName)))) instanceof Resource);
    }
    
    /**
     * _getModelsDirectoryResource()
     *
     * @param \Zend\Tool\Project\Profile\Profile $profile
     * @param string $moduleName
     * @return \Zend\Tool\Project\Profile\Resource\Resource
     */
    protected static function _getModelsDirectoryResource(ProjectProfile $profile, $moduleName = null)
    {
        $profileSearchParams = array();

        if ($moduleName != null && is_string($moduleName)) {
            $profileSearchParams = array('modulesDirectory', 'moduleDirectory' => array('moduleName' => $moduleName));
        }

        $profileSearchParams[] = 'modelsDirectory';

        return $profile->search($profileSearchParams);
    }
    
    /**
     * Create a new model
     *
     * @param string $name
     * @param string $module
     */
    public function create($name, $module = null)
    {
        $this->_loadProfile(self::NO_PROFILE_THROW_EXCEPTION);

        $originalName = $name;
        
        $name = ucwords($name);
        
        // determine if testing is enabled in the project
        $testingEnabled = false; //Zend\Tool\Project\Provider\Test::isTestingEnabled($this->_loadedProfile);
        $testModelResource = null;

        // Check that there is not a dash or underscore, return if doesnt match regex
        if (preg_match('#[_-]#', $name)) {
            throw new Exception\RuntimeException('Model names should be camel cased.');
        }
        
        if (self::hasResource($this->_loadedProfile, $name, $module)) {
            throw new Exception\RuntimeException('This project already has a model named ' . $name);
        }
        
        // get request/response object
        $request = $this->_registry->getRequest();
        $response = $this->_registry->getResponse();
        
        // alert the user about inline converted names
        $tense = (($request->isPretend()) ? 'would be' : 'is');
        
        if ($name !== $originalName) {
            $response->appendContent(
                'Note: The canonical model name that ' . $tense
                    . ' used with other providers is "' . $name . '";'
                    . ' not "' . $originalName . '" as supplied',
                array('color' => array('yellow'))
                );
        }
        
        try {
            $modelResource = self::createResource($this->_loadedProfile, $name, $module);

            if ($testingEnabled) {
                // $testModelResource = Zend\Tool\Project\Provider\Test::createApplicationResource($this->_loadedProfile, $name, 'index', $module);
            }

        } catch (\Exception $e) {
            $response->setException($e);
            return;
        }

        // do the creation
        if ($request->isPretend()) {

            $response->appendContent('Would create a model at '  . $modelResource->getContext()->getPath());

            if ($testModelResource) {
                $response->appendContent('Would create a model test file at ' . $testModelResource->getContext()->getPath());
            }

        } else {

            $response->appendContent('Creating a model at ' . $modelResource->getContext()->getPath());
            $modelResource->create();

            if ($testModelResource) {
                $response->appendContent('Creating a model test file at ' . $testModelResource->getContext()->getPath());
                $testModelResource->create();
            }

            $this->_storeProfile();
        }

    }


}
