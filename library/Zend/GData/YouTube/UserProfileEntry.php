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
 * @subpackage YouTube
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\GData\YouTube;

use Zend\GData\YouTube,
    Zend\GData\App;

/**
 * Represents the YouTube video playlist flavor of an Atom entry
 *
 * @uses       \Zend\GData\App\VersionException
 * @uses       \Zend\GData\Entry
 * @uses       \Zend\GData\Extension\FeedLink
 * @uses       \Zend\GData\Media\Extension\MediaThumbnail
 * @uses       \Zend\GData\YouTube
 * @uses       \Zend\GData\YouTube\Extension\AboutMe
 * @uses       \Zend\GData\YouTube\Extension\Age
 * @uses       \Zend\GData\YouTube\Extension\Books
 * @uses       \Zend\GData\YouTube\Extension\Company
 * @uses       \Zend\GData\YouTube\Extension\Description
 * @uses       \Zend\GData\YouTube\Extension\FirstName
 * @uses       \Zend\GData\YouTube\Extension\Gender
 * @uses       \Zend\GData\YouTube\Extension\Hobbies
 * @uses       \Zend\GData\YouTube\Extension\Hometown
 * @uses       \Zend\GData\YouTube\Extension\LastName
 * @uses       \Zend\GData\YouTube\Extension\Location
 * @uses       \Zend\GData\YouTube\Extension\Movies
 * @uses       \Zend\GData\YouTube\Extension\Music
 * @uses       \Zend\GData\YouTube\Extension\Occupation
 * @uses       \Zend\GData\YouTube\Extension\Relationship
 * @uses       \Zend\GData\YouTube\Extension\School
 * @uses       \Zend\GData\YouTube\Extension\Statistics
 * @uses       \Zend\GData\YouTube\Extension\Username
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage YouTube
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class UserProfileEntry extends \Zend\GData\Entry
{

    protected $_entryClassName = 'Zend\GData\YouTube\UserProfileEntry';

    /**
     * Nested feed links
     *
     * @var array
     */
    protected $_feedLink = array();

    /**
     * The username for this profile entry
     *
     * @var string
     */
    protected $_username = null;

    /**
     * The description of the user
     *
     * @var string
     */
    protected $_description = null;

    /**
     * The contents of the 'About Me' field.
     *
     * @var string
     */
    protected $_aboutMe = null;

    /**
     * The age of the user
     *
     * @var int
     */
    protected $_age = null;

    /**
     * Books of interest to the user
     *
     * @var string
     */
    protected $_books = null;

    /**
     * Company
     *
     * @var string
     */
    protected $_company = null;

    /**
     * Hobbies
     *
     * @var string
     */
    protected $_hobbies = null;

    /**
     * Hometown
     *
     * @var string
     */
    protected $_hometown = null;

    /**
     * Location
     *
     * @var string
     */
    protected $_location = null;

    /**
     * Movies
     *
     * @var string
     */
    protected $_movies = null;

    /**
     * Music
     *
     * @var string
     */
    protected $_music = null;

    /**
     * Occupation
     *
     * @var string
     */
    protected $_occupation = null;

    /**
     * School
     *
     * @var string
     */
    protected $_school = null;

    /**
     * Gender
     *
     * @var string
     */
    protected $_gender = null;

    /**
     * Relationship
     *
     * @var string
     */
    protected $_relationship = null;

    /**
     * First name
     *
     * @var string
     */
    protected $_firstName = null;

    /**
     * Last name
     *
     * @var string
     */
    protected $_lastName = null;

    /**
     * Statistics
     *
     * @var \Zend\GData\YouTube\Extension\Statistics
     */
    protected $_statistics = null;

    /**
     * Thumbnail
     *
     * @var \Zend\GData\Media\Extension\MediaThumbnail
     */
    protected $_thumbnail = null;

    /**
     * Creates a User Profile entry, representing an individual user
     * and their attributes.
     *
     * @param DOMElement $element (optional) DOMElement from which this
     *          object should be constructed.
     */
    public function __construct($element = null)
    {
        $this->registerAllNamespaces(YouTube::$namespaces);
        parent::__construct($element);
    }

    /**
     * Retrieves a DOMElement which corresponds to this element and all
     * child properties.  This is used to build an entry back into a DOM
     * and eventually XML text for sending to the server upon updates, or
     * for application storage/persistence.
     *
     * @param DOMDocument $doc The DOMDocument used to construct DOMElements
     * @return DOMElement The DOMElement representing this element and all
     * child properties.
     */
    public function getDOM($doc = null, $majorVersion = 1, $minorVersion = null)
    {
        $element = parent::getDOM($doc, $majorVersion, $minorVersion);
        if ($this->_description != null) {
            $element->appendChild($this->_description->getDOM($element->ownerDocument));
        }
        if ($this->_aboutMe != null) {
            $element->appendChild($this->_aboutMe->getDOM($element->ownerDocument));
        }
        if ($this->_age != null) {
            $element->appendChild($this->_age->getDOM($element->ownerDocument));
        }
        if ($this->_username != null) {
            $element->appendChild($this->_username->getDOM($element->ownerDocument));
        }
        if ($this->_books != null) {
            $element->appendChild($this->_books->getDOM($element->ownerDocument));
        }
        if ($this->_company != null) {
            $element->appendChild($this->_company->getDOM($element->ownerDocument));
        }
        if ($this->_hobbies != null) {
            $element->appendChild($this->_hobbies->getDOM($element->ownerDocument));
        }
        if ($this->_hometown != null) {
            $element->appendChild($this->_hometown->getDOM($element->ownerDocument));
        }
        if ($this->_location != null) {
            $element->appendChild($this->_location->getDOM($element->ownerDocument));
        }
        if ($this->_movies != null) {
            $element->appendChild($this->_movies->getDOM($element->ownerDocument));
        }
        if ($this->_music != null) {
            $element->appendChild($this->_music->getDOM($element->ownerDocument));
        }
        if ($this->_occupation != null) {
            $element->appendChild($this->_occupation->getDOM($element->ownerDocument));
        }
        if ($this->_school != null) {
            $element->appendChild($this->_school->getDOM($element->ownerDocument));
        }
        if ($this->_gender != null) {
            $element->appendChild($this->_gender->getDOM($element->ownerDocument));
        }
        if ($this->_relationship != null) {
            $element->appendChild($this->_relationship->getDOM($element->ownerDocument));
        }
        if ($this->_firstName != null) {
            $element->appendChild($this->_firstName->getDOM($element->ownerDocument));
        }
        if ($this->_lastName != null) {
            $element->appendChild($this->_lastName->getDOM($element->ownerDocument));
        }
        if ($this->_statistics != null) {
            $element->appendChild($this->_statistics->getDOM($element->ownerDocument));
        }
        if ($this->_thumbnail != null) {
            $element->appendChild($this->_thumbnail->getDOM($element->ownerDocument));
        }
        if ($this->_feedLink != null) {
            foreach ($this->_feedLink as $feedLink) {
                $element->appendChild($feedLink->getDOM($element->ownerDocument));
            }
        }
        return $element;
    }

    /**
     * Creates individual Entry objects of the appropriate type and
     * stores them in the $_entry array based upon DOM data.
     *
     * @param DOMNode $child The DOMNode to process
     */
    protected function takeChildFromDOM($child)
    {
        $absoluteNodeName = $child->namespaceURI . ':' . $child->localName;
        switch ($absoluteNodeName) {
        case $this->lookupNamespace('yt') . ':' . 'description':
            $description = new Extension\Description();
            $description->transferFromDOM($child);
            $this->_description = $description;
            break;
        case $this->lookupNamespace('yt') . ':' . 'aboutMe':
            $aboutMe = new Extension\AboutMe();
            $aboutMe->transferFromDOM($child);
            $this->_aboutMe = $aboutMe;
            break;
        case $this->lookupNamespace('yt') . ':' . 'age':
            $age = new Extension\Age();
            $age->transferFromDOM($child);
            $this->_age = $age;
            break;
        case $this->lookupNamespace('yt') . ':' . 'username':
            $username = new Extension\Username();
            $username->transferFromDOM($child);
            $this->_username = $username;
            break;
        case $this->lookupNamespace('yt') . ':' . 'books':
            $books = new Extension\Books();
            $books->transferFromDOM($child);
            $this->_books = $books;
            break;
        case $this->lookupNamespace('yt') . ':' . 'company':
            $company = new Extension\Company();
            $company->transferFromDOM($child);
            $this->_company = $company;
            break;
        case $this->lookupNamespace('yt') . ':' . 'hobbies':
            $hobbies = new Extension\Hobbies();
            $hobbies->transferFromDOM($child);
            $this->_hobbies = $hobbies;
            break;
        case $this->lookupNamespace('yt') . ':' . 'hometown':
            $hometown = new Extension\Hometown();
            $hometown->transferFromDOM($child);
            $this->_hometown = $hometown;
            break;
        case $this->lookupNamespace('yt') . ':' . 'location':
            $location = new Extension\Location();
            $location->transferFromDOM($child);
            $this->_location = $location;
            break;
        case $this->lookupNamespace('yt') . ':' . 'movies':
            $movies = new Extension\Movies();
            $movies->transferFromDOM($child);
            $this->_movies = $movies;
            break;
        case $this->lookupNamespace('yt') . ':' . 'music':
            $music = new Extension\Music();
            $music->transferFromDOM($child);
            $this->_music = $music;
            break;
        case $this->lookupNamespace('yt') . ':' . 'occupation':
            $occupation = new Extension\Occupation();
            $occupation->transferFromDOM($child);
            $this->_occupation = $occupation;
            break;
        case $this->lookupNamespace('yt') . ':' . 'school':
            $school = new Extension\School();
            $school->transferFromDOM($child);
            $this->_school = $school;
            break;
        case $this->lookupNamespace('yt') . ':' . 'gender':
            $gender = new Extension\Gender();
            $gender->transferFromDOM($child);
            $this->_gender = $gender;
            break;
        case $this->lookupNamespace('yt') . ':' . 'relationship':
            $relationship = new Extension\Relationship();
            $relationship->transferFromDOM($child);
            $this->_relationship = $relationship;
            break;
        case $this->lookupNamespace('yt') . ':' . 'firstName':
            $firstName = new Extension\FirstName();
            $firstName->transferFromDOM($child);
            $this->_firstName = $firstName;
            break;
        case $this->lookupNamespace('yt') . ':' . 'lastName':
            $lastName = new Extension\LastName();
            $lastName->transferFromDOM($child);
            $this->_lastName = $lastName;
            break;
        case $this->lookupNamespace('yt') . ':' . 'statistics':
            $statistics = new Extension\Statistics();
            $statistics->transferFromDOM($child);
            $this->_statistics = $statistics;
            break;
        case $this->lookupNamespace('media') . ':' . 'thumbnail':
            $thumbnail = new \Zend\GData\Media\Extension\MediaThumbnail();
            $thumbnail->transferFromDOM($child);
            $this->_thumbnail = $thumbnail;
            break;
        case $this->lookupNamespace('gd') . ':' . 'feedLink':
            $feedLink = new \Zend\GData\Extension\FeedLink();
            $feedLink->transferFromDOM($child);
            $this->_feedLink[] = $feedLink;
            break;
        default:
            parent::takeChildFromDOM($child);
            break;
        }
    }

    /**
     * Sets the content of the 'about me' field.
     *
     * @param \Zend\GData\YouTube\Extension\AboutMe $aboutMe The 'about me'
     *        information.
     * @throws \Zend\GData\App\VersionException
     * @return \Zend\GData\YouTube\UserProfileEntry Provides a fluent interface
     */
    public function setAboutMe($aboutMe = null)
    {
        if (($this->getMajorProtocolVersion() == null) ||
           ($this->getMajorProtocolVersion() == 1)) {
            throw new App\VersionException('The setAboutMe ' .
                ' method is only supported as of version 2 of the YouTube ' .
                'API.');
        } else {
            $this->_aboutMe = $aboutMe;
            return $this;
        }
    }

    /**
     * Returns the contents of the 'about me' field.
     *
     * @throws \Zend\GData\App\VersionException
     * @return \Zend\GData\YouTube\Extension\AboutMe  The 'about me' information
     */
    public function getAboutMe()
    {
        if (($this->getMajorProtocolVersion() == null) ||
           ($this->getMajorProtocolVersion() == 1)) {
            throw new App\VersionException('The getAboutMe ' .
                ' method is only supported as of version 2 of the YouTube ' .
                'API.');
        } else {
            return $this->_aboutMe;
        }
    }

    /**
     * Sets the content of the 'first name' field.
     *
     * @param \Zend\GData\YouTube\Extension\FirstName $firstName The first name
     * @throws \Zend\GData\App\VersionException
     * @return \Zend\GData\YouTube\UserProfileEntry Provides a fluent interface
     */
    public function setFirstName($firstName = null)
    {
        if (($this->getMajorProtocolVersion() == null) ||
           ($this->getMajorProtocolVersion() == 1)) {
            throw new App\VersionException('The setFirstName ' .
                ' method is only supported as of version 2 of the YouTube ' .
                'API.');
        } else {
            $this->_firstName = $firstName;
            return $this;
        }
    }

    /**
     * Returns the first name
     *
     * @throws \Zend\GData\App\VersionException
     * @return \Zend\GData\YouTube\Extension\FirstName  The first name
     */
    public function getFirstName()
    {
        if (($this->getMajorProtocolVersion() == null) ||
           ($this->getMajorProtocolVersion() == 1)) {
            throw new App\VersionException('The getFirstName ' .
                ' method is only supported as of version 2 of the YouTube ' .
                'API.');
        } else {
            return $this->_firstName;
        }
    }

    /**
     * Sets the content of the 'last name' field.
     *
     * @param \Zend\GData\YouTube\Extension\LastName $lastName The last name
     * @throws \Zend\GData\App\VersionException
     * @return \Zend\GData\YouTube\UserProfileEntry Provides a fluent interface
     */
    public function setLastName($lastName = null)
    {
        if (($this->getMajorProtocolVersion() == null) ||
           ($this->getMajorProtocolVersion() == 1)) {
            throw new App\VersionException('The setLastName ' .
                ' method is only supported as of version 2 of the YouTube ' .
                'API.');
        } else {
            $this->_lastName = $lastName;
            return $this;
        }
    }

    /**
     * Returns the last name
     *
     * @throws \Zend\GData\App\VersionException
     * @return \Zend\GData\YouTube\Extension\LastName  The last name
     */
    public function getLastName()
    {
        if (($this->getMajorProtocolVersion() == null) ||
           ($this->getMajorProtocolVersion() == 1)) {
            throw new App\VersionException('The getLastName ' .
                ' method is only supported as of version 2 of the YouTube ' .
                'API.');
        } else {
            return $this->_lastName;
        }
    }

    /**
     * Returns the statistics
     *
     * @throws \Zend\GData\App\VersionException
     * @return \Zend\GData\YouTube\Extension\Statistics  The profile statistics
     */
    public function getStatistics()
    {
        if (($this->getMajorProtocolVersion() == null) ||
           ($this->getMajorProtocolVersion() == 1)) {
            throw new App\VersionException('The getStatistics ' .
                ' method is only supported as of version 2 of the YouTube ' .
                'API.');
        } else {
            return $this->_statistics;
        }
    }

    /**
     * Returns the thumbnail
     *
     * @throws \Zend\GData\App\VersionException
     * @return \Zend\GData\Media\Extension\MediaThumbnail The profile thumbnail
     */
    public function getThumbnail()
    {
        if (($this->getMajorProtocolVersion() == null) ||
           ($this->getMajorProtocolVersion() == 1)) {
            throw new App\VersionException('The getThumbnail ' .
                ' method is only supported as of version 2 of the YouTube ' .
                'API.');
        } else {
            return $this->_thumbnail;
        }
    }

    /**
     * Sets the age
     *
     * @param \Zend\GData\YouTube\Extension\Age $age The age
     * @return \Zend\GData\YouTube\UserProfileEntry Provides a fluent interface
     */
    public function setAge($age = null)
    {
        $this->_age = $age;
        return $this;
    }

    /**
     * Returns the age
     *
     * @return \Zend\GData\YouTube\Extension\Age  The age
     */
    public function getAge()
    {
        return $this->_age;
    }

    /**
     * Sets the username
     *
     * @param \Zend\GData\YouTube\Extension\Username $username The username
     * @return \Zend\GData\YouTube\UserProfileEntry Provides a fluent interface
     */
    public function setUsername($username = null)
    {
        $this->_username = $username;
        return $this;
    }

    /**
     * Returns the username
     *
     * @return \Zend\GData\YouTube\Extension\Username  The username
     */
    public function getUsername()
    {
        return $this->_username;
    }

    /**
     * Sets the books
     *
     * @param \Zend\GData\YouTube\Extension\Books $books The books
     * @return \Zend\GData\YouTube\UserProfileEntry Provides a fluent interface
     */
    public function setBooks($books = null)
    {
        $this->_books = $books;
        return $this;
    }

    /**
     * Returns the books
     *
     * @return \Zend\GData\YouTube\Extension\Books  The books
     */
    public function getBooks()
    {
        return $this->_books;
    }

    /**
     * Sets the company
     *
     * @param \Zend\GData\YouTube\Extension\Company $company The company
     * @return \Zend\GData\YouTube\UserProfileEntry Provides a fluent interface
     */
    public function setCompany($company = null)
    {
        $this->_company = $company;
        return $this;
    }

    /**
     * Returns the company
     *
     * @return \Zend\GData\YouTube\Extension\Company  The company
     */
    public function getCompany()
    {
        return $this->_company;
    }

    /**
     * Sets the hobbies
     *
     * @param \Zend\GData\YouTube\Extension\Hobbies $hobbies The hobbies
     * @return \Zend\GData\YouTube\UserProfileEntry Provides a fluent interface
     */
    public function setHobbies($hobbies = null)
    {
        $this->_hobbies = $hobbies;
        return $this;
    }

    /**
     * Returns the hobbies
     *
     * @return \Zend\GData\YouTube\Extension\Hobbies  The hobbies
     */
    public function getHobbies()
    {
        return $this->_hobbies;
    }

    /**
     * Sets the hometown
     *
     * @param \Zend\GData\YouTube\Extension\Hometown $hometown The hometown
     * @return \Zend\GData\YouTube\UserProfileEntry Provides a fluent interface
     */
    public function setHometown($hometown = null)
    {
        $this->_hometown = $hometown;
        return $this;
    }

    /**
     * Returns the hometown
     *
     * @return \Zend\GData\YouTube\Extension\Hometown  The hometown
     */
    public function getHometown()
    {
        return $this->_hometown;
    }

    /**
     * Sets the location
     *
     * @param \Zend\GData\YouTube\Extension\Location $location The location
     * @return \Zend\GData\YouTube\UserProfileEntry Provides a fluent interface
     */
    public function setLocation($location = null)
    {
        $this->_location = $location;
        return $this;
    }

    /**
     * Returns the location
     *
     * @return \Zend\GData\YouTube\Extension\Location  The location
     */
    public function getLocation()
    {
        return $this->_location;
    }

    /**
     * Sets the movies
     *
     * @param \Zend\GData\YouTube\Extension\Movies $movies The movies
     * @return \Zend\GData\YouTube\UserProfileEntry Provides a fluent interface
     */
    public function setMovies($movies = null)
    {
        $this->_movies = $movies;
        return $this;
    }

    /**
     * Returns the movies
     *
     * @return \Zend\GData\YouTube\Extension\Movies  The movies
     */
    public function getMovies()
    {
        return $this->_movies;
    }

    /**
     * Sets the music
     *
     * @param \Zend\GData\YouTube\Extension\Music $music The music
     * @return \Zend\GData\YouTube\UserProfileEntry Provides a fluent interface
     */
    public function setMusic($music = null)
    {
        $this->_music = $music;
        return $this;
    }

    /**
     * Returns the music
     *
     * @return \Zend\GData\YouTube\Extension\Music  The music
     */
    public function getMusic()
    {
        return $this->_music;
    }

    /**
     * Sets the occupation
     *
     * @param \Zend\GData\YouTube\Extension\Occupation $occupation The occupation
     * @return \Zend\GData\YouTube\UserProfileEntry Provides a fluent interface
     */
    public function setOccupation($occupation = null)
    {
        $this->_occupation = $occupation;
        return $this;
    }

    /**
     * Returns the occupation
     *
     * @return \Zend\GData\YouTube\Extension\Occupation  The occupation
     */
    public function getOccupation()
    {
        return $this->_occupation;
    }

    /**
     * Sets the school
     *
     * @param \Zend\GData\YouTube\Extension\School $school The school
     * @return \Zend\GData\YouTube\UserProfileEntry Provides a fluent interface
     */
    public function setSchool($school = null)
    {
        $this->_school = $school;
        return $this;
    }

    /**
     * Returns the school
     *
     * @return \Zend\GData\YouTube\Extension\School  The school
     */
    public function getSchool()
    {
        return $this->_school;
    }

    /**
     * Sets the gender
     *
     * @param \Zend\GData\YouTube\Extension\Gender $gender The gender
     * @return \Zend\GData\YouTube\UserProfileEntry Provides a fluent interface
     */
    public function setGender($gender = null)
    {
        $this->_gender = $gender;
        return $this;
    }

    /**
     * Returns the gender
     *
     * @return \Zend\GData\YouTube\Extension\Gender  The gender
     */
    public function getGender()
    {
        return $this->_gender;
    }

    /**
     * Sets the relationship
     *
     * @param \Zend\GData\YouTube\Extension\Relationship $relationship The relationship
     * @return \Zend\GData\YouTube\UserProfileEntry Provides a fluent interface
     */
    public function setRelationship($relationship = null)
    {
        $this->_relationship = $relationship;
        return $this;
    }

    /**
     * Returns the relationship
     *
     * @return \Zend\GData\YouTube\Extension\Relationship  The relationship
     */
    public function getRelationship()
    {
        return $this->_relationship;
    }

    /**
     * Sets the array of embedded feeds related to the video
     *
     * @param array $feedLink The array of embedded feeds relating to the video
     * @return \Zend\GData\YouTube\UserProfileEntry Provides a fluent interface
     */
    public function setFeedLink($feedLink = null)
    {
        $this->_feedLink = $feedLink;
        return $this;
    }

    /**
     * Get the feed link property for this entry.
     *
     * @see setFeedLink
     * @param string $rel (optional) The rel value of the link to be found.
     *          If null, the array of links is returned.
     * @return mixed If $rel is specified, a \Zend\GData\Extension\FeedLink
     *          object corresponding to the requested rel value is returned
     *          if found, or null if the requested value is not found. If
     *          $rel is null or not specified, an array of all available
     *          feed links for this entry is returned, or null if no feed
     *          links are set.
     */
    public function getFeedLink($rel = null)
    {
        if ($rel == null) {
            return $this->_feedLink;
        } else {
            foreach ($this->_feedLink as $feedLink) {
                if ($feedLink->rel == $rel) {
                    return $feedLink;
                }
            }
            return null;
        }
    }

    /**
     * Returns the URL in the gd:feedLink with the provided rel value
     *
     * @param string $rel The rel value to find
     * @return mixed Either the URL as a string or null if a feedLink wasn't
     *     found with the provided rel value
     */
    public function getFeedLinkHref($rel)
    {
        $feedLink = $this->getFeedLink($rel);
        if ($feedLink !== null) {
            return $feedLink->href;
        } else {
            return null;
        }
    }

    /**
     * Returns the URL of the playlist list feed
     *
     * @return string The URL of the playlist video feed
     */
    public function getPlaylistListFeedUrl()
    {
        return $this->getFeedLinkHref(YouTube::USER_PLAYLISTS_REL);
    }

    /**
     * Returns the URL of the uploads feed
     *
     * @return string The URL of the uploads video feed
     */
    public function getUploadsFeedUrl()
    {
        return $this->getFeedLinkHref(YouTube::USER_UPLOADS_REL);
    }

    /**
     * Returns the URL of the subscriptions feed
     *
     * @return string The URL of the subscriptions feed
     */
    public function getSubscriptionsFeedUrl()
    {
        return $this->getFeedLinkHref(YouTube::USER_SUBSCRIPTIONS_REL);
    }

    /**
     * Returns the URL of the contacts feed
     *
     * @return string The URL of the contacts feed
     */
    public function getContactsFeedUrl()
    {
        return $this->getFeedLinkHref(YouTube::USER_CONTACTS_REL);
    }

    /**
     * Returns the URL of the favorites feed
     *
     * @return string The URL of the favorites feed
     */
    public function getFavoritesFeedUrl()
    {
        return $this->getFeedLinkHref(YouTube::USER_FAVORITES_REL);
    }

}
