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
 * @package    Zend_Layout
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @category   Zend
 * @package    Zend_Layout
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Layout_Test_MinimalCustomView implements Zend_View_Interface
{

    public function getEngine() {}

    public function setScriptPath($path) {}

    public function getScriptPaths() {}

    public function setBasePath($path, $classPrefix = 'Zend_View') {}

    public function addBasePath($path, $classPrefix = 'Zend_View') {}

    public function __set($key, $val) {}

    public function __isset($key) {}

    public function __unset($key) {}

    public function assign($spec, $value = null) {}

    public function clearVars() {}

    public function render($name) {}

}
