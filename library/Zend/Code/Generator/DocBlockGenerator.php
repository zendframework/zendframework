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

use Zend\Code\Reflection\DocBlockReflection;

/**
 * @category   Zend
 * @package    Zend_Code_Generator
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class DocBlockGenerator extends AbstractGenerator
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
     * fromReflection() - Build a DocBlock generator object from a reflection object
     *
     * @param DocBlockReflection $reflectionDocBlock
     * @return DocBlockGenerator
     */
    public static function fromReflection(DocBlockReflection $reflectionDocBlock)
    {
        $docBlock = new static();

        $docBlock->setSourceContent($reflectionDocBlock->getContents());
        $docBlock->setSourceDirty(false);

        $docBlock->setShortDescription($reflectionDocBlock->getShortDescription());
        $docBlock->setLongDescription($reflectionDocBlock->getLongDescription());

        foreach ($reflectionDocBlock->getTags() as $tag) {
            $docBlock->setTag(DocBlock\Tag::fromReflection($tag));
        }

        return $docBlock;
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
     * @return DocBlockGenerator
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
     * @return DocBlockGenerator
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
     * @return DocBlockGenerator
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
     * @param array|DocBlock\Tag $tag
     * @throws Exception\InvalidArgumentException
     * @return DocBlockGenerator
     */
    public function setTag($tag)
    {
        if (is_array($tag)) {
            $tag = new DocBlock\Tag($tag);
        } elseif (!$tag instanceof DocBlock\Tag) {
            throw new Exception\InvalidArgumentException(
                'setTag() expects either an array of method options or an '
                    . 'instance of Zend\Code\Generator\DocBlock\Tag'
            );
        }

        $this->tags[] = $tag;

        return $this;
    }

    /**
     * getTags
     *
     * @return DocBlock\Tag[]
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

        $output = '';
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
        $indent  = $this->getIndentation();
        $output  = $indent . '/**' . self::LINE_FEED;
        $content = wordwrap($content, 80, self::LINE_FEED);
        $lines   = explode(self::LINE_FEED, $content);
        foreach ($lines as $line) {
            $output .= $indent . ' * ' . $line . self::LINE_FEED;
        }
        $output .= $indent . ' */' . self::LINE_FEED;
        return $output;
    }

}
