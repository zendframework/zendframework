<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Cache
 */

namespace ZendTest\Cache\Pattern;

use Zend\Cache;

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @group      Zend_Cache
 */
class CommonPatternTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Zend\Cache\Pattern\PageCache
     */
    protected $_pattern;

    public function setUp()
    {
        $this->assertInstanceOf(
            'Zend\Cache\Pattern\PatternInterface',
            $this->_pattern,
            'Internal pattern instance is needed for tests'
        );
    }

    public function tearDown()
    {
        unset($this->_pattern);
    }

    public function testOptionNamesValid()
    {
        $options = $this->_pattern->getOptions();
        $this->assertInstanceOf('Zend\Cache\Pattern\PatternOptions', $options);
    }

    public function testOptionsGetAndSetDefault()
    {
        $options = $this->_pattern->getOptions();
        $this->_pattern->setOptions($options);
        $this->assertSame($options, $this->_pattern->getOptions());
    }
}
