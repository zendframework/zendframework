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
 * Zend_Search_Lucene
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
class Zend_Search_Lucene_Search23Test extends PHPUnit_Framework_TestCase
{
    public function testQueryParser()
    {
        $wildcardMinPrefix = Zend_Search_Lucene_Search_Query_Wildcard::getMinPrefixLength();
        Zend_Search_Lucene_Search_Query_Wildcard::setMinPrefixLength(0);

        $defaultPrefixLength = Zend_Search_Lucene_Search_Query_Fuzzy::getDefaultPrefixLength();
        Zend_Search_Lucene_Search_Query_Fuzzy::setDefaultPrefixLength(0);

        $queries = array('title:"The Right Way" AND text:go',
                         'title:"Do it right" AND right',
                         'title:Do it right',
                         'te?t',
                         'test*',
                         'te*t',
                         '?Ma*',
                         // 'te?t~20^0.8',
                         'test~',
                         'test~0.4',
                         '"jakarta apache"~10',
                         'contents:[business TO by]',
                         '{wish TO zzz}',
                         'jakarta apache',
                         'jakarta^4 apache',
                         '"jakarta apache"^4 "Apache Lucene"',
                         '"jakarta apache" jakarta',
                         '"jakarta apache" OR jakarta',
                         '"jakarta apache" || jakarta',
                         '"jakarta apache" AND "Apache Lucene"',
                         '"jakarta apache" && "Apache Lucene"',
                         '+jakarta apache',
                         '"jakarta apache" AND NOT "Apache Lucene"',
                         '"jakarta apache" && !"Apache Lucene"',
                         '\\ ',
                         'NOT "jakarta apache"',
                         '!"jakarta apache"',
                         '"jakarta apache" -"Apache Lucene"',
                         '(jakarta OR apache) AND website',
                         '(jakarta || apache) && website',
                         'title:(+return +"pink panther")',
                         'title:(+re\\turn\\ value +"pink panther\\"" +body:cool)',
                         '+contents:apache +type:1 +id:5',
                         'contents:apache AND type:1 AND id:5',
                         'f1:word1 f1:word2 and f1:word3',
                         'f1:word1 not f1:word2 and f1:word3');

        $rewrittenQueries = array('+(title:"the right way") +(text:go)',
                                  '+(title:"do it right") +(pathkeyword:right path:right modified:right contents:right)',
                                  '(title:do) (pathkeyword:it path:it modified:it contents:it) (pathkeyword:right path:right modified:right contents:right)',
                                  '(contents:test contents:text)',
                                  '(contents:test contents:tested)',
                                  '(contents:test contents:text)',
                                  '(contents:amazon contents:email)',
                                  // ....
                                  '((contents:test) (contents:text^0.5))',
                                  '((contents:test) (contents:text^0.5833) (contents:latest^0.1667) (contents:left^0.1667) (contents:list^0.1667) (contents:meet^0.1667) (contents:must^0.1667) (contents:next^0.1667) (contents:post^0.1667) (contents:sect^0.1667) (contents:task^0.1667) (contents:tested^0.1667) (contents:that^0.1667) (contents:tort^0.1667))',
                                  '((pathkeyword:"jakarta apache"~10) (path:"jakarta apache"~10) (modified:"jakarta apache"~10) (contents:"jakarta apache"~10))',
                                  '(contents:business contents:but contents:buy contents:buying contents:by)',
                                  '(path:wishlist contents:wishlist contents:wishlists contents:with contents:without contents:won contents:work contents:would contents:write contents:writing contents:written contents:www contents:xml contents:xmlrpc contents:you contents:your)',
                                  '(pathkeyword:jakarta path:jakarta modified:jakarta contents:jakarta) (pathkeyword:apache path:apache modified:apache contents:apache)',
                                  '((pathkeyword:jakarta path:jakarta modified:jakarta contents:jakarta)^4) (pathkeyword:apache path:apache modified:apache contents:apache)',
                                  '(((pathkeyword:"jakarta apache") (path:"jakarta apache") (modified:"jakarta apache") (contents:"jakarta apache"))^4) ((pathkeyword:"apache lucene") (path:"apache lucene") (modified:"apache lucene") (contents:"apache lucene"))',
                                  '((pathkeyword:"jakarta apache") (path:"jakarta apache") (modified:"jakarta apache") (contents:"jakarta apache")) (pathkeyword:jakarta path:jakarta modified:jakarta contents:jakarta)',
                                  '((pathkeyword:"jakarta apache") (path:"jakarta apache") (modified:"jakarta apache") (contents:"jakarta apache")) (pathkeyword:jakarta path:jakarta modified:jakarta contents:jakarta)',
                                  '((pathkeyword:"jakarta apache") (path:"jakarta apache") (modified:"jakarta apache") (contents:"jakarta apache")) (pathkeyword:jakarta path:jakarta modified:jakarta contents:jakarta)',
                                  '+((pathkeyword:"jakarta apache") (path:"jakarta apache") (modified:"jakarta apache") (contents:"jakarta apache")) +((pathkeyword:"apache lucene") (path:"apache lucene") (modified:"apache lucene") (contents:"apache lucene"))',
                                  '+((pathkeyword:"jakarta apache") (path:"jakarta apache") (modified:"jakarta apache") (contents:"jakarta apache")) +((pathkeyword:"apache lucene") (path:"apache lucene") (modified:"apache lucene") (contents:"apache lucene"))',
                                  '+(pathkeyword:jakarta path:jakarta modified:jakarta contents:jakarta) (pathkeyword:apache path:apache modified:apache contents:apache)',
                                  '+((pathkeyword:"jakarta apache") (path:"jakarta apache") (modified:"jakarta apache") (contents:"jakarta apache")) -((pathkeyword:"apache lucene") (path:"apache lucene") (modified:"apache lucene") (contents:"apache lucene"))',
                                  '+((pathkeyword:"jakarta apache") (path:"jakarta apache") (modified:"jakarta apache") (contents:"jakarta apache")) -((pathkeyword:"apache lucene") (path:"apache lucene") (modified:"apache lucene") (contents:"apache lucene"))',
                                  '(<InsignificantQuery>)',
                                  '<InsignificantQuery>',
                                  '<InsignificantQuery>',
                                  '((pathkeyword:"jakarta apache") (path:"jakarta apache") (modified:"jakarta apache") (contents:"jakarta apache")) -((pathkeyword:"apache lucene") (path:"apache lucene") (modified:"apache lucene") (contents:"apache lucene"))',
                                  '+((pathkeyword:jakarta path:jakarta modified:jakarta contents:jakarta) (pathkeyword:apache path:apache modified:apache contents:apache)) +(pathkeyword:website path:website modified:website contents:website)',
                                  '+((pathkeyword:jakarta path:jakarta modified:jakarta contents:jakarta) (pathkeyword:apache path:apache modified:apache contents:apache)) +(pathkeyword:website path:website modified:website contents:website)',
                                  '(+(title:return) +(title:"pink panther"))',
                                  '(+(+title:return +title:value) +(title:"pink panther") +(body:cool))',
                                  '+(contents:apache) +(<InsignificantQuery>) +(<InsignificantQuery>)',
                                  '+(contents:apache) +(<InsignificantQuery>) +(<InsignificantQuery>)',
                                  '(f1:word) (+(f1:word) +(f1:word))',
                                  '(f1:word) (-(f1:word) +(f1:word))');


        $index = Zend_Search_Lucene::open(dirname(__FILE__) . '/_index23Sample/_files');

        foreach ($queries as $id => $queryString) {
            $query = Zend_Search_Lucene_Search_QueryParser::parse($queryString);

            $this->assertTrue($query instanceof Zend_Search_Lucene_Search_Query);
            $this->assertEquals($query->rewrite($index)->__toString(), $rewrittenQueries[$id]);
        }

        Zend_Search_Lucene_Search_Query_Wildcard::setMinPrefixLength($wildcardMinPrefix);
        Zend_Search_Lucene_Search_Query_Fuzzy::setDefaultPrefixLength($defaultPrefixLength);
    }

