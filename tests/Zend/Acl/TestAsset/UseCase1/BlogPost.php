<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Acl
 */

namespace ZendTest\Acl\TestAsset\UseCase1;

use Zend\Acl\Resource;

class BlogPost implements Resource\ResourceInterface
{
    public $owner = null;
    public function getResourceId()
    {
        return 'blogPost';
    }
}
