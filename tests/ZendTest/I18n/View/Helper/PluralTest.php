<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
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
    }

    /**
     * @return array
     */
    public function pluralsTestProvider()
    {
        return array(
            array('nplurals=1; plural=0', 'かさ', 0, 'かさ'),
            array('nplurals=1; plural=0', 'かさ', 10, 'かさ'),

            array('nplurals=2; plural=(n==1 ? 0 : 1)', array('umbrella', 'umbrellas'), 0, 'umbrellas'),
            array('nplurals=2; plural=(n==1 ? 0 : 1)', array('umbrella', 'umbrellas'), 1, 'umbrella'),
            array('nplurals=2; plural=(n==1 ? 0 : 1)', array('umbrella', 'umbrellas'), 2, 'umbrellas'),

            array('nplurals=2; plural=(n==0 || n==1 ? 0 : 1)', array('parapluie', 'parapluies'), 0, 'parapluie'),
            array('nplurals=2; plural=(n==0 || n==1 ? 0 : 1)', array('parapluie', 'parapluies'), 1, 'parapluie'),
            array('nplurals=2; plural=(n==0 || n==1 ? 0 : 1)', array('parapluie', 'parapluies'), 2, 'parapluies'),
        );
    }

    /**
     * @dataProvider pluralsTestProvider
     */
    public function testGetCorrectPlurals($pluralRule, $strings, $number, $expected)
    {
        $this->helper->setPluralRule($pluralRule);
        $result = $this->helper->__invoke($strings, $number);
        $this->assertEquals($expected, $result);
    }
}
