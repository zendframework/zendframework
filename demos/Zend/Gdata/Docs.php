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
 * PHP sample code for the Google Documents List data API.  Utilizes the
 * Zend Framework Gdata components to communicate with the Google API.
 *
 * Requires the Zend Framework Gdata components and PHP >= 5.1.4
 *
 * You can run this sample both from the command line (CLI) and also
 * from a web browser.  When running through a web browser, only
 * AuthSub and outputting a list of documents is demonstrated.  When
 * running via CLI, all functionality except AuthSub is available and dependent
 * upon the command line options passed.  Run this script without any
 * command line options to see usage, eg:
 *     /usr/local/bin/php -f Docs.php
 *
 * More information on the Command Line Interface is available at:
 *     http://www.php.net/features.commandline
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
 * @see Zend_Gdata_ClientLogin
 */
Zend_Loader::loadClass('Zend_Gdata_ClientLogin');

/**
 * @see Zend_Gdata_Docs
 */
Zend_Loader::loadClass('Zend_Gdata_Docs');

/**
 * Returns a HTTP client object with the appropriate headers for communicating
 * with Google using the ClientLogin credentials supplied.
 *
 * @param  string $user The username, in e-mail address format, to authenticate
 * @param  string $pass The password for the user specified
 * @return Zend_Http_Client
 */
function getClientLoginHttpClient($user, $pass)
{
  $service = Zend_Gdata_Docs::AUTH_SERVICE_NAME;
  $client = Zend_Gdata_ClientLogin::getHttpClient($user, $pass, $service);
  return $client;
}

// ************************ BEGIN CLI SPECIFIC CODE ************************

/**
 * Display list of valid commands.
 *
 * @param  string $executable The name of the current script. This is usually available as $argv[0].
 * @return void
 */
function displayHelp($executable)
{
    echo "Usage: php {$executable} <action> [<username>] [<password>] " .
        "[<arg1> <arg2> ...]\n\n";
    echo "Possible action values include:\n" .
        "retrieveAllDocuments\n" .
        "retrieveWPDocs\n" .
        "retrieveSpreadsheets\n" .
        "fullTextSearch\n" .
        "uploadDocument\n";
}

/**
 * Parse command line arguments and execute appropriate function when
 * running from the command line.
 *
 * If no arguments are provided, usage information will be provided.
 *
 * @param  array   $argv The array of command line arguments provided by PHP.
 *                       $argv[0] should be the current executable name or '-' if not available.
 * @param  integer $argc The size of $argv.
 * @return void
 */
