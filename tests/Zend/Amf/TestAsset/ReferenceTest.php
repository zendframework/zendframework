<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Amf
 */

namespace ZendTest\Amf\TestAsset;

/**
 * Used to test recursive cyclic references in the serializer.
 *
 * @category   Zend
 * @package    Zend_Amf
 * @subpackage UnitTests
 * @group      Zend_Amf
 * @group ZF-6205
 */
class ReferenceTest
{
    public function getReference()
    {
        $o = new TestObject();
        $o->recursive = new TestObject();
        $o->recursive->recursive = $o;
        return $o;
    }
}

