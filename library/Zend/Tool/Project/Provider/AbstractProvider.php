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

use Zend\Tool\Project\Context,
    Zend\Tool\Project\Profile\Profile as ProjectProfile;

/**
 * @category   Zend
 * @package    Zend_Tool
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractProvider extends \Zend\Tool\Framework\Provider\AbstractProvider
{

    const NO_PROFILE_THROW_EXCEPTION = true;
    const NO_PROFILE_RETURN_FALSE    = false;

    /**
     * @var bool
     */
    protected static $_isInitialized = false;

    protected $_projectPath = null;

    /**
     * @var \Zend\Tool\Project\Profile\Profile
     */
    protected $_loadedProfile = null;

    /**
     * constructor
     *
     * YOU SHOULD NOT OVERRIDE THIS, unless you know what you are doing
     *
     */
    public function __construct()
    {
        // initialize the ZF Contexts (only once per php request)
        if (!self::$_isInitialized) {
            $contextRegistry = Context\Repository::getInstance();
            $contextRegistry->addContextsFromDirectory(
                dirname(__DIR__) . '/Context/Zf/', 'Zend\Tool\Project\Context\Zf\\'
            );
            $contextRegistry->addContextsFromDirectory(
                dirname(__DIR__) . '/Context/Filesystem/', 'Zend\Tool\Project\Context\Filesystem\\'
            );
            self::$_isInitialized = true;
        }

        // load up the extending providers required context classes
        if ($contextClasses = $this->getContextClasses()) {
            $this->_loadContextClassesIntoRegistry($contextClasses);
        }

    }

    public function getContextClasses()
    {
        return array();
    }

    /**
     * _getProject is designed to find if there is project file in the context of where
     * the client has been called from..   The search order is as follows..
     *    - traversing downwards from (PWD) - current working directory
     *    - if an enpoint variable has been registered in teh client registry - key=workingDirectory
     *    - if an ENV variable with the key ZFPROJECT_PATH is found
     *
     * @param $loadProfileFlag bool Whether or not to throw an exception when no profile is found
     * @param $projectDirectory string The project directory to use to search
     * @param $searchParentDirectories bool Whether or not to search upper level direcotries
     * @return \Zend\Tool\Project\Profile\Profile
     */
    protected function _loadProfile($loadProfileFlag = self::NO_PROFILE_THROW_EXCEPTION, $projectDirectory = null, $searchParentDirectories = true)
    {
        // use the cwd if no directory was provided
        if ($projectDirectory == null) {
            $projectDirectory = getcwd();
        } elseif (realpath($projectDirectory) == false) {
            throw new Exception\InvalidArgumentException('The $projectDirectory supplied does not exist.');
        }

        $profile = new ProjectProfile();

        $parentDirectoriesArray = explode(DIRECTORY_SEPARATOR, ltrim($projectDirectory, DIRECTORY_SEPARATOR));
        while ($parentDirectoriesArray) {
            $projectDirectoryAssembled = implode(DIRECTORY_SEPARATOR, $parentDirectoriesArray);

            if (DIRECTORY_SEPARATOR !== "\\") {
                $projectDirectoryAssembled = DIRECTORY_SEPARATOR . $projectDirectoryAssembled;
            }

            $profile->setAttribute('projectDirectory', $projectDirectoryAssembled);
            if ($profile->isLoadableFromFile()) {
                chdir($projectDirectoryAssembled);

                $profile->loadFromFile();
                $this->_loadedProfile = $profile;
                break;
            }

            // break after first run if we are not to check upper directories
            if ($searchParentDirectories == false) {
                break;
            }

            array_pop($parentDirectoriesArray);
        }

        if ($this->_loadedProfile == null) {
            if ($loadProfileFlag == self::NO_PROFILE_THROW_EXCEPTION) {
                throw new Exception\RuntimeException('A project profile was not found.');
            } elseif ($loadProfileFlag == self::NO_PROFILE_RETURN_FALSE) {
                return false;
            }
        }

        return $profile;
    }

    /**
     * Load the project profile from the current working directory, if not throw exception
     *
     * @return \Zend\Tool\Project\Profile\Profile
     */
    protected function _loadProfileRequired()
    {
        $profile = $this->_loadProfile();
        if ($profile === false) {
            throw new Exception\RuntimeException('A project profile was not found in the current working directory.');
        }
        return $profile;
    }

    /**
     * Return the currently loaded profile
     *
     * @return \Zend\Tool\Project\Profile\Profile
     */
    protected function _getProfile($loadProfileFlag = self::NO_PROFILE_THROW_EXCEPTION)
    {
        if (!$this->_loadedProfile) {
            if (($this->_loadProfile($loadProfileFlag) === false) && ($loadProfileFlag === self::NO_PROFILE_RETURN_FALSE)) {
                return false;
            }
        }

        return $this->_loadedProfile;
    }

    /**
     * _storeProfile()
     *
     * This method will store the profile into its proper location
     *
     */
    protected function _storeProfile()
    {
        $projectProfileFile = $this->_loadedProfile->search('ProjectProfileFile');

        $name = $projectProfileFile->getContext()->getPath();

        $this->_registry->getResponse()->appendContent('Updating project profile \'' . $name . '\'');

        $projectProfileFile->getContext()->save();
    }

    protected function _getContentForContext(Context $context, $methodName, $parameters)
    {
        $storage = $this->_registry->getStorage();
        if (!$storage->isEnabled()) {
            return false;
        }

        $engine = new Context\Content\Engine($storage);
        return $engine->getContent($context, $methodName, $parameters);
    }

    /**
     * _loadContextClassesIntoRegistry() - This is called by the constructor
     * so that child providers can provide a list of contexts to load into the
     * context repository
     *
     * @param array $contextClasses
     */
    private function _loadContextClassesIntoRegistry($contextClasses)
    {
        $registry = Context\Repository::getInstance();

        foreach ($contextClasses as $contextClass) {
            $registry->addContextClass($contextClass);
        }
    }
}
