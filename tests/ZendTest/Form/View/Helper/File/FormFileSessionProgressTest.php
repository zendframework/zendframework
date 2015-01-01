<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Form\View\Helper\File;

use Zend\Form\View\Helper\File\FormFileSessionProgress;
use ZendTest\Form\View\Helper\CommonTestCase;

class FormFileSessionProgressTest extends CommonTestCase
{
    public function setUp()
    {
        $this->helper = new FormFileSessionProgress();
        parent::setUp();
    }

    public function testReturnsNameIdAndValueAttributes()
    {
        $name = ini_get('session.upload_progress.name');
        if (false === $name) {
            $this->markTestSkipped('Session Upload Progress feature is not active');
        }

        $markup = $this->helper->__invoke();
        $this->assertContains('<input ', $markup);
        $this->assertContains('type="hidden"', $markup);
        $this->assertContains('id="progress_key"', $markup);
        $this->assertContains('name="' . $name . '"', $markup);
        $this->assertContains('value="', $markup);
    }
}