function runCLIVersion($argv, $argc)
{
    if (isset($argc) && $argc >= 2) {
        # Prepare a server connection
        if ($argc >= 4) {
            try {
                $client = getClientLoginHttpClient($argv[2], $argv[3]);
                $docs = new Zend_Gdata_Docs($client);
            } catch (Zend_Gdata_App_AuthException $e) {
                echo "Error: Unable to authenticate. Please check your";
                echo " credentials.\n";
                exit(1);
            }
        }

        # Dispatch arguments to the desired method
        switch ($argv[1]) {
            case 'retrieveAllDocuments':
                if ($argc >= 4) {
                    retrieveAllDocuments($docs, false);
                } else {
                    echo "Usage: php {$argv[0]} {$argv[1]} <username>";
                    echo " <password>\n\n";
                    echo "This lists all of the documents in the user's";
                    echo " account.\n";
                }
                break;
            case 'retrieveWPDocs':
                if ($argc >= 4) {
                    //echo "!WP Docs:";
                    //var_dump($docs);
                    retrieveWPDocs($docs, false);
                } else {
                    echo "Usage: php {$argv[0]} {$argv[1]} <username>";
                    echo " <password>\n\n";
                    echo "This lists all of the word processing documents in";
                    echo " the user's account.\n";
                }
                break;
            case 'retrieveSpreadsheets':
                if ($argc >= 4) {
                    retrieveAllDocuments($docs, false);
                } else {
                    echo "Usage: php {$argv[0]} {$argv[1]} <username>";
                    echo " <password>\n\n";
                    echo "This lists all of the spreadsheets in the user's";
                    echo " account.\n";
                }
                break;
            case 'fullTextSearch':
                if ($argc >= 4) {
                    // Combine all of the query args into one query string.
                    // The command line split the query string on space
                    // characters.
                    $queryString = implode(' ', array_slice($argv, 4));
                    fullTextSearch($docs, false, $queryString);
                } else {
                    echo "Usage: php {$argv[0]} {$argv[1]} <username>";
                    echo " <password> <query string>\n\n";
                    echo "This lists all of the documents which contain the";
                    echo " query string.\n";
                }
                break;
            case 'uploadDocument':
                if ($argc >= 5) {
                    // Pass in the file name of the document to be uploaded.
                    // Since the document is on this machine, we  do not need
                    // to set the temporary file name. The temp file name is
                    // used only when uploading to a webserver.
                    uploadDocument($docs, false, $argv[4], null);
                } else {
                    echo "Usage: php {$argv[0]} {$argv[1]} <username>";
                    echo " <password> <file_with_path>\n\n";
                    echo "This lists all of the documents which contain the";
                    echo " query string.\n";
                    echo "\nExample: php {$argv[0]} {$argv[1]} <username>";
                    echo " <password> /tmp/testSpreadsheet.ods\n";
                }
                break;
            default:
                // Invalid action entered
                displayHelp($argv[0]);
        // End switch block
        }
    } else {
        // action left unspecified
        displayHelp($argv[0]);
    }
}

/**
 * Displays the titles for the Google Documents entries in the feed. In HTML
 * mode, the titles are links which point to the HTML version of the document.
 *
 * @param  Zend_Gdata_Docs_DocumentListFeed $feed
 * @param  boolean                          $html True if output should be formatted for display in
 *                                          a web browser
 * @return void
 */
function printDocumentsFeed($feed, $html)
{
  if ($html) {echo "<ul>\n";}

  // Iterate over the document entries in the feed and display each document's
  // title.
  foreach ($feed->entries as $entry) {

    if ($html) {
        // Find the URL of the HTML view of the document.
        $alternateLink = '';
        foreach ($entry->link as $link) {
            if ($link->getRel() === 'alternate') {
                $alternateLink = $link->getHref();
            }
        }
        // Make the title link to the document on docs.google.com.
        echo "<li><a href=\"$alternateLink\">\n";
    }

    echo "$entry->title\n";

    if ($html) {echo "</a></li>\n";}
  }

  if ($html) {echo "</ul>\n";}
}

/**
 * Obtain a list of all of a user's docs.google.com documents and print the
 * titles to the command line.
 *
 * @param  Zend_Gdata_Docs $client The service object to use for communicating with the Google
 *                                 Documents server.
 * @param  boolean         $html   True if output should be formatted for display in a web browser.
 * @return void
 */
function retrieveAllDocuments($client, $html)
{
  if ($html) {echo "<h2>Your documents</h2>\n";}

  $feed = $client->getDocumentListFeed();

  printDocumentsFeed($feed, $html);
}

/**
 * Obtain a list of all of a user's docs.google.com word processing
 * documents and print the titles to the command line.
 *
 * @param  Zend_Gdata_Docs $client The service object to use for communicating with the Google
 *                                 Documents server.
 * @param  boolean         $html   True if output should be formatted for display in a web browser.
 * @return void
 */
function retrieveWPDocs($client, $html)
{
  if ($html) {echo "<h2>Your word processing documents</h2>\n";}

  $feed = $client->getDocumentListFeed(
      'http://docs.google.com/feeds/documents/private/full/-/document');

  printDocumentsFeed($feed, $html);
}

