<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Validator
 */

namespace ZendTest\Validator\Sitemap;

use Zend\Validator\Sitemap\Lastmod;

/**
 * @category   Zend
 * @package    Zend_Validator
 * @subpackage UnitTests
 * @group      Zend_Validator
 */
class LastmodTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Lastmod
     */
    protected $validator;

    protected function setUp()
    {
        $this->validator = new Lastmod();
    }

    /**
     * Tests valid change frequencies
     *
     */
    public function testValidChangefreqs()
    {
        $values = array(
            '1994-05-11T18:00:09-08:45',
            '1997-05-11T18:50:09+00:00',
            '1998-06-11T01:00:09-02:00',
            '1999-11-11T22:23:52+02:00',
            '1999-11-11T22:23+02:00',
            '2000-06-11',
            '2001-04-14',
            '2003-01-13',
            '2005-01-01',
            '2006-03-19',
            '2007-08-31',
            '2007-08-25'
        );

        foreach ($values as $value) {
            $this->assertSame(true, $this->validator->isValid($value));
        }
    }

    /**
     * Tests strings that should be invalid
     *
     */
    public function testInvalidStrings()
    {
        $values = array(
            '1995-05-11T18:60:09-08:45',
            '1996-05-11T18:50:09+25:00',
            '2002-13-11',
            '2004-00-01',
            '2006-01-01\n'
        );

        foreach ($values as $value) {
            $this->assertSame(false, $this->validator->isValid($value));
            $messages = $this->validator->getMessages();
            $this->assertContains('is not a valid', current($messages));
        }
    }

    /**
     * Tests values that are not strings
     *
     */
    public function testNotString()
    {
        $values = array(
            1, 1.4, null, new \stdClass(), true, false
        );

        foreach ($values as $value) {
            $this->assertSame(false, $this->validator->isValid($value));
            $messages = $this->validator->getMessages();
            $this->assertContains('String expected', current($messages));
        }
    }
}
