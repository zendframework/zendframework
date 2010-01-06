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
 * PHP sample code for the Google Calendar data API.  Utilizes the
 * Zend Framework Gdata components to communicate with the Google API.
 *
 * Requires the Zend Framework Gdata components and PHP >= 5.1.4
 *
 * You can run this sample both from the command line (CLI) and also
 * from a web browser.  Run this script without any command line options to
 * see usage, eg:
 *     /usr/bin/env php Gapps.php
 *
 * More information on the Command Line Interface is available at:
 *     http://www.php.net/features.commandline
 *
 * When running this code from a web browser, be sure to fill in your
 * Google Apps credentials below and choose a password for authentication
 * via the web browser.
 *
 * Since this is a demo, only minimal error handling and input validation
 * are performed. THIS CODE IS FOR DEMONSTRATION PURPOSES ONLY. NOT TO BE
 * USED IN A PRODUCTION ENVIRONMENT.
 *
 * NOTE: You must ensure that Zend Framework is in your PHP include
 * path.  You can do this via php.ini settings, or by modifying the
 * argument to set_include_path in the code below.
 */

// ************************ BEGIN WWW CONFIGURATION ************************

/**
 * Google Apps username. This is the username (without domain) used
 * to administer your Google Apps account. This value is only
 * used when accessing this demo on a web server.
 *
 * For example, if you login to Google Apps as 'foo@bar.com.inavlid',
 * your username is 'foo'.
 */
define('GAPPS_USERNAME', 'username');

/**
 * Google Apps domain. This is the domain associated with your
 * Google Apps account. This value is only used when accessing this demo
 * on a web server.
 *
 * For example, if you login to Google Apps as foo@bar.com.inavlid,
 * your domain is 'bar.com.invalid'.
 */
define('GAPPS_DOMAIN', 'example.com.invalid');

/**
 * Google Apps password. This is the password associated with the above
 * username. This value is only used when accessing this demo on a
 * web server.
 */
define('GAPPS_PASSWORD', 'your password here');

/**
 * Login password. This password is used to protect your account from
 * unauthorized access when running this demo on a web server.
 *
 * If this field is blank, all access will be denied. A blank password
 * field is not the same as no password (which is disallowed for
 * security reasons).
 *
 * NOTE: While we could technically just ask the user for their Google Apps
 *       credentials, the ClientLogin API is not intended for direct use by
 *       web applications. If you are the only user of the application, this
 *       is fine--- but you should not ask other users to enter their
 *       credentials via your web application.
 */
define('LOGIN_PASSWORD', '');

// ************************* END WWW CONFIGURATION *************************

/**
 * @see Zend_Loader
 */
require_once 'Zend/Loader.php';

/**
 * @see Zend_Gdata
 */
Zend_Loader::loadClass('Zend_Gdata');

/**
 * @see Zend_Gdata_ClientLogin
 */
Zend_Loader::loadClass('Zend_Gdata_ClientLogin');

/**
 * @see Zend_Gdata_Gapps
 */
Zend_Loader::loadClass('Zend_Gdata_Gapps');

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
  $service = Zend_Gdata_Gapps::AUTH_SERVICE_NAME;
  $client = Zend_Gdata_ClientLogin::getHttpClient($user, $pass, $service);
  return $client;
}

/**
 * Creates a new user for the current domain. The user will be created
 * without admin privileges.
 *
 * @param  Zend_Gdata_Gapps $gapps      The service object to use for communicating with the Google
 *                                      Apps server.
 * @param  boolean          $html       True if output should be formatted for display in a web browser.
 * @param  string           $username   The desired username for the user.
 * @param  string           $givenName  The given name for the user.
 * @param  string           $familyName The family name for the user.
 * @param  string           $password   The plaintext password for the user.
 * @return void
 */
function createUser($gapps, $html, $username, $givenName, $familyName,
        $password)
{
    if ($html) {echo "<h2>Create User</h2>\n";}
    $gapps->createUser($username, $givenName, $familyName,
        $password);
    if ($html) {echo "<p>Done.</p>\n";}
}

/**
 * Retrieves a user for the current domain by username. Information about
 * that user is then output.
 *
 * @param  Zend_Gdata_Gapps $gapps    The service object to use for communicating with the Google Apps server.
 * @param  boolean          $html     True if output should be formatted for display in a web browser.
 * @param  string           $username The desired username for the user.
 * @return void
 */
function retrieveUser($gapps, $html, $username)
{
    if ($html) {echo "<h2>User Information</h2>\n";}

    $user = $gapps->retrieveUser($username);

    if ($html) {echo '<p>';}

    if ($user !== null) {
        echo '             Username: ' . $user->login->username;
        if ($html) {echo '<br />';}
        echo "\n";

        echo '           Given Name: ';
        if ($html) {
            echo htmlspecialchars($user->name->givenName);
        } else {
            echo $user->name->givenName;
        }
        if ($html) {echo '<br />';}
        echo "\n";

        echo '          Family Name: ';
        if ($html) {
            echo htmlspecialchars($user->name->familyName);
        } else {
            echo $user->name->familyName;
        }
        if ($html) {echo '<br />';}
        echo "\n";

        echo '            Suspended: ' . ($user->login->suspended ? 'Yes' : 'No');
        if ($html) {echo '<br />';}
        echo "\n";

        echo '                Admin: ' . ($user->login->admin ? 'Yes' : 'No');
        if ($html) {echo '<br />';}
        echo "\n";

        echo ' Must Change Password: ' .
            ($user->login->changePasswordAtNextLogin ? 'Yes' : 'No');
        if ($html) {echo '<br />';}
        echo "\n";

        echo '  Has Agreed To Terms: ' .
            ($user->login->agreedToTerms ? 'Yes' : 'No');

    } else {
        echo 'Error: Specified user not found.';
    }
    if ($html) {echo '</p>';}
    echo "\n";
}

/**
 * Retrieves the list of users for the current domain and outputs
 * that list.
 *
 * @param  Zend_Gdata_Gapps $gapps The service object to use for communicating with the Google Apps server.
 * @param  boolean          $html  True if output should be formatted for display in a web browser.
 * @return void
 */
function retrieveAllUsers($gapps, $html)
{
    if ($html) {echo "<h2>Registered Users</h2>\n";}

    $feed = $gapps->retrieveAllUsers();

    if ($html) {echo "<ul>\n";}

    foreach ($feed as $user) {
        if ($html) {
            echo "  <li>";
        } else {
            echo "  * ";
        }
        echo $user->login->username . ' (';
        if ($html) {
            echo htmlspecialchars($user->name->givenName . ' ' .
                $user->name->familyName);
        } else {
            echo $user->name->givenName . ' ' . $user->name->familyName;
        }
        echo ')';
        if ($html) {echo '</li>';}
        echo "\n";
    }
    if ($html) {echo "</ul>\n";}
}

