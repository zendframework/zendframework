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
 * @package    Zend_Captcha
 * @subpackage UnitTests
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
