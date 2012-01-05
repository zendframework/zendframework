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
 * @package    Zend_Application
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @category   Zend
 * @package    Zend_Application
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zf7696Bootstrap extends \Zend\Application\AbstractBootstrap
{
    public $barExecuted = 0;
    public $fooExecuted = 0;
    public $executedFooResource = false;
    public $executedFooBarResource = false;

    protected $_arbitraryValue;

    public function run()
    {
    }

    protected function _initFoo()
    {
        $this->fooExecuted++;
    }

    protected function _initBar()
    {
        $this->barExecuted++;
    }

    protected function _initBarbaz()
    {
        $o = new \stdClass();
        $o->baz = 'Baz';
        return $o;
    }
    
    protected function _initFrontController()
    {
        $front = \Zend\Controller\Front::getInstance();
        $front->addModuleDirectory(__DIR__ . '/modules');
        return $front;
    }

    public function setArbitrary($value)
    {
        $this->_arbitraryValue = $value;
        return $this;
    }

    public function getArbitrary()
    {
        return $this->_arbitraryValue;
    }
}
