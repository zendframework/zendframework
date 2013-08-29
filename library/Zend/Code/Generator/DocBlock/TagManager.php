<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Code\Generator\DocBlock;

use Zend\Code\Generator\DocBlock\Tag\TagInterface;
use Zend\Code\Generic\Prototype\PrototypeClassFactory;

use Zend\Code\Reflection\DocBlock\Tag\TagInterface as ReflectionTagInterface;

class TagManager extends PrototypeClassFactory
{
    /**
     * @return void
     */
    public function initializeDefaultTags()
    {
        $this->addPrototype(new Tag\ParamTag());
        $this->addPrototype(new Tag\ReturnTag());
        $this->addPrototype(new Tag\MethodTag());
        $this->addPrototype(new Tag\PropertyTag());
        $this->addPrototype(new Tag\AuthorTag());
        $this->addPrototype(new Tag\LicenseTag());
        $this->addPrototype(new Tag\ThrowsTag());
        $this->setGenericPrototype(new Tag\GenericTag());
    }

    /**
     * @param ReflectionTagInterface $reflectionTag
     * @return TagInterface
     */
    public function createTagFromReflection(ReflectionTagInterface $reflectionTag)
    {
        $tagName = $reflectionTag->getName();

        /* @var TagInterface $newTag */
        $newTag = $this->getPrototype($tagName);

        // transport any properties via accessors and mutators from reflection to codegen object
        $reflectionClass = new \ReflectionClass($reflectionTag);
        foreach ($reflectionClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            if (substr($method->getName(), 0, 3) == 'get') {
                $propertyName = substr($method->getName(), 3);
                if (method_exists($newTag, 'set' . $propertyName)) {
                    $newTag->{'set' . $propertyName}($reflectionTag->{'get' . $propertyName}());
                }
            }
        }
        return $newTag;
    }
}
