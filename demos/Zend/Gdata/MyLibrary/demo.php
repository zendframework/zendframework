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
 * @see Zend_Loader
 */
require_once 'Zend/Loader.php';

/**
 * @see Zend_Gdata_Books
 */
Zend_Loader::loadClass('Zend_Gdata_Books');

/**
 * @see Zend_Gdata_ClientLogin
 */
Zend_Loader::loadClass('Zend_Gdata_ClientLogin');

/**
 * @see Zend_Gdata_App_AuthException
 */
Zend_Loader::loadClass('Zend_Gdata_App_AuthException');


class SimpleDemo {
    /**
     * Constructor
     *
     * @param  string $email
     * @param  string $password
     * @return void
     */
    public function __construct($email, $password)
    {
        try {
          $client = Zend_Gdata_ClientLogin::getHttpClient($email, $password,
                    Zend_Gdata_Books::AUTH_SERVICE_NAME);
        } catch (Zend_Gdata_App_AuthException $ae) {
          exit("Error: ". $ae->getMessage() ."\nCredentials provided were ".
               "email: [$email] and password [$password].\n");
        }
        $this->gdClient = new Zend_Gdata_Books($client);
    }

    /**
     * Print the content of a feed
     *
     * @param  Zend_Gdata_Gbase_Feed $feed
     * @return void
     */
    public function printFeed($feed)
    {
        $i = 0;
        foreach($feed as $entry) {
            $titles = $entry->getTitles();
            $rating = $entry->getRating();
            if (count($titles)) {
                if (!is_object($rating)) {
                    $rating_str = "?";
                } else {
                    $rating_str = $rating->getAverage();
                }
                print $i." ".$titles[0]->getText().
                    ", Rating: ".$rating_str."\n";
                $i++;
            }
        }
    }

    /**
     * List books in the My library feed
     *
     * @return void
     */
    public function listLibrary()
    {
        $feed = $this->gdClient->getUserLibraryFeed();
        print "== Books in my library ==\n";
        $this->printFeed($feed);
        print "\n";
    }

    /**
     * List books in the annotation feed.
     *
     * @return void
     */
    public function listReviewed()
    {
        $feed = $this->gdClient->getUserLibraryFeed(
            Zend_Gdata_Books::MY_ANNOTATION_FEED_URI);
        print "== Books I annotated ==\n";
        $this->printFeed($feed);
        print "\n";
    }

    /**
     * Add an arbitrary book to the library feed.
     *
     * @param string $volumeId Volume to the library
     * @return void
     */
    public function addBookToLibrary($volumeId)
    {
        $entry = new Zend_Gdata_Books_VolumeEntry();
        $entry->setId(
            new Zend_Gdata_App_Extension_Id($volumeId));
        print "Inserting ".$volumeId."\n\n";
        return $this->gdClient->insertVolume($entry);
    }

    /**
     * Add an arbitrary book to the library feed.
     *
     * @param string $volumeId Volume to add a rating to
     * @param float $rating Numeric rating from 0 to 5
     * @return void
     */
    public function addRating($volumeId, $rating)
    {
        $entry = new Zend_Gdata_Books_VolumeEntry();
        $entry->setId(
            new Zend_Gdata_App_Extension_Id($volumeId));
        $entry->setRating(
            new Zend_Gdata_Extension_Rating($rating, "0", 5, 1));
        print "Inserting a rating of ".$rating." for ".$volumeId."\n\n";
        return $this->gdClient->insertVolume($entry,
            Zend_Gdata_Books::MY_ANNOTATION_FEED_URI);
    }

    /**
     * Remove an an arbitrary book from a feed (either remove
     * from library feed or remove the annotations from annotation
     * feed).
     *
     * @param Zend_Gdata_Books_VolumeEntry $entry
     * @return void
     */
    public function removeBook($entry)
    {
        print "Deleting ".$entry->getId()->getText()."\n\n";
        $this->gdClient->deleteVolume($entry);
    }

    /**
     * Main logic for the demo.
     *
     * @return void
     */
    public function run()
    {
        $test_volume = "8YEAAAAAYAAJ";

        // Playing with the library feed
        $this->listLibrary();

        $entry = $this->addBookToLibrary($test_volume);
        $this->listLibrary();

        $this->removeBook($entry);
        $this->listLibrary();

        // Playing with the annotation feed
        $this->listReviewed();

        $entry = $this->addRating($test_volume, 4.0);
        $this->listReviewed();

        $this->removeBook($entry);
        $this->listReviewed();
    }
}

/**
 * getInput
 *
 * @param  string $text
 * @return string
 */
function getInput($text)
{
    echo $text.': ';
    return trim(fgets(STDIN));
}

echo "Books Gdata API - my library demo\n\n";
$email = null;
$pass = null;

// process command line options
foreach ($argv as $argument) {
    $argParts = explode('=', $argument);
    if ($argParts[0] == '--email') {
        $email = $argParts[1];
    } else if ($argParts[0] == '--pass') {
        $pass = $argParts[1];
    }
}

if (($email == null) || ($pass == null)) {
    $email = getInput(
        "Please enter your email address [example: username@gmail.com]");
    $pass = getInput(
        "Please enter your password [example: mypassword]");
}

$demo = new SimpleDemo($email, $pass);
$demo->run();
