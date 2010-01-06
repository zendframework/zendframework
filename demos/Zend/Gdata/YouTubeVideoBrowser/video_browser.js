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
 * @fileoverview Provides functions for browsing and searching YouTube 
 * data API feeds using a PHP backend powered by the Zend_Gdata component
 * of the Zend Framework.
 */

/**
 * provides namespacing for the YouTube Video Browser PHP version (ytvbp)
 */
var ytvbp = {};

/**
 * maximum number of results to return for list of videos
 * @type Number
 */
ytvbp.MAX_RESULTS_LIST = 5;

/**
 * navigation button id used to page to the previous page of
 * results in the list of videos
 * @type String
 */
ytvbp.PREVIOUS_PAGE_BUTTON = 'previousPageButton';

/**
 * navigation button id used to page to the next page of
 * results in the list of videos
 * @type String
 */
ytvbp.NEXT_PAGE_BUTTON = 'nextPageButton';

/**
 * container div id used to hold list of videos
 * @type String
 */
ytvbp.VIDEO_LIST_CONTAINER_DIV = 'searchResultsVideoList';

/**
 * container div id used to hold the video player
 * @type String
 */
ytvbp.VIDEO_PLAYER_DIV = 'videoPlayer';

/**
 * container div id used to hold the search box which displays when the page
 * first loads
 * @type String
 */
ytvbp.MAIN_SEARCH_CONTAINER_DIV = 'mainSearchBox';

/** 
 * container div id used to hold the search box displayed at the top of
 * the browser after one search has already been performed
 * @type String
 */
ytvbp.TOP_SEARCH_CONTAINER_DIV = 'searchBox';

/**
 * the page number to use for the next page navigation button
 * @type Number
 */
ytvbp.nextPage = 2;

/**
 * the page number to use for the previous page navigation button
 * @type Number
 */
ytvbp.previousPage = 0;

/** 
 * the last search term used to query - allows for the navigation
 * buttons to know what string query to perform when clicked
 * @type String
 */
ytvbp.previousSearchTerm = '';

/**
 * the last query type used for querying - allows for the navigation
 * buttons to know what type of query to perform when clicked
 * @type String
 */
ytvbp.previousQueryType = 'all';

/**
 * Retrieves a list of videos matching the provided criteria.  The list of
 * videos can be restricted to a particular standard feed or search criteria.
 * @param {String} queryType The type of query to be done - either 'all'
 *     for querying all videos, or the name of a standard feed.
 * @param {String} searchTerm The search term(s) to use for querying as the
 *     'vq' query parameter value
 * @param {Number} page The 1-based page of results to return.
 */
ytvbp.listVideos = function(queryType, searchTerm, page) {
  ytvbp.previousSearchTerm = searchTerm; 
  ytvbp.previousQueryType = queryType; 
  var maxResults = ytvbp.MAX_RESULTS_LIST;
  var startIndex =  (((page - 1) * ytvbp.MAX_RESULTS_LIST) + 1);
  ytvbp.presentFeed(queryType, maxResults, startIndex, searchTerm);
  ytvbp.updateNavigation(page);
};

/**
 * Sends an AJAX request to the server to retrieve a list of videos or
 * the video player/metadata.  Sends the request to the specified filePath
 * on the same host, passing the specified params, and filling the specified
 * resultDivName with the resutls upon success.
 * @param {String} filePath The path to which the request should be sent
 * @param {String} params The URL encoded POST params
 * @param {String} resultDivName The name of the DIV used to hold the results
 */
ytvbp.sendRequest = function(filePath, params, resultDivName) {
  if (window.XMLHttpRequest) {
    var xmlhr = new XMLHttpRequest();
  } else {
    var xmlhr = new ActiveXObject('MSXML2.XMLHTTP.3.0');
  }
        
  xmlhr.open('POST', filePath, true);
  xmlhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded'); 

  xmlhr.onreadystatechange = function() {
    var resultDiv = document.getElementById(resultDivName);
    if (xmlhr.readyState == 1) {
      resultDiv.innerHTML = '<b>Loading...</b>'; 
    } else if (xmlhr.readyState == 4 && xmlhr.status == 200) {
      if (xmlhr.responseText) {
        resultDiv.innerHTML = xmlhr.responseText;
      }
    } else if (xmlhr.readyState == 4) {
      alert('Invalid response received - Status: ' + xmlhr.status);
    }
  }
  xmlhr.send(params);
}

/**
 * Uses ytvbp.sendRequest to display a YT video player and metadata for the
 * specified video ID.
 * @param {String} videoId The ID of the YouTube video to show
 */
ytvbp.presentVideo = function(videoId) {
  var params = 'queryType=show_video&videoId=' + videoId;
  var filePath = 'index.php';
  ytvbp.sendRequest(filePath, params, ytvbp.VIDEO_PLAYER_DIV);
}

/**
 * Uses ytvbp.sendRequest to display a list of of YT videos.
 * @param {String} queryType The name of a standard video feed or 'all'
 * @param {Number} maxResults The maximum number of videos to list
 * @param {Number} startIndex The first video to include in the list
 * @param {String} searchTerm The search terms to pass to the specified feed
 */
ytvbp.presentFeed = function(queryType, maxResults, startIndex, searchTerm){
  var params = 'queryType=' + queryType + 
               '&maxResults=' + maxResults +
               '&startIndex=' + startIndex + 
               '&searchTerm=' + searchTerm;
  var filePath = 'index.php';
  ytvbp.sendRequest(filePath, params, ytvbp.VIDEO_LIST_CONTAINER_DIV);
}

/**
 * Updates the variables used by the navigation buttons and the 'enabled' 
 * status of the buttons based upon the current page number passed in.
 * @param {Number} page The current page number
 */
ytvbp.updateNavigation = function(page) {
  ytvbp.nextPage = page + 1;
  ytvbp.previousPage = page - 1;
  document.getElementById(ytvbp.NEXT_PAGE_BUTTON).style.display = 'inline';
  document.getElementById(ytvbp.PREVIOUS_PAGE_BUTTON).style.display = 'inline';
  if (ytvbp.previousPage < 1) {
    document.getElementById(ytvbp.PREVIOUS_PAGE_BUTTON).disabled = true;
  } else {
    document.getElementById(ytvbp.PREVIOUS_PAGE_BUTTON).disabled = false;
  }
  document.getElementById(ytvbp.NEXT_PAGE_BUTTON).disabled = false;
};

/**
 * Hides the main (large) search form and enables one that's in the
 * title bar of the application.  The main search form is only used
 * for the first load.  Subsequent searches should use the version in
 * the title bar.
 */
ytvbp.hideMainSearch = function() {
  document.getElementById(ytvbp.MAIN_SEARCH_CONTAINER_DIV).style.display = 
      'none';
  document.getElementById(ytvbp.TOP_SEARCH_CONTAINER_DIV).style.display = 
      'inline';
};

/**
 * Method called when the query type has been changed.  Clears out the
 * value of the search term input box by default if one of the standard
 * feeds is selected.  This is to improve usability, as many of the standard
 * feeds may not include results for even fairly popular search terms.
 * @param {String} queryType The type of query being done - either 'all'
 *     for querying all videos, or the name of one of the standard feeds.
 * @param {Node} searchTermInputElement The HTML input element for the input
 *     element.
 */
ytvbp.queryTypeChanged = function(queryType, searchTermInputElement) {
  if (queryType != 'all') {
    searchTermInputElement.value = '';
  }
};
