<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Code\Generator\DocBlock\Tag;

use Zend\Code\Reflection\DocBlock\Tag\TagInterface as ReflectionTagInterface;

class ReturnTag extends AbstractTypeableTag implements TagInterface
{
    /**
     * @param  ReflectionTagInterface $reflectionTagReturn
     * @return ReturnTag
     * @deprecated Use TagManager::createTag() instead
     */
    public static function fromReflection(ReflectionTagInterface $reflectionTagReturn)
    {
        // @todo TagManager
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'return';
    }

    /**
     * @param string $datatype
     * @return ReturnTag
     * @deprecated Use setTypes() instead
     */
    public function setDatatype($datatype)
    {
        return $this->setTypes($datatype);
    }

    /**
     * @return string
     * @deprecated Use getTypes() instead
     */
    public function getDatatype()
    {
        return implode('|', $this->getTypes());
    }

    /**
     * @return string
     */
    public function generate()
    {
        $output = '@return '
        . implode('|', $this->types)
        . ((!empty($this->description)) ? ' ' . $this->description : '');

        return $output;
    }
}
