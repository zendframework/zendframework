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
 * to padraic dot brady at yahoo dot com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Feed_Writer
 * @copyright  Copyright (c) 2009 Padraic Brady
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @category   Zend
 * @package    Zend_Feed_Writer
 * @copyright  Copyright (c) 2009 Padraic Brady
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface Zend_Feed_Writer_Extension_RendererInterface
{

    public function __construct($container);
    
    public function setDomDocument(DOMDocument $dom, DOMElement $base);
    
    public function render();
    
    public function getDataContainer();

}