    public function testQueryParserExceptionsHandling()
    {
        $this->assertTrue(Zend_Search_Lucene_Search_QueryParser::queryParsingExceptionsSuppressed());

        try {
            $query = Zend_Search_Lucene_Search_QueryParser::parse('contents:[business TO by}');
        } catch (Zend_Search_Lucene_Exception $e) {
            $this->fail('exception raised while parsing a query');
        }

        $this->assertEquals('contents business to by', $query->__toString());

        Zend_Search_Lucene_Search_QueryParser::dontSuppressQueryParsingExceptions();
        $this->assertFalse(Zend_Search_Lucene_Search_QueryParser::queryParsingExceptionsSuppressed());

        try {
            $query = Zend_Search_Lucene_Search_QueryParser::parse('contents:[business TO by}');

            $this->fail('exception wasn\'t raised while parsing a query');
        } catch (Zend_Search_Lucene_Exception $e) {
            $this->assertEquals('Syntax error at char position 25.', $e->getMessage());
        }


        Zend_Search_Lucene_Search_QueryParser::suppressQueryParsingExceptions();
        $this->assertTrue(Zend_Search_Lucene_Search_QueryParser::queryParsingExceptionsSuppressed());
    }

    public function testEmptyQuery()
    {
        $index = Zend_Search_Lucene::open(dirname(__FILE__) . '/_index23Sample/_files');

        $hits = $index->find('');

        $this->assertEquals(count($hits), 0);
    }