/**
 * Change the name for an existing user.
 *
 * @param  Zend_Gdata_Gapps $gapps         The service object to use for communicating with the Google
 *                                         Apps server.
 * @param  boolean          $html          True if output should be formatted for display in a web browser.
 * @param  string           $username      The username which should be updated
 * @param  string           $newGivenName  The new given name for the user.
 * @param  string           $newFamilyName The new family name for the user.
 * @return void
 */
function updateUserName($gapps, $html, $username, $newGivenName, $newFamilyName)
{
    if ($html) {echo "<h2>Update User Name</h2>\n";}

    $user = $gapps->retrieveUser($username);

    if ($user !== null) {
        $user->name->givenName = $newGivenName;
        $user->name->familyName = $newFamilyName;
        $user->save();
    } else {
        if ($html) {echo '<p>';}
        echo 'Error: Specified user not found.';
        if ($html) {echo '</p>';}
        echo "\n";
    }

    if ($html) {echo "<p>Done.</p>\n";}
}

/**
 * Change the password for an existing user.
 *
 * @param  Zend_Gdata_Gapps $gapps       The service object to use for communicating with the Google
 *                                       Apps server.
 * @param  boolean          $html        True if output should be formatted for display in a web browser.
 * @param  string           $username    The username which should be updated
 * @param  string           $newPassword The new password for the user.
 * @return void
 */
function updateUserPassword($gapps, $html, $username, $newPassword)
{
    if ($html) {echo "<h2>Update User Password</h2>\n";}

    $user = $gapps->retrieveUser($username);

    if ($user !== null) {
        $user->login->password = $newPassword;
        $user->save();
    } else {
        if ($html) {echo '<p>';}
        echo 'Error: Specified user not found.';
        if ($html) {echo '</p>';}
        echo "\n";
    }

    if ($html) {echo "<p>Done.</p>\n";}
}

/**
 * Suspend a given user. The user will not be able to login until restored.
 *
 * @param  Zend_Gdata_Gapps $gapps    The service object to use for communicating with the Google
 *                                    Apps server.
 * @param  boolean          $html     True if output should be formatted for display in a web browser.
 * @param  string           $username The username which should be updated.
 * @return void
 */
function suspendUser($gapps, $html, $username)
{
    if ($html) {echo "<h2>Suspend User</h2>\n";}

    $user = $gapps->retrieveUser($username);

    if ($user !== null) {
        $user->login->suspended = true;
        $user->save();
    } else {
        if ($html) {echo '<p>';}
        echo 'Error: Specified user not found.';
        if ($html) {echo '</p>';}
        echo "\n";
    }

    if ($html) {echo "<p>Done.</p>\n";}
}

/**
 * Restore a given user after being suspended.
 *
 * @param  Zend_Gdata_Gapps $gapps    The service object to use for communicating with the Google
 *                                    Apps server.
 * @param  boolean          $html     True if output should be formatted for display in a web browser.
 * @param  string           $username The username which should be updated.
 * @return void
 */
function restoreUser($gapps, $html, $username)
{
    if ($html) {echo "<h2>Restore User</h2>\n";}

    $user = $gapps->retrieveUser($username);

    if ($user !== null) {
        $user->login->suspended = false;
        $user->save();
    } else {
        if ($html) {echo '<p>';}
        echo 'Error: Specified user not found.';
        if ($html) {echo '</p>';}
        echo "\n";
    }

    if ($html) {echo "<p>Done.</p>\n";}
}

/**
 * Give a user admin rights.
 *
 * @param  Zend_Gdata_Gapps $gapps    The service object to use for communicating with the Google
 *                                    Apps server.
 * @param  boolean          $html     True if output should be formatted for display in a web browser.
 * @param  string           $username The username which should be updated.
 * @return void
 */
function giveUserAdminRights($gapps, $html, $username)
{
    if ($html) {echo "<h2>Grant Administrative Rights</h2>\n";}

    $user = $gapps->retrieveUser($username);

    if ($user !== null) {
        $user->login->admin = true;
        $user->save();
    } else {
        if ($html) {echo '<p>';}
        echo 'Error: Specified user not found.';
        if ($html) {echo '</p>';}
        echo "\n";
    }

    if ($html) {echo "<p>Done.</p>\n";}
}

/**
 * Revoke a user's admin rights.
 *
 * @param  Zend_Gdata_Gapps $gapps    The service object to use for communicating with the Google
 *                                    Apps server.
 * @param  boolean          $html     True if output should be formatted for display in a web browser.
 * @param  string           $username The username which should be updated.
 * @return void
 */
function revokeUserAdminRights($gapps, $html, $username)
{
    if ($html) {echo "<h2>Revoke Administrative Rights</h2>\n";}

    $user = $gapps->retrieveUser($username);

    if ($user !== null) {
        $user->login->admin = false;
        $user->save();
    } else {
        if ($html) {echo '<p>';}
        echo 'Error: Specified user not found.';
        if ($html) {echo '</p>';}
        echo "\n";
    }

    if ($html) {echo "<p>Done.</p>\n";}
}

/**
 * Force a user to change their password at next login.
 *
 * @param  Zend_Gdata_Gapps $gapps    The service object to use for communicating with the Google
 *                                    Apps server.
 * @param  boolean          $html     True if output should be formatted for display in a web browser.
 * @param  string           $username The username which should be updated.
 * @return void
 */
function setUserMustChangePassword($gapps, $html, $username)
{
    if ($html) {echo "<h2>Force User To Change Password</h2>\n";}

    $user = $gapps->retrieveUser($username);

    if ($user !== null) {
        $user->login->changePasswordAtNextLogin = true;
        $user->save();
    } else {
        if ($html) {echo '<p>';}
        echo 'Error: Specified user not found.';
        if ($html) {echo '</p>';}
        echo "\n";
    }

    if ($html) {echo "<p>Done.</p>\n";}
}

/**
 * Undo forcing a user to change their password at next login.
 *
 * @param  Zend_Gdata_Gapps $gapps    The service object to use for communicating with the Google
 *                                    Apps server.
 * @param  boolean          $html     True if output should be formatted for display in a web browser.
 * @param  string           $username The username which should be updated.
 * @return void
 */
function clearUserMustChangePassword($gapps, $html, $username)
{
    if ($html) {echo "<h2>Undo Force User To Change Password</h2>\n";}

    $user = $gapps->retrieveUser($username);

    if ($user !== null) {
        $user->login->changePasswordAtNextLogin = false;
        $user->save();
    } else {
        if ($html) {echo '<p>';}
        echo 'Error: Specified user not found.';
        if ($html) {echo '</p>';}
        echo "\n";
    }

    if ($html) {echo "<p>Done.</p>\n";}
}

/**
 * Delete the user who owns a given username.
 *
 * @param  Zend_Gdata_Gapps $gapps    The service object to use for communicating with the Google
 *                                    Apps server.
 * @param  boolean          $html     True if output should be formatted for display in a web browser.
 * @param  string           $username The username which should be deleted.
 * @return void
 */
function deleteUser($gapps, $html, $username)
{
    if ($html) {echo "<h2>Delete User</h2>\n";}

    $gapps->deleteUser($username);

    if ($html) {echo "<p>Done.</p>\n";}
}

