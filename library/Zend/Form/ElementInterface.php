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
 * @package    Zend_Form
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Form;

/**
 * @category   Zend
 * @package    Zend_Form
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface ElementInterface
{
    /**
     * Set a single element attribute
     * 
     * @param  string $key 
     * @param  mixed $value 
     * @return ElementInterface
     */
    public function setAttribute($key, $value);

    /**
     * Retrieve a single element attribute
     * 
     * @param  string $optionalKey 
     * @return mixed
     */
    public function getAttribute($key);

    /**
     * Set many attributes at once
     *
     * Implementation will decide if this will overwrite or merge.
     * 
     * @param  array|\Traversable $arrayOrTraversable 
     * @return ElementInterface
     */
    public function setAttributes($arrayOrTraversable);

    /**
     * Retrieve all attributes at once
     * 
     * @return array|\Traversable
     */
    public function getAttributes();
}
