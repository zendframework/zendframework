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
namespace Zend\Tool\Project\Context\Zf;
use Zend\Tool\Project\Context\Exception;

/**
 * This class is the front most class for utilizing Zend\Tool\Project
 *
 * A profile is a hierarchical set of resources that keep track of
 * items within a specific project.
 *
 * @uses       \Zend\Application\Application
 * @uses       \Zend\Code\Generator\ClassGenerator
 * @uses       \Zend\Code\Generator\FileGenerator
 * @uses       \Zend\Tool\Project\Context\Filesystem\File
 * @uses       \Zend\Tool\Project\Exception
 * @category   Zend
 * @package    Zend_Tool
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class BootstrapFile extends \Zend\Tool\Project\Context\Filesystem\File
{

    /**
     * @var string
     */
    protected $_filesystemName = 'Bootstrap.php';

    /**
     * @var \Zend\Tool\Project\Profile\Resource\Resource
     */
    protected $_applicationConfigFile = null;
    
    /**
     * @var \Zend\Tool\Project\Profile\Resource\Resource
     */
    protected $_applicationDirectory = null;
    
    /**
     * @var \Zend\Application\Application
     */
    protected $_applicationInstance = null;


    /**
     * getName()
     *
     * @return string
     */
    public function getName()
    {
        return 'BootstrapFile';
    }

    public function init()
    {
        parent::init();

        $this->_applicationConfigFile = $this->_resource->getProfile()->search('ApplicationConfigFile');
        $this->_applicationDirectory = $this->_resource->getProfile()->search('ApplicationDirectory');

        if (($this->_applicationConfigFile === false) || ($this->_applicationDirectory === false)) {
            throw new Exception\RuntimeException('To use the BootstrapFile context, your project requires the use of both the "ApplicationConfigFile" and "ApplicationDirectory" contexts.');
        }


    }

    /**
     * getContents()
     *
     * @return array
     */
    public function getContents()
    {

        $codeGenFile = new \Zend\Code\Generator\FileGenerator(array(
            'classes' => array(
                new \Zend\Code\Generator\ClassGenerator('Bootstrap', null, null, '\Zend\Application\Bootstrap'),
            )
        ));

        return $codeGenFile->generate();
    }
    
    public function getApplicationInstance()
    {
        if ($this->_applicationInstance == null) {
            if ($this->_applicationConfigFile->getContext()->exists()) {
                define('APPLICATION_PATH', $this->_applicationDirectory->getPath());
                $applicationOptions = array();
                $applicationOptions['config'] = $this->_applicationConfigFile->getPath();
    
                $this->_applicationInstance = new \Zend\Application\Application(
                    'development',
                    $applicationOptions
                    );
            }
        }
        
        return $this->_applicationInstance;
    }
}
