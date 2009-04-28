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
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'TestHelper.php';

require_once 'Zend/Gdata/Gapps.php';
require_once 'Zend/Gdata/Gapps/UserEntry.php';
require_once 'Zend/Gdata/Gapps/UserQuery.php';
require_once 'Zend/Gdata/ClientLogin.php';
require_once 'Zend/Http/Client.php';

/**
 * @package Zend_Gdata
 * @subpackage UnitTests
 */
class Zend_Gdata_GappsOnlineTest extends PHPUnit_Framework_TestCase
{

    const GIVEN_NAME = 'Zend_Gdata';
    const FAMILY_NAME = 'Automated Test Account';
    const PASSWORD = '4ohtladfl;';
    const PASSWORD_HASH = 'SHA-1';

    public function setUp()
    {
        $this->id = uniqid('ZF-');
        $username = constant('TESTS_ZEND_GDATA_GAPPS_EMAIL');
        $pass = constant('TESTS_ZEND_GDATA_GAPPS_PASSWORD');
        $this->domain = constant('TESTS_ZEND_GDATA_GAPPS_DOMAIN');
        $client = Zend_Gdata_ClientLogin::getHttpClient($username, $pass, Zend_Gdata_Gapps::AUTH_SERVICE_NAME);
        $this->gdata = new Zend_Gdata_Gapps($client, $this->domain);

        // Container to hold users and lists created during tests. All entries in
        // here will have delete() called during tear down.
        //
        // Failed deletions are okay, so add everying creatd in here, even if
        // you plan to delete the user yourself!
        $this->autoDeletePool = array();
    }

    public function tearDown()
    {
        // Delete all entries in $this->autoDeletePool.
        foreach ($this->autoDeletePool as $x) {
            try {
                $x->delete();
            } catch (Exception $e) {
                // Failed deletes are okay. Try and delete the rest anyway.
            }
        }
    }

    // Schedule an entry for deletion at test tear-down.
    protected function autoDelete($entry) {
        $this->autoDeletePool[] = $entry;
    }

    // Test Create/Read/Update/Destroy operations on a UserEntry
    public function testUserCRUDOperations() {
        // Create a new user
        $user = $this->gdata->createUser($this->id, self::GIVEN_NAME, self::FAMILY_NAME,
            sha1(self::PASSWORD), self::PASSWORD_HASH);
        $this->autoDelete($user);

        // Verify that returned values are correct
        $this->assertEquals($this->id, $user->login->username);
        $this->assertEquals(self::GIVEN_NAME, $user->name->givenName);
        $this->assertEquals(self::FAMILY_NAME, $user->name->familyName);

        // Since we can't retrieve the password or hash function via the
        // API, let's see if a ClientLogin auth request succeeds
        try {
            Zend_Gdata_ClientLogin::getHTTPClient($this->id . '@' .
                $this->domain, self::PASSWORD, 'xapi');
        } catch (Zend_Gdata_App_AuthException $e) {
           $this->fail("Unable to authenticate new user via ClientLogin.");
        }

        // Check to make sure there are no extension elements/attributes
        // in the retrieved user
        $this->assertTrue(count($user->extensionElements) == 0);
        $this->assertTrue(count($user->extensionAttributes) == 0);

        // Try searching for the same user and make sure that they're returned
        $user2 = $this->gdata->retrieveUser($this->id);
        $this->assertEquals($user->saveXML(), $user2->saveXML());

        // Delete user (uses builtin delete method, convenience delete
        // method tested further down)
        $user->delete();

        // Ensure that user was deleted
        $deletedUser = $this->gdata->retrieveUser($this->id);
        $this->assertNull($deletedUser);
    }

    // Test to make sure that users with unicode characters can be created 
    // okay.
    public function testUsersSupportUnicode() {
        // Create a user
        $user = $this->gdata->createUser($this->id, 'テスト', 'ユーザー',
            sha1(self::PASSWORD), self::PASSWORD_HASH);
        $this->autoDelete($user);
        
        // Make sure the user is the same as returned by the server
        $this->assertEquals('テスト', $user->name->givenName);
        $this->assertEquals('ユーザー', $user->name->familyName);
    }

    // Test to make sure that a page of users can be retrieved.
    public function testRetrievePageOfUsers() {
        $feed = $this->gdata->retrievePageOfUsers();
        $this->assertTrue(count($feed->entries) > 0);
    }

    // Test to make sure that a page of users can be retrieved with a
    // startUsername parameter.
    public function testRetrievePageOfUsersWithStartingUsername() {
        $feed = $this->gdata->retrievePageOfUsers();
        $this->assertTrue(count($feed->entries) > 0);
        $username = $feed->entries[0]->login->username;
        $feed = $this->gdata->retrievePageOfUsers($username);
        $this->assertTrue(count($feed->entries) > 0);
    }

