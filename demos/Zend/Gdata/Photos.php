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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * PHP sample code for the Photos data API.  Utilizes the
 * Zend Framework Gdata components to communicate with the Google API.
 *
 * Requires the Zend Framework Gdata components and PHP >= 5.1.4
 *
 * You can run this sample from a web browser.
 *
 * NOTE: You must ensure that Zend Framework is in your PHP include
 * path.  You can do this via php.ini settings, or by modifying the
 * argument to set_include_path in the code below.
 *
 * NOTE: As this is sample code, not all of the functions do full error
 * handling.
 */

/**
 * @see Zend_Loader
 */
require_once 'Zend/Loader.php';

/**
 * @see Zend_Gdata
 */
Zend_Loader::loadClass('Zend_Gdata');

/**
 * @see Zend_Gdata_AuthSub
 */
Zend_Loader::loadClass('Zend_Gdata_AuthSub');

/**
 * @see Zend_Gdata_Photos
 */
Zend_Loader::loadClass('Zend_Gdata_Photos');

/**
 * @see Zend_Gdata_Photos_UserQuery
 */
Zend_Loader::loadClass('Zend_Gdata_Photos_UserQuery');

/**
 * @see Zend_Gdata_Photos_AlbumQuery
 */
Zend_Loader::loadClass('Zend_Gdata_Photos_AlbumQuery');

/**
 * @see Zend_Gdata_Photos_PhotoQuery
 */
Zend_Loader::loadClass('Zend_Gdata_Photos_PhotoQuery');

/**
 * @see Zend_Gdata_App_Extension_Category
 */
Zend_Loader::loadClass('Zend_Gdata_App_Extension_Category');

session_start();


/**
 * Adds a new photo to the specified album
 *
 * @param  Zend_Http_Client $client  The authenticated client
 * @param  string           $user    The user's account name
 * @param  integer          $albumId The album's id
 * @param  array            $photo   The uploaded photo
 * @return void
 */
function addPhoto($client, $user, $albumId, $photo)
{
    $photos = new Zend_Gdata_Photos($client);

    $fd = $photos->newMediaFileSource($photo["tmp_name"]);
    $fd->setContentType($photo["type"]);

    $entry = new Zend_Gdata_Photos_PhotoEntry();
    $entry->setMediaSource($fd);
    $entry->setTitle($photos->newTitle($photo["name"]));

    $albumQuery = new Zend_Gdata_Photos_AlbumQuery;
    $albumQuery->setUser($user);
    $albumQuery->setAlbumId($albumId);

    $albumEntry = $photos->getAlbumEntry($albumQuery);

    $result = $photos->insertPhotoEntry($entry, $albumEntry);
    if ($result) {
        outputAlbumFeed($client, $user, $albumId);
    } else {
        echo "There was an issue with the file upload.";
    }
}

/**
 * Deletes the specified photo
 *
 * @param  Zend_Http_Client $client  The authenticated client
 * @param  string           $user    The user's account name
 * @param  integer          $albumId The album's id
 * @param  integer          $photoId The photo's id
 * @return void
 */
function deletePhoto($client, $user, $albumId, $photoId)
{
    $photos = new Zend_Gdata_Photos($client);

    $photoQuery = new Zend_Gdata_Photos_PhotoQuery;
    $photoQuery->setUser($user);
    $photoQuery->setAlbumId($albumId);
    $photoQuery->setPhotoId($photoId);
    $photoQuery->setType('entry');

    $entry = $photos->getPhotoEntry($photoQuery);

    $photos->deletePhotoEntry($entry, true);

    outputAlbumFeed($client, $user, $albumId);
}

/**
 * Adds a new album to the specified user's album
 *
 * @param  Zend_Http_Client $client The authenticated client
 * @param  string           $user   The user's account name
 * @param  string           $name   The name of the new album
 * @return void
 */
function addAlbum($client, $user, $name)
{
    $photos = new Zend_Gdata_Photos($client);

    $entry = new Zend_Gdata_Photos_AlbumEntry();
    $entry->setTitle($photos->newTitle($name));

    $result = $photos->insertAlbumEntry($entry);
    if ($result) {
        outputUserFeed($client, $user);
    } else {
        echo "There was an issue with the album creation.";
    }
}

