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
 * @subpackage Model
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\View;

/**
 * @category   Zend
 * @package    Zend_View
 * @subpackage Model
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface Model
{
    /**
     * Set renderer option/hint
     * 
     * @param  string $name 
     * @param  mixed $value 
     * @return Model
     */
    public function setOption($name, $value);

    /**
     * Set renderer options/hints en masse
     * 
     * @param  array|\Traversable $name 
     * @return Model
     */
    public function setOptions($options);

    /**
     * Get renderer options/hints
     * 
     * @return array|\Traversable
     */
    public function getOptions();
     
    /**
     * Set view variable
     * 
     * @param  string $name 
     * @param  mixed $value 
     * @return Model
     */
    public function setVariable($name, $value);

    /**
     * Set view variables en masse
     * 
     * @param  array|\ArrayAccess $variables 
     * @return Model
     */
    public function setVariables($variables);

    /**
     * Get view variables
     * 
     * @return array|\ArrayAccess
     */
    public function getVariables();
}
