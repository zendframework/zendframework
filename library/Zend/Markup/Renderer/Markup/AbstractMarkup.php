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
 * @subpackage Renderer_Markup
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Markup\Renderer\Markup;

use Zend\Markup\Renderer\Markup,
    Zend\Markup\Renderer\AbstractRenderer;

/**
 * Abstract markup
 *
 * @uses       \Zend\Markup\Renderer\Markup
 * @uses       \Zend\Markup\Renderer\AbstractRenderer
 * @category   Zend
 * @package    Zend_Markup
 * @subpackage Renderer_Markup
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractMarkup implements Markup
{

    /**
     * The renderer
     *
     * @var \Zend\Markup\Renderer\AbstractRenderer
     */
    protected $_renderer;

    /**
     * Markup's encoding
     *
     * @var string
     */
    protected $_encoding;


    /**
     * Set the encoding on this markup
     *
     * @param string $encoding
     *
     * @return \Zend\Markup\Renderer\Markup\AbstractMarkup
     */
    public function setEncoding($encoding = 'UTF-8')
    {
        $this->_encoding = $encoding;

        return $this;
    }

    /**
     * Get this markup's encoding
     *
     * @return string
     */
    public function getEncoding()
    {
        return $this->_encoding;
    }

    /**
     * Set the renderer instance
     *
     * @param \Zend\Markup\Renderer\AbstractRenderer $renderer
     *
     * @return \Zend\Markup\Renderer\Markup\AbstractMarkup
     */
    public function setRenderer(AbstractRenderer $renderer)
    {
        $this->_renderer = $renderer;

        $this->setEncoding($renderer->getEncoding());

        return $this;
    }

    /**
     * Get the renderer instance
     *
     * @return \Zend\Markup\Renderer\AbstractRenderer
     */
    public function getRenderer()
    {
        return $this->_renderer;
    }
}
