<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Form\View\Helper;

use Zend\Escaper\Escaper;

/**
 * Tests for {@see \Zend\Form\View\Helper\AbstractHelper}
 *
 * @covers \Zend\Form\View\Helper\AbstractHelper
 */
class AbstractHelperTest extends CommonTestCase
{
    public function setUp()
    {
        $this->helper = $this->getMockForAbstractClass('Zend\Form\View\Helper\AbstractHelper');

        parent::setUp();
    }

    /**
     * @group 5991
     */
    public function testWillEscapeValueAttributeValuesCorrectly()
    {
        $this->assertSame(
            'data-value="breaking&#x20;your&#x20;HTML&#x20;like&#x20;a&#x20;boss&#x21;&#x20;&#x5C;"',
            $this->helper->createAttributesString(array('data-value' => 'breaking your HTML like a boss! \\'))
        );
    }

    public function testWillEncodeValueAttributeValuesCorrectly()
    {
        $escaper = new Escaper('iso-8859-1');

        $this->helper->setEncoding('iso-8859-1');

        $this->assertSame(
            'data-value="' . $escaper->escapeHtmlAttr('Título') . '"',
            $this->helper->createAttributesString(array('data-value' => 'Título'))
        );
    }

    public function testWillNotEncodeValueAttributeValuesCorrectly()
    {
        $escaper = new Escaper('iso-8859-1');

        $this->assertNotSame(
            'data-value="' . $escaper->escapeHtmlAttr('Título') . '"',
            $this->helper->createAttributesString(array('data-value' => 'Título'))
        );
    }
}
