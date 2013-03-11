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
use Zend\Mvc\Service\ServiceManagerConfig;

class FormAbstractServiceFactoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     *
     * @var \Zend\ServiceManager\ServiceManager
     */
    private $serviceManager;

    /**
     * Set up service manager and form specification.
     */
    protected function setUp ()
    {
        $this->serviceManager = new ServiceManager(new ServiceManagerConfig(array(
            'abstract_factories' => array(
                'Zend\Form\FormAbstractServiceFactory'
            )
        )));
        $this->serviceManager->setService('Config', array(
            'form' => array(
                'Frontend\Form\Authentication' => array(
                    'type' => 'form',
                    'attributes' => array(
                        'action' => '/path/to/controller',
                        'method' => 'POST'
                    ),
                    'elements' => array(
                        array(
                            'spec' => array(
                                'name' => 'username',
                                'attributes' => array(
                                    'type' => 'text'
                                ),
                                'options' => array(
                                    'label' => 'Username'
                                )
                            )
                        ),
                        array(
                            'spec' => array(
                                'name' => 'password',
                                'attributes' => array(
                                    'type' => 'password'
                                ),
                                'options' => array(
                                    'label' => 'Password'
                                )
                            )
                        ),
                        array(
                            'spec' => array(
                                'name' => 'submit',
                                'attributes' => array(
                                    'type' => 'submit'
                                ),
                                'options' => array(
                                    'label' => 'Sign in'
                                )
                            )
                        )
                    ),
                    'input_filter' => array(
                        array(
                            'name' => 'username',
                            'required' => true,
                            'filters' => array(
                                array(
                                    'name' => 'StringTrim'
                                )
                            ),
                            'validators' => array(
                                array(
                                    'name' => 'RegEx',
                                    'options' => array(
                                        'pattern' => '/^[a-zA-Z][a-zA-Z0-9]+_[a-zA-Z][a-zA-Z0-9]+$/',
                                        'messages' => array(
                                            'regexNotMatch' => 'Username is incorrect'
                                        )
                                    )
                                )
                            )
                        ),
                        array(
                            'name' => 'password',
                            'required' => true,
                            'filters' => array(
                                array(
                                    'name' => 'StringTrim'
                                )
                            )
                        )
                    )
                )
            )
        ));
    }

    /**
     *
     * @return array
     */
    public function providerValidService ()
    {
        return array(
            array(
                'Frontend\Form\Authentication'
            )
        );
    }

    /**
     *
     * @return array
     */
    public function providerInvalidService ()
    {
        return array(
            array(
                'Frontend\Form\Unkonwn'
            )
        );
    }

    /**
     *
     * @param string $service
     *            @dataProvider providerValidService
     */
    public function testValidService ($service)
    {
        $actual = $this->serviceManager->get($service);
        $this->assertInstanceOf('Zend\Form\Form', $actual);
    }

    /**
     *
     * @param string $service
     *            @dataProvider providerInvalidService
     *            @expectedException Zend\ServiceManager\Exception\ServiceNotFoundException
     */
    public function testInvalidService ($service)
    {
        $actual = $this->serviceManager->get($service);
    }
}
