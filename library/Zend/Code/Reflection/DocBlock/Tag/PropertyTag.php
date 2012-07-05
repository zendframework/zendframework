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
 * @package    Zend_Reflection
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Code\Reflection\DocBlock\Tag;

/**
 * @category   Zend
 * @package    Zend_Reflection
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class PropertyTag implements TagInterface
{
    /**
     * @var string
     */
    protected $type = null;

    /**
     * @var string
     */
    protected $propertyName = null;

    /**
     * @var string
     */
    protected $description = null;

    /**
     * @return string
     */
    public function getName()
    {
        return 'property';
    }

    /**
     * Initializer
     *
     * @param string $tagDocBlockLine
     */
    public function initialize($tagDocblockLine)
    {
        if (preg_match('#^(.+)?(\$[\S]+)[\s]*(.*)$#m', $tagDocblockLine, $match)) {
            if ($match[1] !== '') {
                $this->type = rtrim($match[1]);
            }

            if ($match[2] !== '') {
                $this->propertyName = $match[2];
            }

            if ($match[3] !== '') {
                $this->description = $match[3];
            }
        }
    }

    /**
     * Get property variable type
     *
     * @return null|string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Get property name
     *
     * @return null|string
     */
    public function getPropertyName()
    {
        return $this->propertyName;
    }

    /**
     * Get property description
     *
     * @return null|string
     */
    public function getDescription()
    {
        return $this->description;
    }

    public function __toString()
    {
        return 'DocBlock Tag [ * @' . $this->getName() . ' ]' . PHP_EOL;
    }
}
