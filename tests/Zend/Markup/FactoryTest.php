<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Markup
 */

namespace ZendTest\Markup;

use Zend\Markup;

/**
 * @category   Zend
 * @package    Zend_Markup
 * @subpackage UnitTests
 * @group      Zend_Markup
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{

    public function testFactory()
    {
        $parsers = Markup\Markup::getParserPluginManager();
        $parsers->setInvokableClass('mockparser', 'ZendTest\Markup\TestAsset\Parser\MockParser');

        $renderers = Markup\Markup::getRendererPluginManager();
        $renderers->setInvokableClass('mockrenderer', 'ZendTest\Markup\TestAsset\Renderer\MockRenderer');

        $renderer = Markup\Markup::factory('MockParser', 'MockRenderer');

        $this->assertInstanceOf('ZendTest\\Markup\\TestAsset\\Renderer\\MockRenderer', $renderer);
        $this->assertInstanceOf('ZendTest\\Markup\\TestAsset\\Parser\\MockParser', $renderer->getParser());
    }

}