/**
 * Obtain a list of all of a user's docs.google.com spreadsheets
 * documents and print the titles to the command line.
 *
 * @param  Zend_Gdata_Docs $client The service object to use for communicating with the Google
 *                                 Documents server.
 * @param  boolean         $html   True if output should be formatted for display in a web browser.
 * @return void
 */
function retrieveSpreadsheets($client, $html)
{
  if ($html) {echo "<h2>Your spreadsheets</h2>\n";}

  $feed = $client->getDocumentListFeed(
      'http://docs.google.com/feeds/documents/private/full/-/spreadsheet');

  printDocumentsFeed($feed, $html);
}

/**
 * Obtain a list of all of a user's docs.google.com documents
 * which match the specified search criteria and print the titles to the
 * command line.
 *
 * @param  Zend_Gdata_Docs $client The service object to use for communicating with the Google
 *                                 Documents server.
 * @param  boolean         $html   True if output should be formatted for display in a web browser.
 * @param  string          $query  The search query to use
 * @return void
 */
function fullTextSearch($client, $html, $query)
{
  if ($html) {echo "<h2>Documents containing $query</h2>\n";}

  $feed = $client->getDocumentListFeed(
      'http://docs.google.com/feeds/documents/private/full?q=' . $query);

  printDocumentsFeed($feed, $html);
}

/**
 * Upload the specified document
 *
 * @param  Zend_Gdata_Docs $docs                  The service object to use for communicating with
 *                                                the Google Documents server.
 * @param  boolean         $html                  True if output should be formatted for display in
 *                                                a web browser.
 * @param  string          $originalFileName      The name of the file to be uploaded. The mime type
 *                                                of the file is determined from the extension on
 *                                                this file name. For example, test.csv is uploaded
 *                                                as a comma seperated volume and converted into a
 *                                                spreadsheet.
 * @param  string          $temporaryFileLocation (optional) The file in which the data for the
 *                                                document is stored. This is used when the file has
 *                                                been uploaded from the client's machine to the
 *                                                server and is stored in a temporary file which
 *                                                does not have an extension. If this parameter is
 *                                                null, the file is read from the originalFileName.
 * @return void
 */
function uploadDocument($docs, $html, $originalFileName,
                        $temporaryFileLocation) {
  $fileToUpload = $originalFileName;
  if ($temporaryFileLocation) {
    $fileToUpload = $temporaryFileLocation;
  }

  // Upload the file and convert it into a Google Document. The original
  // file name is used as the title of the document and the mime type
  // is determined based on the extension on the original file name.
  $newDocumentEntry = $docs->uploadFile($fileToUpload, $originalFileName,
      null, Zend_Gdata_Docs::DOCUMENTS_LIST_FEED_URI);

  echo "New Document Title: ";

  if ($html) {
      // Find the URL of the HTML view of this document.
      $alternateLink = '';
      foreach ($newDocumentEntry->link as $link) {
          if ($link->getRel() === 'alternate') {
              $alternateLink = $link->getHref();
          }
      }
      // Make the title link to the document on docs.google.com.
      echo "<a href=\"$alternateLink\">\n";
  }
  echo $newDocumentEntry->title."\n";
  if ($html) {echo "</a>\n";}
}

// ************************ BEGIN WWW SPECIFIC CODE ************************

/**
 * Writes the HTML prologue for this app.
 *
 * NOTE: We would normally keep the HTML/CSS markup separate from the business
 *       logic above, but have decided to include it here for simplicity of
 *       having a single-file sample.
 *
 *
 * @param  boolean $displayMenu (optional) If set to true, a navigation menu is displayed at the top
 *                              of the page. Default is true.
 * @return void
 */