    public function testTermQuery()
    {
        $index = Zend_Search_Lucene::open(dirname(__FILE__) . '/_index23Sample/_files');

        $hits = $index->find('submitting');

        $this->assertEquals(count($hits), 3);
        $expectedResultset = array(array(2, 0.114555, 'IndexSource/contributing.patches.html'),
                                   array(7, 0.112241, 'IndexSource/contributing.bugs.html'),
                                   array(8, 0.112241, 'IndexSource/contributing.html'));

        foreach ($hits as $resId => $hit) {
            $this->assertEquals($hit->id, $expectedResultset[$resId][0]);
            $this->assertTrue( abs($hit->score - $expectedResultset[$resId][1]) < 0.000001 );
            $this->assertEquals($hit->path, $expectedResultset[$resId][2]);
        }
    }

    public function testMultiTermQuery()
    {
        $index = Zend_Search_Lucene::open(dirname(__FILE__) . '/_index23Sample/_files');

        $hits = $index->find('submitting AND wishlists');

        $this->assertEquals(count($hits), 1);

        $this->assertEquals($hits[0]->id, 8);
        $this->assertTrue( abs($hits[0]->score - 0.141633) < 0.000001 );
        $this->assertEquals($hits[0]->path, 'IndexSource/contributing.html');
    }

    public function testPraseQuery()
    {
        $index = Zend_Search_Lucene::open(dirname(__FILE__) . '/_index23Sample/_files');

        $hits = $index->find('"reporting bugs"');

        $this->assertEquals(count($hits), 4);
        $expectedResultset = array(array(0, 0.247795, 'IndexSource/contributing.documentation.html'),
                                   array(7, 0.212395, 'IndexSource/contributing.bugs.html'),
                                   array(8, 0.212395, 'IndexSource/contributing.html'),
                                   array(2, 0.176996, 'IndexSource/contributing.patches.html'));

        foreach ($hits as $resId => $hit) {
            $this->assertEquals($hit->id, $expectedResultset[$resId][0]);
            $this->assertTrue( abs($hit->score - $expectedResultset[$resId][1]) < 0.000001 );
            $this->assertEquals($hit->path, $expectedResultset[$resId][2]);
        }
    }

    public function testQueryParserKeywordsHandlingPhrase()
    {
        $index = Zend_Search_Lucene::open(dirname(__FILE__) . '/_index23Sample/_files');


        $query = Zend_Search_Lucene_Search_QueryParser::parse('"IndexSource/contributing.wishlist.html" AND Home');

        $this->assertEquals($query->__toString(), '+("IndexSource/contributing.wishlist.html") +(Home)');
        $this->assertEquals($query->rewrite($index)->__toString(),
                            '+((pathkeyword:IndexSource/contributing.wishlist.html) (path:"indexsource contributing wishlist html") (modified:"indexsource contributing wishlist html") (contents:"indexsource contributing wishlist html")) +(pathkeyword:home path:home modified:home contents:home)');
        $this->assertEquals($query->rewrite($index)->optimize($index)->__toString(), '+( (path:"indexsource contributing wishlist html") (pathkeyword:IndexSource/contributing.wishlist.html)) +(contents:home)');


        $hits = $index->find('"IndexSource/contributing.bugs.html"');

        $this->assertEquals(count($hits), 1);
        $expectedResultset = array(array(7, 1, 'IndexSource/contributing.bugs.html'));

        foreach ($hits as $resId => $hit) {
            $this->assertEquals($hit->id, $expectedResultset[$resId][0]);
            $this->assertTrue( abs($hit->score - $expectedResultset[$resId][1]) < 0.000001 );
            $this->assertEquals($hit->path, $expectedResultset[$resId][2]);
        }
    }

