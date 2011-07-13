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
class Test extends AbstractProvider
{

    protected $_specialties = array('Application', 'Library');

    /**
     * isTestingEnabled()
     *
     * @param \Zend\Tool\Project\Profile\Profile $profile
     * @return bool
     */
    public static function isTestingEnabled(ProjectProfile $profile)
    {
        $profileSearchParams = array('testsDirectory');
        $testsDirectory = $profile->search($profileSearchParams);

        return $testsDirectory->isEnabled();
    }

    /**
     * createApplicationResource()
     *
     * @param \Zend\Tool\Project\Profile\Profile $profile
     * @param string $controllerName
     * @param string $actionName
     * @param string $moduleName
     * @return \Zend\Tool\Project\Profile\Resource\Resource
     */
    public static function createApplicationResource(ProjectProfile $profile, $controllerName, $actionName, $moduleName = null)
    {
        if (!is_string($controllerName)) {
            throw new Exception\RuntimeException('Zend\\Tool\\Project\\Provider\\View::createApplicationResource() expects \"controllerName\" is the name of a controller resource to create.');
        }

        if (!is_string($actionName)) {
            throw new Exception\RuntimeException('Zend\\Tool\\Project\\Provider\\View::createApplicationResource() expects \"actionName\" is the name of a controller resource to create.');
        }

        $testsDirectoryResource = $profile->search('testsDirectory');

        if (($testAppDirectoryResource = $testsDirectoryResource->search('testApplicationDirectory')) === false) {
            $testAppDirectoryResource = $testsDirectoryResource->createResource('testApplicationDirectory');
        }

        if ($moduleName) {
            //@todo $moduleName
            $moduleName = '';
        }

        if (($testAppControllerDirectoryResource = $testAppDirectoryResource->search('testApplicationControllerDirectory')) === false) {
            $testAppControllerDirectoryResource = $testAppDirectoryResource->createResource('testApplicationControllerDirectory');
        }

        $testAppControllerFileResource = $testAppControllerDirectoryResource->createResource('testApplicationControllerFile', array('forControllerName' => $controllerName));

        return $testAppControllerFileResource;
    }

    /**
     * createLibraryResource()
     *
     * @param \Zend\Tool\Project\Profile\Profile $profile
     * @param string $libraryClassName
     * @return \Zend\Tool\Project\Profile\Resource\Resource
     */
    public static function createLibraryResource(ProjectProfile $profile, $libraryClassName)
    {
        $testLibraryDirectoryResource = $profile->search(array('TestsDirectory', 'TestLibraryDirectory'));


        $fsParts = explode('_', $libraryClassName);

        $currentDirectoryResource = $testLibraryDirectoryResource;

        while ($nameOrNamespacePart = array_shift($fsParts)) {

            if (count($fsParts) > 0) {

                if (($libraryDirectoryResource = $currentDirectoryResource->search(array('TestLibraryNamespaceDirectory' => array('namespaceName' => $nameOrNamespacePart)))) === false) {
                    $currentDirectoryResource = $currentDirectoryResource->createResource('TestLibraryNamespaceDirectory', array('namespaceName' => $nameOrNamespacePart));
                } else {
                    $currentDirectoryResource = $libraryDirectoryResource;
                }


            } else {

                if (($libraryFileResource = $currentDirectoryResource->search(array('TestLibraryFile' => array('forClassName' => $libraryClassName)))) === false) {
                    $libraryFileResource = $currentDirectoryResource->createResource('TestLibraryFile', array('forClassName' => $libraryClassName));
                }

            }

        }

        return $libraryFileResource;
    }

    public function enable()
    {

    }

    public function disable()
    {

    }

    /**
     * create()
     *
     * @param unknown_type $libraryClassName
     */
    public function create($libraryClassName)
    {
        $profile = $this->_loadProfile();

        if (!self::isTestingEnabled($profile)) {
            $this->_registry->getResponse()->appendContent('Testing is not enabled for this project.');
        }

        $testLibraryResource = self::createLibraryResource($profile, $libraryClassName);

        $response = $this->_registry->getResponse();

        if ($this->_registry->getRequest()->isPretend()) {
            $response->appendContent('Would create a library stub in location ' . $testLibraryResource->getContext()->getPath());
        } else {
            $response->appendContent('Creating a library stub in location ' . $testLibraryResource->getContext()->getPath());
            $testLibraryResource->create();
            $this->_storeProfile();
        }

    }

}
