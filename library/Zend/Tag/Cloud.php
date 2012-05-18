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
 * @package    Zend_Tag
 * @subpackage Cloud
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Tag;

use Traversable;
use Zend\Stdlib\ArrayUtils;
use Zend\Tag\Exception\InvalidArgumentException,
    Zend\Loader\Broker;

/**
 * @category   Zend
 * @package    Zend_Tag
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Cloud
{
    /**
     * DecoratorInterface for the cloud
     *
     * @var Cloud
     */
    protected $_cloudDecorator = null;

    /**
     * DecoratorInterface for the tags
     *
     * @var Tag
     */
    protected $_tagDecorator = null;

    /**
     * List of all tags
     *
     * @var \Zend\Tag\ItemList
     */
    protected $_tags = null;

    /**
     * Plugin broker for decorators
     *
     * @var \Zend\Loader\Broker
     */
    protected $_decoratorBroker = null;

    /**
     * Option keys to skip when calling setOptions()
     *
     * @var array
     */
    protected $_skipOptions = array(
        'options',
        'config',
    );

    /**
     * Create a new tag cloud with options
     *
     * @param  array|Traversable $options
     */
    public function __construct($options = null)
    {
        if ($options instanceof Traversable) {
            $options = ArrayUtils::iteratorToArray($options);
        }
        if (is_array($options)) {
            $this->setOptions($options);
        }
    }

    /**
     * Set options from array
     *
     * @param  array $options Configuration for \Zend\Tag\Cloud
     * @return \Zend\Tag\Cloud
     */
    public function setOptions(array $options)
    {
        if (isset($options['prefixPath'])) {
            $this->addPrefixPaths($options['prefixPath']);
            unset($options['prefixPath']);
        }

        foreach ($options as $key => $value) {
            if (in_array(strtolower($key), $this->_skipOptions)) {
                continue;
            }

            $method = 'set' . ucfirst($key);
            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }

        return $this;
    }

    /**
     * Set the tags for the tag cloud.
     *
     * $tags should be an array containing single tags as array. Each tag
     * array should at least contain the keys 'title' and 'weight'. Optionally
     * you may supply the key 'url', to which the tag links to. Any additional
     * parameter in the array is silently ignored and can be used by custom
     * decorators.
     *
     * @param  array $tags
     * @throws \Zend\Tag\Exception\InvalidArgumentException
     * @return \Zend\Tag\Cloud
     */
    public function setTags(array $tags)
    {
        // Validate and cleanup the tags
        $itemList = $this->getItemList();

        foreach ($tags as $tag) {
            if ($tag instanceof TaggableInterface) {
                $itemList[] = $tag;
            } else if (is_array($tag)) {
                $itemList[] = new Item($tag);
            } else {
                throw new InvalidArgumentException('Tag must be an instance of Zend\Tag\TaggableInterface or an array');
            }
        }

        return $this;
    }

    /**
     * Append a single tag to the cloud
     *
     * @param  \Zend\Tag\TaggableInterface|array $tag
     * @throws \Zend\Tag\Exception\InvalidArgumentException
     * @return \Zend\Tag\Cloud
     */
    public function appendTag($tag)
    {
        $tags = $this->getItemList();
        if ($tag instanceof TaggableInterface) {
            $tags[] = $tag;
        } else if (is_array($tag)) {
            $tags[] = new Item($tag);
        } else {
            throw new InvalidArgumentException('Tag must be an instance of Zend\Tag\TaggableInterface or an array');
        }

        return $this;
    }

    /**
     * Set the item list
     *
     * @param  \Zend\Tag\ItemList $itemList
     * @return \Zend\Tag\Cloud
     */
    public function setItemList(ItemList $itemList)
    {
        $this->_tags = $itemList;
        return $this;
    }

    /**
     * Retrieve the item list
     *
     * If item list is undefined, creates one.
     *
     * @return \Zend\Tag\ItemList
     */
    public function getItemList()
    {
        if (null === $this->_tags) {
            $this->setItemList(new ItemList());
        }
        return $this->_tags;
    }

    /**
     * Set the decorator for the cloud
     *
     * @param  mixed $decorator
     * @throws \Zend\Tag\Exception\InvalidArgumentException
     * @return \Zend\Tag\Cloud
     */
    public function setCloudDecorator($decorator)
    {
        $options = null;

        if (is_array($decorator)) {
            if (isset($decorator['options'])) {
                $options = $decorator['options'];
            }

            if (isset($decorator['decorator'])) {
                $decorator = $decorator['decorator'];
            }
        }

        if (is_string($decorator)) {
            $decorator = $this->getDecoratorBroker()->load($decorator, $options);
        }

        if (!($decorator instanceof Cloud\Decorator\Cloud)) {
            throw new InvalidArgumentException('DecoratorInterface is no instance of Zend\Tag\Cloud\Decorator\Cloud');
        }

        $this->_cloudDecorator = $decorator;

        return $this;
    }

    /**
     * Get the decorator for the cloud
     *
     * @return Cloud
     */
    public function getCloudDecorator()
    {
        if (null === $this->_cloudDecorator) {
            $this->setCloudDecorator('htmlCloud');
        }
        return $this->_cloudDecorator;
    }

    /**
     * Set the decorator for the tags
     *
     * @param  mixed $decorator
     * @throws \Zend\Tag\Exception\InvalidArgumentException
     * @return \Zend\Tag\Cloud
     */
    public function setTagDecorator($decorator)
    {
        $options = null;

        if (is_array($decorator)) {
            if (isset($decorator['options'])) {
                $options = $decorator['options'];
            }

            if (isset($decorator['decorator'])) {
                $decorator = $decorator['decorator'];
            }
        }

        if (is_string($decorator)) {
            $decorator = $this->getDecoratorBroker()->load($decorator, $options);
        }

        if (!($decorator instanceof Cloud\Decorator\Tag)) {
            throw new InvalidArgumentException('DecoratorInterface is no instance of Zend\Tag\Cloud\Decorator\Tag');
        }

        $this->_tagDecorator = $decorator;

        return $this;
    }

    /**
     * Get the decorator for the tags
     *
     * @return Tag
     */
    public function getTagDecorator()
    {
        if (null === $this->_tagDecorator) {
            $this->setTagDecorator('htmlTag');
        }
        return $this->_tagDecorator;
    }

    /**
     * Set plugin broker for use with decorators
     *
     * @param  \Zend\Loader\Broker $broker
     * @return \Zend\Tag\Cloud
     */
    public function setDecoratorBroker(Broker $broker)
    {
        $this->_decoratorBroker = $broker;
        return $this;
    }

    /**
     * Get the plugin broker for decorators
     *
     * @return \Zend\Loader\Broker
     */
    public function getDecoratorBroker()
    {
        if ($this->_decoratorBroker === null) {
            $this->_decoratorBroker = new Cloud\DecoratorBroker();
        }

        return $this->_decoratorBroker;
    }

    /**
     * Render the tag cloud
     *
     * @return string
     */
    public function render()
    {
        $tags = $this->getItemList();

        if (count($tags) === 0) {
            return '';
        }

        $tagsResult  = $this->getTagDecorator()->render($tags);
        $cloudResult = $this->getCloudDecorator()->render($tagsResult);

        return $cloudResult;
    }

    /**
     * Render the tag cloud
     *
     * @return string
     */
    public function __toString()
    {
        try {
            $result = $this->render();
            return $result;
        } catch (\Exception $e) {
            $message = "Exception caught by tag cloud: " . $e->getMessage()
                     . "\nStack Trace:\n" . $e->getTraceAsString();
            trigger_error($message, E_USER_WARNING);
            return '';
        }
    }
}
