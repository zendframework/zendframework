<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Test
 */
namespace Zend\Test\PHPUnit\Controller;

use PHPUnit_Framework_ExpectationFailedException;

/**
 * @category   Zend
 * @package    Zend_Test
 * @subpackage PHPUnit
 */
abstract class AbstractHttpControllerTestCase extends AbstractControllerTestCase
{
    protected $useConsoleRequest = false;

    /**
     * Get response header by key
     * @param string $header
     * @return Zend\Http\Header\HeaderInterface|false
     */
    protected function getResponseHeader($header)
    {
        $response = $this->getResponse();
        $headers = $response->getHeaders();
        $responseHeader = $headers->get($header, false);
        return $responseHeader;
    }

    /**
     * Assert response header exists
     *
     * @param  string $header
     * @return void
     */
    public function assertHeader($header)
    {
        $responseHeader = $this->getResponseHeader($header);
        if(false === $responseHeader) {
            throw new PHPUnit_Framework_ExpectationFailedException(sprintf(
                'Failed asserting response header "%s" found', $header
            ));
        }
        $this->assertNotEquals(false, $responseHeader);
    }

    /**
     * Assert response header does not exist
     *
     * @param  string $header
     * @return void
     */
    public function assertNotHeader($header)
    {
        $responseHeader = $this->getResponseHeader($header);
        if(false !== $responseHeader) {
            throw new PHPUnit_Framework_ExpectationFailedException(sprintf(
                'Failed asserting response header "%s" WAS NOT found', $header
            ));
        }
        $this->assertEquals(false, $responseHeader);
    }

    /**
     * Assert response header exists and contains the given string
     *
     * @param  string $header
     * @param  string $match
     * @return void
     */
    public function assertHeaderContains($header, $match)
    {
        $responseHeader = $this->getResponseHeader($header);
        if(!$responseHeader) {
            throw new PHPUnit_Framework_ExpectationFailedException(sprintf(
                'Failed asserting response header, header "%s" do not exists', $header
            ));
        }
        if($match != $responseHeader->getFieldValue()) {
            throw new PHPUnit_Framework_ExpectationFailedException(sprintf(
                'Failed asserting response header "%s" exists and contains "%s", actual content is "%s"',
                $header, $match, $responseHeader->getFieldValue()
            ));
        }
        $this->assertEquals($match, $responseHeader->getFieldValue());
    }

    /**
     * Assert response header exists and contains the given string
     *
     * @param  string $header
     * @param  string $match
     * @return void
     */
    public function assertNotHeaderContains($header, $match)
    {
        $responseHeader = $this->getResponseHeader($header);
        if(!$responseHeader) {
            throw new PHPUnit_Framework_ExpectationFailedException(sprintf(
                'Failed asserting response header, header "%s" do not exists', $header
            ));
        }
        if($match == $responseHeader->getFieldValue()) {
            throw new PHPUnit_Framework_ExpectationFailedException(sprintf(
                'Failed asserting response header "%s" DOES NOT CONTAIN "%s"',
                $header, $match
            ));
        }
        $this->assertNotEquals($match, $responseHeader->getFieldValue());
    }

    /**
     * Assert response header exists and matches the given pattern
     *
     * @param  string $header
     * @param  string $pattern
     * @return void
     */
    public function assertHeaderRegex($header, $pattern)
    {
        $responseHeader = $this->getResponseHeader($header);;
        if(!$responseHeader) {
            throw new PHPUnit_Framework_ExpectationFailedException(sprintf(
                'Failed asserting response header, header "%s" do not exists', $header
            ));
        }
        if(!preg_match($pattern, $responseHeader->getFieldValue())) {
            throw new PHPUnit_Framework_ExpectationFailedException(sprintf(
                'Failed asserting response header "%s" exists and matches regex "%s", actual content is "%s"',
                $header, $pattern, $responseHeader->getFieldValue()
            ));
        }
        $this->assertEquals(true, (boolean)preg_match($pattern, $responseHeader->getFieldValue()));
    }

