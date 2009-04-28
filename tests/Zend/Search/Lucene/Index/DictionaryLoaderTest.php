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
 * Zend_Search_Lucene_Index_DictionaryLoader
 */
require_once 'Zend/Search/Lucene/Index/DictionaryLoader.php';


/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage UnitTests
 */
class Zend_Search_Lucene_Index_DictionaryLoaderTest extends PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $directory = new Zend_Search_Lucene_Storage_Directory_Filesystem(dirname(__FILE__) . '/_source/_files');

        $stiFile = $directory->getFileObject('_1.sti');
        $stiFileData = $stiFile->readBytes($directory->fileLength('_1.sti'));

        // Load dictionary index data
        list($termDictionary, $termDictionaryInfos) = unserialize($stiFileData);


        $segmentInfo = new Zend_Search_Lucene_Index_SegmentInfo($directory, '_1', 2);
        $tiiFile = $segmentInfo->openCompoundFile('.tii');
        $tiiFileData = $tiiFile->readBytes($segmentInfo->compoundFileLength('.tii'));

        // Load dictionary index data
        list($loadedTermDictionary, $loadedTermDictionaryInfos) =
                    Zend_Search_Lucene_Index_DictionaryLoader::load($tiiFileData);

        $this->assertTrue($termDictionary == $loadedTermDictionary);
        $this->assertTrue($termDictionaryInfos == $loadedTermDictionaryInfos);
    }
}

