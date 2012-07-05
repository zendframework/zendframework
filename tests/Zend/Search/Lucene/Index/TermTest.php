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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Search\Lucene\Index;

use Zend\Search\Lucene\Index;

/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Search_Lucene
 */
class TermTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $term = new Index\Term('term_text');
        $this->assertTrue($term instanceof Index\Term);

        $this->assertEquals($term->text,  'term_text');
        $this->assertEquals($term->field, null);

        $term = new Index\Term('term_text', 'field_name');
        $this->assertEquals($term->text,   'term_text');
        $this->assertEquals($term->field,  'field_name');
    }

    public function testKey()
    {
        $term1_1    = new Index\Term('term_text1', 'field_name1');
        $term2_1    = new Index\Term('term_text2', 'field_name1');
        $term2_2    = new Index\Term('term_text2', 'field_name2');
        $term2_1Dup = new Index\Term('term_text2', 'field_name1');

        $this->assertEquals($term1_1->text >  $term2_1->text,  $term1_1->key() >  $term2_1->key());
        $this->assertEquals($term1_1->text >= $term2_1->text,  $term1_1->key() >= $term2_1->key());

        $this->assertEquals($term1_1->field >  $term2_2->field,  $term1_1->key() >  $term2_2->key());
        $this->assertEquals($term1_1->field >= $term2_2->field,  $term1_1->key() >= $term2_2->key());

        $this->assertEquals($term2_1->key(), $term2_1Dup->key());
    }

    public function testGetPrefix()
    {
        $this->assertEquals(Index\Term::getPrefix('term_text', 10), 'term_text');
        $this->assertEquals(Index\Term::getPrefix('term_text', 9), 'term_text');
        $this->assertEquals(Index\Term::getPrefix('term_text', 4), 'term');
        $this->assertEquals(Index\Term::getPrefix('term_text', 0), '');
    }

    public function testGetPrefixUtf8()
    {
        // UTF-8 string with non-ascii symbols (Russian alphabet)
        $this->assertEquals(Index\Term::getPrefix('абвгдеёжзийклмнопрстуфхцчшщьыъэюя', 64), 'абвгдеёжзийклмнопрстуфхцчшщьыъэюя');
        $this->assertEquals(Index\Term::getPrefix('абвгдеёжзийклмнопрстуфхцчшщьыъэюя', 33), 'абвгдеёжзийклмнопрстуфхцчшщьыъэюя');
        $this->assertEquals(Index\Term::getPrefix('абвгдеёжзийклмнопрстуфхцчшщьыъэюя', 4), 'абвг');
        $this->assertEquals(Index\Term::getPrefix('абвгдеёжзийклмнопрстуфхцчшщьыъэюя', 0), '');



    }
}

