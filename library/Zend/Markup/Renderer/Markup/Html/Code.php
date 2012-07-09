<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Markup
 */

namespace Zend\Markup\Renderer\Markup\Html;

use Zend\Markup\Token;

/**
 * Code markup for HTML
 *
 * @category   Zend
 * @package    Zend_Markup
 * @subpackage Renderer_Markup_Html
 */
class Code extends AbstractHtml
{

    /**
     * Constructor
     *
     * Since we don't want any filters on this markup (since they will collide
     * with PHP's highlight_string() function), we simply override the
     * constructor.
     *
     */
    public function __construct()
    {
    }

    /**
     * Convert the token
     *
     * @param Token $token
     * @param string $text
     *
     * @return string
     */
    public function __invoke(Token $token, $text)
    {
        return highlight_string($text, true);
    }
}
