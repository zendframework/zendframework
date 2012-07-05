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
 * @subpackage Index
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Search\Lucene\Index\SegmentWriter;
use Zend\Search\Lucene\Storage\Directory;
use Zend\Search\Lucene\Index as LuceneIndex;

/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage Index
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class StreamWriter extends AbstractSegmentWriter
{
    /**
     * Object constructor.
     *
     * @param Directory\DirectoryInterface $directory
     * @param string $name
     */
    public function __construct(Directory\DirectoryInterface $directory, $name)
    {
        parent::__construct($directory, $name);
    }


    /**
     * Create stored fields files and open them for write
     */
    public function createStoredFieldsFiles()
    {
        $this->_fdxFile = $this->_directory->createFile($this->_name . '.fdx');
        $this->_fdtFile = $this->_directory->createFile($this->_name . '.fdt');

        $this->_files[] = $this->_name . '.fdx';
        $this->_files[] = $this->_name . '.fdt';
    }

    public function addNorm($fieldName, $normVector)
    {
        if (isset($this->_norms[$fieldName])) {
            $this->_norms[$fieldName] .= $normVector;
        } else {
            $this->_norms[$fieldName] = $normVector;
        }
    }

    /**
     * Close segment, write it to disk and return segment info
     *
     * @return \Zend\Search\Lucene\Index\SegmentInfo
     */
    public function close()
    {
        if ($this->_docCount == 0) {
            return null;
        }

        $this->_dumpFNM();
        $this->_generateCFS();

        return new LuceneIndex\SegmentInfo($this->_directory,
                                           $this->_name,
                                           $this->_docCount,
                                           -1,
                                           null,
                                           true,
                                           true);
    }
}

