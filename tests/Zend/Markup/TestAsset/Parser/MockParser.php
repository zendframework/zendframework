<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Markup
 */

namespace ZendTest\Markup\TestAsset\Parser;

use Zend\Markup\Parser;

/**
 * @category   Zend
 * @package    Zend_Markup
 * @subpackage Parser
 */
class MockParser implements Parser\ParserInterface
{

    public function __construct($options = array())
    {
    }

    public function parse($value)
    {
    }

    public function buildTree(array $tokens, $strategy = 'default')
    {
    }

    public function tokenize($value)
    {
    }
}
