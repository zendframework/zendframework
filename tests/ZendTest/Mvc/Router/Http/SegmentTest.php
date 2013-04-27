<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Mvc
 */

namespace ZendTest\Mvc\Router\Http;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Http\Request;
use Zend\I18n\Translator\TextDomain;
use Zend\I18n\Translator\Translator;
use Zend\Stdlib\Request as BaseRequest;
use Zend\Mvc\Router\Http\Segment;
use ZendTest\I18n\Translator\TestAsset\Loader as TestLoader;
use ZendTest\Mvc\Router\FactoryTester;

class SegmentTest extends TestCase
{
    public static function routeProvider()
    {
        $translator = new Translator();
        $translator->setLocale('en-US');
        $enLoader     = new TestLoader();
        $deLoader     = new TestLoader();
        $domainLoader = new TestLoader();
        $enLoader->textDomain     = new TextDomain(array('fw' => 'framework'));
        $deLoader->textDomain     = new TextDomain(array('fw' => 'baukasten'));
        $domainLoader->textDomain = new TextDomain(array('fw' => 'fw-alternative'));
        $translator->getPluginManager()->setService('test-en',     $enLoader);
        $translator->getPluginManager()->setService('test-de',     $deLoader);
        $translator->getPluginManager()->setService('test-domain', $domainLoader);
        $translator->addTranslationFile('test-en', null, 'default', 'en-US');
        $translator->addTranslationFile('test-de', null, 'default', 'de-DE');
        $translator->addTranslationFile('test-domain', null, 'alternative', 'en-US');

        return array(
            'simple-match' => array(
                new Segment('/:foo'),
                '/bar',
                null,
                array('foo' => 'bar')
            ),
            'no-match-without-leading-slash' => array(
                new Segment(':foo'),
                '/bar/',
                null,
                null
            ),
            'no-match-with-trailing-slash' => array(
                new Segment('/:foo'),
                '/bar/',
                null,
                null
            ),
            'offset-skips-beginning' => array(
                new Segment(':foo'),
                '/bar',
                1,
                array('foo' => 'bar')
            ),
            'offset-enables-partial-matching' => array(
                new Segment('/:foo'),
                '/bar/baz',
                0,
                array('foo' => 'bar')
            ),
            'match-overrides-default' => array(
                new Segment('/:foo', array(), array('foo' => 'baz')),
                '/bar',
                null,
                array('foo' => 'bar')
            ),
            'constraints-prevent-match' => array(
                new Segment('/:foo', array('foo' => '\d+')),
                '/bar',
                null,
                null
            ),
            'constraints-allow-match' => array(
                new Segment('/:foo', array('foo' => '\d+')),
                '/123',
                null,
                array('foo' => '123')
            ),
            'constraints-override-non-standard-delimiter' => array(
                new Segment('/:foo{-}/bar', array('foo' => '[^/]+')),
                '/foo-bar/bar',
                null,
                array('foo' => 'foo-bar')
            ),
            'constraints-with-parantheses-dont-break-parameter-map' => array(
                new Segment('/:foo/:bar', array('foo' => '(bar)')),
                '/bar/baz',
                null,
                array('foo' => 'bar', 'bar' => 'baz')
            ),
            'simple-match-with-optional-parameter' => array(
                new Segment('/[:foo]', array(), array('foo' => 'bar')),
                '/',
                null,
                array('foo' => 'bar')
            ),
            'optional-parameter-is-ignored' => array(
                new Segment('/:foo[/:bar]'),
                '/bar',
                null,
                array('foo' => 'bar')
            ),
            'optional-parameter-is-provided-with-default' => array(
                new Segment('/:foo[/:bar]', array(), array('bar' => 'baz')),
                '/bar',
                null,
                array('foo' => 'bar', 'bar' => 'baz')
            ),
            'optional-parameter-is-consumed' => array(
                new Segment('/:foo[/:bar]'),
                '/bar/baz',
                null,
                array('foo' => 'bar', 'bar' => 'baz')
            ),
            'optional-group-is-discared-with-missing-parameter' => array(
                new Segment('/:foo[/:bar/:baz]', array(), array('bar' => 'baz')),
                '/bar',
                null,
                array('foo' => 'bar', 'bar' => 'baz')
            ),
            'optional-group-within-optional-group-is-ignored' => array(
                new Segment('/:foo[/:bar[/:baz]]', array(), array('bar' => 'baz', 'baz' => 'bat')),
                '/bar',
                null,
                array('foo' => 'bar', 'bar' => 'baz', 'baz' => 'bat')
            ),
            'non-standard-delimiter-before-parameter' => array(
                new Segment('/foo-:bar'),
                '/foo-baz',
                null,
                array('bar' => 'baz')
            ),
            'non-standard-delimiter-between-parameters' => array(
                new Segment('/:foo{-}-:bar'),
                '/bar-baz',
                null,
                array('foo' => 'bar', 'bar' => 'baz')
            ),
            'non-standard-delimiter-before-optional-parameter' => array(
                new Segment('/:foo{-/}[-:bar]/:baz'),
                '/bar-baz/bat',
                null,
                array('foo' => 'bar', 'bar' => 'baz', 'baz' => 'bat')
            ),
            'non-standard-delimiter-before-ignored-optional-parameter' => array(
                new Segment('/:foo{-/}[-:bar]/:baz'),
                '/bar/bat',
                null,
                array('foo' => 'bar', 'baz' => 'bat')
            ),
            'parameter-with-dash-in-name' => array(
                new Segment('/:foo-bar'),
                '/baz',
                null,
                array('foo-bar' => 'baz')
            ),
            'url-encoded-parameters-are-decoded' => array(
                new Segment('/:foo'),
                '/foo%20bar',
                null,
                array('foo' => 'foo bar')
            ),
            'urlencode-flaws-corrected' => array(
                new Segment('/:foo'),
                "/!$&'()*,-.:;=@_~+",
                null,
                array('foo' => "!$&'()*,-.:;=@_~+")
            ),
            'empty-matches-are-replaced-with-defaults' => array(
                new Segment('/foo[/:bar]/baz-:baz', array(), array('bar' => 'bar')),
                '/foo/baz-baz',
                null,
                array('bar' => 'bar', 'baz' => 'baz')
            ),
            'translate-with-default-locale' => array(
                new Segment('/{fw}', array(), array()),
                '/framework',
                null,
                array(),
                array('translator' => $translator)
            ),
            'translate-with-specific-locale' => array(
                new Segment('/{fw}', array(), array()),
                '/baukasten',
                null,
                array(),
                array('translator' => $translator, 'locale' => 'de-DE')
            ),
            'translate-uses-message-id-as-fallback' => array(
                new Segment('/{fw}', array(), array()),
                '/fw',
                null,
                array(),
                array('translator' => $translator, 'locale' => 'fr-FR')
            ),
            'translate-with-specific-text-domain' => array(
                new Segment('/{fw}', array(), array()),
                '/fw-alternative',
                null,
                array(),
                array('translator' => $translator, 'text_domain' => 'alternative')
            ),
        );
    }

