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
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Zend_Search_Lucene_Index_SegmentInfo
 */
require_once 'Zend/Search/Lucene/Index/SegmentInfo.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Search_Lucene
 */
class Zend_Search_Lucene_Index_SegmentInfoTest extends PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $directory = new Zend_Search_Lucene_Storage_Directory_Filesystem(dirname(__FILE__) . '/_source/_files');

        $segmentInfo = new Zend_Search_Lucene_Index_SegmentInfo($directory, '_1', 2);

        $this->assertTrue($segmentInfo instanceof Zend_Search_Lucene_Index_SegmentInfo);
    }

    public function testOpenCompoundFile()
    {
        $directory = new Zend_Search_Lucene_Storage_Directory_Filesystem(dirname(__FILE__) . '/_source/_files');
        $segmentInfo = new Zend_Search_Lucene_Index_SegmentInfo($directory, '_1', 2);

        $file1 = $segmentInfo->openCompoundFile('.fnm');
        $this->assertTrue($file1 instanceof Zend_Search_Lucene_Storage_File);

        $file2 = $segmentInfo->openCompoundFile('.tii');
        $file3 = $segmentInfo->openCompoundFile('.tii');
        $file4 = $segmentInfo->openCompoundFile('.tii', false);

        $this->assertTrue($file2 instanceof Zend_Search_Lucene_Storage_File);
        $this->assertTrue($file2 === $file3);
        $this->assertTrue($file4 instanceof Zend_Search_Lucene_Storage_File);
        $this->assertTrue($file2 !== $file4);
    }


    public function testCompoundFileLength()
    {
        $directory = new Zend_Search_Lucene_Storage_Directory_Filesystem(dirname(__FILE__) . '/_source/_files');
        $segmentInfo = new Zend_Search_Lucene_Index_SegmentInfo($directory, '_1', 2);

        $this->assertEquals($segmentInfo->compoundFileLength('.tii'), 58);
    }

    public function testGetFieldNum()
    {
        $directory = new Zend_Search_Lucene_Storage_Directory_Filesystem(dirname(__FILE__) . '/_source/_files');
        $segmentInfo = new Zend_Search_Lucene_Index_SegmentInfo($directory, '_1', 2);

        $this->assertEquals($segmentInfo->getFieldNum('contents'), 2);
        $this->assertEquals($segmentInfo->getFieldNum('non-presented-field'), -1);
    }

    public function testGetField()
    {
        $directory = new Zend_Search_Lucene_Storage_Directory_Filesystem(dirname(__FILE__) . '/_source/_files');
        $segmentInfo = new Zend_Search_Lucene_Index_SegmentInfo($directory, '_1', 2);

        $fieldInfo = $segmentInfo->getField(2);

        $this->assertEquals($fieldInfo->name, 'contents');
        $this->assertTrue((boolean)$fieldInfo->isIndexed);
        $this->assertEquals($fieldInfo->number, 2);
        $this->assertFalse((boolean)$fieldInfo->storeTermVector);
    }

    public function testGetFields()
    {
        $directory = new Zend_Search_Lucene_Storage_Directory_Filesystem(dirname(__FILE__) . '/_source/_files');
        $segmentInfo = new Zend_Search_Lucene_Index_SegmentInfo($directory, '_1', 2);

        $this->assertTrue($segmentInfo->getFields() == array('path' => 'path', 'modified' => 'modified', 'contents' => 'contents'));
        $this->assertTrue($segmentInfo->getFields(true) == array('path' => 'path', 'modified' => 'modified', 'contents' => 'contents'));
    }

    public function testGetFieldInfos()
    {
        $directory = new Zend_Search_Lucene_Storage_Directory_Filesystem(dirname(__FILE__) . '/_source/_files');
        $segmentInfo = new Zend_Search_Lucene_Index_SegmentInfo($directory, '_1', 2);

        $fieldInfos = $segmentInfo->getFieldInfos();

        $this->assertEquals($fieldInfos[0]->name, 'path');
        $this->assertTrue((boolean)$fieldInfos[0]->isIndexed);
        $this->assertEquals($fieldInfos[0]->number, 0);
        $this->assertFalse((boolean)$fieldInfos[0]->storeTermVector);

        $this->assertEquals($fieldInfos[1]->name, 'modified');
        $this->assertTrue((boolean)$fieldInfos[1]->isIndexed);
        $this->assertEquals($fieldInfos[1]->number, 1);
        $this->assertFalse((boolean)$fieldInfos[1]->storeTermVector);

        $this->assertEquals($fieldInfos[2]->name, 'contents');
        $this->assertTrue((boolean)$fieldInfos[2]->isIndexed);
        $this->assertEquals($fieldInfos[2]->number, 2);
        $this->assertFalse((boolean)$fieldInfos[2]->storeTermVector);
    }

    public function testCount()
    {
        $directory = new Zend_Search_Lucene_Storage_Directory_Filesystem(dirname(__FILE__) . '/_source/_files');
        $segmentInfo = new Zend_Search_Lucene_Index_SegmentInfo($directory, '_1', 2);

        $this->assertEquals($segmentInfo->count(), 2);
    }

    public function testNumDocs()
    {
        $directory = new Zend_Search_Lucene_Storage_Directory_Filesystem(dirname(__FILE__) . '/_source/_files');
        $segmentInfo = new Zend_Search_Lucene_Index_SegmentInfo($directory, '_3', 2);

        $this->assertEquals($segmentInfo->count(), 2);
        $this->assertEquals($segmentInfo->numDocs(), 1);
    }

    public function testGetName()
    {
        $directory = new Zend_Search_Lucene_Storage_Directory_Filesystem(dirname(__FILE__) . '/_source/_files');
        $segmentInfo = new Zend_Search_Lucene_Index_SegmentInfo($directory, '_1', 2);

        $this->assertEquals($segmentInfo->getName(), '_1');
    }

    public function testGetTermInfo()
    {
        $directory = new Zend_Search_Lucene_Storage_Directory_Filesystem(dirname(__FILE__) . '/_source/_files');
        $segmentInfo = new Zend_Search_Lucene_Index_SegmentInfo($directory, '_1', 2);

        $termInfo = $segmentInfo->getTermInfo(new Zend_Search_Lucene_Index_Term('apart', 'contents'));

        $this->assertEquals($termInfo->docFreq, 1);
        $this->assertEquals($termInfo->freqPointer, 29);
        $this->assertEquals($termInfo->proxPointer, 119);
        $this->assertEquals($termInfo->skipOffset, 0);
        $this->assertEquals($termInfo->indexPointer, null);

        $termInfo1 = $segmentInfo->getTermInfo(new Zend_Search_Lucene_Index_Term('apart', 'contents'));
        // test for requesting cached information
        $this->assertTrue($termInfo === $termInfo1);

        // request for non-existing term
        $this->assertTrue($segmentInfo->getTermInfo(new Zend_Search_Lucene_Index_Term('nonusedterm', 'contents')) === null);
    }

    public function testTermFreqs()
    {
        $directory = new Zend_Search_Lucene_Storage_Directory_Filesystem(dirname(__FILE__) . '/_source/_files');
        $segmentInfo = new Zend_Search_Lucene_Index_SegmentInfo($directory, '_1', 2);

        $termPositions = $segmentInfo->termFreqs(new Zend_Search_Lucene_Index_Term('bgcolor', 'contents'));
        $this->assertTrue($termPositions == array(0 => 3, 1 => 1));

        $termPositions = $segmentInfo->termFreqs(new Zend_Search_Lucene_Index_Term('bgcolor', 'contents'), 10);
        $this->assertTrue($termPositions == array(10 => 3, 11 => 1));
    }

    public function testTermPositions()
    {
        $directory = new Zend_Search_Lucene_Storage_Directory_Filesystem(dirname(__FILE__) . '/_source/_files');
        $segmentInfo = new Zend_Search_Lucene_Index_SegmentInfo($directory, '_1', 2);

        $termPositions = $segmentInfo->termPositions(new Zend_Search_Lucene_Index_Term('bgcolor', 'contents'));
        $this->assertTrue($termPositions == array(0 => array(69, 239, 370),
                                                  1 => array(58)
                                                 ));

        $termPositions = $segmentInfo->termPositions(new Zend_Search_Lucene_Index_Term('bgcolor', 'contents'), 10);
        $this->assertTrue($termPositions == array(10 => array(69, 239, 370),
                                                  11 => array(58)
                                                 ));
    }

    public function testNorm()
    {
        $directory = new Zend_Search_Lucene_Storage_Directory_Filesystem(dirname(__FILE__) . '/_source/_files');
        $segmentInfo = new Zend_Search_Lucene_Index_SegmentInfo($directory, '_1', 2);

        $this->assertTrue(abs($segmentInfo->norm(1, 'contents') - 0.0546875) < 0.000001);
    }

    public function testNormVector()
    {
        $directory = new Zend_Search_Lucene_Storage_Directory_Filesystem(dirname(__FILE__) . '/_source/_files');
        $segmentInfo = new Zend_Search_Lucene_Index_SegmentInfo($directory, '_1', 2);

        $this->assertEquals($segmentInfo->normVector('contents'), "\x69\x6B");
    }

    public function testHasDeletions()
    {
        $directory = new Zend_Search_Lucene_Storage_Directory_Filesystem(dirname(__FILE__) . '/_source/_files');

        $segmentInfo = new Zend_Search_Lucene_Index_SegmentInfo($directory, '_1', 2);
        $this->assertFalse($segmentInfo->hasDeletions());

        $segmentInfo1 = new Zend_Search_Lucene_Index_SegmentInfo($directory, '_3', 2);
        $this->assertTrue($segmentInfo1->hasDeletions());
    }

    public function testDelete()
    {
        $directory = new Zend_Search_Lucene_Storage_Directory_Filesystem(dirname(__FILE__) . '/_source/_files');

        $segmentInfo = new Zend_Search_Lucene_Index_SegmentInfo($directory, '_1', 2, 0 /* search for _1.del file */);
        $this->assertFalse($segmentInfo->hasDeletions());

        $segmentInfo->delete(0);
        $this->assertTrue($segmentInfo->hasDeletions());
        $delGen = $segmentInfo->getDelGen();
        // don't write changes
        unset($segmentInfo);

        $segmentInfo1 = new Zend_Search_Lucene_Index_SegmentInfo($directory, '_1', 2, $delGen);
        // Changes wasn't written, segment still has no deletions
        $this->assertFalse($segmentInfo1->hasDeletions());

        $segmentInfo1->delete(0);
        $segmentInfo1->writeChanges();
        $delGen = $segmentInfo1->getDelGen();
        unset($segmentInfo1);

        $segmentInfo2 = new Zend_Search_Lucene_Index_SegmentInfo($directory, '_1', 2, $delGen);
        $this->assertTrue($segmentInfo2->hasDeletions());
        unset($segmentInfo2);

        $directory->deleteFile('_1_' . base_convert($delGen, 10, 36) . '.del');

        $segmentInfo3 = new Zend_Search_Lucene_Index_SegmentInfo($directory, '_1', 2, -1 /* no detetions file */);
        $this->assertFalse($segmentInfo3->hasDeletions());
    }

    public function testIsDeleted()
    {
        $directory = new Zend_Search_Lucene_Storage_Directory_Filesystem(dirname(__FILE__) . '/_source/_files');

        $segmentInfo = new Zend_Search_Lucene_Index_SegmentInfo($directory, '_2', 2);
        $this->assertFalse($segmentInfo->isDeleted(0));

        $segmentInfo1 = new Zend_Search_Lucene_Index_SegmentInfo($directory, '_3', 2);
        $this->assertTrue($segmentInfo1->isDeleted(0));
        $this->assertFalse($segmentInfo1->isDeleted(1));
    }

    public function testTermStreamStyleReading()
    {
        $directory = new Zend_Search_Lucene_Storage_Directory_Filesystem(dirname(__FILE__) . '/_source/_files');

        $segmentInfo = new Zend_Search_Lucene_Index_SegmentInfo($directory, '_3', 2);

        $this->assertEquals($segmentInfo->resetTermsStream(6, Zend_Search_Lucene_Index_SegmentInfo::SM_FULL_INFO), 8);

        $terms = array();

        $terms[] = $segmentInfo->currentTerm();
        $firstTermPositions = $segmentInfo->currentTermPositions();

        $this->assertEquals(count($firstTermPositions), 1);

        reset($firstTermPositions); // go to the first element
        $this->assertEquals(key($firstTermPositions), 7);

        $this->assertTrue(current($firstTermPositions) ==
                          array(105, 113, 130, 138, 153, 168, 171, 216, 243, 253, 258, 265, 302, 324,
                                331, 351, 359, 366, 370, 376, 402, 410, 418, 425, 433, 441, 460, 467));

        while (($term = $segmentInfo->nextTerm()) != null) {
            $terms[] = $term;
        }

        $this->assertTrue($terms ==
                          array(new Zend_Search_Lucene_Index_Term('a', 'contents'),
                                new Zend_Search_Lucene_Index_Term('about', 'contents'),
                                new Zend_Search_Lucene_Index_Term('accesskey', 'contents'),
                                new Zend_Search_Lucene_Index_Term('align', 'contents'),
                                new Zend_Search_Lucene_Index_Term('alink', 'contents'),
                                new Zend_Search_Lucene_Index_Term('already', 'contents'),
                                new Zend_Search_Lucene_Index_Term('and', 'contents'),
                                new Zend_Search_Lucene_Index_Term('are', 'contents'),
                                new Zend_Search_Lucene_Index_Term('at', 'contents'),
                                new Zend_Search_Lucene_Index_Term('b', 'contents'),
                                new Zend_Search_Lucene_Index_Term('be', 'contents'),
                                new Zend_Search_Lucene_Index_Term('been', 'contents'),
                                new Zend_Search_Lucene_Index_Term('bgcolor', 'contents'),
                                new Zend_Search_Lucene_Index_Term('body', 'contents'),
                                new Zend_Search_Lucene_Index_Term('border', 'contents'),
                                new Zend_Search_Lucene_Index_Term('bottom', 'contents'),
                                new Zend_Search_Lucene_Index_Term('bug', 'contents'),
                                new Zend_Search_Lucene_Index_Term('bugs', 'contents'),
                                new Zend_Search_Lucene_Index_Term('can', 'contents'),
                                new Zend_Search_Lucene_Index_Term('care', 'contents'),
                                new Zend_Search_Lucene_Index_Term('cellpadding', 'contents'),
                                new Zend_Search_Lucene_Index_Term('cellspacing', 'contents'),
                                new Zend_Search_Lucene_Index_Term('center', 'contents'),
                                new Zend_Search_Lucene_Index_Term('chapter', 'contents'),
                                new Zend_Search_Lucene_Index_Term('charset', 'contents'),
                                new Zend_Search_Lucene_Index_Term('check', 'contents'),
                                new Zend_Search_Lucene_Index_Term('class', 'contents'),
                                new Zend_Search_Lucene_Index_Term('click', 'contents'),
                                new Zend_Search_Lucene_Index_Term('colspan', 'contents'),
                                new Zend_Search_Lucene_Index_Term('contains', 'contents'),
                                new Zend_Search_Lucene_Index_Term('content', 'contents'),
                                new Zend_Search_Lucene_Index_Term('contributing', 'contents'),
                                new Zend_Search_Lucene_Index_Term('developers', 'contents'),
                                new Zend_Search_Lucene_Index_Term('div', 'contents'),
                                new Zend_Search_Lucene_Index_Term('docbook', 'contents'),
                                new Zend_Search_Lucene_Index_Term('documentation', 'contents'),
                                new Zend_Search_Lucene_Index_Term('does', 'contents'),
                                new Zend_Search_Lucene_Index_Term('don', 'contents'),
                                new Zend_Search_Lucene_Index_Term('double', 'contents'),
                                new Zend_Search_Lucene_Index_Term('easiest', 'contents'),
                                new Zend_Search_Lucene_Index_Term('equiv', 'contents'),
                                new Zend_Search_Lucene_Index_Term('existing', 'contents'),
                                new Zend_Search_Lucene_Index_Term('explanations', 'contents'),
                                new Zend_Search_Lucene_Index_Term('ff', 'contents'),
                                new Zend_Search_Lucene_Index_Term('ffffff', 'contents'),
                                new Zend_Search_Lucene_Index_Term('fill', 'contents'),
                                new Zend_Search_Lucene_Index_Term('find', 'contents'),
                                new Zend_Search_Lucene_Index_Term('fixed', 'contents'),
                                new Zend_Search_Lucene_Index_Term('footer', 'contents'),
                                new Zend_Search_Lucene_Index_Term('for', 'contents'),
                                new Zend_Search_Lucene_Index_Term('form', 'contents'),
                                new Zend_Search_Lucene_Index_Term('found', 'contents'),
                                new Zend_Search_Lucene_Index_Term('generator', 'contents'),
                                new Zend_Search_Lucene_Index_Term('guide', 'contents'),
                                new Zend_Search_Lucene_Index_Term('h', 'contents'),
                                new Zend_Search_Lucene_Index_Term('hasn', 'contents'),
                                new Zend_Search_Lucene_Index_Term('have', 'contents'),
                                new Zend_Search_Lucene_Index_Term('head', 'contents'),
                                new Zend_Search_Lucene_Index_Term('header', 'contents'),
                                new Zend_Search_Lucene_Index_Term('hesitate', 'contents'),
                                new Zend_Search_Lucene_Index_Term('home', 'contents'),
                                new Zend_Search_Lucene_Index_Term('homepage', 'contents'),
                                new Zend_Search_Lucene_Index_Term('how', 'contents'),
                                new Zend_Search_Lucene_Index_Term('hr', 'contents'),
                                new Zend_Search_Lucene_Index_Term('href', 'contents'),
                                new Zend_Search_Lucene_Index_Term('html', 'contents'),
                                new Zend_Search_Lucene_Index_Term('http', 'contents'),
                                new Zend_Search_Lucene_Index_Term('if', 'contents'),
                                new Zend_Search_Lucene_Index_Term('in', 'contents'),
                                new Zend_Search_Lucene_Index_Term('index', 'contents'),
                                new Zend_Search_Lucene_Index_Term('information', 'contents'),
                                new Zend_Search_Lucene_Index_Term('is', 'contents'),
                                new Zend_Search_Lucene_Index_Term('iso', 'contents'),
                                new Zend_Search_Lucene_Index_Term('it', 'contents'),
                                new Zend_Search_Lucene_Index_Term('latest', 'contents'),
                                new Zend_Search_Lucene_Index_Term('left', 'contents'),
                                new Zend_Search_Lucene_Index_Term('link', 'contents'),
                                new Zend_Search_Lucene_Index_Term('list', 'contents'),
                                new Zend_Search_Lucene_Index_Term('manual', 'contents'),
                                new Zend_Search_Lucene_Index_Term('meet', 'contents'),
                                new Zend_Search_Lucene_Index_Term('meta', 'contents'),
                                new Zend_Search_Lucene_Index_Term('modular', 'contents'),
                                new Zend_Search_Lucene_Index_Term('more', 'contents'),
                                new Zend_Search_Lucene_Index_Term('n', 'contents'),
                                new Zend_Search_Lucene_Index_Term('name', 'contents'),
                                new Zend_Search_Lucene_Index_Term('navfooter', 'contents'),
                                new Zend_Search_Lucene_Index_Term('navheader', 'contents'),
                                new Zend_Search_Lucene_Index_Term('navigation', 'contents'),
                                new Zend_Search_Lucene_Index_Term('net', 'contents'),
                                new Zend_Search_Lucene_Index_Term('new', 'contents'),
                                new Zend_Search_Lucene_Index_Term('newpackage', 'contents'),
                                new Zend_Search_Lucene_Index_Term('next', 'contents'),
                                new Zend_Search_Lucene_Index_Term('of', 'contents'),
                                new Zend_Search_Lucene_Index_Term('on', 'contents'),
                                new Zend_Search_Lucene_Index_Term('out', 'contents'),
                                new Zend_Search_Lucene_Index_Term('p', 'contents'),
                                new Zend_Search_Lucene_Index_Term('package', 'contents'),
                                new Zend_Search_Lucene_Index_Term('packages', 'contents'),
                                new Zend_Search_Lucene_Index_Term('page', 'contents'),
                                new Zend_Search_Lucene_Index_Term('patches', 'contents'),
                                new Zend_Search_Lucene_Index_Term('pear', 'contents'),
                                new Zend_Search_Lucene_Index_Term('persists', 'contents'),
                                new Zend_Search_Lucene_Index_Term('php', 'contents'),
                                new Zend_Search_Lucene_Index_Term('please', 'contents'),
                                new Zend_Search_Lucene_Index_Term('prev', 'contents'),
                                new Zend_Search_Lucene_Index_Term('previous', 'contents'),
                                new Zend_Search_Lucene_Index_Term('proper', 'contents'),
                                new Zend_Search_Lucene_Index_Term('quote', 'contents'),
                                new Zend_Search_Lucene_Index_Term('read', 'contents'),
                                new Zend_Search_Lucene_Index_Term('rel', 'contents'),
                                new Zend_Search_Lucene_Index_Term('report', 'contents'),
                                new Zend_Search_Lucene_Index_Term('reported', 'contents'),
                                new Zend_Search_Lucene_Index_Term('reporting', 'contents'),
                                new Zend_Search_Lucene_Index_Term('requirements', 'contents'),
                                new Zend_Search_Lucene_Index_Term('right', 'contents'),
                                new Zend_Search_Lucene_Index_Term('sect', 'contents'),
                                new Zend_Search_Lucene_Index_Term('span', 'contents'),
                                new Zend_Search_Lucene_Index_Term('still', 'contents'),
                                new Zend_Search_Lucene_Index_Term('stylesheet', 'contents'),
                                new Zend_Search_Lucene_Index_Term('submitting', 'contents'),
                                new Zend_Search_Lucene_Index_Term('summary', 'contents'),
                                new Zend_Search_Lucene_Index_Term('system', 'contents'),
                                new Zend_Search_Lucene_Index_Term('t', 'contents'),
                                new Zend_Search_Lucene_Index_Term('table', 'contents'),
                                new Zend_Search_Lucene_Index_Term('take', 'contents'),
                                new Zend_Search_Lucene_Index_Term('target', 'contents'),
                                new Zend_Search_Lucene_Index_Term('td', 'contents'),
                                new Zend_Search_Lucene_Index_Term('text', 'contents'),
                                new Zend_Search_Lucene_Index_Term('th', 'contents'),
                                new Zend_Search_Lucene_Index_Term('that', 'contents'),
                                new Zend_Search_Lucene_Index_Term('the', 'contents'),
                                new Zend_Search_Lucene_Index_Term('think', 'contents'),
                                new Zend_Search_Lucene_Index_Term('this', 'contents'),
                                new Zend_Search_Lucene_Index_Term('tips', 'contents'),
                                new Zend_Search_Lucene_Index_Term('title', 'contents'),
                                new Zend_Search_Lucene_Index_Term('to', 'contents'),
                                new Zend_Search_Lucene_Index_Term('top', 'contents'),
                                new Zend_Search_Lucene_Index_Term('tr', 'contents'),
                                new Zend_Search_Lucene_Index_Term('translating', 'contents'),
                                new Zend_Search_Lucene_Index_Term('type', 'contents'),
                                new Zend_Search_Lucene_Index_Term('u', 'contents'),
                                new Zend_Search_Lucene_Index_Term('unable', 'contents'),
                                new Zend_Search_Lucene_Index_Term('up', 'contents'),
                                new Zend_Search_Lucene_Index_Term('using', 'contents'),
                                new Zend_Search_Lucene_Index_Term('valign', 'contents'),
                                new Zend_Search_Lucene_Index_Term('version', 'contents'),
                                new Zend_Search_Lucene_Index_Term('vlink', 'contents'),
                                new Zend_Search_Lucene_Index_Term('way', 'contents'),
                                new Zend_Search_Lucene_Index_Term('which', 'contents'),
                                new Zend_Search_Lucene_Index_Term('width', 'contents'),
                                new Zend_Search_Lucene_Index_Term('will', 'contents'),
                                new Zend_Search_Lucene_Index_Term('with', 'contents'),
                                new Zend_Search_Lucene_Index_Term('writing', 'contents'),
                                new Zend_Search_Lucene_Index_Term('you', 'contents'),
                                new Zend_Search_Lucene_Index_Term('your', 'contents'),
                                new Zend_Search_Lucene_Index_Term('1178009946', 'modified'),
                                new Zend_Search_Lucene_Index_Term('bugs', 'path'),
                                new Zend_Search_Lucene_Index_Term('contributing', 'path'),
                                new Zend_Search_Lucene_Index_Term('html', 'path'),
                                new Zend_Search_Lucene_Index_Term('indexsource', 'path'),
                                new Zend_Search_Lucene_Index_Term('newpackage', 'path'),
                               ));

        unset($segmentInfo);


        $segmentInfo1 = new Zend_Search_Lucene_Index_SegmentInfo($directory, '_3', 2);
        $this->assertEquals($segmentInfo1->resetTermsStream(6, Zend_Search_Lucene_Index_SegmentInfo::SM_MERGE_INFO), 7);
    }

    public function testTermStreamStyleReadingSkipTo()
    {
        $directory = new Zend_Search_Lucene_Storage_Directory_Filesystem(dirname(__FILE__) . '/_source/_files');

        $segmentInfo = new Zend_Search_Lucene_Index_SegmentInfo($directory, '_3', 2);

        $this->assertEquals($segmentInfo->resetTermsStream(6, Zend_Search_Lucene_Index_SegmentInfo::SM_FULL_INFO), 8);

        $segmentInfo->skipTo(new Zend_Search_Lucene_Index_Term('prefetch', 'contents'));

        $terms = array();

        $terms[] = $segmentInfo->currentTerm();
        $firstTermPositions = $segmentInfo->currentTermPositions();

        $this->assertEquals(count($firstTermPositions), 1);

        reset($firstTermPositions); // go to the first element
        $this->assertEquals(key($firstTermPositions), 7);
        $this->assertTrue(current($firstTermPositions) == array(112, 409));

        while (($term = $segmentInfo->nextTerm()) != null) {
            $terms[] = $term;
        }

        $this->assertTrue($terms ==
                          array(new Zend_Search_Lucene_Index_Term('prev', 'contents'),
                                new Zend_Search_Lucene_Index_Term('previous', 'contents'),
                                new Zend_Search_Lucene_Index_Term('proper', 'contents'),
                                new Zend_Search_Lucene_Index_Term('quote', 'contents'),
                                new Zend_Search_Lucene_Index_Term('read', 'contents'),
                                new Zend_Search_Lucene_Index_Term('rel', 'contents'),
                                new Zend_Search_Lucene_Index_Term('report', 'contents'),
                                new Zend_Search_Lucene_Index_Term('reported', 'contents'),
                                new Zend_Search_Lucene_Index_Term('reporting', 'contents'),
                                new Zend_Search_Lucene_Index_Term('requirements', 'contents'),
                                new Zend_Search_Lucene_Index_Term('right', 'contents'),
                                new Zend_Search_Lucene_Index_Term('sect', 'contents'),
                                new Zend_Search_Lucene_Index_Term('span', 'contents'),
                                new Zend_Search_Lucene_Index_Term('still', 'contents'),
                                new Zend_Search_Lucene_Index_Term('stylesheet', 'contents'),
                                new Zend_Search_Lucene_Index_Term('submitting', 'contents'),
                                new Zend_Search_Lucene_Index_Term('summary', 'contents'),
                                new Zend_Search_Lucene_Index_Term('system', 'contents'),
                                new Zend_Search_Lucene_Index_Term('t', 'contents'),
                                new Zend_Search_Lucene_Index_Term('table', 'contents'),
                                new Zend_Search_Lucene_Index_Term('take', 'contents'),
                                new Zend_Search_Lucene_Index_Term('target', 'contents'),
                                new Zend_Search_Lucene_Index_Term('td', 'contents'),
                                new Zend_Search_Lucene_Index_Term('text', 'contents'),
                                new Zend_Search_Lucene_Index_Term('th', 'contents'),
                                new Zend_Search_Lucene_Index_Term('that', 'contents'),
                                new Zend_Search_Lucene_Index_Term('the', 'contents'),
                                new Zend_Search_Lucene_Index_Term('think', 'contents'),
                                new Zend_Search_Lucene_Index_Term('this', 'contents'),
                                new Zend_Search_Lucene_Index_Term('tips', 'contents'),
                                new Zend_Search_Lucene_Index_Term('title', 'contents'),
                                new Zend_Search_Lucene_Index_Term('to', 'contents'),
                                new Zend_Search_Lucene_Index_Term('top', 'contents'),
                                new Zend_Search_Lucene_Index_Term('tr', 'contents'),
                                new Zend_Search_Lucene_Index_Term('translating', 'contents'),
                                new Zend_Search_Lucene_Index_Term('type', 'contents'),
                                new Zend_Search_Lucene_Index_Term('u', 'contents'),
                                new Zend_Search_Lucene_Index_Term('unable', 'contents'),
                                new Zend_Search_Lucene_Index_Term('up', 'contents'),
                                new Zend_Search_Lucene_Index_Term('using', 'contents'),
                                new Zend_Search_Lucene_Index_Term('valign', 'contents'),
                                new Zend_Search_Lucene_Index_Term('version', 'contents'),
                                new Zend_Search_Lucene_Index_Term('vlink', 'contents'),
                                new Zend_Search_Lucene_Index_Term('way', 'contents'),
                                new Zend_Search_Lucene_Index_Term('which', 'contents'),
                                new Zend_Search_Lucene_Index_Term('width', 'contents'),
                                new Zend_Search_Lucene_Index_Term('will', 'contents'),
                                new Zend_Search_Lucene_Index_Term('with', 'contents'),
                                new Zend_Search_Lucene_Index_Term('writing', 'contents'),
                                new Zend_Search_Lucene_Index_Term('you', 'contents'),
                                new Zend_Search_Lucene_Index_Term('your', 'contents'),
                                new Zend_Search_Lucene_Index_Term('1178009946', 'modified'),
                                new Zend_Search_Lucene_Index_Term('bugs', 'path'),
                                new Zend_Search_Lucene_Index_Term('contributing', 'path'),
                                new Zend_Search_Lucene_Index_Term('html', 'path'),
                                new Zend_Search_Lucene_Index_Term('indexsource', 'path'),
                                new Zend_Search_Lucene_Index_Term('newpackage', 'path'),
                               ));

        unset($segmentInfo);


        $segmentInfo1 = new Zend_Search_Lucene_Index_SegmentInfo($directory, '_3', 2);
        $this->assertEquals($segmentInfo1->resetTermsStream(6, Zend_Search_Lucene_Index_SegmentInfo::SM_MERGE_INFO), 7);
    }
}

