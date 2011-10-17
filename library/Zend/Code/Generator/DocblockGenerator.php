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
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Code\Generator;

use Zend\Code\Reflection\DocBlockReflection;

/**
 * @uses       \Zend\Code\Generator\AbstractGenerator
 * @uses       \Zend\Code\Generator\Docblock\Tag
 * @uses       \Zend\Code\Generator\Exception
 * @category   Zend
 * @package    Zend_CodeGenerator
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class DocblockGenerator extends AbstractGenerator
{
    /**
     * @var string
     */
    protected $shortDescription = null;

    /**
     * @var string
     */
    protected $longDescription = null;

    /**
     * @var array
     */
    protected $tags = array();

    /**
     * @var string
     */
    protected $indentation = '';

    /**
     * fromReflection() - Build a docblock generator object from a reflection object
     *
     * @param ReflectionDocblock $reflectionDocblock
     * @return DocblockGenerator
     */
    public static function fromReflection(DocBlockReflection $reflectionDocblock)
    {
        $docblock = new self();

        $docblock->setSourceContent($reflectionDocblock->getContents());
        $docblock->setSourceDirty(false);

        $docblock->setShortDescription($reflectionDocblock->getShortDescription());
        $docblock->setLongDescription($reflectionDocblock->getLongDescription());

        foreach ($reflectionDocblock->getTags() as $tag) {
            $docblock->setTag(Docblock\Tag::fromReflection($tag));
        }

        return $docblock;
    }

    public function __construct($shortDescription = null, $longDescription = null, array $tags = array())
    {
        if ($shortDescription !== null) {
            $this->setShortDescription($shortDescription);
        }
        if ($longDescription !== null) {
            $this->setLongDescription($longDescription);
        }
        if ($this->tags !== array()) {
            $this->setTag($tags);
        }

    }

    /**
     * setShortDescription()
     *
     * @param string $shortDescription
     * @return DocblockGenerator
     */
    public function setShortDescription($shortDescription)
    {
        $this->shortDescription = $shortDescription;
        return $this;
    }

    /**
     * getShortDescription()
     *
     * @return string
     */
    public function getShortDescription()
    {
        return $this->shortDescription;
    }

    /**
     * setLongDescription()
     *
     * @param string $longDescription
     * @return \Zend\Code\GeneratorDocblock
     */
    public function setLongDescription($longDescription)
    {
        $this->longDescription = $longDescription;
        return $this;
    }

    /**
     * getLongDescription()
     *
     * @return string
     */
    public function getLongDescription()
    {
        return $this->longDescription;
    }

    /**
     * setTags()
     *
     * @param array $tags
     * @return \Zend\Code\GeneratorDocblock
     */
    public function setTags(array $tags)
    {
        foreach ($tags as $tag) {
            $this->setTag($tag);
        }

        return $this;
    }

    /**
     * setTag()
     *
     * @param array|\Zend\Code\Generator\Docblock\Tag $tag
     * @return \Zend\Code\GeneratorDocblock
     */
    public function setTag($tag)
    {
        if (is_array($tag)) {
            $tag = new Docblock\Tag($tag);
        } elseif (!$tag instanceof Docblock\Tag) {
            throw new Exception\InvalidArgumentException(
                'setTag() expects either an array of method options or an '
                . 'instance of Zend\\Code\\Generator\\Docblock\\Tag'
                );
        }

        $this->tags[] = $tag;
        return $this;
    }

    /**
     * getTags
     *
     * @return Docblock\Tag[]
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * generate()
     *
     * @return string
     */
    public function generate()
    {
        if (!$this->isSourceDirty()) {
            return $this->docCommentize($this->getSourceContent());
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

        return $this->docCommentize(trim($output));
    }

    /**
     * docCommentize()
     *
     * @param string $content
     * @return string
     */
    protected function docCommentize($content)
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
