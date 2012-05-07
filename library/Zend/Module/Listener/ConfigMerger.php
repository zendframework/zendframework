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
 * Config merger interface
 * 
 * @category   Zend
 * @package    Zend_Module
 * @subpackage Listener
 */
interface ConfigMerger
{
    /**
     * getMergedConfig
     *
     * @param bool $returnConfigAsObject
     * @return mixed
     */
    public function getMergedConfig($returnConfigAsObject = true);

    /**
     * setMergedConfig
     *
     * @param array $config
     * @return ConfigMerger
     */
    public function setMergedConfig(array $config);
}
