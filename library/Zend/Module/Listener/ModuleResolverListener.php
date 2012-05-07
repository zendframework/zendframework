<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Module
 */
namespace Zend\Module\Listener;

/**
 * Module resolver listener
 * 
 * @category   Zend
 * @package    Zend_Module
 * @subpackage Listener
 */
class ModuleResolverListener extends AbstractListener
{
    /**
     * @param  $e
     * @return object
     */
    public function __invoke($e)
    {
        $moduleName = $e->getModuleName();
        $class = $moduleName . '\Module';
        if (!class_exists($class)) {
            return false;
        }
        $module = new $class;
        return $module;
    }
}
