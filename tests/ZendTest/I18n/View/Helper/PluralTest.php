<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_I18n
 */

namespace ZendTest\I18n\View\Helper;

use Zend\I18n\Translator\Plural\Rule as PluralRule;
use Zend\I18n\View\Helper\Plural as PluralHelper;

/**
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
class PluralTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PluralHelper
     */
    public $helper;

    /**
     * Sets up the fixture
     *
     * @return void
     */
    public function setUp()
    {
        $this->helper = new PluralHelper();

        // Add some rules rules for languages
        $this->helper->addPluralRule('ja', 'nplurals=1; plural=0');
        $this->helper->addPluralRule('fr', 'nplurals=2; plural=(n==0 || n==1 ? 0 : 1)');
        $this->helper->addPluralRule('en', 'nplurals=2; plural=(n==1 ? 0 : 1)');
    }

    /**
     * @return array
     */
    public function pluralsTestProvider()
    {
        return array(
            array('かさ', 0, 'ja', 'かさ'),
            array('かさ', 10, 'ja', 'かさ'),

            array(array('umbrella', 'umbrellas'), 0, 'en', 'umbrellas'),
            array(array('umbrella', 'umbrellas'), 1, 'en', 'umbrella'),
            array(array('umbrella', 'umbrellas'), 2, 'en', 'umbrellas'),

            array(array('parapluie', 'parapluies'), 0, 'fr', 'parapluie'),
            array(array('parapluie', 'parapluies'), 1, 'fr', 'parapluie'),
            array(array('parapluie', 'parapluies'), 2, 'fr', 'parapluies'),
        );
    }

    /**
     * @dataProvider pluralsTestProvider
     */
    public function testGetCorrectPlurals($strings, $number, $locale, $expected)
    {
        $result = $this->helper->__invoke($strings, $number, $locale);
        $this->assertEquals($expected, $result);
    }
}
