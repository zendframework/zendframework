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
 * @package    Zend_Search_Lucene
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Zend_Search_Lucene_Field
 */

/**
 * PHPUnit test case
 */

/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Search_Lucene
 */
class Zend_Search_Lucene_FieldTest extends PHPUnit_Framework_TestCase
{
    public function testBinary()
    {
        $field = Zend_Search_Lucene_Field::Binary('field', 'value');

        $this->assertEquals($field->boost, 1);
        $this->assertEquals($field->encoding, '');
        $this->assertEquals($field->isBinary,    true);
        $this->assertEquals($field->isIndexed,   false);
        $this->assertEquals($field->isStored,    true);
        $this->assertEquals($field->isTokenized, false);

        $this->assertEquals($field->name, 'field');
        $this->assertEquals($field->value, 'value');
    }

    public function testKeyword()
    {
        $field = Zend_Search_Lucene_Field::Keyword('field', 'value');

        $this->assertEquals($field->boost, 1);
        $this->assertEquals($field->encoding, '');
        $this->assertEquals($field->isBinary,    false);
        $this->assertEquals($field->isIndexed,   true);
        $this->assertEquals($field->isStored,    true);
        $this->assertEquals($field->isTokenized, false);

        $this->assertEquals($field->name, 'field');
        $this->assertEquals($field->value, 'value');
    }

    public function testText()
    {
        $field = Zend_Search_Lucene_Field::Text('field', 'value');

        $this->assertEquals($field->boost, 1);
        $this->assertEquals($field->encoding, '');
        $this->assertEquals($field->isBinary,    false);
        $this->assertEquals($field->isIndexed,   true);
        $this->assertEquals($field->isStored,    true);
        $this->assertEquals($field->isTokenized, true);

        $this->assertEquals($field->name, 'field');
        $this->assertEquals($field->value, 'value');
    }

    public function testUnIndexed()
    {
        $field = Zend_Search_Lucene_Field::UnIndexed('field', 'value');

        $this->assertEquals($field->boost, 1);
        $this->assertEquals($field->encoding, '');
        $this->assertEquals($field->isBinary,    false);
        $this->assertEquals($field->isIndexed,   false);
        $this->assertEquals($field->isStored,    true);
        $this->assertEquals($field->isTokenized, false);

        $this->assertEquals($field->name, 'field');
        $this->assertEquals($field->value, 'value');
    }

    public function testUnStored()
    {
        $field = Zend_Search_Lucene_Field::UnStored('field', 'value');

        $this->assertEquals($field->boost, 1);
        $this->assertEquals($field->encoding, '');
        $this->assertEquals($field->isBinary,    false);
        $this->assertEquals($field->isIndexed,   true);
        $this->assertEquals($field->isStored,    false);
        $this->assertEquals($field->isTokenized, true);

        $this->assertEquals($field->name, 'field');
        $this->assertEquals($field->value, 'value');
    }

    public function testEncoding()
    {
        $field = Zend_Search_Lucene_Field::Text('field', 'Words with umlauts: εγό...', 'ISO-8859-1');

        $this->assertEquals($field->encoding, 'ISO-8859-1');

        $this->assertEquals($field->name, 'field');
        $this->assertEquals($field->value, 'Words with umlauts: εγό...');
        $this->assertEquals($field->getUtf8Value(), 'Words with umlauts: Γ₯Γ£ΓΌ...');
    }
}