/**
 * Deletes the specified album
 *
 * @param  Zend_Http_Client $client  The authenticated client
 * @param  string           $user    The user's account name
 * @param  integer          $albumId The album's id
 * @return void
 */
function deleteAlbum($client, $user, $albumId)
{
    $photos = new Zend_Gdata_Photos($client);

    $albumQuery = new Zend_Gdata_Photos_AlbumQuery;
    $albumQuery->setUser($user);
    $albumQuery->setAlbumId($albumId);
    $albumQuery->setType('entry');

    $entry = $photos->getAlbumEntry($albumQuery);

    $photos->deleteAlbumEntry($entry, true);

    outputUserFeed($client, $user);
}

/**
 * Adds a new comment to the specified photo
 *
 * @param  Zend_Http_Client $client  The authenticated client
 * @param  string           $user    The user's account name
 * @param  integer          $albumId The album's id
 * @param  integer          $photoId The photo's id
 * @param  string           $comment The comment to add
 * @return void
 */
function addComment($client, $user, $album, $photo, $comment)
{
    $photos = new Zend_Gdata_Photos($client);

    $entry = new Zend_Gdata_Photos_CommentEntry();
    $entry->setTitle($photos->newTitle($comment));
    $entry->setContent($photos->newContent($comment));

    $photoQuery = new Zend_Gdata_Photos_PhotoQuery;
    $photoQuery->setUser($user);
    $photoQuery->setAlbumId($album);
    $photoQuery->setPhotoId($photo);
    $photoQuery->setType('entry');

    $photoEntry = $photos->getPhotoEntry($photoQuery);

    $result = $photos->insertCommentEntry($entry, $photoEntry);
    if ($result) {
        outputPhotoFeed($client, $user, $album, $photo);
    } else {
        echo "There was an issue with the comment creation.";
    }
}

/**
 * Deletes the specified comment
 *
 * @param  Zend_Http_Client $client    The authenticated client
 * @param  string           $user      The user's account name
 * @param  integer          $albumId   The album's id
 * @param  integer          $photoId   The photo's id
 * @param  integer          $commentId The comment's id
 * @return void
 */
function deleteComment($client, $user, $albumId, $photoId, $commentId)
{
    $photos = new Zend_Gdata_Photos($client);

    $photoQuery = new Zend_Gdata_Photos_PhotoQuery;
    $photoQuery->setUser($user);
    $photoQuery->setAlbumId($albumId);
    $photoQuery->setPhotoId($photoId);
    $photoQuery->setType('entry');

    $path = $photoQuery->getQueryUrl() . '/commentid/' . $commentId;

    $entry = $photos->getCommentEntry($path);

    $photos->deleteCommentEntry($entry, true);

    outputPhotoFeed($client, $user, $albumId, $photoId);
}

/**
 * Adds a new tag to the specified photo
 *
 * @param  Zend_Http_Client $client The authenticated client
 * @param  string           $user   The user's account name
 * @param  integer          $album  The album's id
 * @param  integer          $photo  The photo's id
 * @param  string           $tag    The tag to add to the photo
 * @return void
 */
function addTag($client, $user, $album, $photo, $tag)
{
    $photos = new Zend_Gdata_Photos($client);

    $entry = new Zend_Gdata_Photos_TagEntry();
    $entry->setTitle($photos->newTitle($tag));

    $photoQuery = new Zend_Gdata_Photos_PhotoQuery;
    $photoQuery->setUser($user);
    $photoQuery->setAlbumId($album);
    $photoQuery->setPhotoId($photo);
    $photoQuery->setType('entry');

    $photoEntry = $photos->getPhotoEntry($photoQuery);

    $result = $photos->insertTagEntry($entry, $photoEntry);
    if ($result) {
        outputPhotoFeed($client, $user, $album, $photo);
    } else {
        echo "There was an issue with the tag creation.";
    }
}