    public function testQueryParserKeywordsHandlingTerm()
    {
        $index = Zend_Search_Lucene::open(dirname(__FILE__) . '/_index23Sample/_files');


        $query = Zend_Search_Lucene_Search_QueryParser::parse('IndexSource\/contributing\.wishlist\.html AND Home');

        $this->assertEquals($query->__toString(), '+(IndexSource/contributing.wishlist.html) +(Home)');
        $this->assertEquals($query->rewrite($index)->__toString(),
                            '+(pathkeyword:IndexSource/contributing.wishlist.html path:indexsource path:contributing path:wishlist path:html modified:indexsource modified:contributing modified:wishlist modified:html contents:indexsource contents:contributing contents:wishlist contents:html) +(pathkeyword:home path:home modified:home contents:home)');
        $this->assertEquals($query->rewrite($index)->optimize($index)->__toString(), '+(pathkeyword:IndexSource/contributing.wishlist.html path:indexsource path:contributing path:wishlist path:html contents:contributing contents:wishlist contents:html) +(contents:home)');


        $hits = $index->find('IndexSource\/contributing\.wishlist\.html AND Home');

        $this->assertEquals(count($hits), 9);
        $expectedResultset = array(array(1, 1.000000, 'IndexSource/contributing.wishlist.html'),
                                   array(8, 0.167593, 'IndexSource/contributing.html'),
                                   array(0, 0.154047, 'IndexSource/contributing.documentation.html'),
                                   array(7, 0.108574, 'IndexSource/contributing.bugs.html'),
                                   array(2, 0.104248, 'IndexSource/contributing.patches.html'),
                                   array(3, 0.048998, 'IndexSource/about-pear.html'),
                                   array(9, 0.039942, 'IndexSource/core.html'),
                                   array(5, 0.038530, 'IndexSource/authors.html'),
                                   array(4, 0.036261, 'IndexSource/copyright.html'));

        foreach ($hits as $resId => $hit) {
            $this->assertEquals($hit->id, $expectedResultset[$resId][0]);
            $this->assertTrue( abs($hit->score - $expectedResultset[$resId][1]) < 0.000001 );
            $this->assertEquals($hit->path, $expectedResultset[$resId][2]);
        }
    }

    public function testBooleanQuery()
    {
        $index = Zend_Search_Lucene::open(dirname(__FILE__) . '/_index23Sample/_files');

        $hits = $index->find('submitting AND (wishlists OR requirements)');

        $this->assertEquals(count($hits), 2);
        $expectedResultset = array(array(7, 0.095697, 'IndexSource/contributing.bugs.html'),
                                   array(8, 0.075573, 'IndexSource/contributing.html'));

        foreach ($hits as $resId => $hit) {
            $this->assertEquals($hit->id, $expectedResultset[$resId][0]);
            $this->assertTrue( abs($hit->score - $expectedResultset[$resId][1]) < 0.000001 );
            $this->assertEquals($hit->path, $expectedResultset[$resId][2]);
        }
    }

    public function testBooleanQueryWithPhraseSubquery()
    {
        $index = Zend_Search_Lucene::open(dirname(__FILE__) . '/_index23Sample/_files');

        $hits = $index->find('"PEAR developers" AND Home');

        $this->assertEquals(count($hits), 1);
        $expectedResultset = array(array(1, 0.168270, 'IndexSource/contributing.wishlist.html'));

        foreach ($hits as $resId => $hit) {
            $this->assertEquals($hit->id, $expectedResultset[$resId][0]);
            $this->assertTrue( abs($hit->score - $expectedResultset[$resId][1]) < 0.000001 );
            $this->assertEquals($hit->path, $expectedResultset[$resId][2]);
        }
    }

    public function testBooleanQueryWithNonExistingPhraseSubquery()
    {
        $index = Zend_Search_Lucene::open(dirname(__FILE__) . '/_index23Sample/_files');

        $query = Zend_Search_Lucene_Search_QueryParser::parse('"Non-existing phrase" AND Home');

        $this->assertEquals($query->__toString(), '+("Non-existing phrase") +(Home)');
        $this->assertEquals($query->rewrite($index)->__toString(),
                            '+((pathkeyword:"non existing phrase") (path:"non existing phrase") (modified:"non existing phrase") (contents:"non existing phrase")) +(pathkeyword:home path:home modified:home contents:home)');
        $this->assertEquals($query->rewrite($index)->optimize($index)->__toString(), '<EmptyQuery>');
    }

