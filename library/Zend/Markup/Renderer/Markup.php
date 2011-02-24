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
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Markup\Renderer;

use Zend\Markup\Token,
    Zend\Filter\Filter;

/**
 * Interface for a markup
 *
 * @uses       \Zend\Markup\Renderer\AbstractRenderer
 * @category   Zend
 * @package    Zend_Markup
 * @subpackage Renderer_Markup
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
interface Markup extends Filter
{

    /**
     * Set the encoding on this markup
     *
     * @param string $encoding
     *
     * @return \Zend\Markup\Renderer\Markup
     */
    public function setEncoding($encoding = 'UTF-8');

    /**
     * Set the renderer on this markup
     *
     * @param \Zend\Markup\Renderer\AbstractRenderer $renderer
     *
     * @return \Zend\Markup\Renderer\Markup
     */
    public function setRenderer(AbstractRenderer $renderer);

    /**
     * Invoke the markup
     *
     * @param \Zend\Markup\Token $token
     * @param string $text
     *
     * @return string
     */
    public function __invoke(Token $token, $text);
}
