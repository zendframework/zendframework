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
 * PHP sample code for the YouTube data API.  Utilizes the Zend Framework
 * Zend_Gdata component to communicate with the YouTube data API.
 *
 * Requires the Zend Framework Zend_Gdata component and PHP >= 5.1.4
 *
 * This sample is run from within a web browser.  These files are required:
 * index.php - the main logic, which interfaces with the YouTube API
 * interface.html - the HTML to represent the web UI
 * web_browser.css - the CSS to define the interface style
 * web_browser.js - the JavaScript used to provide the video list AJAX interface
 *
 * NOTE: If using in production, some additional precautions with regards
 * to filtering the input data should be used.  This code is designed only
 * for demonstration purposes.
 */

/**
 * @see Zend_Loader
 */
require_once 'Zend/Loader.php';

/**
 * @see Zend_Gdata_YouTube
 */
Zend_Loader::loadClass('Zend_Gdata_YouTube');

/**
 * Finds the URL for the flash representation of the specified video
 *
 * @param  Zend_Gdata_YouTube_VideoEntry $entry The video entry
 * @return string|null The URL or null, if the URL is not found
 */
function findFlashUrl($entry)
{
    foreach ($entry->mediaGroup->content as $content) {
        if ($content->type === 'application/x-shockwave-flash') {
            return $content->url;
        }
    }
    return null;
}

/**
 * Returns a feed of top rated videos for the specified user
 *
 * @param  string $user The username
 * @return Zend_Gdata_YouTube_VideoFeed The feed of top rated videos
 */
function getTopRatedVideosByUser($user)
{
    $userVideosUrl = 'http://gdata.youtube.com/feeds/users/' .
                     $user . '/uploads';
    $yt = new Zend_Gdata_YouTube();
    $ytQuery = $yt->newVideoQuery($userVideosUrl);
    // order by the rating of the videos
    $ytQuery->setOrderBy('rating');
    // retrieve a maximum of 5 videos
    $ytQuery->setMaxResults(5);
    // retrieve only embeddable videos
    $ytQuery->setFormat(5);
    return $yt->getVideoFeed($ytQuery);
}

/**
 * Returns a feed of videos related to the specified video
 *
 * @param  string $videoId The video
 * @return Zend_Gdata_YouTube_VideoFeed The feed of related videos
 */
function getRelatedVideos($videoId)
{
    $yt = new Zend_Gdata_YouTube();
    $ytQuery = $yt->newVideoQuery();
    // show videos related to the specified video
    $ytQuery->setFeedType('related', $videoId);
    // order videos by rating
    $ytQuery->setOrderBy('rating');
    // retrieve a maximum of 5 videos
    $ytQuery->setMaxResults(5);
    // retrieve only embeddable videos
    $ytQuery->setFormat(5);
    return $yt->getVideoFeed($ytQuery);
}

/**
 * Echo img tags for the first thumbnail representing each video in the
 * specified video feed.  Upon clicking the thumbnails, the video should
 * be presented.
 *
 * @param  Zend_Gdata_YouTube_VideoFeed $feed The video feed
 * @return void
 */
function echoThumbnails($feed)
{
    foreach ($feed as $entry) {
        $videoId = $entry->getVideoId();
        echo '<img src="' . $entry->mediaGroup->thumbnail[0]->url . '" ';
        echo 'width="80" height="72" onclick="ytvbp.presentVideo(\'' . $videoId . '\')">';
    }
}

/**
 * Echo the video embed code, related videos and videos owned by the same user
 * as the specified videoId.
 *
 * @param  string $videoId The video
 * @return void
 */
function echoVideoPlayer($videoId)
{
    $yt = new Zend_Gdata_YouTube();

    $entry = $yt->getVideoEntry($videoId);
    $videoTitle = $entry->mediaGroup->title;
    $videoUrl = findFlashUrl($entry);
    $relatedVideoFeed = getRelatedVideos($entry->getVideoId());
    $topRatedFeed = getTopRatedVideosByUser($entry->author[0]->name);

    print <<<END
    <b>$videoTitle</b><br />
    <object width="425" height="350">
      <param name="movie" value="${videoUrl}&autoplay=1"></param>
      <param name="wmode" value="transparent"></param>
      <embed src="${videoUrl}&autoplay=1" type="application/x-shockwave-flash" wmode="transparent"
        width=425" height="350"></embed>
    </object>
END;
    echo '<br />';
    echoVideoMetadata($entry);
    echo '<br /><b>Related:</b><br />';
    echoThumbnails($relatedVideoFeed);
    echo '<br /><b>Top rated videos by user:</b><br />';
    echoThumbnails($topRatedFeed);
}

