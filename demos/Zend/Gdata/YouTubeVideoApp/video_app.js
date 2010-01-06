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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @fileoverview Provides functions for browsing and searching YouTube 
 * data API feeds, as well as performing authentication, syndicated uploads
 * and playlist management using a PHP backend powered by the Zend_Gdata component
 * of Zend Framework.
 */

/**
 * provides namespacing for the YouTube Video Application PHP version (ytVideoApp)
 */
var ytVideoApp = {};

/**
 * maximum number of results to return for list of videos
 * @type Number
 */
ytVideoApp.MAX_RESULTS_LIST = 5;

/**
 * navigation button id used to page to the previous page of
 * results in the list of videos
 * @type String
 */
ytVideoApp.PREVIOUS_PAGE_BUTTON = 'previousPageButton';

/**
 * navigation button id used to page to the next page of
 * results in the list of videos
 * @type String
 */
ytVideoApp.NEXT_PAGE_BUTTON = 'nextPageButton';

/**
 * container div for navigation elements
 * @type String
 */
ytVideoApp.NAVIGATION_DIV = 'navigationForm';

/**
 * container div id used to hold list of videos
 * @type String
 */
ytVideoApp.VIDEO_LIST_CONTAINER_DIV = 'searchResultsVideoList';

/**
 * container div id used to hold video search results
 * @type String
 */
ytVideoApp.VIDEO_SEARCH_RESULTS_DIV = 'searchResultsVideoColumn';

/**
 * container div id used to hold the video player
 * @type String
 */
ytVideoApp.VIDEO_PLAYER_DIV = 'videoPlayer';

/** 
 * container div id used to hold the search box displayed at the top of
 * the browser after one search has already been performed
 * @type String
 */
ytVideoApp.TOP_SEARCH_CONTAINER_DIV = 'searchBox';

/** container div to show detailed upload status
 * @type String
 */
ytVideoApp.VIDEO_UPLOAD_STATUS = 'detailedUploadStatus';

/** 
 * container div to hold the form for syndicated upload
 * @type String
 */
ytVideoApp.SYNDICATED_UPLOAD_DIV = 'syndicatedUploadDiv';

/** 
 * container div to hold the form to edit video meta-data
 * @type String
 */
ytVideoApp.VIDEO_DATA_EDIT_DIV = 'editForm';

/** 
 * containder div to hold authentication link in special cases where auth gets
 * set prior to developer key
 * @type String
 */
ytVideoApp.AUTHSUB_REQUEST_DIV = 'generateAuthSubLink';

/** 
 * container div to hold the form for editing video meta-data
 * @type String
 */
ytVideoApp.VIDEO_META_DATA_EDIT_DIV = 'editVideoMetaDataDiv';

/** 
 * container div to hold the form for adding a new playlist
 * @type String
 */
ytVideoApp.PLAYLIST_ADD_DIV = 'addNewPlaylist';

/**
 * the page number to use for the next page navigation button
 * @type Number
 */
ytVideoApp.nextPage = 2;

/**
 * the page number to use for the previous page navigation button
 * @type Number
 */
ytVideoApp.previousPage = 0;

/** 
 * the last search term used to query - allows for the navigation
 * buttons to know what string query to perform when clicked
 * @type String
 */
ytVideoApp.previousSearchTerm = '';

/**
 * the last query type used for querying - allows for the navigation
 * buttons to know what type of query to perform when clicked
 * @type String
 */
ytVideoApp.previousQueryType = 'all';

/**
 * Retrieves a list of videos matching the provided criteria.  The list of
 * videos can be restricted to a particular standard feed or search criteria.
 * @param {String} op The type of action to be done.
 *     for querying all videos, or the name of a standard feed.
 * @param {String} searchTerm The search term(s) to use for querying as the
 *     'vq' query parameter value
 * @param {Number} page The 1-based page of results to return.
 */