    public static function parseExceptionsProvider()
    {
        return array(
            'unbalanced-brackets' => array(
                '[',
                'Zend\Mvc\Router\Exception\RuntimeException',
                'Found unbalanced brackets'
            ),
            'closing-bracket-without-opening-bracket' => array(
                ']',
                'Zend\Mvc\Router\Exception\RuntimeException',
                'Found closing bracket without matching opening bracket'
            ),
            'empty-parameter-name' => array(
                ':',
                'Zend\Mvc\Router\Exception\RuntimeException',
                'Found empty parameter name'
            ),
            'translated-literal-without-closing-backet' => array(
                '{test',
                'Zend\Mvc\Router\Exception\RuntimeException',
                'Translated literal missing closing bracket'
            ),
        );
    }

    /**
     * @dataProvider routeProvider
     * @param        Segment $route
     * @param        string  $path
     * @param        integer $offset
     * @param        array   $params
     * @param        array   $options
     */
    public function testMatching(Segment $route, $path, $offset, array $params = null, array $options = array())
    {
        $request = new Request();
        $request->setUri('http://example.com' . $path);
        $match = $route->match($request, $offset, $options);

        if ($params === null) {
            $this->assertNull($match);
        } else {
            $this->assertInstanceOf('Zend\Mvc\Router\Http\RouteMatch', $match);

            if ($offset === null) {
                $this->assertEquals(strlen($path), $match->getLength());
            }

            foreach ($params as $key => $value) {
                $this->assertEquals($value, $match->getParam($key));
            }
        }
    }