    public function testFilteredTokensQueryParserProcessing()
    {
        $index = Zend_Search_Lucene::open(dirname(__FILE__) . '/_index23Sample/_files');

        $this->assertEquals(count(Zend_Search_Lucene_Analysis_Analyzer::getDefault()->tokenize('123456787654321')), 0);


        $hits = $index->find('"PEAR developers" AND Home AND 123456787654321');

        $this->assertEquals(count($hits), 1);
        $expectedResultset = array(array(1, 0.168270, 'IndexSource/contributing.wishlist.html'));

        foreach ($hits as $resId => $hit) {
            $this->assertEquals($hit->id, $expectedResultset[$resId][0]);
            $this->assertTrue( abs($hit->score - $expectedResultset[$resId][1]) < 0.000001 );
            $this->assertEquals($hit->path, $expectedResultset[$resId][2]);
        }
    }

    public function testWildcardQuery()
    {
        $index = Zend_Search_Lucene::open(dirname(__FILE__) . '/_index23Sample/_files');

        $wildcardMinPrefix = Zend_Search_Lucene_Search_Query_Wildcard::getMinPrefixLength();
        Zend_Search_Lucene_Search_Query_Wildcard::setMinPrefixLength(0);

        $hits = $index->find('*cont*');

        $this->assertEquals(count($hits), 9);
        $expectedResultset = array(array(8, 0.328087, 'IndexSource/contributing.html'),
                                   array(2, 0.318592, 'IndexSource/contributing.patches.html'),
                                   array(7, 0.260137, 'IndexSource/contributing.bugs.html'),
                                   array(0, 0.203372, 'IndexSource/contributing.documentation.html'),
                                   array(1, 0.202366, 'IndexSource/contributing.wishlist.html'),
                                   array(4, 0.052931, 'IndexSource/copyright.html'),
                                   array(3, 0.017070, 'IndexSource/about-pear.html'),
                                   array(5, 0.010150, 'IndexSource/authors.html'),
                                   array(9, 0.003504, 'IndexSource/core.html'));

        foreach ($hits as $resId => $hit) {
            $this->assertEquals($hit->id, $expectedResultset[$resId][0]);
            $this->assertTrue( abs($hit->score - $expectedResultset[$resId][1]) < 0.000001 );
            $this->assertEquals($hit->path, $expectedResultset[$resId][2]);
        }

        Zend_Search_Lucene_Search_Query_Wildcard::setMinPrefixLength($wildcardMinPrefix);
    }

    public function testFuzzyQuery()
    {
        $index = Zend_Search_Lucene::open(dirname(__FILE__) . '/_index23Sample/_files');

        $defaultPrefixLength = Zend_Search_Lucene_Search_Query_Fuzzy::getDefaultPrefixLength();
        Zend_Search_Lucene_Search_Query_Fuzzy::setDefaultPrefixLength(0);

        $hits = $index->find('tesd~0.4');

        $this->assertEquals(count($hits), 9);
        $expectedResultset = array(array(2, 0.037139, 'IndexSource/contributing.patches.html'),
                                   array(0, 0.008735, 'IndexSource/contributing.documentation.html'),
                                   array(7, 0.002449, 'IndexSource/contributing.bugs.html'),
                                   array(1, 0.000483, 'IndexSource/contributing.wishlist.html'),
                                   array(3, 0.000483, 'IndexSource/about-pear.html'),
                                   array(9, 0.000483, 'IndexSource/core.html'),
                                   array(5, 0.000414, 'IndexSource/authors.html'),
                                   array(8, 0.000414, 'IndexSource/contributing.html'),
                                   array(4, 0.000345, 'IndexSource/copyright.html'));

        foreach ($hits as $resId => $hit) {
            $this->assertEquals($hit->id, $expectedResultset[$resId][0]);
            $this->assertTrue( abs($hit->score - $expectedResultset[$resId][1]) < 0.000001 );
            $this->assertEquals($hit->path, $expectedResultset[$resId][2]);
        }

        Zend_Search_Lucene_Search_Query_Fuzzy::setDefaultPrefixLength($defaultPrefixLength);
    }

