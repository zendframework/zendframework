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
 * Sample code to demonstrate accessing a Google Data feed using OAuth for
 * authorization.  Utilizes the Zend Framework Zend_OAuth components to
 * communicate with the API(s).
 *
 * NOTE: As this is sample code, not all of the functions do full error
 * handling.
 */

require_once 'Zend/Gdata/Docs.php';
require_once 'Zend/Gdata/Spreadsheets.php';

require_once 'Gdata_OAuth_Helper.php';

session_start();

// Application constants. Replace these values with your own.
$APP_NAME = 'google-ZendGData3LOSample-1.0';
$APP_URL = getAppURL();
$scopes = array(
    'https://docs.google.com/feeds/',
    'http://spreadsheets.google.com/feeds/'
);

// Setup OAuth consumer. Thes values should be replaced with your registered
// app's consumer key/secret.
$CONSUMER_KEY = 'anonymous';
$CONSUMER_SECRET = 'anonymous';
$consumer = new Gdata_OAuth_Helper($CONSUMER_KEY, $CONSUMER_SECRET);

// Main controller logic.
switch (@$_REQUEST['action']) {
    case 'logout':
        logout($APP_URL);
        break;
    case 'request_token':
        $_SESSION['REQUEST_TOKEN'] = serialize($consumer->fetchRequestToken(
            implode(' ', $scopes), $APP_URL . '?action=access_token'));
        $consumer->authorizeRequestToken();
        break;
    case 'access_token':
        $_SESSION['ACCESS_TOKEN'] = serialize($consumer->fetchAccessToken());
        header('Location: ' . $APP_URL);
        break;
    default:
        if (isset($_SESSION['ACCESS_TOKEN'])) {
            $accessToken = unserialize($_SESSION['ACCESS_TOKEN']);

            $httpClient = $accessToken->getHttpClient(
                $consumer->getOauthOptions());
            $docsService = new Zend_Gdata_Docs($httpClient, $APP_NAME);
            $spreadsheetsService = new Zend_Gdata_Spreadsheets($httpClient,
                                                               $APP_NAME);

            // Retrieve user's list of Google Docs and spreadsheet list.
            $docsFeed = $docsService->getDocumentListFeed();
            $spreadsheetFeed = $spreadsheetsService->getSpreadsheetFeed(
                'http://spreadsheets.google.com/feeds/spreadsheets/private/full?max-results=100');

            renderHTML($accessToken, array($docsFeed, $spreadsheetFeed));
        } else {
            renderHTML();
        }
}

/**
 * Returns a the base URL of the current running web app.
 *
 * @return string
 */
function getAppURL() {
    $pageURL = 'http';
    if ($_SERVER['HTTPS'] == 'on') {
        $pageURL .= 's';
    }
    $pageURL .= '://';
    if ($_SERVER['SERVER_PORT'] != '80') {
        $pageURL .= $_SERVER['SERVER_NAME'] . ':' . 
                    $_SERVER['SERVER_PORT'] . $_SERVER['PHP_SELF'];
    } else {
        $pageURL .= $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'];
    }
    return $pageURL;
}

/**
 * Removes session data and redirects the user to a URL.
 *
 * @param string $redirectUrl The URL to direct the user to after session data
 *     is destroyed.
 * @return void
 */
function logout($redirectUrl) {
    session_destroy();
    header('Location: ' . $redirectUrl);
    exit;
}

/**
 * Prints the token string and secret of the token passed in.
 *
 * @param Zend_OAuth_Token $token An access or request token object to print.
 * @return void
 */
function printToken($token) {
    echo '<b>Token:</b>' . $token->getToken() . '<br>';
    echo '<b>Token secret:</b>' . $token->getTokenSecret() . '<br>';
}

/**
 * Prints basic properties of a Google Data feed.
 *
 * @param Zend_Gdata_Feed $feed A feed object to print.
 * @return void
 */
function printFeed($feed) {
    echo '<ol>';
    foreach ($feed->entries as $entry) {
        $alternateLink = '';
        foreach ($entry->link as $link) {
            if ($link->getRel() == 'alternate') {
                $alternateLink = $link->getHref();
            }
        }
        echo "<li><a href=\"$alternateLink\" target=\"_new\">$entry->title</a></li>";
    }
    echo '</ol>';
}

/**
 * Renders the page's HTML.
 *
 * @param Zend_OAuth_Token $token (optional) The user's current OAuth token.
 * @param array $feeds (optional) An array of Zend_Gdata_Feed to print
 *     information for.
 * @return void
 */
function renderHTML($token=null, $feeds=null) {
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset=utf-8 />
<link href="style.css" type="text/css" rel="stylesheet"/>
</head>
<body>
  <?php if (!isset($_SESSION['ACCESS_TOKEN'])) { ?>
    <button onclick="location.href='<?php echo "$APP_URL?action=request_token" ?>';">Grant Access to this app!</button>
  <?php } else { ?>
    <div id="token_info">
      <span style="float:left;"><img src="http://code.google.com/apis/accounts/images/oauth_icon.png"></span>
      <div id="token"><?php printToken($token); ?></div>
    </div>
    <div id="logout"><a href="<?php echo "$APP_URL?action=logout"; ?>">Logout</a></div>
    <div style="clear:both;">
      <div id="doclist">
        <h4>First 100 documents from the <a href="http://code.google.com/apis/documents/" target="_new">Documents List Data API</a>:</h4>
        <div class="feed"><?php printFeed($feeds[0]); ?></div>
      </div>
      <div id="spreadsheets">
        <h4>First 100 spreadsheets from the <a href="http://code.google.com/apis/spreadsheets/" target="_new">Spreadsheets Data API</a>:</h4>
        <div class="feed"><?php printFeed($feeds[1]); ?></div>
      </div>
    </div>
  <?php } ?>
</body>
</html>
<?php
}
