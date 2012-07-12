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

use Zend\Markup\Token;

/**
 * Simple replace markup
 *
 * @category   Zend
 * @package    Zend_Markup
 * @subpackage Renderer_Markup_Html
 */
class Replace extends AbstractMarkup
{

    /**
     * Markup's start replacement
     *
     * @var string
     */
    protected $_start;

    /**
     * Markup's end replacement
     *
     * @var string
     */
    protected $_end;


    /**
     * Constructor
     *
     * @param string $start
     * @param string $end
     */
    public function __construct($start, $end)
    {
        $this->_start = $start;
        $this->_end   = $end;
    }

    /**
     * Invoke the markup on the token
     *
     * @param \Zend\Markup\Token $token
     * @param string $text
     *
     * @return string
     */
    public function __invoke(Token $token, $text)
    {
        return $this->_start . $text . $this->_end;
    }
}
