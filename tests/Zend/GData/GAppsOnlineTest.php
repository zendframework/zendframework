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
 * @package    Zend_GData_GApps
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\GData;
use Zend\GData;
use Zend\GData\GApps;

/**
 * @category   Zend
 * @package    Zend_GData_GApps
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_GData
 * @group      Zend_GData_GApps
 */
class GAppsOnlineTest extends \PHPUnit_Framework_TestCase
{

    const GIVEN_NAME = 'Zend_GData';
    const FAMILY_NAME = 'Automated Test Account';
    const PASSWORD = '4ohtladfl;';
    const PASSWORD_HASH = 'SHA-1';

    public function setUp()
    {
        if (!constant('TESTS_ZEND_GDATA_ONLINE_ENABLED')) {
            $this->markTestSkipped('Zend_GData online tests are not enabled');
        }

        if (!constant('TESTS_ZEND_GDATA_GAPPS_ONLINE_ENABLED')) {
            $this->markTestSkipped('GAppsOnlineTest is skipped');
        }


        $this->id = uniqid('ZF-');
        $username = constant('TESTS_ZEND_GDATA_GAPPS_EMAIL');
        $pass = constant('TESTS_ZEND_GDATA_GAPPS_PASSWORD');
        $this->domain = constant('TESTS_ZEND_GDATA_GAPPS_DOMAIN');
        $client = GData\ClientLogin::getHttpClient($username, $pass, GApps::AUTH_SERVICE_NAME);
        $this->gdata = new GApps($client, $this->domain);

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
            } catch (\Exception $e) {
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
        GData\ClientLogin::getHttpClient($this->id . '@' .
            $this->domain, self::PASSWORD, 'xapi');


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

    public function testGroupCRUDOperations() {
        // Create group
        $generatedGroupName = strtolower(uniqid('zf-group-'));
        $group = $this->gdata->createGroup($generatedGroupName, 'zf-group-',
                'testGroupCRUDOperations()');
        $this->autoDelete($group);

        $groupId = null;
        $properties = $group->getProperty();
        foreach ($properties as $property) {
            if($property->name == 'groupId') {
                $groupId = $property->value;
            }
        }

        $this->assertEquals($generatedGroupName, $groupId);

        // Retrieve group
        $query = $this->gdata->newGroupQuery();
        $groupFeed = $this->gdata->getGroupFeed($query);
        $entryCount = count($groupFeed->entry);
        $this->assertTrue($entryCount > 0);

        // Delete group (uses builtin delete method, convenience delete
        // method tested further down)
        $group->delete();

        // Ensure that group was deleted
        $groupFeed = $this->gdata->getGroupFeed($query);
        $this->assertEquals($entryCount - 1, count($groupFeed->entry));

    }

    public function testCanAssignMultipleGroupsToOneUser() {
        // Create a user
        $user = $this->gdata->createUser($this->id, self::GIVEN_NAME, self::FAMILY_NAME,
            sha1(self::PASSWORD), self::PASSWORD_HASH);
        $this->autoDelete($user);

        // Create two groups
        $groupCount = 2;

        for ($i = 0; $i < $groupCount; $i++) {
            $generatedGroupName = strtolower(uniqid('zf-group-'));
            $group = $this->gdata->createGroup($generatedGroupName, 'Test Group',
                    'testCanAssignMultipleGroupsToOneUser() ' . $i);
            $this->autoDelete($group);
            $this->gdata->addMemberToGroup($this->id, $generatedGroupName);
        }

        // Make sure that the user is subscribed to both groups
        $subscriptions = $this->gdata->retrieveGroups($this->id);
        $this->assertEquals($groupCount, count($subscriptions->entry));

    }

    public function testCanRetrievePageOfGroups() {
        // Create a group
        $generatedGroupName = strtolower(uniqid('zf-group-'));
        $group = $this->gdata->createGroup($generatedGroupName, 'Test Group',
                'testCanRetrievePageOfGroups()');
        $this->autoDelete($group);

        // Try retrieving the group feed
        $feed = $this->gdata->retrievePageOfGroups();
        $this->assertTrue(count($feed->entry) > 0);

    }

    public function testCanRetrieveAllGroups() {
        // Create a couple of users to make sure we don't hit the limit
        // on the max number of groups.
        for ($i = 0; $i < 3; $i++) {
            $user = $this->gdata->createUser(uniqid('ZF-'), self::GIVEN_NAME, self::FAMILY_NAME,
                sha1(self::PASSWORD), self::PASSWORD_HASH);
            $this->autoDelete($user);
        }

        // Create a whole bunch of groups to make sure we trigger
        // paging.
        for ($i = 0; $i < 30; $i++) {
            $generatedGroupName = strtolower(uniqid('zf-group-'));
            $group = $this->gdata->createGroup($generatedGroupName, 'Test Group ' . $i,
                    'testCanRetrieveAllGroups()');
            $this->autoDelete($group);
        }

        // Try retrieving the group feed
        $feed = $this->gdata->retrieveAllGroups();
        $this->assertTrue(count($feed->entry) >= 30);

    }

    // Test the convenience delete method for groups
    public function testCanDeleteGroup() {
        // Create a group
        $generatedGroupName = strtolower(uniqid('zf-group-'));
        $group = $this->gdata->createGroup($generatedGroupName, 'Test Group',
                'testCanDeleteGroup()');
        $this->autoDelete($group);

        // Assert that the group exists, just in case...
        $query = $this->gdata->newGroupQuery();
        $query->setGroupId($generatedGroupName);
        $entry = $this->gdata->getGroupEntry($query);
        $this->assertNotNull($entry);

        // Delete group
        $this->gdata->deleteGroup($generatedGroupName);

        // Ensure that group was deleted
        try {
            $query = $this->gdata->newGroupQuery();
            $query->setGroupId($generatedGroupName);
            $entry = $this->gdata->getGroupEntry($query);
            // This souldn't execute
            $this->fail('Retrieving a non-existant group entry didn\'t' .
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

    public function testCanRetrievePageOfMembers() {
        // Create a new group
        $generatedGroupName = strtolower(uniqid('zf-group-'));
        $group = $this->gdata->createGroup($generatedGroupName, 'Test Group',
                'testCanRetrievePageOfMembers()');
        $this->autoDelete($group);

        // Create two users and assign them to the group
        $userCount = 2;
        for ($i = 0; $i < $userCount; $i++) {
            $generatedUsername = uniqid('ZF-');
            $user = $this->gdata->createUser($generatedUsername,
                self::GIVEN_NAME, self::FAMILY_NAME, sha1(self::PASSWORD),
                self::PASSWORD_HASH);
            $this->autoDelete($user);
            $this->gdata->addMemberToGroup($generatedUsername,
                $generatedGroupName);
        }

        // Retrieve members
        $memberFeed = $this->gdata->retrievePageOfMembers($generatedGroupName);
        $this->assertTrue(count($memberFeed->entry) == $userCount);

    }

    public function testCanRetrievAllMembers() {
        // Create a new group
        $generatedGroupName = strtolower(uniqid('zf-list-'));
        $group = $this->gdata->createGroup($generatedGroupName, 'Test Group',
                'testCanRetrievAllMembers()');
        $this->autoDelete($group);

        // Create enough users to trigger paging and assign them to the group
        $userCount = 30;
        for ($i = 0; $i < $userCount; $i++) {
            $generatedUsername = uniqid('ZF-');
            $user = $this->gdata->createUser($generatedUsername,
                self::GIVEN_NAME, self::FAMILY_NAME, sha1(self::PASSWORD),
                self::PASSWORD_HASH);
            $this->autoDelete($user);
            $this->gdata->addMemberToGroup($generatedUsername, $generatedGroupName);
        }

        // Retrieve members
        $memberFeed = $this->gdata->retrieveAllMembers($generatedGroupName);
        $this->assertTrue(count($memberFeed->entry) == $userCount);

    }

    // Test the convenience removeMemberFromGroup method for group members
    public function testCanRemoveMemberFromGroup() {
        // Create a group
        $generatedGroupName = strtolower(uniqid('zf-list-'));
        $group = $this->gdata->createGroup($generatedGroupName, 'Test Group',
                'testCanDeleteGroupMember()');
        $this->autoDelete($group);

        // Create a user for the group
        $user = $this->gdata->createUser($this->id, self::GIVEN_NAME,
            self::FAMILY_NAME, sha1(self::PASSWORD), self::PASSWORD_HASH);
        $this->autoDelete($user);
        $this->gdata->addMemberToGroup($this->id, $generatedGroupName);

        // Assert that the member exists, just in case...
        $members = $this->gdata->retrieveAllMembers($generatedGroupName);
        $this->assertTrue(count($members->entry) == 1);

        // Remove the member from the group
        $this->gdata->removeMemberFromGroup($user->login->username,
            $generatedGroupName);

        // Ensure that user was deleted
        $members =  $this->gdata->retrieveAllMembers($generatedGroupName);
        $this->assertTrue(count($members->entry) == 0);

    }

    public function testCanRetrieveGroupOwners() {
        // Create a new group
        $generatedGroupName = strtolower(uniqid('zf-list-'));
        $group = $this->gdata->createGroup($generatedGroupName, 'Test Group',
                'testCanRetrievAllOwners()');
        $this->autoDelete($group);

        $userCount = 3;
        for ($i = 0; $i < $userCount; $i++) {
            $generatedUsername = uniqid('ZF-');
            $user = $this->gdata->createUser($generatedUsername,
                self::GIVEN_NAME, self::FAMILY_NAME, sha1(self::PASSWORD),
                self::PASSWORD_HASH);
            $this->autoDelete($user);
            $this->gdata->addOwnerToGroup($generatedUsername,
                $generatedGroupName);
        }

        // Retrieve owners
        $ownerFeed = $this->gdata->retrieveGroupOwners($generatedGroupName);
        $this->assertTrue(count($ownerFeed->entry) == $userCount);

    }

    // Test the convenience removeOwnerFromGroup method for group owners
    public function testCanRemoveOwnerFromGroup() {
        // Create a group
        $generatedGroupName = strtolower(uniqid('zf-list-'));
        $group = $this->gdata->createGroup($generatedGroupName, 'Test Group',
                'testCanDeleteGroupOwner()');
        $this->autoDelete($group);

        // Create a user for the group
        $user = $this->gdata->createUser($this->id, self::GIVEN_NAME,
            self::FAMILY_NAME, sha1(self::PASSWORD), self::PASSWORD_HASH);
        $this->autoDelete($user);
        $this->gdata->addOwnerToGroup($this->id, $generatedGroupName);

        // Assert that the owner exists, just in case...
        $owners = $this->gdata->retrieveGroupOwners($generatedGroupName);
        $this->assertTrue(count($owners->entry) == 1);

        // Remove the owner from the group
        $this->gdata->removeOwnerFromGroup($user->login->username,
            $generatedGroupName);

        // Ensure that user was deleted
        $owners = $this->gdata->retrieveGroupOwners($generatedGroupName);
        $this->assertTrue(count($owners->entry) == 0);
    }

    // Test the convenience isMember method
    public function testIsMember() {
        // Create a group
        $generatedGroupName = strtolower(uniqid('zf-list-'));
        $group = $this->gdata->createGroup($generatedGroupName, 'Test Group',
                'testIsMember()');
        $this->autoDelete($group);

        // Create a user for the group
        $user = $this->gdata->createUser($this->id, self::GIVEN_NAME,
            self::FAMILY_NAME, sha1(self::PASSWORD), self::PASSWORD_HASH);
        $this->autoDelete($user);
        $this->gdata->addMemberToGroup($this->id, $generatedGroupName);

        $isMember = $this->gdata->isMember($this->id, $generatedGroupName);

        $this->assertTrue($isMember);

        $isMember = $this->gdata->isMember('foo_' . $this->id, $generatedGroupName);

        $this->assertFalse($isMember);

    }

    // Test the convenience isOwner method
    public function testIsOwner() {
        // Create a group
        $generatedGroupName = strtolower(uniqid('zf-list-'));
        $group = $this->gdata->createGroup($generatedGroupName, 'Test Group',
                'testIsMember()');
        $this->autoDelete($group);

        // Create a user for the group
        $user = $this->gdata->createUser($this->id, self::GIVEN_NAME,
            self::FAMILY_NAME, sha1(self::PASSWORD), self::PASSWORD_HASH);
        $this->autoDelete($user);
        $this->gdata->addOwnerToGroup($this->id, $generatedGroupName);

        $isOwner = $this->gdata->isOwner($this->id, $generatedGroupName);

        $this->assertTrue($isOwner);

        $isOwner = $this->gdata->isOwner('foo_' . $this->id, $generatedGroupName);

        $this->assertFalse($isOwner);

    }

    // Test the convenience updateGroup method
    public function testCanUpdateGroup() {
        // Create a group
        $generatedGroupName = strtolower(uniqid('zf-list-'));
        $group = $this->gdata->createGroup($generatedGroupName, 'Test Group',
                'testCanUpdateGroup()');
        $this->autoDelete($group);

        //set new value and save it

        $group = $this->gdata->updateGroup($generatedGroupName, null, 'new description here');

        //verify new value
        $description = null;

        $properties = $group->getProperty();
        foreach ($properties as $property) {
            if($property->name == 'description') {
                $description = $property->value;
            }
        }

        $this->assertEquals('new description here', $description);

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
        } catch (GApps\ServiceException $e) {
            if ($e->hasError(GApps\Error::ENTITY_DOES_NOT_EXIST)) {
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
