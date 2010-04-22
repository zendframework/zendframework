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
 * @package    Zend_Markup
 * @subpackage Renderer_Html
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace Zend\Markup\Renderer\HTML;

/**
 * Tag interface
 *
 * @uses       \Zend\Markup\Renderer\TokenConverterInterface
 * @category   Zend
 * @package    Zend_Markup
 * @subpackage Renderer_Html
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class HTMLAbstract implements \Zend\Markup\Renderer\TokenConverterInterface
{

    /**
     * The HTML renderer
     *
     * @var \Zend\Markup\Renderer\HTML
     */
    protected $_renderer;


    /**
     * Set the HTML renderer instance
     *
     * @param \Zend\Markup\Renderer\HTML $renderer
     *
     * @return \Zend\Markup\Renderer\HTML\HTMLAbstract
     */
    public function setRenderer( $renderer)
    {
        $this->_renderer = $renderer;
    }

    /**
     * Get the HTML renderer instance
     *
     * @return \Zend\Markup\Renderer\HTML
     */
    public function getRenderer()
    {
        return $this->_renderer;
    }
}
