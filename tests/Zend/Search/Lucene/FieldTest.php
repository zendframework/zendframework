<?php
/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage UnitTests
 */


/**
 * Zend_Search_Lucene_Field
 */
require_once 'Zend/Search/Lucene/Field.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage UnitTests
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

