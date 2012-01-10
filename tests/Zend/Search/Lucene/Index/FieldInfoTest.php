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
 * @package    Zend_Search_Lucene
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Search\Lucene\Index;
use Zend\Search\Lucene\Index;

/**
 * Zend_Search_Lucene_Index_FieldInfo
 */

/**
 * PHPUnit test case
 */

/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Search_Lucene
 */
class FieldInfoTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $fieldInfo = new Index\FieldInfo('field_name', true, 3, false);
        $this->assertTrue($fieldInfo instanceof Index\FieldInfo);

        $this->assertEquals($fieldInfo->name, 'field_name');
        $this->assertEquals($fieldInfo->isIndexed, true);
        $this->assertEquals($fieldInfo->number, 3);
        $this->assertEquals($fieldInfo->storeTermVector, false);
    }
}

