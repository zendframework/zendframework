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
 * @package    Zend_CodeGenerator
 * @subpackage PHP
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace Zend\CodeGenerator\Php;

/**
 * @uses       \Zend\CodeGenerator\Php\AbstractPhp
 * @uses       \Zend\CodeGenerator\Php\PhpDocblockTag
 * @uses       \Zend\CodeGenerator\Php\Exception
 * @category   Zend
 * @package    Zend_CodeGenerator
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class PhpDocblock extends AbstractPhp
{
    /**
     * @var string
     */
    protected $_shortDescription = null;

    /**
     * @var string
     */
    protected $_longDescription = null;

    /**
     * @var array
     */
    protected $_tags = array();

    /**
     * @var string
     */
    protected $_indentation = '';

    /**
     * fromReflection() - Build a docblock generator object from a reflection object
     *
     * @param Zend_Reflection_Docblock $reflectionDocblock
     * @return \Zend\CodeGenerator\PhpDocblock
     */
    public static function fromReflection(\Zend\Reflection\ReflectionDocblock $reflectionDocblock)
    {
        $docblock = new self();

        $docblock->setSourceContent($reflectionDocblock->getContents());
        $docblock->setSourceDirty(false);

        $docblock->setShortDescription($reflectionDocblock->getShortDescription());
        $docblock->setLongDescription($reflectionDocblock->getLongDescription());

        foreach ($reflectionDocblock->getTags() as $tag) {
            $docblock->setTag(PhpDocblockTag::fromReflection($tag));
        }

        return $docblock;
    }

    /**
     * setShortDescription()
     *
     * @param string $shortDescription
     * @return \Zend\CodeGenerator\PhpDocblock
     */
    public function setShortDescription($shortDescription)
    {
        $this->_shortDescription = $shortDescription;
        return $this;
    }

    /**
     * getShortDescription()
     *
     * @return string
     */
    public function getShortDescription()
    {
        return $this->_shortDescription;
    }

    /**
     * setLongDescription()
     *
     * @param string $longDescription
     * @return \Zend\CodeGenerator\PhpDocblock
     */
    public function setLongDescription($longDescription)
    {
        $this->_longDescription = $longDescription;
        return $this;
    }

    /**
     * getLongDescription()
     *
     * @return string
     */
    public function getLongDescription()
    {
        return $this->_longDescription;
    }

    /**
     * setTags()
     *
     * @param array $tags
     * @return \Zend\CodeGenerator\PhpDocblock
     */
    public function setTags(Array $tags)
    {
        foreach ($tags as $tag) {
            $this->setTag($tag);
        }

        return $this;
    }

    /**
     * setTag()
     *
     * @param array|\Zend\CodeGenerator\Php\PhpDocblockTag $tag
     * @return \Zend\CodeGenerator\PhpDocblock
     */
    public function setTag($tag)
    {
        if (is_array($tag)) {
            $tag = new PhpDocblockTag($tag);
        } elseif (!$tag instanceof PhpDocblockTag) {
            throw new Exception(
                'setTag() expects either an array of method options or an '
                . 'instance of Zend_CodeGenerator_Php_Docblock_Tag'
                );
        }

        $this->_tags[] = $tag;
        return $this;
    }

    /**
     * getTags
     *
     * @return array Array of \Zend\CodeGenerator\Php\PhpDocblockTag
     */
    public function getTags()
    {
        return $this->_tags;
    }

    /**
     * generate()
     *
     * @return string
     */
    public function generate()
    {
        if (!$this->isSourceDirty()) {
            return $this->_docCommentize($this->getSourceContent());
        }

        $output  = '';
        if (null !== ($sd = $this->getShortDescription())) {
            $output .= $sd . self::LINE_FEED . self::LINE_FEED;
        }
        if (null !== ($ld = $this->getLongDescription())) {
            $output .= $ld . self::LINE_FEED . self::LINE_FEED;
        }

        foreach ($this->getTags() as $tag) {
            $output .= $tag->generate() . self::LINE_FEED;
        }

        return $this->_docCommentize(trim($output));
    }

    /**
     * _docCommentize()
     *
     * @param string $content
     * @return string
     */
    protected function _docCommentize($content)
    {
        $indent = $this->getIndentation();
        $output = $indent . '/**' . self::LINE_FEED;
        $content = wordwrap($content, 80, self::LINE_FEED);
        $lines = explode(self::LINE_FEED, $content);
        foreach ($lines as $line) {
            $output .= $indent . ' * ' . $line . self::LINE_FEED;
        }
        $output .= $indent . ' */' . self::LINE_FEED;
        return $output;
    }

}
