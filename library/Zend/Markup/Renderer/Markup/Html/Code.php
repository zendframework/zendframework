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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace Zend\Markup\Renderer\Markup\Html;
use Zend\Markup;

/**
 * Code markup for HTML
 *
 * @uses       \Zend\Markup\Renderer\Markup\Html\AbstractHtml
 * @uses       \Zend\Markup\Token
 * @category   Zend
 * @package    Zend_Markup
 * @subpackage Renderer_Markup_Html
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Code extends AbstractHtml
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
        return highlight_string($text, true);
    }
}
