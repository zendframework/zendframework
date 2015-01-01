<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\View\Resolver;

use Zend\View\Resolver\TemplatePathStack;

/**
 * @group      Zend_View
 */
class TemplatePathStackTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TemplatePathStack
     */
    private $stack;

    /**
     * @var string[]
     */
    private $paths;

    /**
     * @var string
     */
    private $baseDir;

    public function setUp()
    {
        $this->baseDir = realpath(__DIR__ . '/..');
        $this->stack   = new TemplatePathStack();
        $this->paths   = array(
            TemplatePathStack::normalizePath($this->baseDir),
            TemplatePathStack::normalizePath($this->baseDir . '/_templates'),
        );
    }

    public function testAddPathAddsPathToStack()
    {
        $this->stack->addPath($this->baseDir);
        $paths = $this->stack->getPaths();
        $this->assertEquals(1, count($paths));
        $this->assertEquals(TemplatePathStack::normalizePath($this->baseDir), $paths->pop());
    }

    public function testPathsAreProcessedAsStack()
    {
        $paths = array(
            TemplatePathStack::normalizePath($this->baseDir),
            TemplatePathStack::normalizePath($this->baseDir . '/_files'),
        );
        foreach ($paths as $path) {
            $this->stack->addPath($path);
        }
        $test = $this->stack->getPaths()->toArray();
        $this->assertEquals(array_reverse($paths), $test);
    }

    public function testAddPathsAddsPathsToStack()
    {
        $this->stack->addPath($this->baseDir . '/Helper');
        $paths = array(
            TemplatePathStack::normalizePath($this->baseDir),
            TemplatePathStack::normalizePath($this->baseDir . '/_files'),
        );
        $this->stack->addPaths($paths);
        array_unshift($paths, TemplatePathStack::normalizePath($this->baseDir . '/Helper'));
        $this->assertEquals(array_reverse($paths), $this->stack->getPaths()->toArray());
    }

    public function testSetPathsOverwritesStack()
    {
        $this->stack->addPath($this->baseDir . '/Helper');
        $paths = array(
            TemplatePathStack::normalizePath($this->baseDir),
            TemplatePathStack::normalizePath($this->baseDir . '/_files'),
        );
        $this->stack->setPaths($paths);
        $this->assertEquals(array_reverse($paths), $this->stack->getPaths()->toArray());
    }

    public function testClearPathsClearsStack()
    {
        $paths = array(
            $this->baseDir,
            $this->baseDir . '/_files',
        );
        $this->stack->setPaths($paths);
        $this->stack->clearPaths();
        $this->assertEquals(0, $this->stack->getPaths()->count());
    }

    public function testLfiProtectionEnabledByDefault()
    {
        $this->assertTrue($this->stack->isLfiProtectionOn());
    }

    public function testMayDisableLfiProtection()
    {
        $this->stack->setLfiProtection(false);
        $this->assertFalse($this->stack->isLfiProtectionOn());
    }

    public function testStreamWrapperDisabledByDefault()
    {
        $this->assertFalse($this->stack->useStreamWrapper());
    }

    public function testMayEnableStreamWrapper()
    {
        $flag = (bool) ini_get('short_open_tag');
        if (!$flag) {
            $this->markTestSkipped('Short tags are disabled; cannot test');
        }
        $this->stack->setUseStreamWrapper(true);
        $this->assertTrue($this->stack->useStreamWrapper());
    }

    public function testDoesNotAllowParentDirectoryTraversalByDefault()
    {
        $this->stack->addPath($this->baseDir . '/_templates');

        $this->setExpectedException('Zend\View\Exception\ExceptionInterface', 'parent directory traversal');
        $this->stack->resolve('../_stubs/scripts/LfiProtectionCheck.phtml');
    }

    public function testDisablingLfiProtectionAllowsParentDirectoryTraversal()
    {
        $this->stack->setLfiProtection(false)
                    ->addPath($this->baseDir . '/_templates');

        $test = $this->stack->resolve('../_stubs/scripts/LfiProtectionCheck.phtml');
        $this->assertContains('LfiProtectionCheck.phtml', $test);
    }

    public function testReturnsFalseWhenRetrievingScriptIfNoPathsRegistered()
    {
        $this->assertFalse($this->stack->resolve('test.phtml'));
        $this->assertEquals(TemplatePathStack::FAILURE_NO_PATHS, $this->stack->getLastLookupFailure());
    }

    public function testReturnsFalseWhenUnableToResolveScriptToPath()
    {
        $this->stack->addPath($this->baseDir . '/_templates');
        $this->assertFalse($this->stack->resolve('bogus-script.txt'));
        $this->assertEquals(TemplatePathStack::FAILURE_NOT_FOUND, $this->stack->getLastLookupFailure());
    }

    public function testReturnsFullPathNameWhenAbleToResolveScriptPath()
    {
        $this->stack->addPath($this->baseDir . '/_templates');
        $expected = realpath($this->baseDir . '/_templates/test.phtml');
        $test     = $this->stack->resolve('test.phtml');
        $this->assertEquals($expected, $test);
    }

    public function testReturnsPathWithStreamProtocolWhenStreamWrapperEnabled()
    {
        $flag = (bool) ini_get('short_open_tag');
        if (!$flag) {
            $this->markTestSkipped('Short tags are disabled; cannot test');
        }
        $this->stack->setUseStreamWrapper(true)
                    ->addPath($this->baseDir . '/_templates');
        $expected = 'zend.view://' . realpath($this->baseDir . '/_templates/test.phtml');
        $test     = $this->stack->resolve('test.phtml');
        $this->assertEquals($expected, $test);
    }

    public function invalidOptions()
    {
        return array(
            array(true),
            array(1),
            array(1.0),
            array('foo'),
            array(new \stdClass),
        );
    }

    /**
     * @param mixed $options
     *
     * @dataProvider invalidOptions
     */
    public function testSettingOptionsWithInvalidArgumentRaisesException($options)
    {
        $this->setExpectedException('Zend\View\Exception\ExceptionInterface');
        $this->stack->setOptions($options);
    }

    /**
     * @return mixed[][]
     */
    public function validOptions()
    {
        $options = array(
            'lfi_protection'     => false,
            'use_stream_wrapper' => true,
            'default_suffix'     => 'php',
        );
        return array(
            array($options),
            array(new \ArrayObject($options)),
        );
    }

    /**
     * @param array|\ArrayObject $options
     *
     * @dataProvider validOptions
     */
    public function testAllowsSettingOptions($options)
    {
        $options['script_paths'] = $this->paths;
        $this->stack->setOptions($options);
        $this->assertFalse($this->stack->isLfiProtectionOn());

        $expected = (bool) ini_get('short_open_tag');
        $this->assertSame($expected, $this->stack->useStreamWrapper());

        $this->assertSame($options['default_suffix'], $this->stack->getDefaultSuffix());

        $this->assertEquals(array_reverse($this->paths), $this->stack->getPaths()->toArray());
    }

    /**
     * @param array $options
     *
     * @dataProvider validOptions
     */
    public function testAllowsPassingOptionsToConstructor($options)
    {
        $options['script_paths'] = $this->paths;
        $stack = new TemplatePathStack($options);
        $this->assertFalse($stack->isLfiProtectionOn());

        $expected = (bool) ini_get('short_open_tag');
        $this->assertSame($expected, $stack->useStreamWrapper());

        $this->assertEquals(array_reverse($this->paths), $stack->getPaths()->toArray());
    }

    public function testAllowsRelativePharPath()
    {
        $path = 'phar://' . $this->baseDir
            . DIRECTORY_SEPARATOR . '_templates'
            . DIRECTORY_SEPARATOR . 'view.phar'
            . DIRECTORY_SEPARATOR . 'start'
            . DIRECTORY_SEPARATOR . '..'
            . DIRECTORY_SEPARATOR . 'views';

        $this->stack->addPath($path);
        $test = $this->stack->resolve('foo' . DIRECTORY_SEPARATOR . 'hello.phtml');
        $this->assertEquals($path . DIRECTORY_SEPARATOR . 'foo' . DIRECTORY_SEPARATOR . 'hello.phtml', $test);
    }

    public function testDefaultFileSuffixIsPhtml()
    {
        $this->assertEquals('phtml', $this->stack->getDefaultSuffix());
    }

    public function testDefaultFileSuffixIsMutable()
    {
        $this->stack->setDefaultSuffix('php');
        $this->assertEquals('php', $this->stack->getDefaultSuffix());
    }

    public function testSettingDefaultSuffixStripsLeadingDot()
    {
        $this->stack->setDefaultSuffix('.config.php');
        $this->assertEquals('config.php', $this->stack->getDefaultSuffix());
    }
}
