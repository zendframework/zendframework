<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Feed
 */

namespace Zend\Feed\Writer\Extension;

use DOMDocument;
use DOMElement;

/**
* @category Zend
* @package Zend_Feed_Writer
*/
interface Renderer
{
    /**
     * Constructor
     *
     * @param  mixed $container 
     * @return void
     */
    public function __construct($container);
    
    /**
     * Set DOMDocument and DOMElement on which to operate
     * 
     * @param  DOMDocument $dom 
     * @param  DOMElement $base 
     * @return void
     */
    public function setDomDocument(DOMDocument $dom, DOMElement $base);
    
    /**
     * Render
     * 
     * @return void
     */
    public function render();
    
    /**
     * Retrieve container
     * 
     * @return mixed
     */
    public function getDataContainer();
}