    // Test to see if all users can be retrieved
    // NOTE: This test may timeout if the domain used for testing contains
    //       many pages of users.
    public function testRetrieveAllUsers() {
        // Create 35 users to make sure that there's more than one page.
        for ($i = 0; $i < 25; $i++) {
            $user = $this->gdata->createUser(uniqid('ZF-'), self::GIVEN_NAME,
                self::FAMILY_NAME, sha1(self::PASSWORD), self::PASSWORD_HASH);
            $this->autoDelete($user);
        }

        $feed = $this->gdata->retrieveAllUsers();
        $this->assertTrue(count($feed->entry) > 0);
    }

    // Test to see if a user can be manually updated by calling updateUser().
    public function testManualUserEntryUpdate() {
        $user = $this->gdata->createUser($this->id, self::GIVEN_NAME, self::FAMILY_NAME,
            sha1(self::PASSWORD), self::PASSWORD_HASH);
        $this->autoDelete($user);
        $user->name->givenName = "Renamed";
        $user2 = $this->gdata->updateUser($this->id, $user);
        $this->assertEquals("Renamed", $user2->name->givenName);
    }

    // Test to see if a user can be suspended, then un-suspended
    public function testCanSuspendAndRestoreUser() {
        $user = $this->gdata->createUser($this->id, self::GIVEN_NAME, self::FAMILY_NAME,
            sha1(self::PASSWORD), self::PASSWORD_HASH);
        $this->autoDelete($user);

        $returned = $this->gdata->suspendUser($this->id);
        $user = $this->gdata->retrieveUser($this->id);
        $this->assertEquals(true, $user->login->suspended);
        $this->assertEquals($this->id, $returned->login->username);

        $returned = $this->gdata->restoreUser($this->id);
        $user = $this->gdata->retrieveUser($this->id);
        $this->assertEquals(false, $user->login->suspended);
        $this->assertEquals($this->id, $returned->login->username);
    }

    // Test the convenience delete method for users
    public function testCanDeleteUser() {
        $user = $this->gdata->createUser($this->id, self::GIVEN_NAME, self::FAMILY_NAME,
            sha1(self::PASSWORD), self::PASSWORD_HASH);
        $this->autoDelete($user);

        // Assert that the user exists, just in case...
        $rUser = $this->gdata->retrieveUser($this->id);
        $this->assertNotNull($rUser);

        // Delete user
        $this->gdata->deleteUser($this->id);

        // Ensure that user was deleted
        $rUser = $this->gdata->retrieveUser($this->id);
        $this->assertNull($rUser);
    }

    public function testNicknameCRUDOperations() {
        $user = $this->gdata->createUser($this->id, self::GIVEN_NAME, self::FAMILY_NAME,
            sha1(self::PASSWORD), self::PASSWORD_HASH);
        $this->autoDelete($user);

        // Create nickname
        // Apps will convert the nickname to lowercase on the server, so
        // we just make sure the generated nickname is lowercase here to start
        // to avoid confusion later on.
        $generatedNickname = strtolower(uniqid('zf-nick-'));
        $nickname = $this->gdata->createNickname($this->id, $generatedNickname);
        $this->assertEquals($generatedNickname, $nickname->nickname->name);
        $this->assertEquals($this->id, $nickname->login->username);

        // Retrieve nickname
        $nickname = $this->gdata->retrieveNickname($generatedNickname);
        $this->assertEquals($generatedNickname, $nickname->nickname->name);
        $this->assertEquals($this->id, $nickname->login->username);

        // Delete nickname (uses builtin delete method, convenience delete
        // method tested further down)
        $nickname->delete();

        // Ensure that nickname was deleted
        $nickname = $this->gdata->retrieveNickname($generatedNickname);
        $this->assertNull($nickname);
    }

    public function testRetrieveNicknames() {
        $user = $this->gdata->createUser($this->id, self::GIVEN_NAME,
            self::FAMILY_NAME, sha1(self::PASSWORD), self::PASSWORD_HASH);
        $this->autoDelete($user);

        // Create 5 nicknames
        for ($i = 0; $i < 5; $i++) {
            $generatedNickname[$i] = strtolower(uniqid('zf-nick-'));
            $this->gdata->createNickname($this->id, $generatedNickname[$i]);
        }

        // Retrieve all nicknames for the test user and see if they match
        $nicknameFeed = $this->gdata->retrieveNicknames($this->id);
        $this->assertEquals(count($generatedNickname), count($nicknameFeed->entry));
        foreach ($nicknameFeed as $nicknameEntry) {
            $searchResult = array_search($nicknameEntry->nickname->name,
                $generatedNickname);
            $this->assertNotSame(false, $searchResult);
            unset($generatedNickname[$searchResult]);
        }
        $this->assertEquals(0, count($generatedNickname));
    }

