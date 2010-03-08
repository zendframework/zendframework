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
 * @package    Zend_CodeGenerator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id $
 */

/**
 * @see TestHelper
 */

/**
 * @see Zend_CodeGenerator_Php_Class
 */

/**
 * @category   Zend
 * @package    Zend_CodeGenerator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group Zend_CodeGenerator
 * @group Zend_CodeGenerator_Php
 */
class Zend_CodeGenerator_Php_DocblockTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var Zend_CodeGenerator_Php_Docblock
     */
    protected $_docblock = null;

    public function setup()
    {
        $this->_docblock = new Zend_CodeGenerator_Php_Docblock();
    }

    public function teardown()
    {
        $this->_docblock = null;
    }

    public function testShortDescriptionGetterAndSetter()
    {
        $this->_docblock->setShortDescription('Short Description');
        $this->assertEquals('Short Description', $this->_docblock->getShortDescription());
    }

    public function testLongDescriptionGetterAndSetter()
    {
        $this->_docblock->setLongDescription('Long Description');
        $this->assertEquals('Long Description', $this->_docblock->getLongDescription());
    }

    public function testTagGettersAndSetters()
    {
        $this->_docblock->setTag(array('name' => 'blah'));
        $this->_docblock->setTag(new Zend_CodeGenerator_Php_Docblock_Tag_Param(array('datatype' => 'string')));
        $this->_docblock->setTag(new Zend_CodeGenerator_Php_Docblock_Tag_Return(array('datatype' => 'int')));
        $this->assertEquals(3, count($this->_docblock->getTags()));

        $target = <<<EOS
/**
 * @blah 
 * @param string
 * @return int
 */

EOS;

        $this->assertEquals($target, $this->_docblock->generate());

    }

}
