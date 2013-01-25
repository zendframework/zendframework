<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Captcha
 */

namespace ZendTest\Captcha;

use Zend\Captcha\Dumb as DumbCaptcha;

/**
 * @category   Zend
 * @package    Zend_Captcha
 * @subpackage UnitTests
 * @group      Zend_Captcha
 */
class DumbTest extends CommonWordTest
{
    protected $wordClass = 'Zend\Captcha\Dumb';

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        if (isset($this->word)) {
            unset($this->word);
        }

        $this->captcha = new DumbCaptcha(array(
            'sessionClass' => 'ZendTest\Captcha\TestAsset\SessionContainer',
        ));
    }

    public function testUsesCaptchaDumbAsHelper()
    {
        $this->assertEquals('captcha/dumb', $this->captcha->getHelperName());
    }

    public function testGeneratePopulatesId()
    {
        $id   = $this->captcha->generate();
        $test = $this->captcha->getId();
        $this->assertEquals($id, $test);
    }

    public function testGeneratePopulatesSessionWithWord()
    {
        $this->captcha->generate();
        $word    = $this->captcha->getWord();
        $session = $this->captcha->getSession();
        $this->assertEquals($word, $session->word);
    }

    public function testGenerateWillNotUseNumbersIfUseNumbersIsDisabled()
    {
        $this->captcha->setUseNumbers(false);
        $this->captcha->generate();
        $word = $this->captcha->getWord();
        $this->assertNotRegexp('/\d/', $word);
    }

    public function testWordIsExactlyAsLongAsWordLen()
    {
        $this->captcha->setWordLen(10);
        $this->captcha->generate();
        $word = $this->captcha->getWord();
        $this->assertEquals(10, strlen($word));
    }

    /**
     * @group ZF-11522
     */
    public function testDefaultLabelIsUsedWhenNoAlternateLabelSet()
    {
        $this->assertEquals('Please type this word backwards', $this->captcha->getLabel());
    }

    /**
     * @group ZF-11522
     */
    public function testChangeLabelViaSetterMethod()
    {
        $this->captcha->setLabel('Testing');
        $this->assertEquals('Testing', $this->captcha->getLabel());
    }
}
