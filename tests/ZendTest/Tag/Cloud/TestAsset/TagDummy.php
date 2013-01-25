<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Tag
 */

namespace ZendTest\Tag\Cloud\TestAsset;

/**
 * @category   Zend
 * @package    Zend_Tag
 * @subpackage UnitTests
 */
class TagDummy extends \Zend\Tag\Cloud\Decorator\HtmlTag
{
    protected $_foo;

    public function setFoo($value)
    {
        $this->_foo = $value;
    }

    public function getFoo()
    {
        return $this->_foo;
    }
}
