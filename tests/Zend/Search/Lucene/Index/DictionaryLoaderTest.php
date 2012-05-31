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

/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Search_Lucene
 */
class DictionaryLoaderTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $directory = new \Zend\Search\Lucene\Storage\Directory\Filesystem(__DIR__ . '/_source/_files');

        $stiFile = $directory->getFileObject('_1.sti');
        $stiFileData = $stiFile->readBytes($directory->fileLength('_1.sti'));

        // Load dictionary index data
        list($termDictionary, $termDictionaryInfos) = unserialize($stiFileData);


        $segmentInfo = new \Zend\Search\Lucene\Index\SegmentInfo($directory, '_1', 2);
        $tiiFile = $segmentInfo->openCompoundFile('.tii');
        $tiiFileData = $tiiFile->readBytes($segmentInfo->compoundFileLength('.tii'));

        // Load dictionary index data
        list($loadedTermDictionary, $loadedTermDictionaryInfos) =
                    \Zend\Search\Lucene\Index\DictionaryLoader::load($tiiFileData);

        $this->assertTrue($termDictionary == $loadedTermDictionary);
        $this->assertTrue($termDictionaryInfos == $loadedTermDictionaryInfos);
    }
}

