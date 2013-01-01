<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_XmlRpc
 */

namespace ZendTest\XmlRpc;

use Zend\XmlRpc\Generator;

abstract class TestProvider
{
    public static function provideGenerators()
    {
        return array(
            array(new Generator\DomDocument()),
            array(new Generator\XmlWriter()),
        );
    }

    public static function provideGeneratorsWithAlternateEncodings()
    {
        return array(
            array(new Generator\DomDocument('ISO-8859-1')),
            array(new Generator\XmlWriter('ISO-8859-1')),
        );
    }
}
