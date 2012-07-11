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

use Zend\Markup;

/**
 * List item markup
 *
 * @category   Zend
 * @package    Zend_Markup
 * @subpackage Renderer_Markup_Html
 */
class ListItem extends AbstractHtml
{

    /**
     * Convert the token
     *
     * @param \Zend\Markup\Token $token
     * @param string $text
     *
     * @return string
     */
    public function __invoke(Markup\Token $token, $text)
    {
        $type = null;
        if ($token->hasAttribute('list')) {
            // because '01' == '1'
            if ($token->getAttribute('list') === '01') {
                $type = 'decimal-leading-zero';
            } else {
                switch ($token->getAttribute('list')) {
                    case '1':
                        $type = 'decimal';
                        break;
                    case 'i':
                        $type = 'lower-roman';
                        break;
                    case 'I':
                        $type = 'upper-roman';
                        break;
                    case 'a':
                        $type = 'lower-alpha';
                        break;
                    case 'A':
                        $type = 'upper-alpha';
                        break;

                    // the following type is unsupported by IE (including IE8)
                    case 'alpha':
                        $type = 'lower-greek';
                        break;

                    // the CSS names itself
                    case 'armenian': // unsupported by IE (including IE8)
                    case 'decimal':
                    case 'decimal-leading-zero': // unsupported by IE (including IE8)
                    case 'georgian': // unsupported by IE (including IE8)
                    case 'lower-alpha':
                    case 'lower-greek': // unsupported by IE (including IE8)
                    case 'lower-latin': // unsupported by IE (including IE8)
                    case 'lower-roman':
                    case 'upper-alpha':
                    case 'upper-latin': // unsupported by IE (including IE8)
                    case 'upper-roman':
                        $type = $token->getAttribute('list');
                        break;
                }
            }
        }

        if (null !== $type) {
            return "<ol style=\"list-style-type: {$type}\">{$text}</ol>";
        } else {
            return "<ul>{$text}</ul>";
        }
    }

}
