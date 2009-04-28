<?php
/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage UnitTests
 */


/**
 * Zend_Search_Lucene_Index_Term
 */
require_once 'Zend/Search/Lucene/Index/Term.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage UnitTests
 */
class Zend_Search_Lucene_Index_TermTest extends PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $term = new Zend_Search_Lucene_Index_Term('term_text');
        $this->assertTrue($term instanceof Zend_Search_Lucene_Index_Term);

        $this->assertEquals($term->text,  'term_text');
        $this->assertEquals($term->field, null);

        $term = new Zend_Search_Lucene_Index_Term('term_text', 'field_name');
        $this->assertEquals($term->text,   'term_text');
        $this->assertEquals($term->field,  'field_name');
    }

    public function testKey()
    {
        $term1_1    = new Zend_Search_Lucene_Index_Term('term_text1', 'field_name1');
        $term2_1    = new Zend_Search_Lucene_Index_Term('term_text2', 'field_name1');
        $term2_2    = new Zend_Search_Lucene_Index_Term('term_text2', 'field_name2');
        $term2_1Dup = new Zend_Search_Lucene_Index_Term('term_text2', 'field_name1');

        $this->assertEquals($term1_1->text >  $term2_1->text,  $term1_1->key() >  $term2_1->key());
        $this->assertEquals($term1_1->text >= $term2_1->text,  $term1_1->key() >= $term2_1->key());

        $this->assertEquals($term1_1->field >  $term2_2->field,  $term1_1->key() >  $term2_2->key());
        $this->assertEquals($term1_1->field >= $term2_2->field,  $term1_1->key() >= $term2_2->key());

        $this->assertEquals($term2_1->key(), $term2_1Dup->key());
    }

    public function testGetPrefix()
    {
        $this->assertEquals(Zend_Search_Lucene_Index_Term::getPrefix('term_text', 10), 'term_text');
        $this->assertEquals(Zend_Search_Lucene_Index_Term::getPrefix('term_text', 9), 'term_text');
        $this->assertEquals(Zend_Search_Lucene_Index_Term::getPrefix('term_text', 4), 'term');
        $this->assertEquals(Zend_Search_Lucene_Index_Term::getPrefix('term_text', 0), '');
    }

    public function testGetPrefixUtf8()
    {
        // UTF-8 string with non-ascii symbols (Russian alphabet)
        $this->assertEquals(Zend_Search_Lucene_Index_Term::getPrefix('абвгдеёжзийклмнопрстуфхцчшщьыъэюя', 64), 'абвгдеёжзийклмнопрстуфхцчшщьыъэюя');
        $this->assertEquals(Zend_Search_Lucene_Index_Term::getPrefix('абвгдеёжзийклмнопрстуфхцчшщьыъэюя', 33), 'абвгдеёжзийклмнопрстуфхцчшщьыъэюя');
        $this->assertEquals(Zend_Search_Lucene_Index_Term::getPrefix('абвгдеёжзийклмнопрстуфхцчшщьыъэюя', 4), 'абвг');
        $this->assertEquals(Zend_Search_Lucene_Index_Term::getPrefix('абвгдеёжзийклмнопрстуфхцчшщьыъэюя', 0), '');



    }
}

