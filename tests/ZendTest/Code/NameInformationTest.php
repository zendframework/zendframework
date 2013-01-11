<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Code
 */

namespace ZendTest\Code;

use Zend\Code\NameInformation;

class NameInformationTest extends \PHPUnit_Framework_TestCase
{
    public function testNamespaceResolverPersistsNamespace()
    {
        $nr = new NameInformation('Foo\Bar');
        $this->assertEquals('Foo\Bar', $nr->getNamespace());

        $nr = new NameInformation();
        $nr->setNamespace('Bar\Baz');
        $this->assertEquals('Bar\Baz', $nr->getNamespace());
    }

    public function testNamespaceResolverPersistsUseRules()
    {
        $nr = new NameInformation('Foo\Bar', array('Aaa\Bbb\Ccc' => 'C'));
        $this->assertEquals(array('Aaa\Bbb\Ccc' => 'C'), $nr->getUses());

        $nr = new NameInformation();
        $nr->setUses(array('Aaa\Bbb\Ccc'));
        $this->assertEquals(array('Aaa\Bbb\Ccc' => 'Ccc'), $nr->getUses());

        $nr->setUses(array('ArrayObject'));
        $this->assertEquals(array('ArrayObject' => 'ArrayObject'), $nr->getUses());

        $nr->setUses(array('ArrayObject' => 'AO'));
        $this->assertEquals(array('ArrayObject' => 'AO'), $nr->getUses());

        $nr->setUses(array('\Aaa\Bbb\Ccc' => 'Ccc'));
        $this->assertEquals(array('Aaa\Bbb\Ccc' => 'Ccc'), $nr->getUses());
    }

    public function testNamespaceResolverCorrectlyResolvesNames()
    {
        $nr = new NameInformation;
        $nr->setNamespace('Zend\MagicComponent');
        $nr->setUses(array(
            'ArrayObject',
            'Zend\OtherMagicComponent\Foo',
            'Zend\SuperMagic' => 'SM',
        ));

        // test against namespace
        $this->assertEquals('Zend\MagicComponent\Bar', $nr->resolveName('Bar'));

        // test against uses
        $this->assertEquals('ArrayObject', $nr->resolveName('ArrayObject'));
        $this->assertEquals('ArrayObject', $nr->resolveName('\ArrayObject'));
        $this->assertEquals('Zend\OtherMagicComponent\Foo', $nr->resolveName('Foo'));
        $this->assertEquals('Zend\SuperMagic', $nr->resolveName('SM'));
        $this->assertEquals('Zend\SuperMagic\Bar', $nr->resolveName('SM\Bar'));
    }
}