function startHTML($displayMenu = true)
{
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

    <title>Documents List API Demo</title>

    <style type="text/css" media="screen">
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: small;
        }

        #header {
            background-color: #9cF;
            -moz-border-radius: 5px;
            -webkit-border-radius: 5px;
            padding-left: 5px;
            height: 2.4em;
        }

        #header h1 {
            width: 49%;
            display: inline;
            float: left;
            margin: 0;
            padding: 0;
            font-size: 2em;
        }

        #header p {
            width: 49%;
            margin: 0;
            padding-right: 15px;
            float: right;
            line-height: 2.4em;
            text-align: right;
        }

        .clear {
            clear:both;
        }

        h2 {
            background-color: #ccc;
            -moz-border-radius: 5px;
            -webkit-border-radius: 5px;
            margin-top: 1em;
            padding-left: 5px;
        }

        .error {
            color: red;
        }

        form {
            width: 500px;
            background: #ddf8cc;
            border: 1px solid #80c605;
            padding: 0 1em;
            margin: 1em auto;
        }

        .warning {
            width: 500px;
            background: #F4B5B4;
            border: 1px solid #900;
            padding: 0 1em;
            margin: 1em auto;
        }

        label {
            display: block;
            width: 130px;
            float: left;
            text-align: right;
            padding-top: 0.3em;
            padding-right: 3px;
        }

        .radio {
            margin: 0;
            padding-left: 130px;
        }

        #menuSelect {
            padding: 0;
        }

        #menuSelect li {
            display: block;
            width: 500px;
            background: #ddf8cc;
            border: 1px solid #80c605;
            margin: 1em auto;
            padding: 0;
            font-size: 1.3em;
            text-align: center;
            list-style-type: none;
        }

        #menuSelect li:hover {
            background: #c4faa2;
        }

        #menuSelect a {
            display: block;
            height: 2em;
            margin: 0px;
            padding-top: 0.75em;
            padding-bottom: -0.25em;
            text-decoration: none;
        }
        #content {
            width: 600px;
            margin: 0 auto;
            padding: 0;
            text-align: left;
        }
    </style>

</head>

<body>

<div id="header">
    <h1>Documents List API Demo</h1>
    <?php if ($displayMenu === true) { ?>
        <p><a href="?">Main</a> | <a href="?menu=logout">Logout</a></p>
    <?php } ?>
    <div class="clear"></div>
</div>

<div id="content">
<?php
}

/**
 * Writes the HTML epilogue for this app and exit.
 *
 * @param  boolean $displayBackButton (optional) If true, displays a link to go back at the bottom
 *                                    of the page. Defaults to false.
 * @return void
 */
function endHTML($displayBackButton = false)
{
    if ($displayBackButton === true) {
        echo '<div style="clear: both;">';
        echo '<a href="javascript:history.go(-1)">&larr; Back</a></div>';
    }
?>
</div>
</body>
</html>
<?php
exit();
}

/**
 * Displays a notice indicating that a login password needs to be
 * set before continuing.
 *
 * @return void
 */
function displayPasswordNotSetNotice()
{
?>
    <div class="warning">
        <h3>Almost there...</h3>
        <p>Before using this demo, you must set an application password
            to protect your account. You will also need to set your
            Google Apps credentials in order to communicate with the Google
            Apps servers.</p>
        <p>To continue, open this file in a text editor and fill
            out the information in the configuration section.</p>
    </div>
<?php
}

/**
 * Displays a notice indicating that authentication to Google Apps failed.
 *
 * @return void
 */
function displayAuthenticationFailedNotice()
{
?>
    <div class="warning">
        <h3>Google Docs Authentication Failed</h3>
        <p>Authentication with the Google Apps servers failed.</p>
        <p>Please open this file in a text editor and make
            sure your credentials are correct.</p>
    </div>
<?php
}

/**
 * Outputs a request to the user to login to their Google account, including
 * a link to the AuthSub URL.
 *
 * Uses getAuthSubUrl() to get the URL which the user must visit to authenticate
 *
 * @param  string $linkText
 * @return void
 */
