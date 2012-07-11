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

use Zend\Markup\Renderer\Markup;
use Zend\Markup\Token;

/**
 * Image markup for HTML
 *
 * @category   Zend
 * @package    Zend_Markup
 * @subpackage Renderer_Html
 */
class Img extends AbstractHtml
{

    /**
     * Convert the token
     *
     * @param \Zend\Markup\Token $token
     * @param string $text
     *
     * @return string
     */
    public function __invoke(Token $token, $text)
    {
        $uri = $text;

        if (!preg_match('/^([a-z][a-z+\-.]*):/i', $uri)) {
            $uri = 'http://' . $uri;
        }

        // check if the URL is valid
        // TODO: use \Zend\Uri for this
        if (!\Zend\Markup\Renderer\Html::isValidUri($uri)) {
            return $text;
        }

        if ($token->hasAttribute('alt')) {
            $alt = $token->getAttribute('alt');
        } else {
            // try to get the alternative from the URL
            $alt = rtrim($text, '/');
            $alt = strrchr($alt, '/');
            if (false !== strpos($alt, '.')) {
                $alt = substr($alt, 1, strpos($alt, '.') - 1);
            }
        }

        // run the URI and alt through htmlentities
        $uri = htmlentities($uri, ENT_QUOTES, $this->getEncoding());
        $alt = htmlentities($alt, ENT_QUOTES, $this->getEncoding());

        return "<img src=\"{$uri}\" alt=\"{$alt}\"" . $this->renderAttributes($token) . " />";
    }
}
