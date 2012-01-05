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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * PHP sample code for the YouTube data API.  Utilizes the Zend Framework
 * Zend_Gdata component to communicate with the YouTube data API.
 *
 * Requires the Zend Framework Zend_Gdata component and PHP >= 5.1.4
 * This sample is run from within a web browser.  These files are required:
 * session_details.php - a script to view log output and session variables
 * operations.php - the main logic, which interfaces with the YouTube API
 * index.php - the HTML to represent the web UI, contains some PHP
 * video_app.css - the CSS to define the interface style
 * video_app.js - the JavaScript used to provide the video list AJAX interface
 *
 * NOTE: If using in production, some additional precautions with regards
 * to filtering the input data should be used.  This code is designed only
 * for demonstration purposes.
 */
session_start();

/**
 * Set your developer key here.
 *
 * NOTE: In a production application you may want to store this information in
 * an external file.
 */
$_SESSION['developerKey'] = '<YOUR DEVELOPER KEY>';

/**
 * Convert HTTP status into normal text.
 *
 * @param number $status HTTP status received after posting syndicated upload
 * @param string $code Alphanumeric description of error
 * @param string $videoId (optional) Video id received back to which the status
 *        code refers to
 */
function uploadStatus($status, $code = null, $videoId = null)
{
    switch ($status) {
        case $status < 400:
            echo  'Success ! Entry created (id: '. $videoId .
                  ') <a href="#" onclick=" ytVideoApp.checkUploadDetails(\''.
                  $videoId .'\'); ">(check details)</a>';
            break;
        default:
            echo 'There seems to have been an error: '. $code .
                 '<a href="#" onclick=" ytVideoApp.checkUploadDetails(\''.
                 $videoId . '\'); ">(check details)</a>';
    }
}

/**
 * Helper function to check whether a session token has been set
 *
 * @return boolean Returns true if a session token has been set
 */
function authenticated()
{
    if (isset($_SESSION['sessionToken'])) {
        return true;
    }
}

/**
 * Helper function to print a list of authenticated actions for a user.
 */
function printAuthenticatedActions()
{
    print <<<END
        <div id="actions"><h3>Authenticated Actions</h3>
        <ul>
        <li><a href="#" onclick="ytVideoApp.listVideos('search_owner', '', 1);
        return false;">retrieve my videos</a></li>
        <li><a href="#" onclick="ytVideoApp.prepareUploadForm();
        return false;">upload a video</a><br />
        <div id="syndicatedUploadDiv"></div><div id="syndicatedUploadStatusDiv">
        </div></li>
        <li><a href="#" onclick="ytVideoApp.retrievePlaylists();
        return false;">manage my playlists</a><br /></li>
        </ul></div>
END;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
  <title>YouTube data API Video Browser in PHP</title>
  <link href="video_app.css" type="text/css" rel="stylesheet" />
  <script src="video_app.js" type="text/javascript"></script>
</head>

<body>
  <div id="main">
    <div id="titleBar">
      <h2>YouTube data API Video App in PHP</h2>
        <a href="session_details.php">click to examine session variables</a><br/>
        <div id="searchBox">
        <form id="searchForm" onsubmit="ytVideoApp.listVideos(this.queryType.value, this.searchTerm.value, 1); return false;" action="javascript:void();" >
        <div id="searchBoxTop"><select name="queryType" onchange="ytVideoApp.queryTypeChanged(this.value, this.form.searchTerm);" >
          <option value="search_all" selected="selected">All Videos</option>
          <option value="search_top_rated">Top Rated Videos</option>
          <option value="search_most_viewed">Most Viewed Videos</option>
          <option value="search_recently_featured">Recently Featured Videos</option>
          <option value="search_username">Videos from a specific user</option>
          <?php
                if (authenticated()) {
                    echo '<option value="search_owner">Display my videos</option>';
                }
          ?>
        </select></div>
        <div><input name="searchTerm" type="text" value="YouTube Data API" />
        <input type="submit" value="Search" /></div>
      </form>
    </div>
    <br />

    </div>
    <br />
    <!-- Authentication status -->
    <div id="authStatus">Authentication status:
    <?php
      if (authenticated()) {
          print <<<END
              authenticated <br />
END;
      } else {
          print <<<END
                    <div id="generateAuthSubLink"><a href="#"
                    onclick="ytVideoApp.presentAuthLink();
                    return false;">Click here to generate authentication link</a>
                    </div>
END;
    }
    ?>
    </div>
    <!-- end Authentication status -->
    <br clear="all" />
    <?php
        // if $_GET['status'] is populated then we have a response
        // about a syndicated upload from YouTube's servers
        if (isset($_GET['status'])) {
            (isset($_GET['code']) ? $code = $_GET['code'] : $code = null);
            (isset($_GET['id']) ? $id = $_GET['id'] : $id = null);
            print '<div id="generalStatus">' .
                  uploadStatus($_GET['status'], $code, $id) .
                  '<div id="detailedUploadStatus"></div></div>';
         }
    ?>
    <!-- General status -->
    <?php
        if (authenticated()) {
            printAuthenticatedActions();
        }
    ?>
    <!-- end General status -->
    <br clear="all" />
    <div id="searchResults">
      <div id="searchResultsListColumn">
        <div id="searchResultsVideoList"></div>
        <div id="searchResultsNavigation">
          <form id="navigationForm" action="javascript:void();">
            <input type="button" id="previousPageButton" onclick="ytVideoApp.listVideos(ytVideoApp.previousQueryType, ytVideoApp.previousSearchTerm, ytVideoApp.previousPage);" value="Back" style="display: none;" />
            <input type="button" id="nextPageButton" onclick="ytVideoApp.listVideos(ytVideoApp.previousQueryType, ytVideoApp.previousSearchTerm, ytVideoApp.nextPage);" value="Next" style="display: none;" />
          </form>
        </div>
      </div>
    <div id="searchResultsVideoColumn">
      <div id="videoPlayer"></div>
    </div>
  </div>
</div>
</body>
</html>