ytVideoApp.listVideos = function(op, searchTerm, page) {
  ytVideoApp.previousSearchTerm = searchTerm; 
  ytVideoApp.previousQueryType = op; 
  var maxResults = ytVideoApp.MAX_RESULTS_LIST;
  var startIndex =  (((page - 1) * ytVideoApp.MAX_RESULTS_LIST) + 1);
  ytVideoApp.presentFeed(op, maxResults, startIndex, searchTerm);
  ytVideoApp.updateNavigation(page);
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
ytVideoApp.sendRequest = function(filePath, params, resultDivName) {
  if (window.XMLHttpRequest) {
    var xmlhr = new XMLHttpRequest();
  } else {
    var xmlhr = new ActiveXObject('MSXML2.XMLHTTP.3.0');
  }

  xmlhr.open('POST', filePath);
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
 * Uses ytVideoApp.sendRequest to display a YT video player and metadata for the
 * specified video ID.
 * @param {String} videoId The ID of the YouTube video to show
 */
ytVideoApp.presentVideo = function(videoId, updateThumbnail) {
  var params = 'operation=show_video&videoId=' + videoId;
  var filePath = 'operations.php';
  ytVideoApp.sendRequest(filePath, params, ytVideoApp.VIDEO_PLAYER_DIV);
}

/**
 * Creates a form to enter video meta-data in preparation for syndicated upload.
 */
ytVideoApp.prepareUploadForm = function() { 
  var  metaDataForm = ['<br clear="all"><form id="uploadForm" ',
    'onsubmit="ytVideoApp.prepareSyndicatedUpload(',
    'this.videoTitle.value, ',
    'this.videoDescription.value, ',
    'this.videoCategory.value, ',
    'this.videoTags.value); ',
    'return false;">',
    'Enter video title:<br /><input size="50" name="videoTitle" ',
    'type="text" /><br />',
    'Enter video description:<br /><textarea cols="50" ',
    'name="videoDescription"></textarea><br />',
    'Select a category: <select name="videoCategory">',
    '<option value="Autos">Autos &amp; Vehicles</option>',
    '<option value="Music">Music</option>',
    '<option value="Animals">Pets &amp; Animals</option>',
    '<option value="Sports">Sports</option>',
    '<option value="Travel">Travel &amp; Events</option>',
    '<option value="Games">Gadgets &amp; Games</option>',
    '<option value="Comedy">Comedy</option>',
    '<option value="People">People &amp; Blogs</option>',
    '<option value="News">News &amp; Politics</option>',
    '<option value="Entertainment">Entertainment</option>',
    '<option value="Education">Education</option>',
    '<option value="Howto">Howto &amp; Style</option>',
    '<option value="Nonprofit">Nonprofit &amp; Activism</option>',
    '<option value="Tech">Science &amp; Technology</option>',
    '</select><br />',
    'Enter some tags to describe your video ',
    '<em>(separated by spaces)</em>:<br />',
    '<input name="videoTags" type="text" size="50" value="video" /><br />',
    '<input type="submit" value="go">',
    '</form>'].join('');

  document.getElementById(ytVideoApp.SYNDICATED_UPLOAD_DIV).innerHTML = metaDataForm;
}

/** 
 * Uses ytVideoApp.sendRequest to prepare a syndicated upload.
 * 
 * @param {String} videoTitle The title for new video
 * @param {String} videoDescription The video's description
 * @param {String} videoCategory The category for the video
 * @param {String} videoTags A white-space separated string of Tags
 */
ytVideoApp.prepareSyndicatedUpload = function(videoTitle, videoDescription, videoCategory, videoTags) {
  var filePath = 'operations.php';
  var params = 'operation=create_upload_form' +
               '&videoTitle=' + videoTitle +
               '&videoDescription=' + videoDescription +
               '&videoCategory=' + videoCategory +
               '&videoTags=' + videoTags;
  ytVideoApp.sendRequest(filePath, params, ytVideoApp.SYNDICATED_UPLOAD_DIV);
}

/** 
 * Uses ytVideoApp.sendRequest to create the authSub link.
 */
ytVideoApp.presentAuthLink = function() {
  var filePath = 'operations.php';
  var params = 'operation=auth_sub_request';
  ytVideoApp.sendRequest(filePath, params, ytVideoApp.AUTHSUB_REQUEST_DIV);
}


/** 
 * Uses ytVideoApp.sendRequest to check a videos upload status.
 * 
 * @param {String} videoId The id of the video to check
 */
ytVideoApp.checkUploadDetails = function(videoId) {
  var filePath = 'operations.php';
  var params = 'operation=check_upload_status' +
               '&videoId=' + videoId;
  ytVideoApp.sendRequest(filePath, params, ytVideoApp.VIDEO_UPLOAD_STATUS);
}


/** 
 * Creates an HTML form to edit a video's meta-data, populated with the 
 * videos current meta-data.
 * 
 * @param {String} oldVideoTitle The old title of the video
 * @param {String} oldVideoDescription The old description of the video
 * @param {String} oldVideoCategory The old category of the video
 * @param {String} oldVideoTags The old tags for the video (separated by white-space)
 * @param {String} videoId The id of the video to be edited
 */
ytVideoApp.presentMetaDataEditForm = function(oldVideoTitle, oldVideoDescription, oldVideoCategory, oldVideoTags, videoId) {
  // split oldVideoTags by comma and present as whitespace separated
  var oldVideoTagsArray = oldVideoTags.split(',');
  oldVideoTags = oldVideoTagsArray.join(' ');
  var editMetaDataForm = ['<form id="editForm" ',
    'onsubmit="ytVideoApp.editMetaData(',
    'this.newVideoTitle.value, ',
    'this.newVideoDescription.value, ',
    'this.newVideoCategory.value, ',
    'this.newVideoTags.value, ',
    'this.videoId.value);',
    'return false;">',
    'Enter a new video title:<br />',
    '<input size="50" name="newVideoTitle" ',
    'type="text" value="',
    oldVideoTitle,
    '"/><br />',
    'Enter a new video description:<br />',
    '<textarea cols="50" name="newVideoDescription">', 
    oldVideoDescription,
    '</textarea><br />',
    'Select a new category: <select ',
    'name="newVideoCategory">',
    '<option value="Autos">Autos &amp; Vehicles</option>',
    '<option value="Music">Music</option>',
    '<option value="Animals">Pets &amp; Animals</option>',
    '<option value="Sports">Sports</option>',
    '<option value="Travel">Travel &amp; Events</option>',
    '<option value="Games">Gadgets &amp; Games</option>',
    '<option value="Comedy">Comedy</option>',
    '<option value="People">People &amp; Blogs</option>',
    '<option value="News">News &amp; Politics</option>',
    '<option value="Entertainment">Entertainment</option>',
    '<option value="Education">Education</option>',
    '<option value="Howto">Howto &amp; Style</option>',
    '<option value="Nonprofit">Nonprofit &amp; Activism</option>',
    '<option value="Tech">Science &amp; Technology</option>',
    '</select><br />',
    'Enter some new tags to describe your video ',
    '<em>(separated by spaces)</em>:<br />',
    '<input name="newVideoTags" type="text" size="50" ',
    'value="',
    oldVideoTags,
    '"/><br />',
    '<input name="videoId" type="hidden" value="',
    videoId,
    '" /><br />',
    '<input type="submit" value="go">',
    '</form>'].join('');
  
  document.getElementById(ytVideoApp.VIDEO_SEARCH_RESULTS_DIV).innerHTML = editMetaDataForm;
}

/** 
 * Uses ytVideoApp.sendRequest to submit updated video meta-data.
 * 
 * @param {String} newVideoTitle The new title of the video
 * @param {String} newVideoDescription The new description of the video
 * @param {String} newVideoCategory The new category of the video
 * @param {String} newVideoTags The new tags for the video (separated by white-space)
 * @param {String} videoId The id of the video to be edited
 */
ytVideoApp.editMetaData = function(newVideoTitle, newVideoDescription, newVideoCategory, newVideoTags, videoId) {
  var filePath = 'operations.php';
  var params = 'operation=edit_meta_data' +
               '&newVideoTitle=' + newVideoTitle +
               '&newVideoDescription=' + newVideoDescription +
               '&newVideoCategory=' + newVideoCategory +
               '&newVideoTags=' + newVideoTags +
               '&videoId=' + videoId;
  ytVideoApp.sendRequest(filePath, params, ytVideoApp.VIDEO_SEARCH_RESULTS_DIV);
};


/**
 * Confirms whether user wants to delete a video.
 * @param {String} videoId  The video Id to be deleted
 */
ytVideoApp.confirmDeletion = function(videoId) {
  var answer =  confirm('Do you really want to delete the video with id: ' + videoId + ' ?');
  if (answer) {
    ytVideoApp.prepareDeletion(videoId);
  }
}

/**
 * Uses ytVideoApp.sendRequest to request a video to be deleted.
 * @param {String} videoId  The video Id to be deleted
 */
ytVideoApp.prepareDeletion = function(videoId) {
  var filePath = 'operations.php';
  var params = 'operation=delete_video' +
               '&videoId=' + videoId;

  var table  = document.getElementById('videoResultList');
  var indexOfRowToBeDeleted = -1;
  var tableRows = document.getElementsByTagName('TR');
  for (var i = 0, tableRow; tableRow = tableRows[i]; i++) {
    if (tableRow.id == videoId) {
      indexOfRowToBeDeleted = i;
    }
  }
  if (indexOfRowToBeDeleted > -1) {
    table.deleteRow(indexOfRowToBeDeleted);
  }
  ytVideoApp.sendRequest(filePath, params, ytVideoApp.VIDEO_SEARCH_RESULTS_DIV);
}

/**
 * Uses ytVideoApp.sendRequest to display a list of of YT videos.
 * @param {String} op  The operation to perform to retrieve a feed
 * @param {Number} maxResults The maximum number of videos to list
 * @param {Number} startIndex The first video to include in the list
 * @param {String} searchTerm The search terms to pass to the specified feed
 */
ytVideoApp.presentFeed = function(op, maxResults, startIndex, searchTerm){
  var params = 'operation=' + op + 
               '&maxResults=' + maxResults +
               '&startIndex=' + startIndex + 
               '&searchTerm=' + searchTerm;
  var filePath = 'operations.php';
  ytVideoApp.sendRequest(filePath, params, ytVideoApp.VIDEO_LIST_CONTAINER_DIV);
};

/**
 * Updates the variables used by the navigation buttons and the 'enabled' 
 * status of the buttons based upon the current page number passed in.
 * @param {Number} page The current page number
 */
ytVideoApp.updateNavigation = function(page) {
  ytVideoApp.nextPage = page + 1;
  ytVideoApp.previousPage = page - 1;
  document.getElementById(ytVideoApp.NEXT_PAGE_BUTTON).style.display = 'inline';
  document.getElementById(ytVideoApp.PREVIOUS_PAGE_BUTTON).style.display = 'inline';
  if (ytVideoApp.previousPage < 1) {
    document.getElementById(ytVideoApp.PREVIOUS_PAGE_BUTTON).disabled = true;
  } else {
    document.getElementById(ytVideoApp.PREVIOUS_PAGE_BUTTON).disabled = false;
  }
  document.getElementById(ytVideoApp.NEXT_PAGE_BUTTON).disabled = false;
};

/**
 * Hides the navigation.
 */
ytVideoApp.hideNavigation = function() {
  document.getElementById(ytVideoApp.NAVIGATION_DIV).style.display = 'none';
};

/**
 * Update video results div
 */
ytVideoApp.refreshSearchResults = function() {
  document.getElementById(ytVideoApp.VIDEO_SEARCH_RESULTS_DIV).innerHTML = '';
}

/**
 * Method called when the query type has been changed.  Clears out the
 * value of the search term input box by default if one of the standard
 * feeds is selected.  This is to improve usability, as many of the standard
 * feeds may not include results for even fairly popular search terms.
 * @param {String} op The operation to perform.
 *     for querying all videos, or the name of one of the standard feeds.
 * @param {Node} searchTermInputElement The HTML input element for the input
 *     element.
 */
ytVideoApp.queryTypeChanged = function(op, searchTermInputElement) {
  if (op == 'search_username') {
    searchTermInputElement.value = '-- enter username --';
  } else if (op != 'search_all') {
    searchTermInputElement.value = '';
  }
};

/**
 * Create a basic HTML form to use for creating a new playlist.
 */
ytVideoApp.prepareCreatePlaylistForm = function() {
  var newPlaylistForm = ['<br /><form id="addPlaylist" ',
    'onsubmit="ytVideoApp.createNewPlaylist(this.newPlaylistTitle.value, ',
    'this.newPlaylistDescription.value); ">',
    'Enter a title for the new playlist:<br />',
    '<input size="50" name="newPlaylistTitle" type="text" /><br />',
    'Enter a description:<br />',
    '<textarea cols="25" name="newPlaylistDescription" >',
    '</textarea><br />',
    '<input type="submit" value="go">',
    '</form>'].join('');
    
  document.getElementById(ytVideoApp.PLAYLIST_ADD_DIV).innerHTML = newPlaylistForm;
}

/**
* Uses ytVideoApp.sendRequest to create a new playlist.
*
* @param {String} playlistTitle The title of the new playlist
* @param {String} playlistDescription A description of the new playlist
*/
ytVideoApp.createNewPlaylist = function(playlistTitle, playlistDescription) {
  var filePath = 'operations.php';
  var params = 'operation=create_playlist' +
               '&playlistTitle=' + playlistTitle +
               '&playlistDescription=' + playlistDescription;
  ytVideoApp.hideNavigation();
  ytVideoApp.sendRequest(filePath, params, ytVideoApp.VIDEO_SEARCH_RESULTS_DIV);
}

/**
 * Confirm user wants to delete a playlist
 *
 * @param {String} playlistTitle The title of the playlist to be deleted
 */
ytVideoApp.confirmPlaylistDeletion = function(playlistTitle) {
  var answer =  confirm('Do you really want to delete the playlist titled : ' + 
    playlistTitle + ' ?');
  if (answer) {
    ytVideoApp.deletePlaylist(playlistTitle);
  }
}

/**
* Uses ytVideoApp.sendRequest to delete a playlist.
*
* @param {String} playlistTitle The title of the new playlist
*/
ytVideoApp.deletePlaylist = function(playlistTitle) {
  var filePath = 'operations.php';
  var params = 'operation=delete_playlist' +
               '&playlistTitle=' + playlistTitle;
  ytVideoApp.sendRequest(filePath, params, ytVideoApp.VIDEO_SEARCH_RESULTS_DIV);
}

/**
 * Create a basic HTML form to use for modifying a playlist.
 *
 * @param {String} oldPlaylistTitle The old title of the playlist
 * @param {String} oldPlaylistDescription The old description of the playlist
 */
ytVideoApp.prepareUpdatePlaylistForm = function(oldPlaylistTitle, oldPlaylistDescription) {
  var playlistUpdateForm = ['<br /><form id="updatePlaylist" ',
    'onsubmit="ytVideoApp.updatePlaylist(this.newPlaylistTitle.value, ',
    'this.newPlaylistDescription.value, this.oldPlaylistTitle.value);">',
    'Enter a title for the new playlist:<br />',
    '<input size="50" name="newPlaylistTitle" type="text" value="',
    oldPlaylistTitle,
    '"/><br />',
    'Enter a description:<br />',
    '<textarea cols="25" name="newPlaylistDescription" >',
    oldPlaylistDescription,
    '</textarea><br />',
    '<input type="submit" value="go" />',
    '<input type="hidden" value="',
    oldPlaylistTitle,
    '" name="oldPlaylistTitle" />',
    '</form>'].join('');
    
  document.getElementById(ytVideoApp.VIDEO_SEARCH_RESULTS_DIV).innerHTML = playlistUpdateForm;
}

/**
* Uses ytVideoApp.sendRequest to update a playlist.
*
* @param {String} newPlaylistTitle The new title of the playlist
* @param {String} newPlaylistDescription A new description of the playlist
*/
ytVideoApp.updatePlaylist = function(newPlaylistTitle, newPlaylistDescription, oldPlaylistTitle) {
  var filePath = 'operations.php';
  var params = 'operation=update_playlist' +
               '&newPlaylistTitle=' + newPlaylistTitle +
               '&newPlaylistDescription=' + newPlaylistDescription +
               '&oldPlaylistTitle=' + oldPlaylistTitle;
  ytVideoApp.sendRequest(filePath, params, ytVideoApp.VIDEO_LIST_CONTAINER_DIV);
}

/**
* Uses ytVideoApp.sendRequest to retrieve a users playlist.
*
*/
ytVideoApp.retrievePlaylists = function() {
  var filePath = 'operations.php';
  var params = 'operation=retrieve_playlists';
  ytVideoApp.hideNavigation();
  ytVideoApp.sendRequest(filePath, params, ytVideoApp.VIDEO_LIST_CONTAINER_DIV);
}
