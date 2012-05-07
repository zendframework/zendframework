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
 * @package    Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\View\Helper;

use Zend\View\Model\ModelInterface as Model;

/**
 * Helper for storing and retrieving the root and current view model
 *
 * @package    Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ViewModel extends AbstractHelper
{
    /**
     * @var Model
     */
    protected $current;

    /**
     * @var Model
     */
    protected $root;

    /**
     * Get the root view model
     * 
     * @return null|Model
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * Is a root view model composed?
     * 
     * @return bool
     */
    public function hasRoot()
    {
        return ($this->root instanceof Model);
    }

    /**
     * Get the current view model
     * 
     * @return null|Model
     */
    public function getCurrent()
    {
        return $this->current;
    }

    /**
     * Is a current view model composed?
     * 
     * @return bool
     */
    public function hasCurrent()
    {
        return ($this->current instanceof Model);
    }

    /**
     * Set the root view model
     * 
     * @param  Model $model 
     * @return ViewModel
     */
    public function setRoot(Model $model)
    {
        $this->root = $model;
        return $this;
    }

    /**
     * Set the current view model
     * 
     * @param  Model $model 
     * @return ViewModel
     */
    public function setCurrent(Model $model)
    {
        $this->current = $model;
        return $this;
    }
}
