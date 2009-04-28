<?php
/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage UnitTests
 */


/**
 * Zend_Search_Lucene_Index_FieldInfo
 */
require_once 'Zend/Search/Lucene/Index/FieldInfo.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage UnitTests
 */
class Zend_Search_Lucene_Index_FieldInfoTest extends PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $fieldInfo = new Zend_Search_Lucene_Index_FieldInfo('field_name', true, 3, false);
        $this->assertTrue($fieldInfo instanceof Zend_Search_Lucene_Index_FieldInfo);

        $this->assertEquals($fieldInfo->name, 'field_name');
        $this->assertEquals($fieldInfo->isIndexed, true);
        $this->assertEquals($fieldInfo->number, 3);
        $this->assertEquals($fieldInfo->storeTermVector, false);
    }
}

