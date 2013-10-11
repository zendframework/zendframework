<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Code\Reflection;

use ReflectionFunction;
use Zend\Code\Reflection\DocBlock\Tag\ReturnTag;

class FunctionReflection extends ReflectionFunction implements ReflectionInterface
{
    /**
     * Get function DocBlock
     *
     * @throws Exception\InvalidArgumentException
     * @return DocBlockReflection
     */
    public function getDocBlock()
    {
        if ('' == ($comment = $this->getDocComment())) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s does not have a DocBlock',
                $this->getName()
            ));
        }

        $instance = new DocBlockReflection($comment);

        return $instance;
    }

    /**
     * Get start line (position) of function
     *
     * @param  bool $includeDocComment
     * @return int
     */
    public function getStartLine($includeDocComment = false)
    {
        if ($includeDocComment) {
            if ($this->getDocComment() != '') {
                return $this->getDocBlock()->getStartLine();
            }
        }

        return parent::getStartLine();
    }

    /**
     * Get contents of function
     *
     * @param  bool   $includeDocBlock
     * @return string
     */
    public function getContents($includeDocBlock = true)
    {
        $fileName = $this->getFileName();
        if (false === $fileName) {
            throw new Exception\InvalidArgumentException(
                'Cannot determine internals functions contents'
            );
        }
        return implode("\n",
            array_splice(
                file($fileName),
                $this->getStartLine($includeDocBlock),
                ($this->getEndLine() - $this->getStartLine()),
                true
            )
        );
    }

    /**
     * Get function parameters
     *
     * @return ParameterReflection[]
     */
    public function getParameters()
    {
        $phpReflections  = parent::getParameters();
        $zendReflections = array();
        while ($phpReflections && ($phpReflection = array_shift($phpReflections))) {
            $instance          = new ParameterReflection($this->getName(), $phpReflection->getName());
            $zendReflections[] = $instance;
            unset($phpReflection);
        }
        unset($phpReflections);

        return $zendReflections;
    }

    /**
     * Get return type tag
     *
     * @throws Exception\InvalidArgumentException
     * @return ReturnTag
     */
    public function getReturn()
    {
        $docBlock = $this->getDocBlock();
        if (!$docBlock->hasTag('return')) {
            throw new Exception\InvalidArgumentException(
                'Function does not specify an @return annotation tag; cannot determine return type'
            );
        }

        $tag    = $docBlock->getTag('return');

        return new DocBlockReflection('@return ' . $tag->getDescription());
    }

    /**
     * Get method body
     *
     * @return string
     */
    public function getBody()
    {
        $fileName = $this->getFileName();
        if (false === $fileName) {
            throw new Exception\InvalidArgumentException(
                'Cannot determine internals functions body'
            );
        }

        $lines = array_slice(
            file($fileName, FILE_IGNORE_NEW_LINES),
            $this->getStartLine() - 1,
            ($this->getEndLine() - ($this->getStartLine() - 1)),
            true
        );

        $functionLine = implode("\n", $lines);
        if ($this->isClosure()) {
            preg_match('#function\s*\([^\)]*\)\s*\{(.*\;)\s*\}#s', $functionLine, $matches);
        } else {
            preg_match('#^\s*function\s*[^\(]+\([^\)]*\)\s*\{(.*)\}\s*$#s', $functionLine, $matches);
        }

        if (!isset($matches[1])) {
            return false;
        }

        $body = $matches[1];

        return $body;
    }

    public function toString()
    {
        return $this->__toString();
    }

    /**
     * Required due to bug in php
     *
     * @return string
     */
    public function __toString()
    {
        return parent::__toString();
    }
}