    public function testInclusiveRangeQuery()
    {
        $index = Zend_Search_Lucene::open(dirname(__FILE__) . '/_index23Sample/_files');

        $hits = $index->find('[xml TO zzzzz]');

        $this->assertEquals(count($hits), 5);
        $expectedResultset = array(array(4, 0.156366, 'IndexSource/copyright.html'),
                                   array(2, 0.080458, 'IndexSource/contributing.patches.html'),
                                   array(7, 0.060214, 'IndexSource/contributing.bugs.html'),
                                   array(1, 0.009687, 'IndexSource/contributing.wishlist.html'),
                                   array(5, 0.005871, 'IndexSource/authors.html'));

        foreach ($hits as $resId => $hit) {
            $this->assertEquals($hit->id, $expectedResultset[$resId][0]);
            $this->assertTrue( abs($hit->score - $expectedResultset[$resId][1]) < 0.000001 );
            $this->assertEquals($hit->path, $expectedResultset[$resId][2]);
        }
    }

    public function testNonInclusiveRangeQuery()
    {
        $index = Zend_Search_Lucene::open(dirname(__FILE__) . '/_index23Sample/_files');

        $hits = $index->find('{xml TO zzzzz}');

        $this->assertEquals(count($hits), 5);
        $expectedResultset = array(array(2, 0.1308671, 'IndexSource/contributing.patches.html'),
                                   array(7, 0.0979391, 'IndexSource/contributing.bugs.html'),
                                   array(4, 0.0633930, 'IndexSource/copyright.html'),
                                   array(1, 0.0157556, 'IndexSource/contributing.wishlist.html'),
                                   array(5, 0.0095493, 'IndexSource/authors.html'));

        foreach ($hits as $resId => $hit) {
            $this->assertEquals($hit->id, $expectedResultset[$resId][0]);
            $this->assertTrue( abs($hit->score - $expectedResultset[$resId][1]) < 0.000001 );
            $this->assertEquals($hit->path, $expectedResultset[$resId][2]);
        }
    }

    public function testDefaultSearchField()
    {
        $index = Zend_Search_Lucene::open(dirname(__FILE__) . '/_index23Sample/_files');

        $storedDefaultSearchField = Zend_Search_Lucene::getDefaultSearchField();

        Zend_Search_Lucene::setDefaultSearchField('path');
        $hits = $index->find('contributing');

        $this->assertEquals(count($hits), 5);
        $expectedResultset = array(array(8, 0.847922, 'IndexSource/contributing.html'),
                                   array(0, 0.678337, 'IndexSource/contributing.documentation.html'),
                                   array(1, 0.678337, 'IndexSource/contributing.wishlist.html'),
                                   array(2, 0.678337, 'IndexSource/contributing.patches.html'),
                                   array(7, 0.678337, 'IndexSource/contributing.bugs.html'));

        foreach ($hits as $resId => $hit) {
            $this->assertEquals($hit->id, $expectedResultset[$resId][0]);
            $this->assertTrue( abs($hit->score - $expectedResultset[$resId][1]) < 0.000001 );
            $this->assertEquals($hit->path, $expectedResultset[$resId][2]);
        }

        Zend_Search_Lucene::setDefaultSearchField($storedDefaultSearchField);
    }

    public function testQueryHit()
    {
        // Restore default search field if it wasn't done by previous test because of failure
        Zend_Search_Lucene::setDefaultSearchField(null);

        $index = Zend_Search_Lucene::open(dirname(__FILE__) . '/_index23Sample/_files');

        $hits = $index->find('submitting AND wishlists');
        $hit = $hits[0];


        $this->assertTrue($hit instanceof Zend_Search_Lucene_Search_QueryHit);
        $this->assertTrue($hit->getIndex() instanceof Zend_Search_Lucene_Interface);

        $doc = $hit->getDocument();
        $this->assertTrue($doc instanceof Zend_Search_Lucene_Document);

        $this->assertEquals($doc->path, 'IndexSource/contributing.html');
    }

