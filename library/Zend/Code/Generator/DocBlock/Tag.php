<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Code\Generator\DocBlock;

use ReflectionClass;
use ReflectionMethod;
use Zend\Code\Generator\DocBlock\Tag\GenericTag;
use Zend\Code\Reflection\DocBlock\Tag\TagInterface as ReflectionDocBlockTag;

/**
 * @deprecated Use GenericTag instead
 */
class Tag extends GenericTag
{

    /**
     * @param  ReflectionDocBlockTag $reflectionTag
     * @return Tag
     * @deprecated use TagManager::createTag() instead
     */
    public static function fromReflection(ReflectionDocBlockTag $reflectionTag)
    {
        $tagName = $reflectionTag->getName();

        $codeGenDocBlockTag = new static();
        $codeGenDocBlockTag->setName($tagName);

        // transport any properties via accessors and mutators from reflection to codegen object
        $reflectionClass = new ReflectionClass($reflectionTag);
        foreach ($reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if (substr($method->getName(), 0, 3) == 'get') {
                $propertyName = substr($method->getName(), 3);
                if (method_exists($codeGenDocBlockTag, 'set' . $propertyName)) {
                    $codeGenDocBlockTag->{'set' . $propertyName}($reflectionTag->{'get' . $propertyName}());
                }
            }
        }

        return $codeGenDocBlockTag;
    }

    /**
     * @param  string $description
     * @return Tag
     * @deprecated Use GenericTag::setContent() instead
     */
    public function setDescription($description)
    {
        return $this->setContent($description);
    }

    /**
     * @return string
     * @deprecated Use GenericTag::getContent() instead
     */
    public function getDescription()
    {
        return $this->getContent();
    }
}