/**
 * Echo video metadata
 *
 * @param  Zend_Gdata_YouTube_VideoEntry $entry The video entry
 * @return void
 */
function echoVideoMetadata($entry)
{
    $title = $entry->mediaGroup->title;
    $description = $entry->mediaGroup->description;
    $authorUsername = $entry->author[0]->name;
    $authorUrl = 'http://www.youtube.com/profile?user=' . $authorUsername;
    $tags = $entry->mediaGroup->keywords;
    $duration = $entry->mediaGroup->duration->seconds;
    $watchPage = $entry->mediaGroup->player[0]->url;
    $viewCount = $entry->statistics->viewCount;
    $rating = $entry->rating->average;
    $numRaters = $entry->rating->numRaters;
    $flashUrl = findFlashUrl($entry);
    print <<<END
    <b>Title:</b> ${title}<br />
    <b>Description:</b> ${description}<br />
    <b>Author:</b> <a href="${authorUrl}">${authorUsername}</a><br />
    <b>Tags:</b> ${tags}<br />
    <b>Duration:</b> ${duration} seconds<br />
    <b>View count:</b> ${viewCount}<br />
    <b>Rating:</b> ${rating} (${numRaters} ratings)<br />
    <b>Flash:</b> <a href="${flashUrl}">${flashUrl}</a><br />
    <b>Watch page:</b> <a href="${watchPage}">${watchPage}</a> <br />
END;
}

/**
 * Echo the list of videos in the specified feed.
 *
 * @param  Zend_Gdata_YouTube_VideoFeed $feed The video feed
 * @return void
 */
function echoVideoList($feed)
{
    echo '<table class="videoList">';
    echo '<tbody width="100%">';
    foreach ($feed as $entry) {
        $videoId = $entry->getVideoId();
        $thumbnailUrl = $entry->mediaGroup->thumbnail[0]->url;
        $videoTitle = $entry->mediaGroup->title;
        $videoDescription = $entry->mediaGroup->description;
        print <<<END
        <tr onclick="ytvbp.presentVideo('${videoId}')">
        <td width="130"><img src="${thumbnailUrl}" /></td>
        <td width="100%">
        <a href="#">${videoTitle}</a>
        <p class="videoDescription">${videoDescription}</p>
        </td>
        </tr>
END;
    }
    echo '</table>';
}

/*
 * The main controller logic of the YouTube video browser demonstration app.
 */
$queryType = isset($_POST['queryType']) ? $_POST['queryType'] : null;

if ($queryType === null) {
    /* display the entire interface */
    include 'interface.html';
} else if ($queryType == 'show_video') {
    /* display an individual video */
    if (array_key_exists('videoId', $_POST)) {
        $videoId = $_POST['videoId'];
        echoVideoPlayer($videoId);
    } else if (array_key_exists('videoId', $_GET)) {
        $videoId = $_GET['videoId'];
        echoVideoPlayer($videoId);
    } else {
        echo 'No videoId found.';
        exit;
    }
} else {
    /* display a list of videos */
    $searchTerm = $_POST['searchTerm'];
    $startIndex = $_POST['startIndex'];
    $maxResults = $_POST['maxResults'];

    $yt = new Zend_Gdata_YouTube();
    $query = $yt->newVideoQuery();
    $query->setQuery($searchTerm);
    $query->setStartIndex($startIndex);
    $query->setMaxResults($maxResults);

    /* check for one of the standard feeds, or list from 'all' videos */
    switch ($queryType) {
    case 'most_viewed':
        $query->setFeedType('most viewed');
        $query->setTime('this_week');
        $feed = $yt->getVideoFeed($query);
        break;
    case 'most_recent':
        $query->setFeedType('most recent');
        $feed = $yt->getVideoFeed($query);
        break;
    case 'recently_featured':
        $query->setFeedType('recently featured');
        $feed = $yt->getVideoFeed($query);
        break;
    case 'top_rated':
        $query->setFeedType('top rated');
        $query->setTime('this_week');
        $feed = $yt->getVideoFeed($query);
        break;
    case 'all':
        $feed = $yt->getVideoFeed($query);
        break;
    default:
        echo 'ERROR - unknown queryType - "' . $queryType . '"';
        break;
    }
    echoVideoList($feed);
}
