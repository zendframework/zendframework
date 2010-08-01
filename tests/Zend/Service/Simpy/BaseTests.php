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
 * @package    Zend_Service_Simpy
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * Test helper
 */


/**
 * @category   Zend
 * @package    Zend_Service_Simpy
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Service
 * @group      Zend_Service_Simpy
 */
abstract class Zend_Service_Simpy_BaseTests extends PHPUnit_Framework_TestCase
{
    /**
     * Simpy service consumer proxy
     *
     * @var Zend_Service_Simpy_BaseProxy
     */
    protected $_simpy;

    /**
     * Sample link data
     *
     * @var array
     */
    protected $_link = array(
        'title'       => 'Zend Framework',
        'href'        => 'http://framework.zend.com',
        'accessType'  => 'public',
        'tags'        => array('zend', 'framework', 'php'),
        'urlNickname' => 'Zend Framework web site',
        'note'        => 'This web site rules!'
    );

    /**
     * Sample note data
     *
     * @var array
     */
    protected $_note = array(
        'title'       => 'Test Note',
        'tags'        => array('test'),
        'description' => 'This is a test note.'
    );

    public function testException()
    {
        try {
            $this->_simpy->deleteNote(null);
            $this->fail('Exception not thrown');
        } catch (Zend_Service_Exception $e) {
            // success
        }
    }

    public function testSaveLink()
    {
        try {
            extract($this->_link);

            /**
             * Delete the link if it already exists and bypass the exception
             * that would be thrown as a result
             */
            try {
                $this->_simpy->deleteLink($href);
            } catch (Zend_Service_Exception $e) {
                // ignore exception
            }

            /**
             * @see Zend_Service_Simpy_Link
             */

            $this->_simpy->saveLink(
                $title,
                $href,
                Zend_Service_Simpy_Link::ACCESSTYPE_PUBLIC,
                $tags,
                $urlNickname,
                $note
            );
        } catch (Zend_Service_Exception $e) {
            $this->fail('Could not save link: ' . $e->getMessage());
        }
    }

    public function testGetLinks()
    {
        $linkSet = $this->_simpy->getLinks();

        $this->assertEquals(
            $linkSet->getLength(),
            1,
            'Link set does not have expected size'
        );

        $link = $linkSet->getIterator()->current();
        extract($this->_link);

        $this->assertEquals(
            $link->getAccessType(),
            $accessType,
            'Access type does not match'
        );

        $this->assertEquals(
            $link->getUrl(),
            $href,
            'URL does not match'
        );

        $this->assertNotEquals(
            strtotime($link->getModDate()),
            false,
            'Mod date is invalid'
        );

        $this->assertNotEquals(
            strtotime($link->getAddDate()),
            false,
            'Add date is invalid'
        );

        $this->assertEquals(
            $link->getTitle(),
            $title,
            'Title does not match'
        );

        $this->assertEquals(
            $link->getNickname(),
            $urlNickname,
            'Nickname does not match'
        );

        $this->assertEquals(
            $link->getTags(),
            $tags,
            'Tags do not match'
        );

        $this->assertEquals(
            $link->getNote(),
            $note,
            'Note does not match'
        );
    }

    public function testLinkQuery()
    {
        $date = date('Y-m-d');

        /**
         * @see Zend_Service_Simpy_LinkQuery
         */
        $linkQuery = new Zend_Service_Simpy_LinkQuery;
        $linkQuery->setQueryString($this->_link['title']);
        $linkQuery->setBeforeDate($date);

        $this->assertNull(
            $linkQuery->getDate(),
            'Date has been initialized'
        );

        $linkQuery->setAfterDate($date);

        $this->assertNull(
            $linkQuery->getDate(),
            'Date has been initialized'
        );

        $linkQuery->setDate($date);

        $this->assertNull(
            $linkQuery->getBeforeDate(),
            'Before date has retained its value'
        );

        $this->assertNull(
            $linkQuery->getAfterDate(),
            'After date has retained its value'
        );

        $linkQuery
            ->setLimit(1)
            ->setDate(null);

        $this->assertEquals(
            $linkQuery->getLimit(),
            1,
            'Limit was not set'
        );

        $linkQuery->setLimit(array());

        $this->assertNull(
            $linkQuery->getLimit(),
            'Invalid limit value was accepted'
        );

        $linkSet = $this->_simpy->getLinks($linkQuery);

        $this->assertEquals(
            $linkSet->getLength(),
            1,
            'Link set does not have the expected size'
        );
    }

