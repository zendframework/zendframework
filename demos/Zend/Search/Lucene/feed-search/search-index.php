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

$index = new Zend_Search_Lucene('/tmp/feeds_index');
echo "Index contains {$index->count()} documents.\n";

$search = 'php';
$hits   = $index->find(strtolower($search));
echo "Search for \"$search\" returned " .count($hits). " hits.\n\n";

foreach ($hits as $hit) {
    echo str_repeat('-', 80) . "\n";
    echo 'ID:    ' . $hit->id                     ."\n";
    echo 'Score: ' . sprintf('%.2f', $hit->score) ."\n\n";

    foreach ($hit->getDocument()->getFieldNames() as $field) {
        echo "$field: \n";
        echo '    ' . trim(substr($hit->$field,0,76)) . "\n";
    }
}
