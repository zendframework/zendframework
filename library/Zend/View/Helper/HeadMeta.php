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
 * @package    Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\View\Helper;

use Zend\View,
    Zend\View\Exception;

/**
 * Zend_Layout_View_Helper_HeadMeta
 *
 * @see        http://www.w3.org/TR/xhtml1/dtds.html
 * @uses       stdClass
 * @uses       \Zend\View\Exception
 * @uses       \Zend\View\Helper\Placeholder\Container\AbstractContainer
 * @uses       \Zend\View\Helper\Placeholder\Container\Standalone
 * @package    Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class HeadMeta extends Placeholder\Container\Standalone
{
    /**
     * Types of attributes
     * @var array
     */
    protected $_typeKeys     = array('name', 'http-equiv', 'charset');
    protected $_requiredKeys = array('content');
    protected $_modifierKeys = array('lang', 'scheme');

    /**
     * @var string registry key
     */
    protected $_regKey = 'Zend_View_Helper_HeadMeta';

    /**
     * Constructor
     *
     * Set separator to PHP_EOL
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->setSeparator(PHP_EOL);
    }

    /**
     * Retrieve object instance; optionally add meta tag
     *
     * @param  string $content
     * @param  string $keyValue
     * @param  string $keyType
     * @param  array $modifiers
     * @param  string $placement
     * @return \Zend\View\Helper\HeadMeta
     */
    public function __invoke($content = null, $keyValue = null, $keyType = 'name', $modifiers = array(), $placement = Placeholder\Container\AbstractContainer::APPEND)
    {
        if ((null !== $content) && (null !== $keyValue)) {
            $item   = $this->createData($keyType, $keyValue, $content, $modifiers);
            $action = strtolower($placement);
            switch ($action) {
                case 'append':
                case 'prepend':
                case 'set':
                    $this->$action($item);
                    break;
                default:
                    $this->append($item);
                    break;
            }
        }

        return $this;
    }

    /**
     * Normalize type attribut of meta
     *
     * @param $type type in CamelCase
     * @return string
     * @throws Exception\DomainException
     */
    protected function _normalizeType($type)
    {
        switch ($type) {
            case 'Name':
                return 'name';
            case 'HttpEquiv':
                return 'http-equiv';
            default:
                throw new Exception\DomainException(sprintf(
                    'Invalid type "%s" passed to _normalizeType',
                    $type
                ));
        }
    }

    /**
     * Overload method access
     *
     * Allows the following 'virtual' methods:
     * - appendName($keyValue, $content, $modifiers = array())
     * - offsetGetName($index, $keyValue, $content, $modifers = array())
     * - prependName($keyValue, $content, $modifiers = array())
     * - setName($keyValue, $content, $modifiers = array())
     * - appendHttpEquiv($keyValue, $content, $modifiers = array())
     * - offsetGetHttpEquiv($index, $keyValue, $content, $modifers = array())
     * - prependHttpEquiv($keyValue, $content, $modifiers = array())
     * - setHttpEquiv($keyValue, $content, $modifiers = array())
     *
     * @param  string $method
     * @param  array $args
     * @return \Zend\View\Helper\HeadMeta
     * @throws Exception\BadMethodCallException
     */
    public function __call($method, $args)
    {
        if (preg_match('/^(?P<action>set|(pre|ap)pend|offsetSet)(?P<type>Name|HttpEquiv)$/', $method, $matches)) {
            $action = $matches['action'];
            $type   = $this->_normalizeType($matches['type']);
            $argc   = count($args);
            $index  = null;

            if ('offsetSet' == $action) {
                if (0 < $argc) {
                    $index = array_shift($args);
                    --$argc;
                }
            }

            if (2 > $argc) {
                throw new Exception\BadMethodCallException(
                    'Too few arguments provided; requires key value, and content'
                );
            }

            if (3 > $argc) {
                $args[] = array();
            }

            $item  = $this->createData($type, $args[0], $args[1], $args[2]);

            if ('offsetSet' == $action) {
                return $this->offsetSet($index, $item);
            }

            $this->$action($item);
            return $this;
        }

        return parent::__call($method, $args);
    }

	/**
	 * Create an HTML5-style meta charset tag. Something like <meta charset="utf-8">
	 * 
	 * Not valid in a non-HTML5 doctype
	 *
	 * @param string $charset 
	 * @return \Zend\View\Helper\HeadMeta Provides a fluent interface
	 */
    public function setCharset($charset)
    {
        $item = new \stdClass;
        $item->type = 'charset';
        $item->charset = $charset;
        $item->content = null;
        $item->modifiers = array();
        $this->set($item);
        return $this;
    }

    /**
     * Determine if item is valid
     *
     * @param  mixed $item
     * @return boolean
     */
    protected function _isValid($item)
    {
        if ((!$item instanceof \stdClass)
            || !isset($item->type)
            || !isset($item->modifiers))
        {
            return false;
        }

        if (!isset($item->content)
        && (! $this->view->plugin('doctype')->isHtml5()
        || (! $this->view->plugin('doctype')->isHtml5() && $item->type !== 'charset'))) {
            return false;
        }

        return true;
    }

    /**
     * Append
     *
     * @param  string $value
     * @return void
     * @throws Exception\InvalidArgumentException
     */
    public function append($value)
    {
        if (!$this->_isValid($value)) {
            throw new Exception\InvalidArgumentException(
                'Invalid value passed to append; please use appendMeta()'
            );
        }

        return $this->getContainer()->append($value);
    }

    /**
     * OffsetSet
     *
     * @param  string|int $index
     * @param  string $value
     * @return void
     * @throws Exception\InvalidArgumentException
     */
    public function offsetSet($index, $value)
    {
        if (!$this->_isValid($value)) {
            throw  new Exception\InvalidArgumentException(
                'Invalid value passed to offsetSet; please use offsetSetName() or offsetSetHttpEquiv()'
            );
        }

        return $this->getContainer()->offsetSet($index, $value);
    }

    /**
     * OffsetUnset
     *
     * @param  string|int $index
     * @return void
     * @throws Exception\InvalidArgumentException
     */
    public function offsetUnset($index)
    {
        if (!in_array($index, $this->getContainer()->getKeys())) {
            throw new Exception\InvalidArgumentException('Invalid index passed to offsetUnset()');
        }

        return $this->getContainer()->offsetUnset($index);
    }

    /**
     * Prepend
     *
     * @param  string $value
     * @return void
     * @throws Exception\InvalidArgumentException
     */
    public function prepend($value)
    {
        if (!$this->_isValid($value)) {
            throw new Exception\InvalidArgumentException(
                'Invalid value passed to prepend; please use prependMeta()'
            );
        }

        return $this->getContainer()->prepend($value);
    }

    /**
     * Set
     *
     * @param  string $value
     * @return void
     * @throws Exception\InvalidArgumentException
     */
    public function set($value)
    {
        if (!$this->_isValid($value)) {
            throw new Exception\InvalidArgumentException('Invalid value passed to set; please use setMeta()');
        }

        $container = $this->getContainer();
        foreach ($container->getArrayCopy() as $index => $item) {
            if ($item->type == $value->type && $item->{$item->type} == $value->{$value->type}) {
                $this->offsetUnset($index);
            }
        }

        return $this->append($value);
    }

    /**
     * Build meta HTML string
     *
     * @param  string $type
     * @param  string $typeValue
     * @param  string $content
     * @param  array $modifiers
     * @return string
     * @throws Exception\InvalidArgumentException
     */
    public function itemToString(\stdClass $item)
    {
        if (!in_array($item->type, $this->_typeKeys)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Invalid type "%s" provided for meta',
                $item->type
            ));
        }
        $type = $item->type;

        $modifiersString = '';
        foreach ($item->modifiers as $key => $value) {
            if ($this->view->plugin('doctype')->isHtml5()
                && $key == 'scheme'
            ) {
                throw new Exception\InvalidArgumentException(
                    'Invalid modifier "scheme" provided; not supported by HTML5'
                );
            }
            if (!in_array($key, $this->_modifierKeys)) {
                continue;
            }
            $modifiersString .= $key . '="' . $this->_escape($value) . '" ';
        }

        if ($this->view instanceof \Zend\Loader\Pluggable) {
            if ($this->view->plugin('doctype')->isHtml5()
                && $type == 'charset'
            ) {
				$tpl = ($this->view->plugin('doctype')->isXhtml())
					? '<meta %s="%s"/>'
					: '<meta %s="%s">';
            } elseif ($this->view->plugin('doctype')->isXhtml()) {
                $tpl = '<meta %s="%s" content="%s" %s/>';
            } else {
                $tpl = '<meta %s="%s" content="%s" %s>';
            }
        } else {
            $tpl = '<meta %s="%s" content="%s" %s/>';
        }

        $meta = sprintf(
            $tpl,
            $type,
            $this->_escape($item->$type),
            $this->_escape($item->content),
            $modifiersString
        );

        if (isset($item->modifiers['conditional'])
            && !empty($item->modifiers['conditional'])
            && is_string($item->modifiers['conditional']))
        {
            $meta = '<!--[if ' . $this->_escape($item->modifiers['conditional']) . ']>' . $meta . '<![endif]-->';
        }

        return $meta;
    }

    /**
     * Render placeholder as string
     *
     * @param  string|int $indent
     * @return string
     */
    public function toString($indent = null)
    {
        $indent = (null !== $indent)
                ? $this->getWhitespace($indent)
                : $this->getIndent();

        $items = array();
        $this->getContainer()->ksort();
        try {
            foreach ($this as $item) {
                $items[] = $this->itemToString($item);
            }
        } catch (Exception\InvalidArgumentException $e) {
            trigger_error($e->getMessage(), E_USER_WARNING);
            return '';
        }
        return $indent . implode($this->_escape($this->getSeparator()) . $indent, $items);
    }

    /**
     * Create data item for inserting into stack
     *
     * @param  string $type
     * @param  string $typeValue
     * @param  string $content
     * @param  array $modifiers
     * @return stdClass
     */
    public function createData($type, $typeValue, $content, array $modifiers)
    {
        $data            = new \stdClass;
        $data->type      = $type;
        $data->$type     = $typeValue;
        $data->content   = $content;
        $data->modifiers = $modifiers;
        return $data;
    }
}
