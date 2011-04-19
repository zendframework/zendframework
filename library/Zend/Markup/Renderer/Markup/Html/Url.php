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
 * @subpackage Renderer_Markup_Html
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Markup\Renderer\Markup\Html;
use Zend\Markup\Token;

/**
 * URL markup for HTML
 *
 * @uses       \Zend\Markup\Renderer\Html
 * @uses       \Zend\Markup\Renderer\Markup\Html\AbstractHtml
 * @uses       \Zend\Markup\Token
 * @category   Zend
 * @package    Zend_Markup
 * @subpackage Renderer_Markup_Html
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Url extends AbstractHtml
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
        if ($token->hasAttribute('url')) {
            $uri = $token->getAttribute('url');
        } else {
            $uri = $text;
        }

        if (!preg_match('/^([a-z][a-z+\-.]*):/i', $uri)) {
            $uri = 'http://' . $uri;
        }

        // check if the URL is valid
        // TODO: re-implement this (probably with the new \Zend\Uri)
        //if (!\Zend\Markup\Renderer\Html::isValidUri($uri)) {
        //   return $text;
        //}

        $attributes = $this->renderAttributes($token);

        // run the URI through htmlentities
        $uri = htmlentities($uri, ENT_QUOTES, $this->getEncoding());

        return "<a href=\"{$uri}\"{$attributes}>{$text}</a>";
    }
}
