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

use Zend\Tool\Project\Context\Context,
    Zend\Tool\Project\Context\Exception,
    Zend\Tool\Project\Profile\Resource\Resource,
    Zend\Code\Generator\FileGenerator,
    Zend\Code\Generator\ClassGenerator,
    Zend\Code\Generator\MethodGenerator;

   
/**
 * This class is the front most class for utilizing Zend\Tool\Project
 *
 * A profile is a hierarchical set of resources that keep track of
 * items within a specific project.
 *
 * @uses       \Zend\Code\Generator\ClassGenerator
 * @uses       \Zend\Reflection\ReflectionFile
 * @uses       \Zend\Tool\Project\Context\Exception
 * @uses       \Zend\Tool\Project\Context
 * @category   Zend
 * @package    Zend_Tool
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ActionMethod implements Context
{

    /**
     * @var \Zend\Tool\Project\Profile\Resource\Resource
     */
    protected $_resource = null;

    /**
     * @var \Zend\Tool\Project\Profile\Resource\Resource
     */
    protected $_controllerResource = null;

    /**
     * @var string
     */
    protected $_controllerPath = '';

    /**
     * @var string
     */
    protected $_actionName = null;

    /**
     * init()
     *
     * @return \Zend\Tool\Project\Context\Zf\ActionMethod
     */
    public function init()
    {
        $this->_actionName = $this->_resource->getAttribute('actionName');

        $this->_resource->setAppendable(false);
        $this->_controllerResource = $this->_resource->getParentResource();
        if (!$this->_controllerResource->getContext() instanceof ControllerFile) {
            throw new Exception\RuntimeException('ActionMethod must be a sub resource of a ControllerFile');
        }
        // make the ControllerFile node appendable so we can tack on the actionMethod.
        $this->_resource->getParentResource()->setAppendable(true);

        $this->_controllerPath = $this->_controllerResource->getContext()->getPath();

        return $this;
    }

    /**
     * getPersistentAttributes
     *
     * @return array
     */
    public function getPersistentAttributes()
    {
        return array(
            'actionName' => $this->getActionName()
            );
    }

    /**
     * getName()
     *
     * @return string
     */
    public function getName()
    {
        return 'ActionMethod';
    }

    /**
     * setResource()
     *
     * @param \Zend\Tool\Project\Profile\Resource\Resource $resource
     * @return \Zend\Tool\Project\Context\Zf\ActionMethod
     */
    public function setResource(Resource $resource)
    {
        $this->_resource = $resource;
        return $this;
    }

    /**
     * setActionName()
     *
     * @param string $actionName
     * @return \Zend\Tool\Project\Context\Zf\ActionMethod
     */
    public function setActionName($actionName)
    {
        $this->_actionName = $actionName;
        return $this;
    }

    /**
     * getActionName()
     *
     * @return string
     */
    public function getActionName()
    {
        return $this->_actionName;
    }

    /**
     * create()
     *
     * @return \Zend\Tool\Project\Context\Zf\ActionMethod
     */
    public function create()
    {
        if (self::createActionMethod($this->_controllerPath, $this->_actionName) === false) {
            throw new Exception\RuntimeException(
                'Could not create action within controller ' . $this->_controllerPath
                . ' with action name ' . $this->_actionName
                );
        }
        return $this;
    }

    /**
     * delete()
     *
     * @return \Zend\Tool\Project\Context\Zf\ActionMethod
     */
    public function delete()
    {
        // @todo do this
        return $this;
    }

    /**
     * createAcionMethod()
     *
     * @param string $controllerPath
     * @param string $actionName
     * @param string $body
     * @return true
     */
    public static function createActionMethod($controllerPath, $actionName, $body = '        // action body')
    {
        if (!file_exists($controllerPath)) {
            return false;
        }

        $controllerCodeGenFile = FileGenerator::fromReflectedFileName($controllerPath, true, true);
        $controllerCodeGenFile->setClass(new ClassGenerator(basename($controllerPath, '.php')));
        $controllerCodeGenFile->getClass()->setMethod(new MethodGenerator($actionName . 'Action', array(), MethodGenerator::FLAG_PUBLIC, $body));

        file_put_contents($controllerPath, $controllerCodeGenFile->generate());
        return true;
    }

    /**
     * hasActionMethod()
     *
     * @param string $controllerPath
     * @param string $actionName
     * @return bool
     */
    public static function hasActionMethod($controllerPath, $actionName)
    {
        if (!file_exists($controllerPath)) {
            return false;
        }

        $controllerCodeGenFile = FileGenerator::fromReflectedFileName($controllerPath, true, true);
        return $controllerCodeGenFile->getClass()->hasMethod($actionName . 'Action');
    }

}
