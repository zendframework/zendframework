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

namespace ZendTest\Search\Lucene\Index;

use Zend\Search\Lucene\Storage\Directory;
use Zend\Search\Lucene\Index;

/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Search_Lucene
 */
class SegmentMergerTest extends \PHPUnit_Framework_TestCase
{
    public function testMerge()
    {
        $segmentsDirectory = new Directory\Filesystem(__DIR__ . '/_source/_files');
        $outputDirectory   = new Directory\Filesystem(__DIR__ . '/_files');
        $segmentsList = array('_0', '_1', '_2', '_3', '_4');

        $segmentMerger = new Index\SegmentMerger($outputDirectory, 'mergedSegment');

        foreach ($segmentsList as $segmentName) {
            $segmentMerger->addSource(new Index\SegmentInfo($segmentsDirectory, $segmentName, 2));
        }

        $mergedSegment = $segmentMerger->merge();
        $this->assertTrue($mergedSegment instanceof Index\SegmentInfo);
        unset($mergedSegment);

        $mergedFile = $outputDirectory->getFileObject('mergedSegment.cfs');
        $mergedFileData = $mergedFile->readBytes($outputDirectory->fileLength('mergedSegment.cfs'));

        $sampleFile = $outputDirectory->getFileObject('mergedSegment.cfs.sample');
        $sampleFileData = $sampleFile->readBytes($outputDirectory->fileLength('mergedSegment.cfs.sample'));

        $this->assertEquals($mergedFileData, $sampleFileData);

        $outputDirectory->deleteFile('mergedSegment.cfs');
    }
}

