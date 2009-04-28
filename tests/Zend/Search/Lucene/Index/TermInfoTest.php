<?php
/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage UnitTests
 */


/**
 * Zend_Search_Lucene_Index_TermInfo
 */
require_once 'Zend/Search/Lucene/Index/TermInfo.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage UnitTests
 */
class Zend_Search_Lucene_Index_TermInfoTest extends PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $termInfo = new Zend_Search_Lucene_Index_TermInfo(0, 1, 2, 3);
        $this->assertTrue($termInfo instanceof Zend_Search_Lucene_Index_TermInfo);

        $this->assertEquals($termInfo->docFreq,      0);
        $this->assertEquals($termInfo->freqPointer,  1);
        $this->assertEquals($termInfo->proxPointer,  2);
        $this->assertEquals($termInfo->skipOffset,   3);
        $this->assertEquals($termInfo->indexPointer, null);

        $termInfo = new Zend_Search_Lucene_Index_TermInfo(0, 1, 2, 3, 4);
        $this->assertEquals($termInfo->indexPointer, 4);
    }
}

