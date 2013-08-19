<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Form
 */

namespace ZendTest\Form;

use Zend\ServiceManager\ServiceManager;
use Zend\Form\Factory;
use Zend\Form\Form;
use Zend\Form\FormElementManager;

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage UnitTests
 * @group      Zend_Form
 */
class FormElementManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var FormElementManager
     */
    protected $manager;

    public function setUp()
    {
        $this->manager = new FormElementManager();
    }

    public function testInjectToFormFactoryAware()
    {
        $form = $this->manager->get('Form');
        $this->assertSame($this->manager, $form->getFormFactory()->getFormElementManager());
    }

    /**
     * @group 3735
     */
    public function testInjectsFormElementManagerToFormComposedByFormFactoryAwareElement()
    {
        $factory = new Factory();
        $this->manager->setFactory('my-form', function ($elements) use ($factory) {
            $form = new Form();
            $form->setFormFactory($factory);
            return $form;
        });
        $form = $this->manager->get('my-Form');
        $this->assertSame($factory, $form->getFormFactory());
        $this->assertSame($this->manager, $form->getFormFactory()->getFormElementManager());
    }

    public function testRegisteringInvalidElementRaisesException()
    {
        $this->setExpectedException('Zend\Form\Exception\InvalidElementException');
        $this->manager->setService('test', $this);
    }

    public function testLoadingInvalidElementRaisesException()
    {
        $this->manager->setInvokableClass('test', get_class($this));
        $this->setExpectedException('Zend\Form\Exception\InvalidElementException');
        $this->manager->get('test');
    }
}