    /**
     * Assert response header does not exist and/or does not match the given regex
     *
     * @param  string $header
     * @param  string $pattern
     * @return void
     */
    public function assertNotHeaderRegex($header, $pattern)
    {
        $responseHeader = $this->getResponseHeader($header);
        if(!$responseHeader) {
            throw new PHPUnit_Framework_ExpectationFailedException(sprintf(
                'Failed asserting response header, header "%s" do not exists', $header
            ));
        }
        if(preg_match($pattern, $responseHeader->getFieldValue())) {
            throw new PHPUnit_Framework_ExpectationFailedException(sprintf(
                'Failed asserting response header "%s" DOES NOT MATCH regex "%s"',
                $header, $pattern
            ));
        }
        $this->assertEquals(false, (boolean)preg_match($pattern, $responseHeader->getFieldValue()));
    }

    /**
     * Assert that response is a redirect
     *
     * @return void
     */
    public function assertRedirect()
    {
        $responseHeader = $this->getResponseHeader('Location');
        if(false === $responseHeader) {
            throw new PHPUnit_Framework_ExpectationFailedException(sprintf(
                'Failed asserting response is NOT a redirect'
            ));
        }
        $this->assertNotEquals(false, $responseHeader);
    }

    /**
     * Assert that response is NOT a redirect
     *
     * @param  string $message
     * @return void
     */
    public function assertNotRedirect()
    {
        $responseHeader = $this->getResponseHeader('Location');
        if(false !== $responseHeader) {
            throw new PHPUnit_Framework_ExpectationFailedException(sprintf(
                'Failed asserting response is a redirect, actual redirection is "%s"',
                $responseHeader->getFieldValue()
            ));
        }
        $this->assertEquals(false, $responseHeader);
    }

    /**
     * Assert that response redirects to given URL
     *
     * @param  string $url
     * @return void
     */
    public function assertRedirectTo($url)
    {
        $responseHeader = $this->getResponseHeader('Location');
        if(!$responseHeader) {
            throw new PHPUnit_Framework_ExpectationFailedException(sprintf(
                'Failed asserting response is a redirect'
            ));
        }
        if($url != $responseHeader->getFieldValue()) {
            throw new PHPUnit_Framework_ExpectationFailedException(sprintf(
                'Failed asserting response redirects to "%s", actual redirection is "%s"',
                $url, $responseHeader->getFieldValue()
            ));
        }
        $this->assertEquals($url, $responseHeader->getFieldValue());
    }

    /**
     * Assert that response does not redirect to given URL
     *
     * @param  string $url
     * @param  string $message
     * @return void
     */
    public function assertNotRedirectTo($url)
    {
        $responseHeader = $this->getResponseHeader('Location');
        if(!$responseHeader) {
            throw new PHPUnit_Framework_ExpectationFailedException(sprintf(
                'Failed asserting response is a redirect'
            ));
        }
        if($url == $responseHeader->getFieldValue()) {
            throw new PHPUnit_Framework_ExpectationFailedException(sprintf(
                'Failed asserting response redirects to "%s"', $url
            ));
        }
        $this->assertNotEquals($url, $responseHeader->getFieldValue());
    }

    /**
     * Assert that redirect location matches pattern
     *
     * @param  string $pattern
     * @return void
     */
    public function assertRedirectRegex($pattern)
    {
        $responseHeader = $this->getResponseHeader('Location');
        if(!$responseHeader) {
            throw new PHPUnit_Framework_ExpectationFailedException(sprintf(
                'Failed asserting response is a redirect'
            ));
        }
        if(!preg_match($pattern, $responseHeader->getFieldValue())) {
            throw new PHPUnit_Framework_ExpectationFailedException(sprintf(
                'Failed asserting response redirects to URL MATCHING "%s", actual redirection is "%s"',
                $pattern, $responseHeader->getFieldValue()
            ));
        }
        $this->assertEquals(true, (boolean)preg_match($pattern, $responseHeader->getFieldValue()));
    }

    /**
     * Assert that redirect location does not match pattern
     *
     * @param  string $pattern
     * @return void
     */
    public function assertNotRedirectRegex($pattern)
    {
        $responseHeader = $this->getResponseHeader('Location');
        if(!$responseHeader) {
            throw new PHPUnit_Framework_ExpectationFailedException(sprintf(
                'Failed asserting response is a redirect'
            ));
        }
        if(preg_match($pattern, $responseHeader->getFieldValue())) {
            throw new PHPUnit_Framework_ExpectationFailedException(sprintf(
                'Failed asserting response DOES NOT redirect to URL MATCHING "%s"', $pattern
            ));
        }
        $this->assertEquals(false, (boolean)preg_match($pattern, $responseHeader->getFieldValue()));
    }
}
