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
 * @package    Zend_Gdata
 * @subpackage Demos
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @see Zend_Loader
 */
require_once 'Zend/Loader.php';

/**
 * @see Zend_Gdata_Books
 */
Zend_Loader::loadClass('Zend_Gdata_Books');

/**
 * Return a comma separated string representing the elements of an array
 *
 * @param Array $elements The array of elements
 * @return string Comma separated string
 */
function printArray($elements) {
    $result = '';
    foreach ($elements as $element) {
      if (!empty($result)) $result = $result.', ';
      $result = $result.$element;
    }
    return $result;
}


/**
 * Echo the list of videos in the specified feed.
 *
 * @param Zend_Gdata_Books_BookFeed $feed The video feed
 * @return void
 */
function echoBookList($feed)
{
    print <<<HTML
    <table><tr><td id="resultcell">
    <div id="searchResults">
        <table class="volumeList"><tbody width="100%">
HTML;
    $flipflop = false;
    foreach ($feed as $entry) {
        $title = printArray($entry->getTitles());
        $volumeId = $entry->getVolumeId();
        if ($thumbnailLink = $entry->getThumbnailLink()) {
            $thumbnail = $thumbnailLink->href;
        } else {
            $thumbnail = null;
        }
        $preview = $entry->getPreviewLink()->href;
        $embeddability = $entry->getEmbeddability()->getValue();
        $creators = printArray($entry->getCreators());
        if (!empty($creators)) $creators = "by " . $creators;
        if ($embeddability ==
            "http://schemas.google.com/books/2008#embeddable") {
            $preview_link = '<a href="javascript:load_viewport(\''.
                $preview.'\',\'viewport\');">'.
                '<img class="previewbutton" src="http://code.google.com/' .
                'apis/books/images/gbs_preview_button1.png" />' .
                '</a><br>';
        } else {
            $preview_link = '';
        }
        $thumbnail_img = (!$thumbnail) ? '' : '<a href="'.$preview.
            '"><img src="'.$thumbnail.'"/></a>';

        print <<<HTML
        <tr>
        <td><div class="thumbnail">
            $thumbnail_img
        </div></td>
        <td width="100%">
            <a href="${preview}">$title</a><br>
            $creators<br>
            $preview_link
        </td></tr>
HTML;
    }
    print <<<HTML
    </table></div></td>
        <td width=50% id="previewcell"><div id="viewport"></div>&nbsp;
    </td></tr></table><br></body></html>
HTML;
}

/*
 * The main controller logic of the Books volume browser demonstration app.
 */
$queryType = isset($_GET['queryType']) ? $_GET['queryType'] : null;

include 'interface.html';

if ($queryType === null) {
    /* display the entire interface */
} else {
    $books = new Zend_Gdata_Books();
    $query = $books->newVolumeQuery();

    /* display a list of volumes */
    if (isset($_GET['searchTerm'])) {
        $searchTerm = $_GET['searchTerm'];
        $query->setQuery($searchTerm);
    }
    if (isset($_GET['startIndex'])) {
        $startIndex = $_GET['startIndex'];
        $query->setStartIndex($startIndex);
    }
    if (isset($_GET['maxResults'])) {
        $maxResults = $_GET['maxResults'];
        $query->setMaxResults($maxResults);
    }
    if (isset($_GET['minViewability'])) {
        $minViewability = $_GET['minViewability'];
        $query->setMinViewability($minViewability);
    }

    /* check for one of the restricted feeds, or list from 'all' videos */
    switch ($queryType) {
    case 'full_view':
    case 'partial_view':
        $query->setMinViewability($queryType);
        echo 'Requesting feed: ' . ($query->getQueryUrl()) . '<br><br>';
        $feed = $books->getVolumeFeed($query);
        break;
    case 'all':
        echo 'Requesting feed: ' . ($query->getQueryUrl()) . '<br><br>';
        $feed = $books->getVolumeFeed($query);
        break;
    default:
        echo 'ERROR - unknown queryType - "' . $queryType . '"';
        break;
    }
    echoBookList($feed);
}