/**
 * Deletes the specified tag
 *
 * @param  Zend_Http_Client $client     The authenticated client
 * @param  string           $user       The user's account name
 * @param  integer          $albumId    The album's id
 * @param  integer          $photoId    The photo's id
 * @param  string           $tagContent The name of the tag to be deleted
 * @return void
 */
function deleteTag($client, $user, $albumId, $photoId, $tagContent)
{
    $photos = new Zend_Gdata_Photos($client);

    $photoQuery = new Zend_Gdata_Photos_PhotoQuery;
    $photoQuery->setUser($user);
    $photoQuery->setAlbumId($albumId);
    $photoQuery->setPhotoId($photoId);
    $query = $photoQuery->getQueryUrl() . "?kind=tag";

    $photoFeed = $photos->getPhotoFeed($query);

    foreach ($photoFeed as $entry) {
        if ($entry instanceof Zend_Gdata_Photos_TagEntry) {
            if ($entry->getContent() == $tagContent) {
                $tagEntry = $entry;
            }
        }
    }

    $photos->deleteTagEntry($tagEntry, true);

    outputPhotoFeed($client, $user, $albumId, $photoId);
}

/**
 * Returns the path to the current script, without any query params
 *
 * Env variables used:
 * $_SERVER['PHP_SELF']
 *
 * @return string Current script path
 */
function getCurrentScript()
{
    global $_SERVER;
    return $_SERVER["PHP_SELF"];
}

/**
 * Returns the full URL of the current page, based upon env variables
 *
 * Env variables used:
 * $_SERVER['HTTPS'] = (on|off|)
 * $_SERVER['HTTP_HOST'] = value of the Host: header
 * $_SERVER['SERVER_PORT'] = port number (only used if not http/80,https/443)
 * $_SERVER['REQUEST_URI'] = the URI after the method of the HTTP request
 *
 * @return string Current URL
 */
