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
 * @see Zend_Feed
 */
require_once 'Zend/Feed.php';

/**
 * @see Zend_Search_Lucene
 */
require_once 'Zend/Search/Lucene.php';

//create the index
$index = new Zend_Search_Lucene('/tmp/feeds_index', true);

// index each item
$rss = Zend_Feed::import('http://feeds.feedburner.com/ZendDeveloperZone');

foreach ($rss->items as $item) {
    $doc = new Zend_Search_Lucene_Document();

    if ($item->link && $item->title && $item->description) {

        $link = htmlentities(strip_tags( $item->link() ));
        $doc->addField(Zend_Search_Lucene_Field::UnIndexed('link', $link));

        $title = htmlentities(strip_tags( $item->title() ));
        $doc->addField(Zend_Search_Lucene_Field::Text('title', $title));

        $contents = htmlentities(strip_tags( $item->description() ));
        $doc->addField(Zend_Search_Lucene_Field::Text('contents', $contents));

        echo "Adding {$item->title()}...\n";
        $index->addDocument($doc);
    }
}

$index->commit();
