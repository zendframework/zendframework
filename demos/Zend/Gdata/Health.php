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

//////////////////////////////////////////////////////////////////////////////
// Configuration: You must change these settings before running this sample //
//////////////////////////////////////////////////////////////////////////////

// Change this to point to the location of your private signing key. See:
// http://code.google.com/apis/health/getting_started.html#DomainRegistration
define('HEALTH_PRIVATE_KEY', '/path/to/your/rsa_private_key.pem');

//////////////////////////////////////////////////////////////////////////////
// End Configuration                                                        //
//////////////////////////////////////////////////////////////////////////////

// Load the Zend Gdata classes.
require_once 'Zend/Loader.php';
Zend_Loader::loadClass('Zend_Gdata_AuthSub');
Zend_Loader::loadClass('Zend_Gdata_Health');
Zend_Loader::loadClass('Zend_Gdata_Health_Query');

session_start();

// Google H9 Sandbox AuthSub/OAuth scope
define('SCOPE', 'https://www.google.com/h9/feeds/');

try {
  // Setup the HTTP client and fetch an AuthSub token for H9
  $client = authenticate(@$_GET['token']);
  $useH9 = true;
  $healthService = new Zend_Gdata_Health($client, 'google-HealthPHPSample-v1.0', $useH9);
} catch(Zend_Gdata_App_Exception $e) {
  echo 'Error: ' . $e->getMessage();
}
?>

