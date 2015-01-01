<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Filter\Word;

use ReflectionProperty;
use Zend\Stdlib\StringUtils;

/**
 * Test class for Zend\Filter\Word\CamelCaseToSeparator which simulates the
 * PCRE Unicode features disabled
 */
class CamelCaseToSeparatorNoPcreUnicodeTest extends CamelCaseToSeparatorTest
{
    protected $reflection;

    public function setUp()
    {
        if (!StringUtils::hasPcreUnicodeSupport()) {
            return $this->markTestSkipped('PCRE is not compiled with Unicode support');
        }

        $this->reflection = new ReflectionProperty('Zend\Stdlib\StringUtils', 'hasPcreUnicodeSupport');
        $this->reflection->setAccessible(true);
        $this->reflection->setValue(false);
    }

    public function tearDown()
    {
        $this->reflection->setValue(true);
    }
}