/**
 * Create a new nickname.
 *
 * @param  Zend_Gdata_Gapps $gapps    The service object to use for communicating with the Google
 *                                    Apps server.
 * @param  boolean          $html     True if output should be formatted for display in a web browser.
 * @param  string           $username The username to which the nickname should be assigned.
 * @param  string           $nickname The name of the nickname to be created.
 * @return void
 */
function createNickname($gapps, $html, $username, $nickname)
{
    if ($html) {echo "<h2>Create Nickname</h2>\n";}

    $gapps->createNickname($username, $nickname);

    if ($html) {echo "<p>Done.</p>\n";}
}

/**
 * Retrieve a specified nickname and output its ownership information.
 *
 * @param  Zend_Gdata_Gapps $gapps    The service object to use for communicating with the Google
 *                                    Apps server.
 * @param  boolean          $html     True if output should be formatted for display in a web browser.
 * @param  string           $nickname The name of the nickname to be retrieved.
 * @return void
 */
function retrieveNickname($gapps, $html, $nickname)
{
    if ($html) {echo "<h2>Nickname Information</h2>\n";}

    $nickname = $gapps->retrieveNickname($nickname);

    if ($html) {echo '<p>';}

    if ($nickname !== null) {
        echo ' Nickname: ' . $nickname->nickname->name;
        if ($html) {echo '<br />';}
        echo "\n";

        echo '    Owner: ' . $nickname->login->username;
    } else {
        echo 'Error: Specified nickname not found.';
    }
    if ($html) {echo '</p>';}
    echo "\n";
}

/**
 * Outputs all nicknames owned by a specific username.
 *
 * @param  Zend_Gdata_Gapps $gapps    The service object to use for communicating with the Google
 *                                    Apps server.
 * @param  boolean          $html     True if output should be formatted for display in a web browser.
 * @param  string           $username The username whose nicknames should be displayed.
 * @return void
 */
function retrieveNicknames($gapps, $html, $username)
{
    if ($html) {echo "<h2>Registered Nicknames For {$username}</h2>\n";}

    $feed = $gapps->retrieveNicknames($username);

    if ($html) {echo "<ul>\n";}

    foreach ($feed as $nickname) {
        if ($html) {
            echo "  <li>";
        } else {
            echo "  * ";
        }
        echo $nickname->nickname->name;
        if ($html) {echo '</li>';}
        echo "\n";
    }
    if ($html) {echo "</ul>\n";}
}


/**
 * Retrieves the list of nicknames for the current domain and outputs
 * that list.
 *
 * @param  Zend_Gdata_Gapps $gapps The service object to use for communicating with the Google
 *                                 Apps server.
 * @param  boolean          $html  True if output should be formatted for display in a web browser.
 * @return void
 */
function retrieveAllNicknames($gapps, $html)
{
    if ($html) {echo "<h2>Registered Nicknames</h2>\n";}

    $feed = $gapps->retrieveAllNicknames();

    if ($html) {echo "<ul>\n";}

    foreach ($feed as $nickname) {
        if ($html) {
            echo "  <li>";
        } else {
            echo "  * ";
        }
        echo $nickname->nickname->name . ' => ' . $nickname->login->username;
        if ($html) {echo '</li>';}
        echo "\n";
    }
    if ($html) {echo "</ul>\n";}
}

/**
 * Delete's a specific nickname from the current domain.
 *
 * @param  Zend_Gdata_Gapps $gapps    The service object to use for communicating with the Google
 *                                    Apps server.
 * @param  boolean          $html     True if output should be formatted for display in a web browser.
 * @param  string           $nickname The nickname that should be deleted.
 * @return void
 */
function deleteNickname($gapps, $html, $nickname)
{
    if ($html) {echo "<h2>Delete Nickname</h2>\n";}

    $gapps->deleteNickname($nickname);

    if ($html) {echo "<p>Done.</p>\n";}

}

/**
 * Create a new email list.
 *
 * @param  Zend_Gdata_Gapps $gapps     The service object to use for communicating with the Google
 *                                     Apps server.
 * @param  boolean          $html      True if output should be formatted for display in a web browser.
 * @param  string           $emailList The name of the email list to be created.
 * @return void
 */
function createEmailList($gapps, $html, $emailList)
{
    if ($html) {echo "<h2>Create Email List</h2>\n";}

    $gapps->createEmailList($emailList);

    if ($html) {echo "<p>Done.</p>\n";}
}

/**
 * Outputs the list of email lists to which the specified address is
 * subscribed.
 *
 * @param  Zend_Gdata_Gapps $gapps     The service object to use for communicating with the Google
 *                                     Apps server.
 * @param  boolean          $html      True if output should be formatted for display in a web browser.
 * @param  string           $recipient The email address of the recipient whose subscriptions should
 *                                     be retrieved. Only a username is required if the recipient is a
 *                                     member of the current domain.
 * @return void
 */
function retrieveEmailLists($gapps, $html, $recipient)
{
    if ($html) {echo "<h2>Email List Subscriptions For {$recipient}</h2>\n";}

    $feed = $gapps->retrieveEmailLists($recipient);

    if ($html) {echo "<ul>\n";}

    foreach ($feed as $list) {
        if ($html) {
            echo "  <li>";
        } else {
            echo "  * ";
        }
        echo $list->emailList->name;
        if ($html) {echo '</li>';}
        echo "\n";
    }
    if ($html) {echo "</ul>\n";}
}

/**
 * Outputs the list of all email lists on the current domain.
 *
 * @param  Zend_Gdata_Gapps $gapps The service object to use for communicating with the Google
 *                                 Apps server.
 * @param  boolean          $html  True if output should be formatted for display in a web browser.
 * @return void
 */
function retrieveAllEmailLists($gapps, $html)
{
    if ($html) {echo "<h2>Registered Email Lists</h2>\n";}

    $feed = $gapps->retrieveAllEmailLists();

    if ($html) {echo "<ul>\n";}

    foreach ($feed as $list) {
        if ($html) {
            echo "  <li>";
        } else {
            echo "  * ";
        }
        echo $list->emailList->name;
        if ($html) {echo '</li>';}
        echo "\n";
    }
    if ($html) {echo "</ul>\n";}
}

/**
 * Delete's a specific email list from the current domain.
 *
 * @param  Zend_Gdata_Gapps $gapps     The service object to use for communicating with the Google
 *                                     Apps server.
 * @param  boolean          $html      True if output should be formatted for display in a web browser.
 * @param  string           $emailList The email list that should be deleted.
 * @return void
 */
function deleteEmailList($gapps, $html, $emailList)
{
    if ($html) {echo "<h2>Delete Email List</h2>\n";}

    $gapps->deleteEmailList($emailList);

    if ($html) {echo "<p>Done.</p>\n";}
}

