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
 * @package    Zend_Markup
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Markup;
use Zend\Markup;

/**
 * @category   Zend
 * @package    Zend_Markup
 * @subpackage UnitTests
 * @group      Zend_Markup
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{

    public function testFactory()
    {
        $parserBroker = Markup\Markup::getParserBroker();
        $parserBroker->getClassLoader()->registerPlugin('mockparser', 'ZendTest\Markup\TestAsset\Parser\MockParser');

        $rendererBroker = Markup\Markup::getRendererBroker();
        $rendererBroker->getClassLoader()->registerPlugin('mockrenderer', 'ZendTest\Markup\TestAsset\Renderer\MockRenderer');

        $renderer = Markup\Markup::factory('MockParser', 'MockRenderer');

        $this->assertInstanceOf('ZendTest\\Markup\\TestAsset\\Renderer\\MockRenderer', $renderer);
        $this->assertInstanceOf('ZendTest\\Markup\\TestAsset\\Parser\\MockParser', $renderer->getParser());
    }

}
