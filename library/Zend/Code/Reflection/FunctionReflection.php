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
     * @param  bool        $includeDocBlock
     * @return string|bool
     */
    public function getContents($includeDocBlock = true)
    {
        $fileName = $this->getFileName();
        if (false === $fileName) {
            throw new Exception\InvalidArgumentException(
                'Cannot determine internals functions contents'
            );
        }

        $startLine = $this->getStartLine();
        $endLine = $this->getEndLine();

        // eval'd protect
        if (preg_match('#\((\d+)\) : eval\(\)\'d code$#', $fileName, $matches)) {
            $fileName = preg_replace('#\(\d+\) : eval\(\)\'d code$#', '', $fileName);
            $startLine = $endLine = $matches[1];
        }

        $lines = array_slice(
            file($fileName, FILE_IGNORE_NEW_LINES),
            $startLine - 1,
            ($endLine - ($startLine - 1)),
            true
        );

        $functionLine = implode("\n", $lines);

        $content = false;
        if ($this->isClosure()) {
            preg_match('#function\s*\([^\)]*\)\s*(use\s*\([^\)]+\))?\s*\{(.*\;)?\s*\}#s', $functionLine, $matches);
            if (isset($matches[0])) {
                $content = $matches[0];
            }
        } else {
            $name = substr($this->getName(), strrpos($this->getName(), '\\')+1);
            preg_match('#function\s+' . preg_quote($name) . '\s*\([^\)]*\)\s*{([^{}]+({[^}]+})*[^}]+)?}#', $functionLine, $matches);
            if (isset($matches[0])) {
                $content = $matches[0];
            }
        }

        $docComment = $this->getDocComment();

        return $includeDocBlock && $docComment ? $docComment . "\n" . $content : $content;
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
     * @return string|bool
     */
    public function getBody()
    {
        $fileName = $this->getFileName();
        if (false === $fileName) {
            throw new Exception\InvalidArgumentException(
                'Cannot determine internals functions body'
            );
        }

        $startLine = $this->getStartLine();
        $endLine = $this->getEndLine();

        // eval'd protect
        if (preg_match('#\((\d+)\) : eval\(\)\'d code$#', $fileName, $matches)) {
            $fileName = preg_replace('#\(\d+\) : eval\(\)\'d code$#', '', $fileName);
            $startLine = $endLine = $matches[1];
        }

        $lines = array_slice(
            file($fileName, FILE_IGNORE_NEW_LINES),
            $startLine - 1,
            ($endLine - ($startLine - 1)),
            true
        );

        $functionLine = implode("\n", $lines);

        $body = false;
        if ($this->isClosure()) {
            preg_match('#function\s*\([^\)]*\)\s*(use\s*\([^\)]+\))?\s*\{(.*\;)\s*\}#s', $functionLine, $matches);
            if (isset($matches[2])) {
                $body = $matches[2];
            }
        } else {
            $name = substr($this->getName(), strrpos($this->getName(), '\\')+1);
            preg_match('#function\s+' . $name . '\s*\([^\)]*\)\s*{([^{}]+({[^}]+})*[^}]+)}#', $functionLine, $matches);
            if (isset($matches[1])) {
                $body = $matches[1];
            }
        }

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
