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
 * @package    Zend_GData_YouTube
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\GData\YouTube;
use Zend\GData\YouTube;
use Zend\GData\App;

/**
 * @category   Zend
 * @package    Zend_GData_YouTube
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_GData
 * @group      Zend_GData_YouTube
 */
class VideoQueryTest extends \PHPUnit_Framework_TestCase
{

    public function testQueryStringConstruction () {
        $yt = new YouTube();
        $query = $yt->newVideoQuery();
        $query->setOrderBy('viewCount');
        $query->setVideoQuery('foobar');
        $expectedString = '?orderby=viewCount&vq=foobar';
        $this->assertEquals($expectedString, $query->getQueryString());
    }

    public function testQueryStringConstructionV2() {
        $yt = new YouTube();
        $query = $yt->newVideoQuery();
        $query->setOrderBy('viewCount');
        $query->setVideoQuery('version2');
        $expectedString = '?orderby=viewCount&q=version2';
        $this->assertEquals($expectedString, $query->getQueryString(2));
    }

    public function testSafeSearchQueryV2() {
        $yt = new YouTube();
        $query = $yt->newVideoQuery();
        $exceptionCaught = false;
        $query->setRacy('include');
        try {
            $query->getQueryString(2);
        } catch (App\VersionException $e) {
          $exceptionCaught = true;
        }
        $this->assertTrue($exceptionCaught, 'Zend\GData\App\VersionException' .
            ' expected but not found');
    }

    public function testLocationRadiusV1() {
        $yt = new YouTube();
        $query = $yt->newVideoQuery();
        $exceptionCaught = false;
        $query->setLocationRadius('1km');
        try {
            $query->getQueryString(1);
        } catch (App\VersionException $e) {
          $exceptionCaught = true;
        }
        $this->assertTrue($exceptionCaught, 'Zend\GData\App\VersionException' .
            ' expected but not found');
    }

    public function testLocationV2() {
        $yt = new YouTube();
        $query = $yt->newVideoQuery();
        $query->setLocation('-37.122,122.01');
        $expectedString = '?location=-37.122%2C122.01';
        $this->assertEquals($expectedString, $query->getQueryString(2));
    }

    public function testLocationExceptionOnNonNumericV2() {
        $yt = new YouTube();
        $query = $yt->newVideoQuery();
        $exceptionCaught = false;

        try {
            $query->setLocation('mars');
        } catch (App\InvalidArgumentException $e) {
            $exceptionCaught = true;
        }

        $this->assertTrue($exceptionCaught, 'Expected Zend_GData_App_' .
            'IllegalArgumentException when using alpha in setLocation');
    }

    public function testLocationExceptionOnOnlyOneCoordinateV2() {
        $yt = new YouTube();
        $query = $yt->newVideoQuery();
        $exceptionCaught = false;

        try {
            $query->setLocation('-25.001');
        } catch (App\InvalidArgumentException $e) {
            $exceptionCaught = true;
        }

        $this->assertTrue($exceptionCaught, 'Expected Zend_GData_App_' .
            'IllegalArgumentException when using only 1 coordinate ' .
            'in setLocation');
    }

    public function testUploaderExceptionOnInvalidV2() {
        $yt = new YouTube();
        $query = $yt->newVideoQuery();
        $exceptionCaught = false;

        try {
            $query->setUploader('invalid');
        } catch (App\InvalidArgumentException $e) {
            $exceptionCaught = true;
        }

        $this->assertTrue($exceptionCaught, 'Expected Zend_GData_App_' .
            'IllegalArgumentException when using invalid string in ' .
            'setUploader.');
    }

    public function testProjectionPresentInV2Query() {
        $yt = new YouTube();
        $query = $yt->newVideoQuery();
        $query->setVideoQuery('foo');
        $expectedString = 'http://gdata.youtube.com/feeds/api/videos?q=foo';
        $this->assertEquals($expectedString, $query->getQueryUrl(2));
    }

    public function testSafeSearchParametersInV2() {
        $yt = new YouTube();
        $query = $yt->newVideoQuery();
        $exceptionCaught = false;
        try {
            $query->setSafeSearch('invalid');
        } catch (App\InvalidArgumentException $e) {
            $exceptionCaught = true;
        }
        $this->assertTrue($exceptionCaught, 'Expected Zend_GData_App_' .
            'InvalidArgumentException when using invalid value for ' .
            'safeSearch.');
    }

    /**
     * @group ZF-8720
     */
    public function testVideoQuerySetLocationException()
    {
        $this->setExpectedException('Zend\GData\App\InvalidArgumentException');
        $yt = new YouTube();
        $query = $yt->newVideoQuery();
        $location = 'foobar';
        $this->assertNull($query->setLocation($location));
    }

    /**
     * @group ZF-8720
     */
    public function testVideoQuerySetLocationExceptionV2()
    {
        $this->setExpectedException('Zend\GData\App\InvalidArgumentException');
        $yt = new YouTube();
        $query = $yt->newVideoQuery();
        $location = '-100x,-200y';
        $this->assertNull($query->setLocation($location));
    }

    /**
     * @group ZF-8720
     */
    public function testVideoQuerySetLocationExceptionV3()
    {
        $this->setExpectedException('Zend\GData\App\InvalidArgumentException');
        $yt = new YouTube();
        $query = $yt->newVideoQuery();
        $location = '-100x,-200y!';
        $this->assertNull($query->setLocation($location));
    }

    /**
     * @group ZF-8720
     */
    public function testQueryExclamationMarkRemoveBug()
    {
        $yt = new YouTube();
        $query = $yt->newVideoQuery();

        $location = '37.42307,-122.08427';
        $this->assertNull($query->setLocation($location));
        $this->assertEquals($location, $query->getLocation());

        $location = '37.42307,-122.08427!';
        $this->assertNull($query->setLocation($location));
        $this->assertEquals($location, $query->getLocation());
    }
}