<html>
<head>
<style>
body { margin:0; }
div { width:75%;margin-left:10px;padding:5px;font-family: "Courier New"; }
div#tokenstats { border-bottom:5px solid;background-color:#99ccff;margin:0 0 10px 0;padding:5px;width:100%; }
.code { margin:5px 0 5px 10px;background-color:#eee; }
div.data{ height:600px;border:1px solid;overflow:auto; }
</style>
</head>
<body>

<div id="tokenstats">
  <b>Token info</b>: <?php echo getTokenInfo($client); ?><br>
  <b>Session Token</b>: <?php echo $client->getAuthSubToken(); ?><br>
</div>

<?php
// =============================================================================
// Return the user's entire profile in a single atom <entry>
// =============================================================================
try {
  $snippet = '
    // =========================================================================
    // Return the user\'s entire profile in a single atom <entry>
    // =========================================================================

    $query = new Zend_Gdata_Health_Query();
    $query->setDigest("true");
    $profileFeed = $healthService->getHealthProfileFeed($query);';

  $snippet2 = '
    $entries = $profileFeed->getEntries();
    $ccr = $entries[0]->getCcr();
    $xmlStr = $ccr->saveXML($ccr);
    echo "<p>" . xmlpp($xmlStr) . "</p>";';
  echo '<div class="code"><pre>' . htmlentities($snippet . $snippet2) . '</pre></div>';
  eval($snippet);
} catch(Zend_Gdata_App_Exception $e) {
  echo 'Error: ' . $e->getMessage();
}

echo '<div class="data"><pre>';
echo 'num entries: ' . count($profileFeed->getEntries());
eval($snippet2);
echo '</pre></div>';

// =============================================================================
// Return a user's medication from the entire CCR
// =============================================================================
try {
  $snippet = '
    // =========================================================================
    // Return a user\'s medication for the entire CCR
    // =========================================================================
    $profileFeed = $healthService->getHealthProfileFeed();';

  $snippet2 = '
    foreach ($profileFeed->entry as $entry) {
      $medications = $entry->getCcr()->getMedications();
      foreach ($medications as $med) {
        $xmlStr = $med->ownerDocument->saveXML($med);
        echo "<p>" . xmlpp($xmlStr) . "</p>";
      }
    }';

} catch(Zend_Gdata_App_Exception $e) {
  echo 'Error: ' . $e->getMessage();
}

echo '<div class="code"><pre>' . htmlentities($snippet . $snippet2) . '</pre></div>';
eval($snippet);

echo '<div class="data"><pre>';
eval($snippet2);
echo '</pre></div>';

// =============================================================================
// Category query: return a user's medication
// =============================================================================
try {
  $snippet = '
    // =========================================================================
    // Category query: return a user\'s medication
    // =========================================================================

    $query = new Zend_Gdata_Health_Query(SCOPE . "profile/default");
    $query->setCategory("medication");
    $profileFeed = $healthService->getHealthProfileFeed($query);';

  $snippet2 = '
    foreach ($profileFeed->entry as $entry) {
      $medications = $entry->getCcr()->getMedications();
      foreach ($medications as $med) {
        $xmlStr = $med->ownerDocument->saveXML($med);
        echo "<p>" . xmlpp($xmlStr) . "</p>";
      }
    }';

} catch(Zend_Gdata_App_Exception $e) {
  echo 'Error: ' . $e->getMessage();
}

echo '<div class="code"><pre>' . htmlentities($snippet . $snippet2) . '</pre></div>';
eval($snippet);

echo '<div class="data"><pre>';
echo 'query: ' . $query->getQueryUrl() . '<br>';
echo 'num entries: ' . count($profileFeed->getEntries());
eval($snippet2);
echo '</pre></div>';

// =============================================================================
// Query for a specific item within a category: allgergy A-Fil
// =============================================================================
try {
  $snippet = '
    // =========================================================================
    // Query for a specific item within a category: allgergy A-Fil
    // =========================================================================

    $query = new Zend_Gdata_Health_Query(SCOPE . "profile/default");
    $query->setCategory("allergy", "A-Fil");
    $query->setGrouped("true");
    $profileFeed = $healthService->getHealthProfileFeed($query);';

  $snippet2 = '
    foreach ($profileFeed->getEntries() as $entry) {
      $allergies = $entry->getCcr()->getAllergies();
      foreach ($allergies as $allergy) {
        $xmlStr = $allergy->ownerDocument->saveXML($allergy);
        echo "<p>" . xmlpp($xmlStr) . "</p>";
      }
    }';

} catch(Zend_Gdata_App_Exception $e) {
  echo 'Error: ' . $e->getMessage();
}

echo '<div class="code"><pre>' . htmlentities($snippet . $snippet2) . '</pre></div>';
eval($snippet);

echo '<div class="data"><pre>';
echo 'query: ' . $query->getQueryUrl() . '<br>';
echo 'num entries: ' . count($profileFeed->getEntries());
eval($snippet2);
echo '</pre></div>';

// =============================================================================
// Query (and return) the user\'s medications OR conditions
// =============================================================================
try {
  $snippet = '
    // =========================================================================
    // Query (and return) the user\'s medications OR conditions
    // =========================================================================

    $queryStr = SCOPE . "profile/default/-/medication%7Ccondition?digest=false";
    $profileFeed = $healthService->getHealthProfileFeed($queryStr);';
  $snippet2 = '
    $entries = $profileFeed->getEntries();
    foreach ($entries as $entry) {
      $ccr = $entry->getCcr();
      $xmlStr = $ccr->saveXML($ccr);
      echo "<p>" . xmlpp($xmlStr) . "</p>";
    }';

  echo '<div class="code"><pre>' . htmlentities($snippet . $snippet2) . '</pre></div>';
  eval($snippet);
} catch(Zend_Gdata_App_Exception $e) {
  echo 'Error: ' . $e->getMessage();
}

echo '<div class="data"><pre>';
echo 'num entries: ' . count($profileFeed->getEntries());
eval($snippet2);
echo '</pre></div>';

// =============================================================================
// Send a notice to the user's profile that includes a CCR payload
// =============================================================================
try {
  $snippet = '
    // =========================================================================
    // Send a notice to the user\'s profile that includes a CCR payload
    // =========================================================================

    $subject = "Title of your notice goes here";
    $body = "Notice body can contain <b>html</b> entities";
    $ccr = \'<ContinuityOfCareRecord xmlns="urn:astm-org:CCR">
      <Body>
       <Problems>
        <Problem>
          <DateTime>
            <Type><Text>Start date</Text></Type>
            <ExactDateTime>2007-04-04T07:00:00Z</ExactDateTime>
          </DateTime>
          <Description>
            <Text>Aortic valve disorders</Text>
            <Code>
              <Value>410.10</Value>
              <CodingSystem>ICD9</CodingSystem>
              <Version>2004</Version>
            </Code>
          </Description>
          <Status><Text>Active</Text></Status>
        </Problem>
      </Problems>
      </Body>
    </ContinuityOfCareRecord>\';
    $responseEntry = $healthService->sendHealthNotice($subject, $body, "html", $ccr);';
} catch(Zend_Gdata_App_Exception $e) {
  echo 'Error: ' . $e->getMessage();
}

echo '<div class="code"><pre>' . htmlentities($snippet) . '</pre></div>';
eval($snippet);

echo '<div class="data"><pre>';
echo xmlpp($responseEntry->getXML());
echo '</pre></div>';

// =============================================================================
// Revoke the AuthSub session token
// =============================================================================
//$revoked = Zend_Gdata_AuthSub::AuthSubRevokeToken($client->getAuthSubToken(), $client) ? 'yes' : 'no';
//echo '<b>Token revoked</b>: ' . @$revoked;
//unset($_SESSION['sessionToken']);
?>
</body>
</html>

<?php
function getCurrentUrl() {
  $phpRequestUri =
    htmlentities(substr($_SERVER['REQUEST_URI'], 0, strcspn($_SERVER['REQUEST_URI'], "\n\r")), ENT_QUOTES);

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
  return $protocol . $host . $port . $phpRequestUri;
}

function authenticate($singleUseToken=null) {
  $sessionToken = isset($_SESSION['sessionToken']) ? $_SESSION['sessionToken'] : null;

  // If there is no AuthSub session or one-time token waiting for us,
  // redirect the user to Google Health's AuthSub handler to get one.
  if (!$sessionToken && !$singleUseToken) {
    $next = getCurrentUrl();
    $secure = 1;
    $session = 1;
    $authSubHandler = 'https://www.google.com/h9/authsub';
    $permission = 1;  // 1 - allows reading of the profile && posting notices
    $authSubURL =
      Zend_Gdata_AuthSub::getAuthSubTokenUri($next, SCOPE, $secure, $session,
                                             $authSubHandler);
    $authSubURL .= '&permission=' . $permission;
    echo '<a href="' . $authSubURL . '">Link your Google Health Account</a>';
    exit();
  }

  $client = new Zend_Gdata_HttpClient();
  $client->setAuthSubPrivateKeyFile(HEALTH_PRIVATE_KEY, null, true);

  // Convert an AuthSub one-time token into a session token if needed
  if ($singleUseToken && !$sessionToken) {
    $sessionToken =
      Zend_Gdata_AuthSub::getAuthSubSessionToken($singleUseToken, $client);
    $_SESSION['sessionToken'] = $sessionToken;
  }
  $client->setAuthSubToken($sessionToken);
  return $client;
}

function getTokenInfo($client) {
  $sessionToken = $client->getAuthSubToken();
  return Zend_Gdata_AuthSub::getAuthSubTokenInfo($sessionToken, $client);
}

function revokeToken($client) {
  $sessionToken = $client->getAuthSubToken();
  return Zend_Gdata_AuthSub::AuthSubRevokeToken($sessionToken, $client);
}

/** Prettifies an XML string into a human-readable and indented work of art
 *  @param string $xml The XML as a string
 *  @param boolean $html_output True if the output should be escaped (for use in HTML)
 */
function xmlpp($xml, $html_output=true) {
  $xml_obj = new SimpleXMLElement($xml);
  $level = 4;
  $indent = 0; // current indentation level
  $pretty = array();

  // get an array containing each XML element
  $xml = explode("\n", preg_replace('/>\s*</', ">\n<", $xml_obj->asXML()));

  // shift off opening XML tag if present
  if (count($xml) && preg_match('/^<\?\s*xml/', $xml[0])) {
    $pretty[] = array_shift($xml);
  }

  foreach ($xml as $el) {
    if (preg_match('/^<([\w])+[^>\/]*>$/U', $el)) {
      // opening tag, increase indent
      $pretty[] = str_repeat(' ', $indent) . $el;
      $indent += $level;
    } else {
      if (preg_match('/^<\/.+>$/', $el)) {
        $indent -= $level;  // closing tag, decrease indent
      }
      if ($indent < 0) {
        $indent += $level;
      }
      $pretty[] = str_repeat(' ', $indent) . $el;
    }
  }
  $xml = implode("\n", $pretty);
  return ($html_output) ? htmlentities($xml) : $xml;
}
?>