    public function testRetrievePageOfNicknames() {
        $user = $this->gdata->createUser($this->id, self::GIVEN_NAME,
            self::FAMILY_NAME, sha1(self::PASSWORD), self::PASSWORD_HASH);
        $this->autoDelete($user);

        // Create 5 nicknames
        for ($i = 0; $i < 5; $i++) {
            $generatedNickname[$i] = strtolower(uniqid('zf-nick-'));
            $this->gdata->createNickname($this->id, $generatedNickname[$i]);
        }

        // Test to make sure that we receive at least 5 nicknames back
        // from the server
        $results = $this->gdata->retrievePageOfNicknames();
        $this->assertTrue(count($results->entry) >= 5);
    }

    public function testRetrieveAllNicknames() {
        // Create 3 users, each with 10 nicknames
        for ($i = 0; $i < 3; $i++) {
            $user = $this->gdata->createUser(uniqid('ZF-'), self::GIVEN_NAME,
                self::FAMILY_NAME, sha1(self::PASSWORD), self::PASSWORD_HASH);
            $this->autoDelete($user);
            for ($j = 0; $j < 10; $j++) {
                $generatedNickname = strtolower(uniqid('zf-nick-'));
                $this->gdata->createNickname($user->login->username, $generatedNickname);
            }
        }

        // Test to make sure that we receive at least 5 nicknames back
        // from the server
        $results = $this->gdata->retrieveAllNicknames();
        $this->assertTrue(count($results->entry) >= 30);
    }

    // Test the convenience delete method for nicknames
    public function testCanDeleteNickname() {
        $user = $this->gdata->createUser($this->id, self::GIVEN_NAME, self::FAMILY_NAME,
            sha1(self::PASSWORD), self::PASSWORD_HASH);
        $this->autoDelete($user);
        $generatedNickname = strtolower(uniqid('zf-nick-'));
        $this->gdata->createNickname($this->id, $generatedNickname);

        // Assert that the nickname exists, just in case...
        $rNick = $this->gdata->retrieveNickname($generatedNickname);
        $this->assertNotNull($rNick);

        // Delete nickname
        $this->gdata->deleteNickname($generatedNickname);

        // Ensure that nickname was deleted
        $rNick = $this->gdata->retrieveNickname($generatedNickname);
        $this->assertNull($rNick);
    }

    public function testEmailListCRUDOperations() {
        // Create email list
        $generatedListName = strtolower(uniqid('zf-list-'));
        $list = $this->gdata->createEmailList($generatedListName);
        $this->autoDelete($list);
        $this->assertEquals($generatedListName, $list->emailList->name);

        // Retrieve email list
        $query = $this->gdata->newEmailListQuery();
        $listFeed = $this->gdata->getEmailListFeed($query);
        $entryCount = count($listFeed->entry);
        $this->assertTrue($entryCount > 0);

        // Delete email list (uses builtin delete method, convenience delete
        // method tested further down)
        $list->delete();

        // Ensure that nickname was deleted
        $listFeed = $this->gdata->getEmailListFeed($query);
        $this->assertEquals($entryCount - 1, count($listFeed->entry));
    }

    public function testCanAssignMultipleEmailListsToOneUser() {
        // Create a user
        $user = $this->gdata->createUser($this->id, self::GIVEN_NAME, self::FAMILY_NAME,
            sha1(self::PASSWORD), self::PASSWORD_HASH);
        $this->autoDelete($user);

        // Create two email lists
        $listCount = 2;

        for ($i = 0; $i < $listCount; $i++) {
            $generatedListName = strtolower(uniqid('zf-list-'));
            $list = $this->gdata->createEmailList($generatedListName);
            $this->autoDelete($list);
            $this->gdata->addRecipientToEmailList($this->id, $generatedListName);
        }

        // Make sure that the user is subscribed to both lists
        $subscriptions = $this->gdata->retrieveEmailLists($this->id);
        $this->assertEquals($listCount, count($subscriptions->entry));
    }

    public function testCanRetrievePageOfEmailLists() {
        // Create an email list
        $generatedListName = strtolower(uniqid('zf-list-'));
        $list = $this->gdata->createEmailList($generatedListName);
        $this->autoDelete($list);

        // Try retrieving the email list feed
        $feed = $this->gdata->retrievePageOfEmailLists();
        $this->assertTrue(count($feed->entry) > 0);
    }

