<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Code\Generator;

use Zend\Code\Generator\DocBlock\Tag as DockBlockTag;
use Zend\Code\Reflection\DocBlockReflection;

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
     * Build a DocBlock generator object from a reflection object
     *
     * @param  DocBlockReflection $reflectionDocBlock
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
            $docBlock->setTag(DockBlockTag::fromReflection($tag));
        }

        return $docBlock;
    }

    /**
     * Generate from array
     *
     * @configkey shortdescription string The short description for this doc block
     * @configkey longdescription  string The long description for this doc block
     * @configkey tags             array
     *
     * @throws Exception\InvalidArgumentException
     * @param  array $array
     * @return DocBlockGenerator
     */
    public static function fromArray(array $array)
    {
        $docBlock = new static();

        foreach ($array as $name => $value) {
            // normalize key
            switch (strtolower(str_replace(array('.', '-', '_'), '', $name))) {
                case 'shortdescription':
                    $docBlock->setShortDescription($value);
                case 'longdescription':
                    $docBlock->setLongDescription($value);
                    break;
                case 'tags':
                    $docBlock->setTags($value);
                    break;
            }
        }

        return $docBlock;
    }

    /**
     * @param  string $shortDescription
     * @param  string $longDescription
     * @param  array $tags
     */
    public function __construct($shortDescription = null, $longDescription = null, array $tags = array())
    {
        if ($shortDescription) {
            $this->setShortDescription($shortDescription);
        }
        if ($longDescription) {
            $this->setLongDescription($longDescription);
        }
        if (is_array($tags) && $tags) {
            $this->setTags($tags);
        }
    }

    /**
     * @param  string $shortDescription
     * @return DocBlockGenerator
     */
    public function setShortDescription($shortDescription)
    {
        $this->shortDescription = $shortDescription;

        return $this;
    }

    /**
     * @return string
     */
    public function getShortDescription()
    {
        return $this->shortDescription;
    }

    /**
     * @param  string $longDescription
     * @return DocBlockGenerator
     */
    public function setLongDescription($longDescription)
    {
        $this->longDescription = $longDescription;

        return $this;
    }

    /**
     * @return string
     */
    public function getLongDescription()
    {
        return $this->longDescription;
    }

    /**
     * @param  array $tags
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
     * @param  array|DockBlockTag $tag
     * @throws Exception\InvalidArgumentException
     * @return DocBlockGenerator
     */
    public function setTag($tag)
    {
        if (is_array($tag)) {
            $tag = new DockBlockTag($tag);
        } elseif (!$tag instanceof DockBlockTag) {
            throw new Exception\InvalidArgumentException(
                '%s expects either an array of method options or an instance of %s\DocBlock\Tag',
                __METHOD__,
                __NAMESPACE__
            );
        }

        $this->tags[] = $tag;

        return $this;
    }

    /**
     * @return DockBlockTag[]
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
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
     * @param  string $content
     * @return string
     */
    protected function docCommentize($content)
    {
        $indent  = $this->getIndentation();
        $output  = $indent . '/**' . self::LINE_FEED;
        $content = wordwrap($content, 80, self::LINE_FEED);
        $lines   = explode(self::LINE_FEED, $content);
        foreach ($lines as $line) {
            $output .= $indent . ' *';
            if ($line) {
                $output .= " $line";
            }
            $output .= self::LINE_FEED;
        }
        $output .= $indent . ' */' . self::LINE_FEED;

        return $output;
    }
}