    public function testGetTags()
    {
        $tagSet = $this->_simpy->getTags();

        $this->assertEquals(
            $tagSet->getLength(),
            count($this->_link['tags']),
            'Tag set does not have the expected size'
        );

        foreach ($tagSet as $tag) {
            $this->assertContains(
                $tag->getTag(),
                $this->_link['tags'],
                'Tag ' . $tag->getTag() . ' does not exist'
            );

            $this->assertEquals(
                $tag->getCount(),
                1,
                'Tag count does not match'
            );
        }
    }

    public function testRenameTag()
    {
        $fromTag = 'framework';
        $toTag = 'frameworks';

        $tagsArray = $this->_getTagsArray();

        $this->assertContains(
            $fromTag,
            $tagsArray,
            'Source tag was not found'
        );

        $this->assertNotContains(
            $toTag,
            $tagsArray,
            'Destination tag already exists'
        );

        $this->_simpy->renameTag($fromTag, $toTag);

        $tagsArray = $this->_getTagsArray();

        $this->assertContains(
            $toTag,
            $tagsArray,
            'Destination tag was not found'
        );
    }

    public function testSplitTag()
    {
        $fromTag = 'frameworks';
        $toTag1 = 'framework';
        $toTag2 = 'frameworks';

        $tagsArray = $this->_getTagsArray();

        $this->assertContains(
            $fromTag,
            $tagsArray,
            'Source tag was not found'
        );

        $this->_simpy->splitTag($fromTag, $toTag1, $toTag2);

        $tagsArray = $this->_getTagsArray();

        $this->assertContains(
            $toTag1,
            $tagsArray,
            'First destination tag was not found'
        );

        $this->assertContains(
            $toTag2,
            $tagsArray,
            'Second destination tag was not found'
        );
    }

    public function testMergeTags()
    {
        $fromTag1 = 'framework';
        $fromTag2 = 'frameworks';
        $toTag = 'framework';

        $tagsArray = $this->_getTagsArray();

        $this->assertContains(
            $fromTag1,
            $tagsArray,
            'First source tag was not found'
        );

        $this->assertContains(
            $fromTag2,
            $tagsArray,
            'Second source tag was not found'
        );

        $this->_simpy->mergeTags($fromTag1, $fromTag2, $toTag);

        $tagsArray = $this->_getTagsArray();

        $this->assertContains(
            $toTag,
            $tagsArray,
            'Destination tag was not found'
        );
    }

    public function testRemoveTag()
    {
        $tag = 'zend';

        $tagsArray = $this->_getTagsArray();

        $this->assertContains(
            $tag,
            $tagsArray,
            'Tag to remove was not found'
        );

        $this->_simpy->removeTag($tag);

        $tagsArray = $this->_getTagsArray();

        $this->assertNotContains(
            $tag,
            $tagsArray,
            'Tag was not removed'
        );
    }

    public function testDeleteLink()
    {
        $this->_simpy->deleteLink($this->_link['href']);

        $linkSet = $this->_simpy->getLinks();

        $this->assertEquals(
            $linkSet->getLength(),
            0,
            'Link was not deleted'
        );
    }

    public function testSaveNote()
    {
        try {
            extract($this->_note);

            $this->_simpy->saveNote(
                $title,
                $tags,
                $description
            );
        } catch (Zend_Service_Exception $e) {
            $this->fail('Could not save note: ' . $e->getMessage());
        }
    }

