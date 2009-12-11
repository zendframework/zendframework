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
 * @package    Zend_Service_Audioscrobbler
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

require_once dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'TestHelper.php';

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Service_Audioscrobbler_AllTests::main');
}

require_once 'Zend/Service/Audioscrobbler/AudioscrobblerTest.php';
require_once 'Zend/Service/Audioscrobbler/ProfileTest.php';
require_once 'Zend/Service/Audioscrobbler/ArtistTest.php';
require_once 'Zend/Service/Audioscrobbler/AlbumDataTest.php';
require_once 'Zend/Service/Audioscrobbler/TrackDataTest.php';
require_once 'Zend/Service/Audioscrobbler/TagDataTest.php';
require_once 'Zend/Service/Audioscrobbler/GroupTest.php';

/**
 * @category   Zend
 * @package    Zend_Service_Audioscrobbler
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Service
 * @group      Zend_Service_Audioscrobbler
 */
class Zend_Service_Audioscrobbler_AllTests
{
    public static function main()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new PHPUnit_Framework_TestSuite('Zend Framework - Zend_Service_Audioscrobbler');

        $suite->addTestSuite('Zend_Service_Audioscrobbler_AudioscrobblerTest');
        $suite->addTestSuite('Zend_Service_Audioscrobbler_ProfileTest');
        $suite->addTestSuite('Zend_Service_Audioscrobbler_ArtistTest');
        $suite->addTestSuite('Zend_Service_Audioscrobbler_AlbumDataTest');
        $suite->addTestSuite('Zend_Service_Audioscrobbler_TrackDataTest');
        $suite->addTestSuite('Zend_Service_Audioscrobbler_TagDataTest');
        $suite->addTestSuite('Zend_Service_Audioscrobbler_GroupTest');

        return $suite;
    }
}