    /**
     * @dataProvider routeProvider
     * @param        Segment $route
     * @param        string  $path
     * @param        integer $offset
     * @param        array   $params
     * @param        array   $options
     */
    public function testAssembling(Segment $route, $path, $offset, array $params = null, array $options = array())
    {
        if ($params === null) {
            // Data which will not match are not tested for assembling.
            return;
        }

        $result = $route->assemble($params, $options);

        if ($offset !== null) {
            $this->assertEquals($offset, strpos($path, $result, $offset));
        } else {
            $this->assertEquals($path, $result);
        }
    }

    /**
     * @dataProvider parseExceptionsProvider
     * @param        string $route
     * @param        string $exceptionName
     * @param        string $exceptionMessage
     */
    public function testParseExceptions($route, $exceptionName, $exceptionMessage)
    {
        $this->setExpectedException($exceptionName, $exceptionMessage);
        new Segment($route);
    }

    public function testAssemblingWithMissingParameterInRoot()
    {
        $this->setExpectedException('Zend\Mvc\Router\Exception\InvalidArgumentException', 'Missing parameter "foo"');
        $route = new Segment('/:foo');
        $route->assemble();
    }

    public function testTranslatedAssemblingThrowsExceptionWithoutTranslator()
    {
        $this->setExpectedException('Zend\Mvc\Router\Exception\RuntimeException', 'No translator provided');
        $route = new Segment('/{foo}');
        $route->assemble();
    }

    public function testTranslatedMatchingThrowsExceptionWithoutTranslator()
    {
        $this->setExpectedException('Zend\Mvc\Router\Exception\RuntimeException', 'No translator provided');
        $route = new Segment('/{foo}');
        $route->match(new Request());
    }

    public function testNoMatchWithoutUriMethod()
    {
        $route   = new Segment('/foo');
        $request = new BaseRequest();

        $this->assertNull($route->match($request));
    }

    public function testAssemblingWithExistingChild()
    {
        $route = new Segment('/[:foo]', array(), array('foo' => 'bar'));
        $path = $route->assemble(array(), array('has_child' => true));

        $this->assertEquals('/bar', $path);
    }

    public function testFactory()
    {
        $tester = new FactoryTester($this);
        $tester->testFactory(
            'Zend\Mvc\Router\Http\Segment',
            array(
                'route' => 'Missing "route" in options array'
            ),
            array(
                'route'       => '/:foo[/:bar{-}]',
                'constraints' => array('foo' => 'bar')
            )
        );
    }

    public function testRawDecode()
    {
        // verify all characters which don't absolutely require encoding pass through match unchanged
        // this includes every character other than #, %, / and ?
        $raw = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789`-=[]\\;\',.~!@$^&*()_+{}|:"<>';
        $request = new Request();
        $request->setUri('http://example.com/' . $raw);
        $route   = new Segment('/:foo');
        $match   = $route->match($request);

        $this->assertSame($raw, $match->getParam('foo'));
    }

    public function testEncodedDecode()
    {
        // every character
        $in  = '%61%62%63%64%65%66%67%68%69%6a%6b%6c%6d%6e%6f%70%71%72%73%74%75%76%77%78%79%7a%41%42%43%44%45%46%47%48%49%4a%4b%4c%4d%4e%4f%50%51%52%53%54%55%56%57%58%59%5a%30%31%32%33%34%35%36%37%38%39%60%2d%3d%5b%5d%5c%3b%27%2c%2e%2f%7e%21%40%23%24%25%5e%26%2a%28%29%5f%2b%7b%7d%7c%3a%22%3c%3e%3f';
        $out = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789`-=[]\\;\',./~!@#$%^&*()_+{}|:"<>?';
        $request = new Request();
        $request->setUri('http://example.com/' . $in);
        $route   = new Segment('/:foo');
        $match   = $route->match($request);

        $this->assertSame($out, $match->getParam('foo'));
    }
}
