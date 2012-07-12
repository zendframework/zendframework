<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Markup
 */

namespace ZendTest\Markup\TestAsset\Renderer\Html;

use Zend\Markup\Renderer\AbstractRenderer;

/**
 * Tag interface
 *
 * @category   Zend
 * @package    Zend_Markup
 * @subpackage Renderer_Html
 */
class Bar implements \Zend\Markup\Renderer\Markup\MarkupInterface
{
    public function setEncoding($encoding = 'UTF-8')
    {
    }

    public function setRenderer(AbstractRenderer $renderer)
    {
    }

    /**
     * Convert the token
     *
     * @param Zend_Markup_Token $token
     * @param string $text
     *
     * @return string
     */
    public function __invoke(\Zend\Markup\Token $token, $text)
    {
        $bar = $token->getAttribute('bar');

        if (!empty($bar)) {
            $bar = '=' . $bar;
        }

        return "[foo{$bar}]" . $text . '[/foo]';
    }

    /**
     * Returns the result of filtering $value
     *
     * @param  mixed $value
     * @throws Zend\Filter\Exception\RuntimeException If filtering $value is impossible
     * @return mixed
     */
    public function filter($value)
    {
        // TODO: Implement filter() method.
    }
}