function getCurrentUrl()
{
    global $_SERVER;

    /**
     * Filter php_self to avoid a security vulnerability.
     */
    $php_request_uri = htmlentities(substr($_SERVER['REQUEST_URI'], 0,
    strcspn($_SERVER['REQUEST_URI'], "\n\r")), ENT_QUOTES);

    if (isset($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on') {
        $protocol = 'https://';
    } else {
        $protocol = 'http://';
    }
    $host = $_SERVER['HTTP_HOST'];
    if ($_SERVER['SERVER_PORT'] != '' &&
        (($protocol == 'http://' && $_SERVER['SERVER_PORT'] != '80') ||
        ($protocol == 'https://' && $_SERVER['SERVER_PORT'] != '443'))) {
            $port = ':' . $_SERVER['SERVER_PORT'];
    } else {
        $port = '';
    }
    return $protocol . $host . $port . $php_request_uri;
}

/**
 * Returns the AuthSub URL which the user must visit to authenticate requests
 * from this application.
 *
 * Uses getCurrentUrl() to get the next URL which the user will be redirected
 * to after successfully authenticating with the Google service.
 *
 * @return string AuthSub URL
 */
function getAuthSubUrl()
{
    $next = getCurrentUrl();
    $scope = 'http://picasaweb.google.com/data';
    $secure = false;
    $session = true;
    return Zend_Gdata_AuthSub::getAuthSubTokenUri($next, $scope, $secure,
        $session);
}

/**
 * Outputs a request to the user to login to their Google account, including
 * a link to the AuthSub URL.
 *
 * Uses getAuthSubUrl() to get the URL which the user must visit to authenticate
 *
 * @return void
 */
function requestUserLogin($linkText)
{
    $authSubUrl = getAuthSubUrl();
    echo "<a href=\"{$authSubUrl}\">{$linkText}</a>";
}

/**
 * Returns a HTTP client object with the appropriate headers for communicating
 * with Google using AuthSub authentication.
 *
 * Uses the $_SESSION['sessionToken'] to store the AuthSub session token after
 * it is obtained.  The single use token supplied in the URL when redirected
 * after the user succesfully authenticated to Google is retrieved from the
 * $_GET['token'] variable.
 *
 * @return Zend_Http_Client
 */
function getAuthSubHttpClient()
{
    global $_SESSION, $_GET;
    if (!isset($_SESSION['sessionToken']) && isset($_GET['token'])) {
        $_SESSION['sessionToken'] =
            Zend_Gdata_AuthSub::getAuthSubSessionToken($_GET['token']);
    }
    $client = Zend_Gdata_AuthSub::getHttpClient($_SESSION['sessionToken']);
    return $client;
}

/**
 * Processes loading of this sample code through a web browser.  Uses AuthSub
 * authentication and outputs a list of a user's albums if succesfully
 * authenticated.
 *
 * @return void
 */
function processPageLoad()
{
    global $_SESSION, $_GET;
    if (!isset($_SESSION['sessionToken']) && !isset($_GET['token'])) {
        requestUserLogin('Please login to your Google Account.');
    } else {
        $client = getAuthSubHttpClient();
        if (!empty($_REQUEST['command'])) {
            switch ($_REQUEST['command']) {
                case 'retrieveSelf':
                    outputUserFeed($client, "default");
                    break;
                case 'retrieveUser':
                outputUserFeed($client, $_REQUEST['user']);
                    break;
                case 'retrieveAlbumFeed':
                    outputAlbumFeed($client, $_REQUEST['user'], $_REQUEST['album']);
                    break;
                case 'retrievePhotoFeed':
                    outputPhotoFeed($client, $_REQUEST['user'], $_REQUEST['album'],
                        $_REQUEST['photo']);
                    break;
            }
        }

        // Now we handle the potentially destructive commands, which have to
        // be submitted by POST only.
        if (!empty($_POST['command'])) {
            switch ($_POST['command']) {
                case 'addPhoto':
                    addPhoto($client, $_POST['user'], $_POST['album'], $_FILES['photo']);
                    break;
                case 'deletePhoto':
                    deletePhoto($client, $_POST['user'], $_POST['album'],
                        $_POST['photo']);
                    break;
                case 'addAlbum':
                    addAlbum($client, $_POST['user'], $_POST['name']);
                    break;
                case 'deleteAlbum':
                    deleteAlbum($client, $_POST['user'], $_POST['album']);
                    break;
                case 'addComment':
                    addComment($client, $_POST['user'], $_POST['album'], $_POST['photo'],
                        $_POST['comment']);
                    break;
                case 'addTag':
                    addTag($client, $_POST['user'], $_POST['album'], $_POST['photo'],
                        $_POST['tag']);
                    break;
                case 'deleteComment':
                    deleteComment($client, $_POST['user'], $_POST['album'],
                        $_POST['photo'], $_POST['comment']);
                    break;
                case 'deleteTag':
                    deleteTag($client, $_POST['user'], $_POST['album'], $_POST['photo'],
                        $_POST['tag']);
                    break;
              default:
                    break;
          }
        }

        // If a menu parameter is available, display a submenu.
        if (!empty($_REQUEST['menu'])) {
            switch ($_REQUEST['menu']) {
              case 'user':
                displayUserMenu();
                    break;
                case 'photo':
                    displayPhotoMenu();
                    break;
            case 'album':
              displayAlbumMenu();
                    break;
            case 'logout':
              logout();
                    break;
            default:
                header('HTTP/1.1 400 Bad Request');
                echo "<h2>Invalid menu selection.</h2>\n";
                echo "<p>Please check your request and try again.</p>";
          }
        }

        if (empty($_REQUEST['menu']) && empty($_REQUEST['command'])) {
            displayMenu();
        }
    }
}

/**
 * Displays the main menu, allowing the user to select from a list of actions.
 *
 * @return void
 */
function displayMenu()
{
?>
<h2>Main Menu</h2>

<p>Welcome to the Photos API demo page. Please select
    from one of the following four options to fetch information.</p>

    <ul>
        <li><a href="?command=retrieveSelf">Your Feed</a></li>
        <li><a href="?menu=user">User Menu</a></li>
        <li><a href="?menu=photo">Photos Menu</a></li>
        <li><a href="?menu=album">Albums Menu</a></li>
    </ul>
<?php
}

/**
 * Outputs an HTML link to return to the previous page.
 *
 * @return void
 */
function displayBackLink()
{
    echo "<br><br>";
    echo "<a href='javascript: history.go(-1);'><< Back</a>";
}

/**
 * Displays the user menu, allowing the user to request a specific user's feed.
 *
 * @return void
 */
function displayUserMenu()
{
?>
<h2>User Menu</h2>

<div class="menuForm">
    <form method="get" accept-charset="utf-8">
        <h3 class='nopad'>Retrieve User Feed</h3>
        <p>Retrieve the feed for an existing user.</p>
        <p>
            <input type="hidden" name="command" value="retrieveUser" />
            <label for="user">Username: </label>
            <input type="text" name="user" value="" /><br />
        </p>

        <p><input type="submit" value="Retrieve User &rarr;"></p>
    </form>
</div>
<?php

    displayBackLink();
}

/**
 * Displays the photo menu, allowing the user to request a specific photo's feed.
 *
 * @return void
 */
function displayPhotoMenu()
{
?>
<h2>Photo Menu</h2>

<div class="menuForm">
    <form method="get" accept-charset="utf-8">
        <h3 class='nopad'>Retrieve Photo Feed</h3>
        <p>Retrieve the feed for an existing photo.</p>
        <p>
            <input type="hidden" name="command" value="retrievePhotoFeed" />
            <label for="user">User: </label>
            <input type="text" name="user" value="" /><br />
            <label for="album">Album ID: </label>
            <input type="text" name="album" value="" /><br />
            <label for="photoid">Photo ID: </label>
            <input type="text" name="photo" value="" /><br />
        </p>

        <p><input type="submit" value="Retrieve Photo Feed &rarr;"></p>
    </form>
</div>
<?php

    displayBackLink();
}

/**
 * Displays the album menu, allowing the user to request a specific album's feed.
 *
 * @return void
 */
function displayAlbumMenu()
{
?>
<h2>Album Menu</h2>

<div class="menuForm">
    <form method="get" accept-charset="utf-8">
        <h3 class='nopad'>Retrieve Album Feed</h3>
        <p>Retrieve the feed for an existing album.</p>
        <p>
            <input type="hidden" name="command" value="retrieveAlbumFeed" />
            <label for="user">User: </label>
                    <input type="text" name="user" value="" /><br />
                    <label for="album">Album ID: </label>
                    <input type="text" name="album" value="" /><br />
        </p>

        <p><input type="submit" value="Retrieve Album Feed &rarr;"></p>
    </form>
</div>
<?php

    displayBackLink();
}

/**
 * Outputs an HTML unordered list (ul), with each list item representing an
 * album in the user's feed.
 *
 * @param  Zend_Http_Client $client The authenticated client object
 * @param  string           $user   The user's account name
 * @return void
 */
function outputUserFeed($client, $user)
{
    $photos = new Zend_Gdata_Photos($client);

    $query = new Zend_Gdata_Photos_UserQuery();
    $query->setUser($user);

    $userFeed = $photos->getUserFeed(null, $query);
    echo "<h2>User Feed for: " . $userFeed->getTitle() . "</h2>";
    echo "<ul class='user'>\n";
    foreach ($userFeed as $entry) {
        if ($entry instanceof Zend_Gdata_Photos_AlbumEntry) {
            echo "\t<li class='user'>";
            echo "<a href='?command=retrieveAlbumFeed&user=";
            echo $userFeed->getTitle() . "&album=" . $entry->getGphotoId();
            echo "'>";
            $thumb = $entry->getMediaGroup()->getThumbnail();
            echo "<img class='thumb' src='" . $thumb[0]->getUrl() . "' /><br />";
            echo $entry->getTitle() . "</a>";
            echo "<form action='" . getCurrentScript() . "'' method='post' class='deleteForm'>";
            echo "<input type='hidden' name='user' value='" . $user . "' />";
            echo "<input type='hidden' name='album' value='" . $entry->getGphotoId();
            echo "' />";
            echo "<input type='hidden' name='command' value='deleteAlbum' />";
            echo "<input type='submit' value='Delete' /></form>";
            echo "</li>\n";
        }
    }
    echo "</ul><br />\n";

    echo "<h3>Add an Album</h3>";
?>
    <form method="POST" action="<?php echo getCurrentScript(); ?>">
        <input type="hidden" name="command" value="addAlbum" />
        <input type="hidden" name="user" value="<?php echo $user; ?>" />
        <input type="text" name="name" />
        <input type="submit" name="Add Album" />
    </form>
<?php

    displayBackLink();
}

/**
 * Outputs an HTML unordered list (ul), with each list item representing a
 * photo in the user's album feed.
 *
 * @param  Zend_Http_Client $client  The authenticated client object
 * @param  string           $user    The user's account name
 * @param  integer          $albumId The album's id
 * @return void
 */
function outputAlbumFeed($client, $user, $albumId)
{
    $photos = new Zend_Gdata_Photos($client);

    $query = new Zend_Gdata_Photos_AlbumQuery();
    $query->setUser($user);
    $query->setAlbumId($albumId);

    $albumFeed = $photos->getAlbumFeed($query);
    echo "<h2>Album Feed for: " . $albumFeed->getTitle() . "</h2>";
    echo "<ul class='albums'>\n";
    foreach ($albumFeed as $entry) {
        if ($entry instanceof Zend_Gdata_Photos_PhotoEntry) {
            echo "\t<li class='albums'>";
            echo "<a href='" . getCurrentScript() . "?command=retrievePhotoFeed&user=" . $user;
            echo "&album=" . $albumId . "&photo=" . $entry->getGphotoId() . "'>";
            $thumb = $entry->getMediaGroup()->getThumbnail();
            echo "<img class='thumb' src='" . $thumb[1]->getUrl() . "' /><br />";
            echo $entry->getTitle() . "</a>";
            echo "<form action='" . getCurrentScript() . "' method='post' class='deleteForm'>";
            echo "<input type='hidden' name='user' value='" . $user . "' />";
            echo "<input type='hidden' name='album' value='" . $albumId . "' />";
            echo "<input type='hidden' name='photo' value='" . $entry->getGphotoId();
            echo "' /><input type='hidden' name='command' value='deletePhoto' />";
            echo "<input type='submit' value='Delete' /></form>";
            echo "</li>\n";
        }
    }
    echo "</ul><br />\n";

    echo "<h3>Add a Photo</h3>";
?>
    <form enctype="multipart/form-data" method="POST" action="<?php echo getCurrentScript(); ?>">
        <input type="hidden" name="MAX_FILE_SIZE" value="20971520" />
        <input type="hidden" name="command" value="addPhoto" />
        <input type="hidden" name="user" value="<?php echo $user; ?>" />
        <input type="hidden" name="album" value="<?php echo $albumId; ?>" />
        Please select a photo to upload: <input name="photo" type="file" /><br />
        <input type="submit" name="Upload" />
    </form>
<?php

    displayBackLink();
}

/**
 * Outputs the feed of the specified photo
 *
 * @param  Zend_Http_Client $client  The authenticated client object
 * @param  string           $user    The user's account name
 * @param  integer          $albumId The album's id
 * @param  integer          $photoId The photo's id
 * @return void
 */
function outputPhotoFeed($client, $user, $albumId, $photoId)
{
    $photos = new Zend_Gdata_Photos($client);

    $query = new Zend_Gdata_Photos_PhotoQuery();
    $query->setUser($user);
    $query->setAlbumId($albumId);
    $query->setPhotoId($photoId);
    $query = $query->getQueryUrl() . "?kind=comment,tag";

    $photoFeed = $photos->getPhotoFeed($query);
    echo "<h2>Photo Feed for: " . $photoFeed->getTitle() . "</h2>";
    $thumbs = $photoFeed->getMediaGroup()->getThumbnail();
    echo "<img src='" . $thumbs[2]->url . "' />";

    echo "<h3 class='nopad'>Comments:</h3>";
    echo "<ul>\n";
    foreach ($photoFeed as $entry) {
        if ($entry instanceof Zend_Gdata_Photos_CommentEntry) {
            echo "\t<li>" . $entry->getContent();
            echo "<form action='" . getCurrentScript() . "' method='post' class='deleteForm'>";
            echo "<input type='hidden' name='user' value='" . $user . "' />";
            echo "<input type='hidden' name='album' value='" . $albumId . "' />";
            echo "<input type='hidden' name='photo' value='" . $photoId . "' />";
            echo "<input type='hidden' name='comment' value='" . $entry->getGphotoId();
            echo "' />";
            echo "<input type='hidden' name='command' value='deleteComment' />";
            echo "<input type='submit' value='Delete' /></form>";
            echo "</li>\n";
        }
    }
    echo "</ul>\n";
    echo "<h4>Add a Comment</h4>";
?>
    <form method="POST" action="<?php echo getCurrentScript(); ?>">
        <input type="hidden" name="command" value="addComment" />
        <input type="hidden" name="user" value="<?php echo $user; ?>" />
        <input type="hidden" name="album" value="<?php echo $albumId; ?>" />
        <input type="hidden" name="photo" value="<?php echo $photoId; ?>" />
        <input type="text" name="comment" />
        <input type="submit" name="Comment" value="Comment" />
    </form>
<?php
    echo "<br />";
    echo "<h3 class='nopad'>Tags:</h3>";
    echo "<ul>\n";
    foreach ($photoFeed as $entry) {
        if ($entry instanceof Zend_Gdata_Photos_TagEntry) {
            echo "\t<li>" . $entry->getTitle();
            echo "<form action='" . getCurrentScript() . "' method='post' class='deleteForm'>";
            echo "<input type='hidden' name='user' value='" . $user . "' />";
            echo "<input type='hidden' name='album' value='" . $albumId . "' />";
            echo "<input type='hidden' name='photo' value='" . $photoId . "' />";
            echo "<input type='hidden' name='tag' value='" . $entry->getContent();
            echo "' />";
            echo "<input type='hidden' name='command' value='deleteTag' />";
            echo "<input type='submit' value='Delete' /></form>";
            echo "</li>\n";
        }
    }
    echo "</ul>\n";
    echo "<h4>Add a Tag</h4>";
?>
    <form method="POST" action="<?php echo getCurrentScript(); ?>">
        <input type="hidden" name="command" value="addTag" />
        <input type="hidden" name="user" value="<?php echo $user; ?>" />
        <input type="hidden" name="album" value="<?php echo $albumId; ?>" />
        <input type="hidden" name="photo" value="<?php echo $photoId; ?>" />
        <input type="text" name="tag" />
        <input type="submit" name="Tag" value="Tag" />
    </form>
<?php

    displayBackLink();
}

/**
 * Output the CSS for the page
 */

?>
<style type="text/css">
    h2 {
        color: #0056FF;
    }
    h3 {
        color: #0056FF;
        padding-top: 15px;
        clear: left;
    }
    h3.nopad {
        padding: 0px;
    }
    ul {
        background-color: #E0EAFF;
        color: #191D1D;
        margin: 10px;
        padding: 10px 10px 10px 25px;
        border: 1px solid #515B5C;
    }
    ul.user, ul.albums {
        background-color: #FFFFFF;
        border: 0px;
        padding: 0px;
    }
    li.user, li.albums {
        display: block;
        float: left;
        margin: 5px;
        padding: 5px;
        text-align: center;
        background-color: #E0EAFF;
        border: 1px solid #515B5C;
    }
    a {
        color: #0056FF;
        font-weight: bold;
        text-decoration: none;
    }
    a:hover {
        text-decoration: underline;
        color: #E00000;
    }
    div.menuForm {
        margin: 10px;
        padding: 0px 10px;
        background-color: #E0EAFF;
        border: 1px solid #515B5C;
    }
    form.deleteForm {
        padding-left: 10px;
        display: inline;
    }
    img.thumb {
        margin: 5px;
        border: 0px;
    }
</style>
<?php

/**
 * Calls the main processing function for running in a browser
 */

processPageLoad();