function requestUserLogin($linkText)
{
    $authSubUrl = getAuthSubUrl();
    echo "<a href=\"{$authSubUrl}\">{$linkText}</a>";
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
    $scope = 'http://docs.google.com/feeds/documents';
    $secure = false;
    $session = true;
    return Zend_Gdata_AuthSub::getAuthSubTokenUri($next, $scope, $secure,
        $session);
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
    if (!isset($_SESSION['docsSampleSessionToken']) && isset($_GET['token'])) {
        $_SESSION['docsSampleSessionToken'] =
            Zend_Gdata_AuthSub::getAuthSubSessionToken($_GET['token']);
    }
    $client = Zend_Gdata_AuthSub::getHttpClient($_SESSION['docsSampleSessionToken']);
    return $client;
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
 * Display the main menu for running in a web browser.
 *
 * @return void
 */
function displayMenu()
{
?>
<h2>Main Menu</h2>

<p>Welcome to the Google Documents List API demo page. Please select
    from one of the following three options to see a list of commands.</p>

    <ul id="menuSelect">
        <li><a class="menuSelect" href="?menu=list">List Documents</a></li>
        <li><a class="menuSelect" href="?menu=query">Query your Documents</a></li>
        <li><a class="menuSelect" href="?menu=upload">Upload a new document</a></li>
    </ul>

<p>Tip: You can also run this demo from the command line if your system
    has PHP CLI support enabled.</p>
<?php
}

/**
 * Log the current user out of the application.
 *
 * @return void
 */
function logout()
{
session_destroy();
?>
<h2>Logout</h2>

<p>Logout successful.</p>

<ul id="menuSelect">
    <li><a class="menuSelect" href="?">Login</a></li>
</ul>
<?php
}


/**
 * Processes loading of this sample code through a web browser.
 *
 * @return void
 */
function runWWWVersion()
{
    session_start();

    // Note that all calls to endHTML() below end script execution!

    global $_SESSION, $_GET;
    if (!isset($_SESSION['docsSampleSessionToken']) && !isset($_GET['token'])) {
        requestUserLogin('Please login to your Google Account.');
    } else {
        $client = getAuthSubHttpClient();
        $docs = new Zend_Gdata_Docs($client);

        // First we check for commands that can be submitted either though
        // POST or GET (they don't make any changes).
        if (!empty($_REQUEST['command'])) {
            switch ($_REQUEST['command']) {
                case 'retrieveAllDocuments':
                    startHTML();
                    retrieveAllDocuments($docs, true);
                    endHTML(true);
                case 'retrieveWPDocs':
                    startHTML();
                    retrieveWPDocs($docs, true);
                    endHTML(true);
                case 'retrieveSpreadsheets':
                    startHTML();
                    retrieveSpreadsheets($docs, true);
                    endHTML(true);
                case 'fullTextSearch':
                    startHTML();
                    fullTextSearch($docs, true, $_REQUEST['query']);
                    endHTML(true);

            }
        }

        // Now we handle the potentially destructive commands, which have to
        // be submitted by POST only.
        if (!empty($_POST['command'])) {
            switch ($_POST['command']) {
                case 'uploadDocument':
                    startHTML();
                    uploadDocument($docs, true,
                        $_FILES['uploadedFile']['name'],
                        $_FILES['uploadedFile']['tmp_name']);
                    endHTML(true);
                case 'modifySubscription':
                    if ($_POST['mode'] == 'subscribe') {
                        startHTML();
                        endHTML(true);
                    } elseif ($_POST['mode'] == 'unsubscribe') {
                        startHTML();
                        endHTML(true);
                    } else {
                        header('HTTP/1.1 400 Bad Request');
                        startHTML();
                        echo "<h2>Invalid mode.</h2>\n";
                        echo "<p>Please check your request and try again.</p>";
                        endHTML(true);
                    }
            }
        }

        // Check for an invalid command. If so, display an error and exit.
        if (!empty($_REQUEST['command'])) {
            header('HTTP/1.1 400 Bad Request');
            startHTML();
            echo "<h2>Invalid command.</h2>\n";
            echo "<p>Please check your request and try again.</p>";
            endHTML(true);
        }
        // If a menu parameter is available, display a submenu.

        if (!empty($_REQUEST['menu'])) {
            switch ($_REQUEST['menu']) {
                case 'list':
                    startHTML();
                    displayListMenu();
                    endHTML();
                case 'query':
                    startHTML();
                    displayQueryMenu();
                    endHTML();
                case 'upload':
                    startHTML();
                    displayUploadMenu();
                    endHTML();
                case 'logout':
                    startHTML(false);
                    logout();
                    endHTML();
                default:
                    header('HTTP/1.1 400 Bad Request');
                    startHTML();
                    echo "<h2>Invalid menu selection.</h2>\n";
                    echo "<p>Please check your request and try again.</p>";
                    endHTML(true);
            }
        }
        // If we get this far, that means there's nothing to do. Display
        // the main menu.
        // If no command was issued and no menu was selected, display the
        // main menu.
        startHTML();
        displayMenu();
        endHTML();
    }
}

/**
 * Display the menu for running in a web browser.
 *
 * @return void
 */
function displayListMenu()
{
?>
<h2>List Documents Menu</h2>

<form method="get" accept-charset="utf-8">
    <h3>Retrieve Google Documents Feed</h3>
    <p>Retrieve the feed for all of your documents.</p>
    <p>
        <input type="hidden" name="command" value="retrieveAllDocuments" />
    </p>
    <p><input type="submit" value="Retrieve Documents Feed &rarr;"></p>
</form>

<form method="get" accept-charset="utf-8">
    <h3>Retrieve Google Word Processing Documents</h3>
    <p>Query the documents list feed for all word processing documents.</p>
    <p>
        <input type="hidden" name="command" value="retrieveWPDocs" />
    </p>
    <p><input type="submit" value="Retrieve Word Processing Documents &rarr;"></p>
</form>

<form method="get" accept-charset="utf-8">
    <h3>Retrieve Google Spreadsheets</h3>
    <p>Query the documents list feed for all spreadsheets.</p>
    <p>
        <input type="hidden" name="command" value="retrieveSpreadsheets" />
    </p>
    <p><input type="submit" value="Retrieve Spreadsheets &rarr;"></p>
</form>
<?php
}

/**
 * Display the menu for running in a web browser.
 *
 * @return void
 */
function displayQueryMenu()
{
?>
<h2>Query the Documents List Feed</h2>

<form method="get" accept-charset="utf-8">
    <h3>Search the Documents List Feed</h3>
    <p>Find documents which contain the desired text.</p>
    <p>
        <input type="hidden" name="command" value="fullTextSearch" />
        <input type="text" name="query" />
    </p>
    <p><input type="submit" value="Search Documents Feed &rarr;"></p>
</form>

<?php
}

/**
 * Display the menu for running in a web browser.
 *
 * @return void
 */
function displayUploadMenu()
{
?>
<h2>Upload a document</h2>

<form method="post" enctype="multipart/form-data">
    <h3>Select a Document to Upload</h3>
    <p>Upload a file from your computer to <a href="http://docs.google.com">Google Documents</a>.</p>
    <p>
        <input type="hidden" name="command" value="uploadDocument" />
        <input name="uploadedFile" type="file" />
    </p>
    <p><input type="submit" value="Upload the Document &rarr;"></p>
</form>

<?php
}

// ************************** PROGRAM ENTRY POINT **************************

if (!isset($_SERVER["HTTP_HOST"]))  {
    // running through command line
    runCLIVersion($argv, $argc);
} else {
    // running through web server
    try {
        runWWWVersion();
    } catch (Zend_Gdata_Gapps_ServiceException $e) {
        // Try to recover gracefully from a service exception.
        // The HTML prologue will have already been sent.
        echo "<p><strong>Service Error Encountered</strong></p>\n";
        echo "<pre>" . htmlspecialchars($e->__toString()) . "</pre>";
        endHTML(true);
    }
}
