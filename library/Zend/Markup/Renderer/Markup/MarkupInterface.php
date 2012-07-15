<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Markup
 */

namespace Zend\Markup\Renderer\Markup;

use Zend\Filter\FilterInterface;
use Zend\Markup\Renderer\AbstractRenderer;
use Zend\Markup\Token;

/**
 * Interface for a markup
 *
 * @category   Zend
 * @package    Zend_Markup
 * @subpackage Renderer_Markup
 */
interface MarkupInterface extends FilterInterface
{

    /**
     * Set the encoding on this markup
     *
     * @param string $encoding
     *
     * @return MarkupInterface
     */
    public function setEncoding($encoding = 'UTF-8');

    /**
     * Set the renderer on this markup
     *
     * @param AbstractRenderer $renderer
     *
     * @return MarkupInterface
     */
    public function setRenderer(AbstractRenderer $renderer);

    /**
     * Invoke the markup
     *
     * @param Token $token
     * @param string $text
     *
     * @return string
     */
    public function __invoke(Token $token, $text);
}
