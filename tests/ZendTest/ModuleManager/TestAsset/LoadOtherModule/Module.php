<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_ModuleManager
 */

namespace LoadOtherModule;


class Module
{
    public function init($moduleManager)
    {
        $moduleManager->loadModule('BarModule');
    }

    public function getConfig()
    {
        return array('loaded' => 'oh, yeah baby!');
    }
}