/**
 * Add a recipient to an existing email list.
 *
 * @param  Zend_Gdata_Gapps $gapps            The service object to use for communicating with the
 *                                            Google Apps server.
 * @param  boolean          $html             True if output should be formatted for display in a
 *                                            web browser.
 * @param  string           $recipientAddress The address of the recipient who should be added.
 * @param  string           $emailList        The name of the email address the recipient be added to.
 * @return void
 */
function addRecipientToEmailList($gapps, $html, $recipientAddress,
        $emailList)
{
    if ($html) {echo "<h2>Subscribe Recipient</h2>\n";}

    $gapps->addRecipientToEmailList($recipientAddress, $emailList);

    if ($html) {echo "<p>Done.</p>\n";}
}

/**
 * Outputs the list of all recipients for a given email list.
 *
 * @param  Zend_Gdata_Gapps $gapps     The service object to use for communicating with the Google
 *                                     Apps server.
 * @param  boolean          $html      True if output should be formatted for display in a web browser.
 * @param  string           $emailList The email list whose recipients should be output.
 * @return void
 */
function retrieveAllRecipients($gapps, $html, $emailList)
{
    if ($html) {echo "<h2>Email List Recipients For {$emailList}</h2>\n";}

    $feed = $gapps->retrieveAllRecipients($emailList);

    if ($html) {echo "<ul>\n";}

    foreach ($feed as $recipient) {
        if ($html) {
            echo "  <li>";
        } else {
            echo "  * ";
        }
        echo $recipient->who->email;
        if ($html) {echo '</li>';}
        echo "\n";
    }
    if ($html) {echo "</ul>\n";}
}

/**
 * Remove an existing recipient from an email list.
 *
 * @param  Zend_Gdata_Gapps $gapps            The service object to use for communicating with the
 *                                            Google Apps server.
 * @param  boolean          $html             True if output should be formatted for display in a
 *                                            web browser.
 * @param  string           $recipientAddress The address of the recipient who should be removed.
 * @param  string           $emailList        The email list from which the recipient should be removed.
 * @return void
 */
function removeRecipientFromEmailList($gapps, $html, $recipientAddress,
        $emailList)
{
    if ($html) {echo "<h2>Unsubscribe Recipient</h2>\n";}

    $gapps->removeRecipientFromEmailList($recipientAddress, $emailList);

    if ($html) {echo "<p>Done.</p>\n";}

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
        "createUser\n" .
        "retrieveUser\n" .
        "retrieveAllUsers\n" .
        "updateUserName\n" .
        "updateUserPassword\n" .
        "suspendUser\n" .
        "restoreUser\n" .
        "giveUserAdminRights\n" .
        "revokeUserAdminRights\n" .
        "setUserMustChangePassword\n" .
        "clearUserMustChangePassword\n" .
        "deleteUser\n" .
        "createNickname\n" .
        "retrieveNickname\n" .
        "retrieveNicknames\n" .
        "retrieveAllNicknames\n" .
        "deleteNickname\n" .
        "createEmailList\n" .
        "retrieveEmailLists\n" .
        "retrieveAllEmailLists\n" .
        "deleteEmailList\n" .
        "addRecipientToEmailList\n" .
        "retrieveAllRecipients\n" .
        "removeRecipientFromEmailList\n";
}

/**
 * Parse command line arguments and execute appropriate function when
 * running from the command line.
 *
 * If no arguments are provided, usage information will be provided.
 *
 * @param  array   $argv    The array of command line arguments provided by PHP.
 *                 $argv[0] should be the current executable name or '-' if not available.
 * @param  integer $argc    The size of $argv.
 * @return void
 */