    public function testGetNotes()
    {
        $noteSet = $this->_simpy->getNotes();

        $this->assertGreaterThanOrEqual(
            $noteSet->getLength(),
            1,
            'Note set does not have the expected size'
        );

        $note = $noteSet->getIterator()->current();
        extract($this->_note);

        $this->assertEquals(
            $note->getAccessType(),
            'private',
            'Access type does not match'
        );

        $this->assertEquals(
            $note->getUri(),
            'http://www.simpy.com/simpy/NoteDetails.do?noteId=' . $note->getId(),
            'URI does not match'
        );

        $this->assertEquals(
            $note->getTitle(),
            $title,
            'Title does not match'
           );

        $this->assertEquals(
            $note->getTags(),
            $tags,
            'Tags do not match'
        );

        $this->assertEquals(
            $note->getDescription(),
            $description,
            'Description does not match'
        );

        $this->assertNotEquals(
            strtotime($note->getAddDate()),
            false,
            'Add date is invalid'
        );

        $this->assertNotEquals(
            strtotime($note->getModDate()),
            false,
            'Mod date is invalid'
        );
    }

    public function testDeleteNote()
    {
        $noteSet = $this->_simpy->getNotes();
        $noteId = $noteSet->getIterator()->current()->getId();
        $this->_simpy->deleteNote($noteId);
        $noteSet = $this->_simpy->getNotes();
        foreach ($noteSet as $note) {
            $this->assertNotEquals(
                $note->getId(),
                $noteId,
                'Note was not deleted'
            );
        }
    }

    private function _getWatchlistIterator()
    {
        $watchlistSet = $this->_simpy->getWatchlists();
        $watchlistSetIterator = $watchlistSet->getIterator();

        if (!count($watchlistSetIterator)) {
            $this->markTestSkipped('Account has no watchlists');
        }

        return $watchlistSetIterator;
    }

    public function testGetWatchlists()
    {
        $watchlistSetIterator = $this->_getWatchlistIterator();
        $watchlist = $watchlistSetIterator->current();

        $this->assertNotNull(
            $watchlist,
            'Watchlist is invalid'
        );
    }

    public function testGetWatchlist()
    {
        $watchlistSetIterator = $this->_getWatchlistIterator();
        $watchlistId = $watchlistSetIterator->current()->getId();

        $watchlist = $this->_simpy->getWatchlist($watchlistId);

        $this->assertEquals(
            $watchlist->getId(),
            $watchlistId,
            'ID does not match'
        );

        $watchlistName = $watchlist->getName();

        $this->assertFalse(
            empty($watchlistName),
            'Name is empty'
        );

        $this->assertNotEquals(
            strtotime($watchlist->getAddDate()),
            false,
            'Add date is invalid'
        );

        $this->assertGreaterThanOrEqual(
            $watchlist->getNewLinks(),
            0,
            'New link count is invalid'
        );

        $this->assertTrue(
            is_array($watchlist->getUsers()),
            'User list is not an array'
        );
    }

    public function testWatchlistFilters()
    {
        $watchlistSetIterator = $this->_getWatchlistIterator();
        $watchlistId = $watchlistSetIterator->current()->getId();
        $watchlist = $this->_simpy->getWatchlist($watchlistId);
        $filterSet = $watchlist->getFilters();

        if (!$filterSet->getLength()) {
            $this->markTestSkipped('Watchlist has no filters');
        }

        $filter = $filterSet->getIterator()->current();

        $filterName = $filter->getName();

        $this->assertFalse(
            empty($filterName),
            'Name is invalid'
        );

        $filterQuery = $filter->getQuery();

        $this->assertFalse(
            empty($filterQuery),
            'Query is invalid'
        );
    }

    protected function _getTagsArray()
    {
        $tagSet = $this->_simpy->getTags();
        $tagArray = array();

        foreach ($tagSet as $tag) {
            $tagArray[] = $tag->getTag();
        }

        return $tagArray;
    }
}
