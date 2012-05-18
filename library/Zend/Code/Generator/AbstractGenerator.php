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
 * @package    Zend_Code_Generator
 * @subpackage PHP
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Code\Generator;

use Traversable;

/**
 * @category   Zend
 * @package    Zend_Code_Generator
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractGenerator implements GeneratorInterface
{

    /**
     * Line feed to use in place of EOL
     *
     */
    const LINE_FEED = "\n";

    /**
     * @var bool
     */
    protected $isSourceDirty = true;

    /**
     * @var int|string 4 spaces by default
     */
    protected $indentation = '    ';

    /**
     * @var string
     */
    protected $sourceContent = null;

    /**
     * setSourceDirty()
     *
     * @param bool $isSourceDirty
     * @return AbstractGenerator
     */
    public function setSourceDirty($isSourceDirty = true)
    {
        $this->isSourceDirty = ($isSourceDirty) ? true : false;
        return $this;
    }

    /**
     * isSourceDirty()
     *
     * @return bool
     */
    public function isSourceDirty()
    {
        return $this->isSourceDirty;
    }

    /**
     * setIndentation()
     *
     * @param string|int $indentation
     * @return AbstractGenerator
     */
    public function setIndentation($indentation)
    {
        $this->indentation = $indentation;
        return $this;
    }

    /**
     * getIndentation()
     *
     * @return string|int
     */
    public function getIndentation()
    {
        return $this->indentation;
    }

    /**
     * setSourceContent()
     *
     * @param string $sourceContent
     * @return AbstractGenerator
     */
    public function setSourceContent($sourceContent)
    {
        $this->sourceContent = $sourceContent;
        return $this;
    }

    /**
     * getSourceContent()
     *
     * @return string
     */
    public function getSourceContent()
    {
        return $this->sourceContent;
    }

    /**
     * setOptions()
     *
     * @param array|Traversable $options
     * @throws Exception\InvalidArgumentException
     * @return self
     */
    public function setOptions($options)
    {
        if (!is_array($options) && !$options instanceof Traversable) {
            throw new Exception\InvalidArgumentException(
                sprintf(
                    '%s expects an array or Traversable object; received "%s"',
                    __METHOD__,
                    (is_object($options) ? get_class($options) : gettype($options))
                ));
        }

        foreach ($options as $optionName => $optionValue) {
            $methodName = 'set' . $optionName;
            if (method_exists($this, $methodName)) {
                $this->{$methodName}($optionValue);
            }
        }

        return $this;
    }
}