function runCLIVersion($argv, $argc)
{
    if (isset($argc) && $argc >= 2) {
        # Prepare a server connection
        if ($argc >= 5) {
            try {
                $client = getClientLoginHttpClient($argv[2] . '@' . $argv[3], $argv[4]);
                $gapps = new Zend_Gdata_Gapps($client, $argv[3]);
            } catch (Zend_Gdata_App_AuthException $e) {
                echo "Error: Unable to authenticate. Please check your credentials.\n";
                exit(1);
            }
        }

        # Dispatch arguments to the desired method
        switch ($argv[1]) {
            case 'createUser':
                if ($argc == 9) {
                    createUser($gapps, false, $argv[5], $argv[6], $argv[7], $argv[8]);
                } else {
                    echo "Usage: php {$argv[0]} {$argv[1]} <username> <domain> <password> " .
                        "<user's username> <given name> <family name> <user's password>\n\n";
                    echo "This creates a new user with the given username.\n";
                    echo "EXAMPLE: php {$argv[0]} {$argv[1]} <username> <domain> <password> " .
                        "jdoe John Doe p4ssw0rd\n";
                }
                break;
            case 'retrieveUser':
                if ($argc == 6) {
                    retrieveUser($gapps, false, $argv[5]);
                } else {
                    echo "Usage: php {$argv[0]} {$argv[1]} <username> <domain> <password> " .
                        "<user's username>\n\n";
                    echo "This retrieves the user with the specified " .
                        "username and displays information about that user.\n";
                    echo "EXAMPLE: php {$argv[0]} {$argv[1]} <username> <domain> <password> " .
                        "jdoe\n";
                }
                break;
            case 'retrieveAllUsers':
                if ($argc == 5) {
                    retrieveAllUsers($gapps, false);
                } else {
                    echo "Usage: php {$argv[0]} {$argv[1]} <username> <domain> <password> " .
                        "\n\n";
                    echo "This lists all users on the current domain.\n";
                    echo "EXAMPLE: php {$argv[0]} {$argv[1]} <username> <domain> <password>\n";
                }
                break;
            case 'updateUserName':
                if ($argc == 8) {
                    updateUserName($gapps, false, $argv[5], $argv[6], $argv[7]);
                } else {
                    echo "Usage: php {$argv[0]} {$argv[1]} <username> <domain> <password> " .
                        "<user's username> <new given name> <new family name>\n\n";
                    echo "Renames an existing user.\n";
                    echo "EXAMPLE: php {$argv[0]} {$argv[1]} <username> <domain> <password> " .
                        "jdoe Jane Doe\n";
                }
                break;
            case 'updateUserPassword':
                if ($argc == 7) {
                    updateUserPassword($gapps, false, $argv[5], $argv[6]);
                } else {
                    echo "Usage: php {$argv[0]} {$argv[1]} <username> <domain> <password> " .
                        "<user's username> <new user password>\n\n";
                    echo "Changes the password for an existing user.\n";
                    echo "EXAMPLE: php {$argv[0]} {$argv[1]} <username> <domain> <password> " .
                        "jdoe password1\n";
                }
                break;
            case 'suspendUser':
                if ($argc == 6) {
                    suspendUser($gapps, false, $argv[5]);
                } else {
                    echo "Usage: php {$argv[0]} {$argv[1]} <username> <domain> <password> " .
                        "<user's username>\n\n";
                    echo "This suspends the given user.\n";
                    echo "EXAMPLE: php {$argv[0]} {$argv[1]} <username> <domain> <password> " .
                        "jdoe\n";
                }
                break;
            case 'restoreUser':
                if ($argc == 6) {
                    restoreUser($gapps, false, $argv[5]);
                } else {
                    echo "Usage: php {$argv[0]} {$argv[1]} <username> <domain> <password> " .
                        "<user's username>\n\n";
                    echo "This restores the given user after being suspended.\n";
                    echo "EXAMPLE: php {$argv[0]} {$argv[1]} <username> <domain> <password> " .
                        "jdoe\n";
                }
                break;
            case 'giveUserAdminRights':
                if ($argc == 6) {
                    giveUserAdminRights($gapps, false, $argv[5]);
                } else {
                    echo "Usage: php {$argv[0]} {$argv[1]} <username> <domain> <password> " .
                        "<user's username>\n\n";
                    echo "Give a user admin rights for this domain.\n";
                    echo "EXAMPLE: php {$argv[0]} {$argv[1]} <username> <domain> <password> " .
                        "jdoe\n";
                }
                break;
            case 'revokeUserAdminRights':
                if ($argc == 6) {
                    revokeUserAdminRights($gapps, false, $argv[5]);
                } else {
                    echo "Usage: php {$argv[0]} {$argv[1]} <username> <domain> <password> " .
                        "<user's username>\n\n";
                    echo "Remove a user's admin rights for this domain.\n";
                    echo "EXAMPLE: php {$argv[0]} {$argv[1]} <username> <domain> <password> " .
                        "jdoe\n";
                }
                break;
            case 'setUserMustChangePassword':
                if ($argc == 6) {
                    setUserMustChangePassword($gapps, false, $argv[5]);
                } else {
                    echo "Usage: php {$argv[0]} {$argv[1]} <username> <domain> <password> " .
                        "<user's username>\n\n";
                    echo "Force a user to change their password at next login.\n";
                    echo "EXAMPLE: php {$argv[0]} {$argv[1]} <username> <domain> <password> " .
                        "jdoe\n";
                }
                break;
            case 'clearUserMustChangePassword':
                if ($argc == 6) {
                    clearUserMustChangePassword($gapps, false, $argv[5]);
                } else {
                    echo "Usage: php {$argv[0]} {$argv[1]} <username> <domain> <password> " .
                        "<user's username>\n\n";
                    echo "Clear the flag indicating that a user must change " .
                        "their password at next login.\n";
                    echo "EXAMPLE: php {$argv[0]} {$argv[1]} <username> <domain> <password> " .
                        "jdoe\n";
                }
                break;
            case 'deleteUser':
                if ($argc == 6) {
                    deleteUser($gapps, false, $argv[5]);
                } else {
                    echo "Usage: php {$argv[0]} {$argv[1]} <username> <domain> <password> " .
                        "<user's username>\n\n";
                    echo "Delete the user who owns a given username.\n";
                    echo "EXAMPLE: php {$argv[0]} {$argv[1]} <username> <domain> <password> " .
                        "jdoe\n";
                }
                break;
            case 'createNickname':
                if ($argc == 7) {
                    createNickname($gapps, false, $argv[5], $argv[6]);
                } else {
                    echo "Usage: php {$argv[0]} {$argv[1]} <username> <domain> <password> " .
                        "<user's username> <nickname>\n\n";
                    echo "Create a new nickname for the specified user.\n";
                    echo "EXAMPLE: php {$argv[0]} {$argv[1]} <username> <domain> <password> " .
                        "jdoe johnny\n";
                }
                break;
            case 'retrieveNickname':
                if ($argc == 6) {
                    retrieveNickname($gapps, false, $argv[5]);
                } else {
                    echo "Usage: php {$argv[0]} {$argv[1]} <username> <domain> <password> " .
                        "<nickname>\n\n";
                    echo "Retrieve a nickname and display its ownership " .
                        "information.\n";
                    echo "EXAMPLE: php {$argv[0]} {$argv[1]} <username> <domain> <password> " .
                        "johnny\n";
                }
                break;
            case 'retrieveNicknames':
                if ($argc == 6) {
                    retrieveNicknames($gapps, false, $argv[5]);
                } else {
                    echo "Usage: php {$argv[0]} {$argv[1]} <username> <domain> <password> " .
                        "<user's username>\n\n";
                    echo "Output all nicknames owned by a specific username.\n";
                    echo "EXAMPLE: php {$argv[0]} {$argv[1]} <username> <domain> <password> " .
                        "jdoe\n";
                }
                break;
            case 'retrieveAllNicknames':
                if ($argc == 5) {
                    retrieveAllNicknames($gapps, false);
                } else {
                    echo "Usage: php {$argv[0]} {$argv[1]} <username> <domain> <password> " .
                        "\n\n";
                    echo "Output all registered nicknames on the system.\n";
                    echo "EXAMPLE: php {$argv[0]} {$argv[1]} <username> <domain> <password> " .
                        "\n";
                }
                break;
            case 'deleteNickname':
                if ($argc == 6) {
                    deleteNickname($gapps, false, $argv[5]);
                } else {
                    echo "Usage: php {$argv[0]} {$argv[1]} <username> <domain> <password> " .
                        "<nickname>\n\n";
                    echo "Delete a specific nickname.\n";
                    echo "EXAMPLE: php {$argv[0]} {$argv[1]} <username> <domain> <password> " .
                        "johnny\n";
                }
                break;
            case 'createEmailList':
                if ($argc == 6) {
                    createEmailList($gapps, false, $argv[5]);
                } else {
                    echo "Usage: php {$argv[0]} {$argv[1]} <username> <domain> <password> " .
                        "<email list>\n\n";
                    echo "Create a new email list with the specified name.\n";
                    echo "EXAMPLE: php {$argv[0]} {$argv[1]} <username> <domain> <password> " .
                        "friends\n";
                }
                break;
            case 'retrieveEmailLists':
                if ($argc == 6) {
                    retrieveEmailLists($gapps, false, $argv[5]);
                } else {
                    echo "Usage: php {$argv[0]} {$argv[1]} <username> <domain> <password> " .
                        "<recipient>\n\n";
                    echo "Retrieve all email lists to which the specified " .
                        "address is subscribed.\n";
                    echo "EXAMPLE: php {$argv[0]} {$argv[1]} <username> <domain> <password> " .
                        "johnny@somewhere.com.invalid\n";
                }
                break;
            case 'retrieveAllEmailLists':
                if ($argc == 5) {
                    retrieveAllEmailLists($gapps, false);
                } else {
                    echo "Usage: php {$argv[0]} {$argv[1]} <username> <domain> <password> " .
                        "\n\n";
                    echo "Retrieve a list of all email lists on the current " .
                        "domain.\n";
                    echo "EXAMPLE: php {$argv[0]} {$argv[1]} <username> <domain> <password> " .
                        "\n";
                }
                break;
            case 'deleteEmailList':
                if ($argc == 6) {
                    deleteEmailList($gapps, false, $argv[5]);
                } else {
                    echo "Usage: php {$argv[0]} {$argv[1]} <username> <domain> <password> " .
                        "<email list>\n\n";
                    echo "Delete a specified email list.\n";
                    echo "EXAMPLE: php {$argv[0]} {$argv[1]} <username> <domain> <password> " .
                        "friends\n";
                }
                break;
            case 'addRecipientToEmailList':
                if ($argc == 7) {
                    addRecipientToEmailList($gapps, false, $argv[5], $argv[6]);
                } else {
                    echo "Usage: php {$argv[0]} {$argv[1]} <username> <domain> <password> " .
                        "<recipient> <email list>\n\n";
                    echo "Add a recipient to an existing email list.\n";
                    echo "EXAMPLE: php {$argv[0]} {$argv[1]} <username> <domain> <password> " .
                        "johnny@somewhere.com.invalid friends\n";
                }
                break;
            case 'retrieveAllRecipients':
                if ($argc == 6) {
                    retrieveAllRecipients($gapps, false, $argv[5]);
                } else {
                    echo "Usage: php {$argv[0]} {$argv[1]} <username> <domain> <password> " .
                        "<email list>\n\n";
                    echo "Retrieve all recipients for an existing email list.\n";
                    echo "EXAMPLE: php {$argv[0]} {$argv[1]} <username> <domain> <password> " .
                        "friends\n";
                }
                break;
            case 'removeRecipientFromEmailList':
                if ($argc == 7) {
                    removeRecipientFromEmailList($gapps, false, $argv[5], $argv[6]);
                } else {
                    echo "Usage: php {$argv[0]} {$argv[1]} <username> <domain> <password> " .
                        "<recipient> <email list>\n\n";
                    echo "Remove an existing recipient from an email list.\n";
                    echo "EXAMPLE: php {$argv[0]} {$argv[1]} <username> <domain> <password> " .
                        "johnny@somewhere.com.invalid friends\n";
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

// ************************ BEGIN WWW SPECIFIC CODE ************************

/**
 * Writes the HTML prologue for this app.
 *
 * NOTE: We would normally keep the HTML/CSS markup separate from the business
 *       logic above, but have decided to include it here for simplicity of
 *       having a single-file sample.
 *
 *
 * @param  boolean $displayMenu (optional) If set to true, a navigation
 *                              menu is displayed at the top of the page. Default is true.
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

    <title>Google Apps Provisioning API Demo</title>

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
    <h1>Google Apps API Demo</h1>
    <?php if ($displayMenu === true) { ?>
        <p><?php echo GAPPS_DOMAIN ?> | <a href="?">Main</a> | <a href="?menu=logout">Logout</a></p>
    <?php } ?>
    <div class="clear"></div>
</div>

<div id="content">
<?php
}

/**
 * Writes the HTML epilogue for this app and exit.
 *
 * @param  boolean $displayBackButton (optional) If true, displays a
 *                                    link to go back at the bottom of the page. Defaults to false.
 * @return void
 */
function endHTML($displayBackButton = false)
{
    if ($displayBackButton === true) {
        echo '<a href="javascript:history.go(-1)">&larr; Back</a>';
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
        <h3>Google Apps Authentication Failed</h3>
        <p>Authentication with the Google Apps servers failed.</p>
        <p>Please open this file in a text editor and make
            sure your credentials are correct.</p>
    </div>
<?php
}

/**
 * Outputs a request to the user to enter their login password.
 *
 * @param  string $errorText (optional) Error text to be displayed next to the login form.
 * @return void
 */
function requestUserLogin($errorText = null)
{
?>
    <form method="post" accept-charset="utf-8">
        <h3>Authentication Required</h3>
        <?php
            if ($errorText !== null) {
                echo '<span class="error">' . $errorText . "</span>\n";
            }
        ?>
        <p>Please enter your login password to continue.</p>
        <p><label for="password">Password: </label>
             <input type="password" name="password" value="" /></p>
        <p><strong>Notice:</strong> This application is for demonstration
            purposes only. Not for use in a production environment.</p>
        <p><input type="submit" value="Continue &rarr;"></p>
    </form>
<?php
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

<p>Welcome to the Google Apps Provisioning API demo page. Please select
    from one of the following three options to see a list of commands.</p>

    <ul id="menuSelect">
        <li><a class="menuSelect" href="?menu=user">User Maintenance Menu</a></li>
        <li><a class="menuSelect" href="?menu=nickname">Nickname Maintenance Menu</a></li>
        <li><a class="menuSelect" href="?menu=emailList">Email List Maintenance Menu</a></li>
    </ul>

<p>Tip: You can also run this demo from the command line if your system
    has PHP CLI support enabled.</p>
<?php
}

/**
 * Display the user maintenance menu for running in a web browser.
 *
 * @return void
 */
function displayUserMenu()
{
?>
<h2>User Maintenance Menu</h2>

<form method="post" accept-charset="utf-8">
    <h3>Create User</h3>
    <p>Create a new user with the given properties.</p>
    <p>
        <input type="hidden" name="command" value="createUser" />
        <label for="user">Username: </label>
        <input type="text" name="user" value="" /><br />
        <label for="givenName">Given Name: </label>
        <input type="text" name="givenName" value="" /><br />
        <label for="familyName">Family Name: </label>
        <input type="text" name="familyName" value="" /><br />
        <label for="pass">Password: </label>
        <input type="password" name="pass" value="" />
    </p>

    <p><input type="submit" value="Create User &rarr;"></p>
</form>
<form method="get" accept-charset="utf-8">
    <h3>Retrieve User</h3>
    <p>Retrieve the information for an existing user.</p>
    <p>
        <input type="hidden" name="command" value="retrieveUser" />
        <label for="user">Username: </label>
        <input type="text" name="user" value="" /><br />
    </p>

    <p><input type="submit" value="Retrieve User &rarr;"></p>
</form>
<form method="get" accept-charset="utf-8">
    <h3>Retrieve All Users</h3>
    <p>Retrieve the list of all users on the current domain.</p>
    <p>
        <input type="hidden" name="command" value="retrieveAllUsers" />
    </p>

    <p><input type="submit" value="Retrieve Users &rarr;"></p>
</form>
<form method="post" accept-charset="utf-8">
    <h3>Update Name</h3>
    <p>Update the name for an existing user.</p>
    <p>
        <input type="hidden" name="command" value="updateUserName" />
        <label for="user">Username: </label>
        <input type="text" name="user" value="" /><br />
        <label for="givenName">New Given Name: </label>
        <input type="text" name="givenName" value="" /><br />
        <label for="familyName">New Family Name: </label>
        <input type="text" name="familyName" value="" /><br />
    </p>

    <p><input type="submit" value="Update User &rarr;"></p>
</form>
<form method="post" accept-charset="utf-8">
    <h3>Update Password</h3>
    <p>Update the password for an existing user.</p>
    <p>
        <input type="hidden" name="command" value="updateUserPassword" />
        <label for="user">Username: </label>
        <input type="text" name="user" value="" /><br />
        <label for="pass">New Password: </label>
        <input type="password" name="pass" value="" /></p>
    </p>

    <p><input type="submit" value="Update User &rarr;"></p>
</form>
<form method="post" accept-charset="utf-8">
    <h3>Suspend/Restore User</h3>
    <p>Mark an existing user as suspended or restore a suspended user.
        While suspended, the user will be prohibited from logging into
        this domain.</p>
    <p>
        <input type="hidden" name="command" value="setUserSuspended" />
        <label for="user">Username: </label>
        <input type="text" name="user" value="" />
    </p>
    <div class="radio">
        <input type="radio" name="mode" value="restore">User may log into
            this domain.</input><br />
        <input type="radio" name="mode" value="suspend" checked="true">User
            may <strong>not</strong> log into this domain.</input>
    </div>

    <p><input type="submit" value="Update User &rarr;"></p>
</form>
<form method="post" accept-charset="utf-8">
    <h3>Issue/Revoke Admin Rights</h3>
    <p>Set whether an existing user has administrative rights for the current
         domain.</p>
    <p>
        <input type="hidden" name="command" value="setUserAdmin" />
        <label for="user">Username: </label>
        <input type="text" name="user" value="" />
    </p>
    <div class="radio">
        <input type="radio" name="mode" value="issue">User
            may administer this domain.</input><br />
        <input type="radio" name="mode" value="revoke" checked="true">User
            may <strong>not</strong> administer this domain.</input>
    </div>

    <p><input type="submit" value="Update User &rarr;"></p>
</form>
<form method="post" accept-charset="utf-8">
    <h3>Force User To Change Password</h3>
    <p>Set whether an existing user must change their password at
        their next login.</p>
    <p>
        <input type="hidden" name="command" value="setForceChangePassword" />
        <label for="user">Username: </label>
        <input type="text" name="user" value="" />
    </p>
    <div class="radio">
        <input type="radio" name="mode" value="set">User is required to
            change their password at next login.</input><br />
        <input type="radio" name="mode" value="clear" checked="true">User is
            <strong>not</strong> required to change their password at next
                login.</input>
    </div>

    <p><input type="submit" value="Update User &rarr;"></p>
</form>
<form method="post" accept-charset="utf-8">
    <h3>Delete User</h3>
    <p>Delete an existing user on the current domain.</p>
    <p>
        <input type="hidden" name="command" value="deleteUser" />
        <label for="user">Username: </label>
        <input type="text" name="user" value="" /><br />
    </p>

    <p><input type="submit" value="Delete User &rarr;"></p>
</form>
<?php
}

/**
 * Display the nickname maintenance menu for running in a web browser.
 *
 * @return void
 */
function displayNicknameMenu()
{
?>
<h2>Nickname Maintenance Menu</h2>

<form method="post" accept-charset="utf-8">
    <h3>Create Nickname</h3>
    <p>Create a nickname for an existing user.</p>
    <p>
        <input type="hidden" name="command" value="createNickname" />
        <label for="user">Username: </label>
        <input type="text" name="user" value="" /><br />
        <label for="nickname">Nickname: </label>
        <input type="text" name="nickname" value="" /><br />
    </p>

    <p><input type="submit" value="Create Nickname &rarr;"></p>
</form>
<form method="get" accept-charset="utf-8">
    <h3>Retrieve Nickname</h3>
    <p>Retrieve the information for an existing nickname.</p>
    <p>
        <input type="hidden" name="command" value="retrieveNickname" />
        <label for="nickname">Nickname: </label>
        <input type="text" name="nickname" value="" /><br />
    </p>

    <p><input type="submit" value="Retrieve Nickname &rarr;"></p>
</form>
<form method="get" accept-charset="utf-8">
    <h3>Retrieve Nicknames</h3>
    <p>Retrieve the nicknames associated with an existing username.</p>
    <p>
        <input type="hidden" name="command" value="retrieveNicknames" />
        <label for="user">Username: </label>
        <input type="text" name="user" value="" /><br />
    </p>

    <p><input type="submit" value="Retrieve Nicknames &rarr;"></p>
</form>
<form method="get" accept-charset="utf-8">
    <h3>Retrieve All Nicknames</h3>
    <p>Retrieve the nicknames on the current domain.</p>
    <p>
        <input type="hidden" name="command" value="retrieveAllNicknames" />
    </p>

    <p><input type="submit" value="Retrieve Nicknames &rarr;"></p>
</form>
<form method="post" accept-charset="utf-8">
    <h3>Delete Nickname</h3>
    <p>Delete an existing nickname from the current domain.</p>
    <p>
        <input type="hidden" name="command" value="deleteNickname" />
        <label for="nickname">Nickname: </label>
        <input type="text" name="nickname" value="" /><br />
    </p>

    <p><input type="submit" value="Delete Nickname &rarr;"></p>
</form>
<?php
}

/**
 * Display the email list maintenance menu for running in a web browser.
 *
 * @return void
 */
function displayEmailListMenu()
{
?>
<h2>Email List Maintenance Menu</h2>

<form method="post" accept-charset="utf-8">
    <h3>Create Email List</h3>
    <p>Create a new email list for the current domain.</p>
    <p>
        <input type="hidden" name="command" value="createEmailList" />
        <label for="emailList">List Name: </label>
        <input type="text" name="emailList" value="" /><br />
    </p>

    <p><input type="submit" value="Create List &rarr;"></p>
</form>
<form method="get" accept-charset="utf-8">
    <h3>Retrieve Email Lists</h3>
    <p>Retrieve all email lists to which a given email address is
        subscribed.</p>
    <p>
        <input type="hidden" name="command" value="retrieveEmailLists" />
        <label for="recipient">Recipient Address: </label>
        <input type="text" name="recipient" value="" /><br />
    </p>

    <p><input type="submit" value="Retrieve Lists &rarr;"></p>
</form>
<form method="get" accept-charset="utf-8">
    <h3>Retrieve All Email Lists</h3>
    <p>Retrieve all email lists on the current domain.</p>
    <p>
        <input type="hidden" name="command" value="retrieveAllEmailLists" />
    </p>

    <p><input type="submit" value="Retrieve Lists &rarr;"></p>
</form>
<form method="post" accept-charset="utf-8">
    <h3>Delete Email List</h3>
    <p>Delete an existing email list from the current domain.</p>
    <p>
        <input type="hidden" name="command" value="deleteEmailList" />
        <label for="emailList">List Name: </label>
        <input type="text" name="emailList" value="" /><br />
    </p>

    <p><input type="submit" value="Delete List &rarr;"></p>
</form>
<form method="post" accept-charset="utf-8">
    <h3>Add Recipient To Email List</h3>
    <p>Add or remove a recipient from an existing email list. A complete
        email address is required for recipients outside the current
        domain.</p>
    <p>
        <input type="hidden" name="command" value="modifySubscription" />
        <label for="emailList">List Name: </label>
        <input type="text" name="emailList" value="" /><br />
        <label for="recipient">Recipient Address: </label>
        <input type="text" name="recipient" value="" /><br />
        <div class="radio">
            <input type="radio" name="mode" value="subscribe">Subscribe
                recipient.</input><br />
            <input type="radio" name="mode" value="unsubscribe"
                checked="true">Unsubscribe recipient.</input>
        </div>
    </p>

    <p><input type="submit" value="Update Subscription &rarr;"></p>
</form>
<form method="get" accept-charset="utf-8">
    <h3>Retrieve All Recipients</h3>
    <p>Retrieve all recipients subscribed to an existing email list.</p>
    <p>
        <input type="hidden" name="command" value="retrieveAllRecipients" />
        <label for="emailList">List Name: </label>
        <input type="text" name="emailList" value="" /><br />
    </p>

    <p><input type="submit" value="Retrieve Recipients &rarr;"></p>
</form>
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

    // Check to make sure that the user has set a password.
    $p = LOGIN_PASSWORD;
    if (empty($p)) {
        startHTML(false);
        displayPasswordNotSetNotice();
        endHTML();
    }

    // Grab any login credentials that might be waiting in the request
    if (!empty($_POST['password'])) {
        if ($_POST['password'] == LOGIN_PASSWORD) {
            $_SESSION['authenticated'] = 'true';
        } else {
            // Invalid password. Stop and display a login screen.
            startHTML(false);
            requestUserLogin("Incorrect password.");
            endHTML();
        }
    }

    // If the user isn't authenticated, display a login screen
    if (!isset($_SESSION['authenticated'])) {
        startHTML(false);
        requestUserLogin();
        endHTML();
    }

    // Try to login. If login fails, log the user out and display an
    // error message.
    try {
        $client = getClientLoginHttpClient(GAPPS_USERNAME . '@' .
            GAPPS_DOMAIN, GAPPS_PASSWORD);
        $gapps = new Zend_Gdata_Gapps($client, GAPPS_DOMAIN);
    } catch (Zend_Gdata_App_AuthException $e) {
        session_destroy();
        startHTML(false);
        displayAuthenticationFailedNotice();
        endHTML();
    }

    // Success! We're logged in.
    // First we check for commands that can be submitted either though
    // POST or GET (they don't make any changes).
    if (!empty($_REQUEST['command'])) {
        switch ($_REQUEST['command']) {
            case 'retrieveUser':
                startHTML();
                retrieveUser($gapps, true, $_REQUEST['user']);
                endHTML(true);
            case 'retrieveAllUsers':
                startHTML();
                retrieveAllUsers($gapps, true);
                endHTML(true);
            case 'retrieveNickname':
                startHTML();
                retrieveNickname($gapps, true, $_REQUEST['nickname']);
                endHTML(true);
            case 'retrieveNicknames':
                startHTML();
                retrieveNicknames($gapps, true, $_REQUEST['user']);
                endHTML(true);
            case 'retrieveAllNicknames':
                startHTML();
                retrieveAllNicknames($gapps, true);
                endHTML(true);
            case 'retrieveEmailLists':
                startHTML();
                retrieveEmailLists($gapps, true, $_REQUEST['recipient']);
                endHTML(true);
            case 'retrieveAllEmailLists':
                startHTML();
                retrieveAllEmailLists($gapps, true);
                endHTML(true);
            case 'retrieveAllRecipients':
                startHTML();
                retrieveAllRecipients($gapps, true, $_REQUEST['emailList']);
                endHTML(true);
        }
    }

    // Now we handle the potentially destructive commands, which have to
    // be submitted by POST only.
    if (!empty($_POST['command'])) {
        switch ($_POST['command']) {
            case 'createUser':
                startHTML();
                createUser($gapps, true, $_POST['user'],
                    $_POST['givenName'], $_POST['familyName'],
                    $_POST['pass']);
                endHTML(true);
            case 'updateUserName':
                startHTML();
                updateUserName($gapps, true, $_POST['user'],
                    $_POST['givenName'], $_POST['familyName']);
                endHTML(true);
            case 'updateUserPassword':
                startHTML();
                updateUserPassword($gapps, true, $_POST['user'],
                    $_POST['pass']);
                endHTML(true);
            case 'setUserSuspended':
                if ($_POST['mode'] == 'suspend') {
                    startHTML();
                    suspendUser($gapps, true, $_POST['user']);
                    endHTML(true);
                } elseif ($_POST['mode'] == 'restore') {
                    startHTML();
                    restoreUser($gapps, true, $_POST['user']);
                    endHTML(true);
                } else {
                    header('HTTP/1.1 400 Bad Request');
                    startHTML();
                    echo "<h2>Invalid mode.</h2>\n";
                    echo "<p>Please check your request and try again.</p>";
                    endHTML(true);
                }
            case 'setUserAdmin':
                if ($_POST['mode'] == 'issue') {
                    startHTML();
                    giveUserAdminRights($gapps, true, $_POST['user']);
                    endHTML(true);
                } elseif ($_POST['mode'] == 'revoke') {
                    startHTML();
                    revokeUserAdminRights($gapps, true, $_POST['user']);
                    endHTML(true);
                } else {
                    header('HTTP/1.1 400 Bad Request');
                    startHTML();
                    echo "<h2>Invalid mode.</h2>\n";
                    echo "<p>Please check your request and try again.</p>";
                    endHTML(true);
                }
            case 'setForceChangePassword':
                if ($_POST['mode'] == 'set') {
                    startHTML();
                    setUserMustChangePassword($gapps, true, $_POST['user']);
                    endHTML(true);
                } elseif ($_POST['mode'] == 'clear') {
                    startHTML();
                    clearUserMustChangePassword($gapps, true, $_POST['user']);
                    endHTML(true);
                } else {
                    header('HTTP/1.1 400 Bad Request');
                    startHTML();
                    echo "<h2>Invalid mode.</h2>\n";
                    echo "<p>Please check your request and try again.</p>";
                    endHTML(true);
                }
            case 'deleteUser':
                startHTML();
                deleteUser($gapps, true, $_POST['user']);
                endHTML(true);
            case 'createNickname':
                startHTML();
                createNickname($gapps, true, $_POST['user'],
                    $_POST['nickname']);
                endHTML(true);
            case 'deleteNickname':
                startHTML();
                deleteNickname($gapps, true, $_POST['nickname']);
                endHTML(true);
            case 'createEmailList':
                startHTML();
                createEmailList($gapps, true, $_POST['emailList']);
                endHTML(true);
            case 'deleteEmailList':
                startHTML();
                deleteEmailList($gapps, true, $_POST['emailList']);
                endHTML(true);
            case 'modifySubscription':
                if ($_POST['mode'] == 'subscribe') {
                    startHTML();
                    addRecipientToEmailList($gapps, true, $_POST['recipient'],
                        $_POST['emailList']);
                    endHTML(true);
                } elseif ($_POST['mode'] == 'unsubscribe') {
                    startHTML();
                    removeRecipientFromEmailList($gapps, true,
                        $_POST['recipient'], $_POST['emailList']);
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
            case 'user':
                startHTML();
                displayUserMenu();
                endHTML();
            case 'nickname':
                startHTML();
                displayNicknameMenu();
                endHTML();
            case 'emailList':
                startHTML();
                displayEmailListMenu();
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