    public function testCanRetrieveAllEmailLists() {
        // Create a couple of users to make sure we don't hit the limit
        // on the max number of email lists.
        for ($i = 0; $i < 3; $i++) {
            $user = $this->gdata->createUser(uniqid('ZF-'), self::GIVEN_NAME, self::FAMILY_NAME,
                sha1(self::PASSWORD), self::PASSWORD_HASH);
            $this->autoDelete($user);
        }

        // Create a whole bunch of email lists to make sure we trigger
        // paging.
        for ($i = 0; $i < 30; $i++) {
            $generatedListName = strtolower(uniqid('zf-list-'));
            $list = $this->gdata->createEmailList($generatedListName);
            $this->autoDelete($list);
        }

        // Try retrieving the email list feed
        $feed = $this->gdata->retrieveAllEmailLists();
        $this->assertTrue(count($feed->entry) >= 30);
    }

    // Test the convenience delete method for email lists
    public function testCanDeleteEmailList() {
        // Create an email list
        $generatedListName = strtolower(uniqid('zf-list-'));
        $list = $this->gdata->createEmailList($generatedListName);
        $this->autoDelete($list);

        // Assert that the email list exists, just in case...
        $query = $this->gdata->newEmailListQuery();
        $query->setEmailListName($generatedListName);
        $entry = $this->gdata->getEmailListEntry($query);
        $this->assertNotNull($entry);

        // Delete nickname
        $this->gdata->deleteEmailList($generatedListName);

        // Ensure that nickname was deleted
        try {
            $query = $this->gdata->newEmailListQuery();
            $query->setEmailListName($generatedListName);
            $entry = $this->gdata->getEmailListEntry($query);
            // This souldn't execute
            $this->fail('Retrieving a non-existant email list entry didn\'t' .
                'raise exception.');
        } catch (Zend_Gdata_Gapps_ServiceException $e) {
            if ($e->hasError(Zend_Gdata_Gapps_Error::ENTITY_DOES_NOT_EXIST)) {
                // Dummy assertion just to say we tested something here.
                $this->assertTrue(true);
            } else {
                // Exception thrown for an unexpected reason
                throw $e;
            }
        }
    }

    public function testCanRetrievePageOfRecipients() {
        // Create a new email list
        $generatedListName = strtolower(uniqid('zf-list-'));
        $list = $this->gdata->createEmailList($generatedListName);
        $this->autoDelete($list);

        // Create two users and assign them to the email list
        $userCount = 2;
        for ($i = 0; $i < $userCount; $i++) {
            $generatedUsername = uniqid('ZF-');
            $user = $this->gdata->createUser($generatedUsername,
                self::GIVEN_NAME, self::FAMILY_NAME, sha1(self::PASSWORD),
                self::PASSWORD_HASH);
            $this->autoDelete($user);
            $this->gdata->addRecipientToEmailList($generatedUsername,
                $generatedListName);
        }

        // Retrieve recipients
        $recipientFeed =
            $this->gdata->retrievePageOfRecipients($generatedListName);
        $this->assertTrue(count($recipientFeed->entry) == $userCount);
    }

    public function testCanRetrievAllRecipients() {
        // Create a new email list
        $generatedListName = strtolower(uniqid('zf-list-'));
        $list = $this->gdata->createEmailList($generatedListName);
        $this->autoDelete($list);

        // Create enough users to trigger paging and assign them to the email
        // list
        $userCount = 30;
        for ($i = 0; $i < $userCount; $i++) {
            $generatedUsername = uniqid('ZF-');
            $user = $this->gdata->createUser($generatedUsername,
                self::GIVEN_NAME, self::FAMILY_NAME, sha1(self::PASSWORD),
                self::PASSWORD_HASH);
            $this->autoDelete($user);
            $this->gdata->addRecipientToEmailList($generatedUsername,
                $generatedListName);
        }

        // Retrieve recipients
        $recipientFeed =
            $this->gdata->retrieveAllRecipients($generatedListName);
        $this->assertTrue(count($recipientFeed->entry) == $userCount);
    }

    // Test the convenience delete method for email list recipients
    public function testCanDeleteEmailListRecipient() {
        // Create an email list
        $generatedListName = strtolower(uniqid('zf-list-'));
        $list = $this->gdata->createEmailList($generatedListName);
        $this->autoDelete($list);

        // Create a user for the email list
        $user = $this->gdata->createUser($this->id, self::GIVEN_NAME,
            self::FAMILY_NAME, sha1(self::PASSWORD), self::PASSWORD_HASH);
        $this->autoDelete($user);
        $this->gdata->addRecipientToEmailList($this->id, $generatedListName);

        // Assert that the recipient exists, just in case...
        $recipients =
            $this->gdata->retrieveAllRecipients($generatedListName);
        $this->assertTrue(count($recipients->entry) == 1);

        // Remove the user from the list
        $this->gdata->removeRecipientFromEmailList($user->login->username,
            $generatedListName);

        // Ensure that user was deleted
        $recipients =
            $this->gdata->retrieveAllRecipients($generatedListName);
        $this->assertTrue(count($recipients->entry) == 0);
    }

}