    public function testDelayedResourceCleanUp()
    {
        $index = Zend_Search_Lucene::open(dirname(__FILE__) . '/_index23Sample/_files');

        $hits = $index->find('submitting AND wishlists');
        unset($index);

        $hit = $hits[0];
        $this->assertTrue($hit instanceof Zend_Search_Lucene_Search_QueryHit);
        $this->assertTrue($hit->getIndex() instanceof Zend_Search_Lucene_Interface);

        $doc = $hit->getDocument();
        $this->assertTrue($doc instanceof Zend_Search_Lucene_Document);
        $this->assertTrue($hit->getIndex() instanceof Zend_Search_Lucene_Interface);

        $this->assertEquals($doc->path, 'IndexSource/contributing.html');
    }

    public function testSortingResult()
    {
        $index = Zend_Search_Lucene::open(dirname(__FILE__) . '/_index23Sample/_files');

        $hits = $index->find('"reporting bugs"', 'path');

        $this->assertEquals(count($hits), 4);
        $expectedResultset = array(array(7, 0.212395, 'IndexSource/contributing.bugs.html'),
                                   array(0, 0.247795, 'IndexSource/contributing.documentation.html'),
                                   array(8, 0.212395, 'IndexSource/contributing.html'),
                                   array(2, 0.176996, 'IndexSource/contributing.patches.html'));

        foreach ($hits as $resId => $hit) {
            $this->assertEquals($hit->id, $expectedResultset[$resId][0]);
            $this->assertTrue( abs($hit->score - $expectedResultset[$resId][1]) < 0.000001 );
            $this->assertEquals($hit->path, $expectedResultset[$resId][2]);
        }
    }

    public function testSortingResultByScore()
    {
        $index = Zend_Search_Lucene::open(dirname(__FILE__) . '/_index23Sample/_files');

        $hits = $index->find('"reporting bugs"', 'score', SORT_NUMERIC, SORT_ASC,
                                                 'path',  SORT_STRING,  SORT_ASC);
        $this->assertEquals(count($hits), 4);
        $expectedResultset = array(array(2, 0.176996, 'IndexSource/contributing.patches.html'),
                                   array(7, 0.212395, 'IndexSource/contributing.bugs.html'),
                                   array(8, 0.212395, 'IndexSource/contributing.html'),
                                   array(0, 0.247795, 'IndexSource/contributing.documentation.html'));

        foreach ($hits as $resId => $hit) {
            $this->assertEquals($hit->id, $expectedResultset[$resId][0]);
            $this->assertTrue( abs($hit->score - $expectedResultset[$resId][1]) < 0.000001 );
            $this->assertEquals($hit->path, $expectedResultset[$resId][2]);
        }

        $hits = $index->find('"reporting bugs"', 'score', SORT_NUMERIC, SORT_ASC,
                                                 'path',  SORT_STRING,  SORT_DESC);
        $this->assertEquals(count($hits), 4);
        $expectedResultset = array(array(2, 0.176996, 'IndexSource/contributing.patches.html'),
                                   array(8, 0.212395, 'IndexSource/contributing.html'),
                                   array(7, 0.212395, 'IndexSource/contributing.bugs.html'),
                                   array(0, 0.247795, 'IndexSource/contributing.documentation.html'));

        foreach ($hits as $resId => $hit) {
            $this->assertEquals($hit->id, $expectedResultset[$resId][0]);
            $this->assertTrue( abs($hit->score - $expectedResultset[$resId][1]) < 0.000001 );
            $this->assertEquals($hit->path, $expectedResultset[$resId][2]);
        }
    }

    public function testLimitingResult()
    {
        $index = Zend_Search_Lucene::open(dirname(__FILE__) . '/_index23Sample/_files');

        $storedResultSetLimit = Zend_Search_Lucene::getResultSetLimit();

        Zend_Search_Lucene::setResultSetLimit(3);

        $hits = $index->find('"reporting bugs"', 'path');

        $this->assertEquals(count($hits), 3);
        $expectedResultset = array(array(7, 0.212395, 'IndexSource/contributing.bugs.html'),
                                   array(0, 0.247795, 'IndexSource/contributing.documentation.html'),
                                   array(2, 0.176996, 'IndexSource/contributing.patches.html'));

        foreach ($hits as $resId => $hit) {
            $this->assertEquals($hit->id, $expectedResultset[$resId][0]);
            $this->assertTrue( abs($hit->score - $expectedResultset[$resId][1]) < 0.000001 );
            $this->assertEquals($hit->path, $expectedResultset[$resId][2]);
        }

        Zend_Search_Lucene::setResultSetLimit($storedResultSetLimit);
    }
}
