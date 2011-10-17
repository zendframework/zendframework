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

use Zend\Code\Generator\FileGeneratorRegistry,
    Zend\Code\Generator\MethodGenerator,
    Zend\Code\Generator\ClassGenerator,
    Zend\Code\Generator\FileGenerator;

/**
 * This class is the front most class for utilizing Zend\Tool\Project
 *
 * A profile is a hierarchical set of resources that keep track of
 * items within a specific project.
 *
 * @uses       \Zend\Code\Generator\ClassGenerator
 * @uses       \Zend\Code\Generator\FileGenerator
 * @uses       \Zend\Code\Generator\MethodGenerator
 * @category   Zend
 * @package    Zend_Tool
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ControllerFile extends \Zend\Tool\Project\Context\Filesystem\File
{

    /**
     * @var string
     */
    protected $_controllerName = 'index';

    /**
     * @var string
     */
    protected $_moduleName = null;
    
    /**
     * @var string
     */
    protected $_filesystemName = 'controllerName';

    /**
     * init()
     *
     */
    public function init()
    {
        $this->_controllerName = $this->_resource->getAttribute('controllerName');
        $this->_moduleName = $this->_resource->getAttribute('moduleName');
        $this->_filesystemName = ucfirst($this->_controllerName) . 'Controller.php';
        parent::init();
    }

    /**
     * getPersistentAttributes
     *
     * @return array
     */
    public function getPersistentAttributes()
    {
        return array(
            'controllerName' => $this->getControllerName()
            );
    }

    /**
     * getName()
     *
     * @return string
     */
    public function getName()
    {
        return 'ControllerFile';
    }

    /**
     * getControllerName()
     *
     * @return string
     */
    public function getControllerName()
    {
        return $this->_controllerName;
    }

    /**
     * getContents()
     *
     * @return string
     */
    public function getContents()
    {
        $className = ($this->_moduleName) ? ucfirst($this->_moduleName) . '\\' : '';
        $className .= ucfirst($this->_controllerName) . 'Controller';
        
        $codeGenFile = new FileGenerator();
        $codeGenFile->setFilename($this->getPath());
        $cg = new ClassGenerator($className);
        $cg->setMethod(new MethodGenerator('init', array(), null, '/* Initialize action controller here */'));
        $codeGenFile->setClass($cg);
        
        if ($className == 'ErrorController') {
        	
        	$codeGenFile = new FileGenerator();
            $codeGenFile->setFilename($this->getPath());
            $cg = new ClassGenerator($className);
            
            $cg->setMethods(array(new MethodGenerator('errorAction', array(), null, <<<'EOS'
$errors = $this->_getParam('error_handler');

if (!$errors || !$errors instanceof \ArrayObject) {
    $this->view->vars()->message = 'You have reached the error page';
    return;
}

switch ($errors->type) {
    case \Zend\Controller\Plugin\ErrorHandler::EXCEPTION_NO_ROUTE:
    case \Zend\Controller\Plugin\ErrorHandler::EXCEPTION_NO_CONTROLLER:
    case \Zend\Controller\Plugin\ErrorHandler::EXCEPTION_NO_ACTION:
        // 404 error -- controller or action not found
        $this->getResponse()->setHttpResponseCode(404);
        $priority = \Zend\Log\Logger::NOTICE;
        $this->view->vars()->message = 'Page not found';
        break;
    default:
        // application error
        $this->getResponse()->setHttpResponseCode(500);
        $priority = \Zend\Log\Logger::CRIT;
        $this->view->vars()->message = 'Application error';
        break;
}

// Log exception, if logger available
if (($log = $this->getLog())) {
    $log->log($this->view->vars()->message, $priority, $errors->exception);
    $log->log('Request Parameters', $priority, $errors->request->getParams());
}

// conditionally display exceptions
if ($this->getInvokeArg('displayExceptions') == true) {
    $this->view->vars()->exception = $errors->exception;
}

$this->view->vars()->request = $errors->request;
EOS
                    ), new MethodGenerator('getLog', array(), null, <<<'EOS'
/* @var $bootstrap Zend\Application\Bootstrap */
$bootstrap = $this->getInvokeArg('bootstrap');
if (!$bootstrap->getBroker()->hasPlugin('Log')) {
    return false;
}
$log = $bootstrap->getResource('Log');
return $log;
EOS
                    )
                )
            );
        }

        // store the generator into the registry so that the addAction command can use the same object later
        FileGeneratorRegistry::registerFileCodeGenerator($codeGenFile); // REQUIRES filename to be set
        return $codeGenFile->generate();
    }

    /**
     * addAction()
     *
     * @param string $actionName
     */
    public function addAction($actionName)
    {
        $classCodeGen = $this->getCodeGenerator();
        $classCodeGen->setMethod(new MethodGenerator($actionName . 'Action', array(), null, '        // action body here'));
        file_put_contents($this->getPath(), $classCodeGen->generate());
    }

    /**
     * getCodeGenerator()
     *
     * @return \Zend\Code\Generator\FileGenerator
     */
    public function getCodeGenerator()
    {
        $codeGenFile = FileGenerator::fromReflectedFileName($this->getPath());
        $codeGenFileClasses = $codeGenFile->getClasses();
        $class = array_shift($codeGenFileClasses);
        return $class;
    }

}
