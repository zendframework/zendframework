<?php
/**
 * @author Stefano Torresi (http://stefanotorresi.it)
 * @license See the file LICENSE.txt for copying permission.
 * ************************************************
 */

namespace ZendTest\Mail\Transport;

use PHPUnit_Framework_TestCase;
use Zend\Mail\Transport\Factory;
use Zend\Stdlib\ArrayObject;

class FactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider invalidSpecTypeProvider
     * @expectedException \Zend\Mail\Transport\Exception\InvalidArgumentException
     * @param $spec
     */
    public function testInvalidSpecThrowsInvalidArgumentException($spec)
    {
        Factory::create($spec);
    }

    public function invalidSpecTypeProvider()
    {
        return array(
            array('spec'),
            array(new \stdClass()),
        );
    }

    /**
     *
     */
    public function testDefaultTypeIsSendmail()
    {
        $transport = Factory::create();

        $this->assertInstanceOf('Zend\Mail\Transport\Sendmail', $transport);
    }

    /**
     * @dataProvider typeProvider
     * @param $type
     */
    public function testCanCreateClassUsingTypeKey($type)
    {
        $transport = Factory::create(array(
            'type' => $type,
        ));

        $this->assertInstanceOf($type, $transport);
    }

    public function typeProvider()
    {
        return array(
            array('Zend\Mail\Transport\File'),
            array('Zend\Mail\Transport\Null'),
            array('Zend\Mail\Transport\Sendmail'),
            array('Zend\Mail\Transport\Smtp'),
        );
    }

    /**
     * @dataProvider typeAliasProvider
     * @param $type
     * @param $expectedClass
     */
    public function testCanCreateClassFromTypeAlias($type, $expectedClass)
    {
        $transport = Factory::create(array(
            'type' => $type,
        ));

        $this->assertInstanceOf($expectedClass, $transport);
    }

    public function typeAliasProvider()
    {
        return array(
            array('file', 'Zend\Mail\Transport\File'),
            array('null', 'Zend\Mail\Transport\Null'),
            array('sendmail', 'Zend\Mail\Transport\Sendmail'),
            array('smtp', 'Zend\Mail\Transport\Smtp'),
            array('File', 'Zend\Mail\Transport\File'),
            array('Null', 'Zend\Mail\Transport\Null'),
            array('NULL', 'Zend\Mail\Transport\Null'),
            array('Sendmail', 'Zend\Mail\Transport\Sendmail'),
            array('SendMail', 'Zend\Mail\Transport\Sendmail'),
            array('Smtp', 'Zend\Mail\Transport\Smtp'),
            array('SMTP', 'Zend\Mail\Transport\Smtp'),
        );
    }

    /**
     *
     */
    public function testCanUseTraversableAsSpec()
    {
        $spec = new ArrayObject(array(
            'type' => 'null'
        ));

        $transport = Factory::create($spec);

        $this->assertInstanceOf('Zend\Mail\Transport\Null', $transport);
    }

    /**
     * @dataProvider invalidClassProvider
     * @expectedException \Zend\Mail\Transport\Exception\DomainException
     * @param $class
     */
    public function testInvalidClassThrowsDomainException($class)
    {
        Factory::create(array(
            'type' => $class
        ));
    }

    public function invalidClassProvider()
    {
        return array(
            array('stdClass'),
            array('non-existent-class'),
        );
    }

    /**
     *
     */
    public function testCanCreateSmtpTransportWithOptions()
    {
        $transport = Factory::create(array(
            'type' => 'smtp',
            'options' => array(
                'host' => 'somehost',
            )
        ));

        $this->assertEquals($transport->getOptions()->getHost(), 'somehost');
    }

    /**
     *
     */
    public function testCanCreateFileTransportWithOptions()
    {
        $transport = Factory::create(array(
            'type' => 'file',
            'options' => array(
                'path' => __DIR__,
            )
        ));

        $this->assertEquals($transport->getOptions()->getPath(), __DIR__);
    }
}
