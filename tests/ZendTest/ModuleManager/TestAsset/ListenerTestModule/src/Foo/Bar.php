<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_ModuleManager
 */

namespace Foo;

use ListenerTestModule\Module;
use Zend\ModuleManager\ModuleManager;

class Bar
{
    public $module;
    public $moduleManager;

    public function __construct(Module $module, ModuleManager $moduleManager)
    {
        $this->module        = $module;
        $this->moduleManager = $moduleManager;
    }
}
