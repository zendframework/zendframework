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
 * @uses       \Zend\Tool\Project\Provider\Test
 * @category   Zend
 * @package    Zend_Tool
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Form extends AbstractProvider
{

    public static function createResource(ProjectProfile $profile, $formName, $moduleName = null)
    {
        if (!is_string($formName)) {
            throw new Exception\RuntimeException('Zend\\Tool\\Project\\Provider\\Form::createResource() expects \"formName\" is the name of a form resource to create.');
        }

        if (!($formsDirectory = self::_getFormsDirectoryResource($profile, $moduleName))) {
            if ($moduleName) {
                $exceptionMessage = 'A form directory for module "' . $moduleName . '" was not found.';
            } else {
                $exceptionMessage = 'A form directory was not found.';
            }
            throw new Exception\RuntimeException($exceptionMessage);
        }

        $newForm = $formsDirectory->createResource(
            'formFile', 
            array('formName' => $formName, 'moduleName' => $moduleName)
            );

        return $newForm;
    }

    /**
     * hasResource()
     *
     * @param \Zend\Tool\Project\Profile\Profile $profile
     * @param string $formName
     * @param string $moduleName
     * @return \Zend\Tool\Project\Profile\Resource\Resource
     */
    public static function hasResource(ProjectProfile $profile, $formName, $moduleName = null)
    {
        if (!is_string($formName)) {
            throw new Exception\RuntimeException('Zend\\Tool\\Project\\Provider\\Form::createResource() expects \"formName\" is the name of a form resource to check for existence.');
        }

        $formsDirectory = self::_getFormsDirectoryResource($profile, $moduleName);
        return (($formsDirectory->search(array('formFile' => array('formName' => $formName)))) instanceof Resource);
    }
    
    /**
     * _getFormsDirectoryResource()
     *
     * @param \Zend\Tool\Project\Profile\Profile $profile
     * @param string $moduleName
     * @return \Zend\Tool\Project\Profile\Resource\Resource
     */
    protected static function _getFormsDirectoryResource(ProjectProfile $profile, $moduleName = null)
    {
        $profileSearchParams = array();

        if ($moduleName != null && is_string($moduleName)) {
            $profileSearchParams = array('modulesDirectory', 'moduleDirectory' => array('moduleName' => $moduleName));
        }

        $profileSearchParams[] = 'formsDirectory';

        return $profile->search($profileSearchParams);
    }
    
    /**
     * Create a new form
     *
     * @param string $name
     * @param string $module
     */
    public function create($name, $module = null)
    {
        $this->_loadProfile(self::NO_PROFILE_THROW_EXCEPTION);

        // determine if testing is enabled in the project
        $testingEnabled = Test::isTestingEnabled($this->_loadedProfile);

        if (self::hasResource($this->_loadedProfile, $name, $module)) {
            throw new Exception\RuntimeException('This project already has a form named ' . $name);
        }

        // Check that there is not a dash or underscore, return if doesnt match regex
        if (preg_match('#[_-]#', $name)) {
            throw new Exception\RuntimeException('Form names should be camel cased.');
        }
        
        $name = ucwords($name);
        
        try {
            $formResource = self::createResource($this->_loadedProfile, $name, $module);

            if ($testingEnabled) {
                $testFormResource = null;
                // $testFormResource = Zend\Tool\Project\Provider\Test::createApplicationResource($this->_loadedProfile, $name, 'index', $module);
            }

        } catch (\Exception $e) {
            $response = $this->_registry->getResponse();
            $response->setException($e);
            return;
        }

        // do the creation
        if ($this->_registry->getRequest()->isPretend()) {

            $this->_registry->getResponse()->appendContent('Would create a form at '  . $formResource->getContext()->getPath());

            if ($testFormResource) {
                $this->_registry->getResponse()->appendContent('Would create a form test file at ' . $testFormResource->getContext()->getPath());
            }

        } else {

            $this->_registry->getResponse()->appendContent('Creating a form at ' . $formResource->getContext()->getPath());
            $formResource->create();

            if ($testFormResource) {
                $this->_registry->getResponse()->appendContent('Creating a form test file at ' . $testFormResource->getContext()->getPath());
                $testFormResource->create();
            }

            $this->_storeProfile();
        }

    }


}
