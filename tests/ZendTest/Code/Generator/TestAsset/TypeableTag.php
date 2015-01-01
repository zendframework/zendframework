<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Code\Generator\TestAsset;

use Zend\Code\Generator\DocBlock\Tag\AbstractTypeableTag;
use Zend\Code\Generator\DocBlock\Tag\TagInterface;

class TypeableTag extends AbstractTypeableTag implements TagInterface
{
    public function generate()
    {
         return '';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'typable';
    }


}
