<?php
/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage UnitTests
 */


/**
 * Zend_Search_Lucene_Storage_Directory_Filesystem
 */
require_once 'Zend/Search/Lucene/Storage/Directory/Filesystem.php';

/**
 * Zend_Search_Lucene_Index_SegmentInfo
 */
require_once 'Zend/Search/Lucene/Index/SegmentInfo.php';


/**
 * Zend_Search_Lucene_Index_SegmentMerger
 */
require_once 'Zend/Search/Lucene/Index/SegmentMerger.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage UnitTests
 */
class Zend_Search_Lucene_Index_SegmentMergerTest extends PHPUnit_Framework_TestCase
{
    public function testMerge()
    {
        $segmentsDirectory = new Zend_Search_Lucene_Storage_Directory_Filesystem(dirname(__FILE__) . '/_source/_files');
        $outputDirectory   = new Zend_Search_Lucene_Storage_Directory_Filesystem(dirname(__FILE__) . '/_files');
        $segmentsList = array('_0', '_1', '_2', '_3', '_4');

        $segmentMerger = new Zend_Search_Lucene_Index_SegmentMerger($outputDirectory, 'mergedSegment');

        foreach ($segmentsList as $segmentName) {
            $segmentMerger->addSource(new Zend_Search_Lucene_Index_SegmentInfo($segmentsDirectory, $segmentName, 2));
        }

        $mergedSegment = $segmentMerger->merge();
        $this->assertTrue($mergedSegment instanceof Zend_Search_Lucene_Index_SegmentInfo);
        unset($mergedSegment);

        $mergedFile = $outputDirectory->getFileObject('mergedSegment.cfs');
        $mergedFileData = $mergedFile->readBytes($outputDirectory->fileLength('mergedSegment.cfs'));

        $sampleFile = $outputDirectory->getFileObject('mergedSegment.cfs.sample');
        $sampleFileData = $sampleFile->readBytes($outputDirectory->fileLength('mergedSegment.cfs.sample'));

        $this->assertEquals($mergedFileData, $sampleFileData);

        $outputDirectory->deleteFile('mergedSegment.cfs');
    }
}

