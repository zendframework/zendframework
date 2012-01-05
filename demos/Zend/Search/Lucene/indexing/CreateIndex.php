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
 * @subpackage Demos
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @see Zend_Search_Lucene
 */
require_once 'Zend/Search/Lucene.php';

/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage Demos
 * @uses       Zend_Search_Lucene_Document
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class FileDocument extends Zend_Search_Lucene_Document
{
    /**
     * Object constructor
     *
     * @param  string  $fileName
     * @param  boolean $storeContent
     * @throws Zend_Search_Lucene_Exception
     * @return void
     */
    public function __construct($fileName, $storeContent = false)
    {
        if (!file_exists($fileName)) {
            throw new Zend_Search_Lucene_Exception("File doesn't exists. Filename: '$fileName'");
        }
        $this->addField(Zend_Search_Lucene_Field::Text('path', $fileName));
        $this->addField(Zend_Search_Lucene_Field::Keyword( 'modified', filemtime($fileName) ));

        $f = fopen($fileName,'rb');
        $byteCount = filesize($fileName);

        $data = '';
        while ( $byteCount > 0 && ($nextBlock = fread($f, $byteCount)) != false ) {
            $data .= $nextBlock;
            $byteCount -= strlen($nextBlock);
        }
        fclose($f);

        if ($storeContent) {
            $this->addField(Zend_Search_Lucene_Field::Text('contents', $data, 'ISO8859-1'));
        } else {
            $this->addField(Zend_Search_Lucene_Field::UnStored('contents', $data, 'ISO8859-1'));
        }
    }
}


// Create index
$index = new Zend_Search_Lucene('index', true);
// Uncomment next line if you want to have case sensitive index
// ZSearchAnalyzer::setDefault(new ZSearchTextAnalyzer());

setlocale(LC_CTYPE, 'en_US');

$indexSourceDir = 'IndexSource';
$dir = opendir($indexSourceDir);
while (($file = readdir($dir)) !== false) {
    if (is_dir($indexSourceDir . '/' . $file)) {
        continue;
    }
    if (strcasecmp(substr($file, strlen($file)-5), '.html') != 0) {
        continue;
    }

    // Create new Document from a file
    $doc = new FileDocument($indexSourceDir . '/' . $file, true);
    // Add document to the index
    $index->addDocument($doc);

    echo $file . "...\n";
    flush();
}
closedir($dir);
