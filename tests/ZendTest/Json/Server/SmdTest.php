<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Json
 */

namespace ZendTest\Json\Server;

use Zend\Json\Server\Smd;
use Zend\Json\Server;
use Zend\Json\Server\Exception\InvalidArgumentException;
use Zend\Json\Server\Exception\RuntimeException;
use Zend\Json;

/**
 * Test class for Zend_JSON_Server_Smd
 *
 * @category   Zend
 * @package    Zend_Json_Server
 * @subpackage UnitTests
 * @group      Zend_Json
 * @group      Zend_Json_Server
 */
class SmdTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $this->smd = new SMD();
    }

    public function testTransportShouldDefaultToPost()
    {
        $this->assertEquals('POST', $this->smd->getTransport());
    }

    public function testTransportAccessorsShouldWorkUnderNormalInput()
    {
        $this->smd->setTransport('POST');
        $this->assertEquals('POST', $this->smd->getTransport());
    }

    public function testTransportShouldBeLimitedToPost()
    {
        foreach (array('GET', 'REST') as $transport) {
            try {
                $this->smd->setTransport($transport);
                $this->fail('Invalid transport should throw exception');
            } catch (InvalidArgumentException $e) {
                $this->assertContains('Invalid transport', $e->getMessage());
            }
        }
    }

    public function testEnvelopeShouldDefaultToJSONRpcVersion1()
    {
        $this->assertEquals(Smd::ENV_JSONRPC_1, $this->smd->getEnvelope());
    }

    public function testEnvelopeAccessorsShouldWorkUnderNormalInput()
    {
        $this->testEnvelopeShouldDefaultToJSONRpcVersion1();
        $this->smd->setEnvelope(Smd::ENV_JSONRPC_2);
        $this->assertEquals(Smd::ENV_JSONRPC_2, $this->smd->getEnvelope());
        $this->smd->setEnvelope(Smd::ENV_JSONRPC_1);
        $this->assertEquals(Smd::ENV_JSONRPC_1, $this->smd->getEnvelope());
    }

    public function testEnvelopeShouldBeLimitedToJSONRpcVersions()
    {
        foreach (array('URL', 'PATH', 'JSON') as $env) {
            try {
                $this->smd->setEnvelope($env);
                $this->fail('Invalid envelope type should throw exception');
            } catch (InvalidArgumentException $e) {
                $this->assertContains('Invalid envelope', $e->getMessage());
            }
        }
    }

    public function testContentTypeShouldDefaultToApplicationJSON()
    {
        $this->assertEquals('application/json', $this->smd->getContentType());
    }

    public function testContentTypeAccessorsShouldWorkUnderNormalInput()
    {
        foreach (array('text/json', 'text/plain', 'application/x-json') as $type) {
            $this->smd->setContentType($type);
            $this->assertEquals($type, $this->smd->getContentType());
        }
    }

    public function testContentTypeShouldBeLimitedToMimeFormatStrings()
    {
        foreach (array('plain', 'json', 'foobar') as $type) {
            try {
                $this->smd->setContentType($type);
                $this->fail('Invalid content type should raise exception');
            } catch (InvalidArgumentException $e) {
                $this->assertContains('Invalid content type', $e->getMessage());
            }
        }
    }

    public function testTargetShouldDefaultToNull()
    {
        $this->assertNull($this->smd->getTarget());
    }

    public function testTargetAccessorsShouldWorkUnderNormalInput()
    {
        $this->testTargetShouldDefaultToNull();
        $this->smd->setTarget('foo');
        $this->assertEquals('foo', $this->smd->getTarget());
    }

    public function testIdShouldDefaultToNull()
    {
        $this->assertNull($this->smd->getId());
    }

    public function testIdAccessorsShouldWorkUnderNormalInput()
    {
        $this->testIdShouldDefaultToNull();
        $this->smd->setId('foo');
        $this->assertEquals('foo', $this->smd->getId());
    }

    public function testDescriptionShouldDefaultToNull()
    {
        $this->assertNull($this->smd->getDescription());
    }

    public function testDescriptionAccessorsShouldWorkUnderNormalInput()
    {
        $this->testDescriptionShouldDefaultToNull();
        $this->smd->setDescription('foo');
        $this->assertEquals('foo', $this->smd->getDescription());
    }

    public function testDojoCompatibilityShouldBeDisabledByDefault()
    {
        $this->assertFalse($this->smd->isDojoCompatible());
    }

    public function testDojoCompatibilityFlagShouldBeMutable()
    {
        $this->testDojoCompatibilityShouldBeDisabledByDefault();
        $this->smd->setDojoCompatible(true);
        $this->assertTrue($this->smd->isDojoCompatible());
        $this->smd->setDojoCompatible(false);
        $this->assertFalse($this->smd->isDojoCompatible());
    }

    public function testServicesShouldBeEmptyByDefault()
    {
        $services = $this->smd->getServices();
        $this->assertTrue(is_array($services));
        $this->assertTrue(empty($services));
    }

    public function testShouldBeAbleToUseServiceObjectToAddService()
    {
        $service = new Smd\Service('foo');
        $this->smd->addService($service);
        $this->assertSame($service, $this->smd->getService('foo'));
    }

    public function testShouldBeAbleToUseArrayToAddService()
    {
        $service = array(
            'name' => 'foo',
        );
        $this->smd->addService($service);
        $foo = $this->smd->getService('foo');
        $this->assertTrue($foo instanceof Smd\Service);
        $this->assertEquals('foo', $foo->getName());
    }

    public function testAddingServiceWithExistingServiceNameShouldThrowException()
    {
        $service = new Smd\Service('foo');
        $this->smd->addService($service);
        $test    = new Smd\Service('foo');
        try {
            $this->smd->addService($test);
            $this->fail('Adding service with existing service name should throw exception');
        } catch (RuntimeException $e) {
            $this->assertContains('already register', $e->getMessage());
        }
    }

    public function testAttemptingToRegisterInvalidServiceShouldThrowException()
    {
        foreach (array('foo', false, 1, 1.0) as $service) {
            try {
                $this->smd->addService($service);
                $this->fail('Attempt to register invalid service should throw exception');
            } catch (InvalidArgumentException $e) {
                $this->assertContains('Invalid service', $e->getMessage());
            }
        }
    }

    public function testShouldBeAbleToAddManyServicesAtOnceWithArrayOfServiceObjects()
    {
        $one   = new Smd\Service('one');
        $two   = new Smd\Service('two');
        $three = new Smd\Service('three');
        $services = array($one, $two, $three);
        $this->smd->addServices($services);
        $test = $this->smd->getServices();
        $this->assertSame($services, array_values($test));
    }

    public function testShouldBeAbleToAddManyServicesAtOnceWithArrayOfArrays()
    {
        $services = array(
            array('name' => 'one'),
            array('name' => 'two'),
            array('name' => 'three'),
        );
        $this->smd->addServices($services);
        $test = $this->smd->getServices();
        $this->assertSame(array('one', 'two', 'three'), array_keys($test));
    }

    public function testShouldBeAbleToAddManyServicesAtOnceWithMixedArrayOfObjectsAndArrays()
    {
        $two = new Smd\Service('two');
        $services = array(
            array('name' => 'one'),
            $two,
            array('name' => 'three'),
        );
        $this->smd->addServices($services);
        $test = $this->smd->getServices();
        $this->assertSame(array('one', 'two', 'three'), array_keys($test));
        $this->assertEquals($two, $test['two']);
    }

    public function testSetServicesShouldOverwriteExistingServices()
    {
        $this->testShouldBeAbleToAddManyServicesAtOnceWithMixedArrayOfObjectsAndArrays();
        $five = new Smd\Service('five');
        $services = array(
            array('name' => 'four'),
            $five,
            array('name' => 'six'),
        );
        $this->smd->setServices($services);
        $test = $this->smd->getServices();
        $this->assertSame(array('four', 'five', 'six'), array_keys($test));
        $this->assertEquals($five, $test['five']);
    }

    public function testShouldBeAbleToRetrieveServiceByName()
    {
        $this->testShouldBeAbleToUseServiceObjectToAddService();
    }

    public function testShouldBeAbleToRemoveServiceByName()
    {
        $this->testShouldBeAbleToUseServiceObjectToAddService();
        $this->assertTrue($this->smd->removeService('foo'));
        $this->assertFalse($this->smd->getService('foo'));
    }

    public function testShouldBeAbleToCastToArray()
    {
        $options = $this->getOptions();
        $this->smd->setOptions($options);
        $service = $this->smd->toArray();
        $this->validateServiceArray($service, $options);
    }

    public function testShouldBeAbleToCastToDojoArray()
    {
        $options = $this->getOptions();
        $this->smd->setOptions($options);
        $smd = $this->smd->toDojoArray();

        $this->assertTrue(is_array($smd));

        $this->assertTrue(array_key_exists('SMDVersion', $smd));
        $this->assertTrue(array_key_exists('serviceType', $smd));
        $this->assertTrue(array_key_exists('methods', $smd));

        $this->assertEquals('.1', $smd['SMDVersion']);
        $this->assertEquals('JSON-RPC', $smd['serviceType']);
        $methods = $smd['methods'];
        $this->assertEquals(2, count($methods));

        $foo = array_shift($methods);
        $this->assertTrue(array_key_exists('name', $foo));
        $this->assertTrue(array_key_exists('serviceURL', $foo));
        $this->assertTrue(array_key_exists('parameters', $foo));
        $this->assertEquals('foo', $foo['name']);
        $this->assertEquals($this->smd->getTarget(), $foo['serviceURL']);
        $this->assertTrue(is_array($foo['parameters']));
        $this->assertEquals(1, count($foo['parameters']));

        $bar = array_shift($methods);
        $this->assertTrue(array_key_exists('name', $bar));
        $this->assertTrue(array_key_exists('serviceURL', $bar));
        $this->assertTrue(array_key_exists('parameters', $bar));
        $this->assertEquals('bar', $bar['name']);
        $this->assertEquals($this->smd->getTarget(), $bar['serviceURL']);
        $this->assertTrue(is_array($bar['parameters']));
        $this->assertEquals(1, count($bar['parameters']));
    }

    public function testShouldBeAbleToRenderAsJSON()
    {
        $options = $this->getOptions();
        $this->smd->setOptions($options);
        $json = $this->smd->toJSON();
        $smd  = Json\Json::decode($json, Json\Json::TYPE_ARRAY);
        $this->validateServiceArray($smd, $options);
    }

    public function testToStringImplementationShouldProxyToJSON()
    {
        $options = $this->getOptions();
        $this->smd->setOptions($options);
        $json = $this->smd->__toString();
        $smd  = Json\Json::decode($json, Json\Json::TYPE_ARRAY);
        $this->validateServiceArray($smd, $options);
    }

    public function getOptions()
    {
        return array(
            'target'   => '/test/me',
            'id'       => '/test/me',
            'services' => array(
                array(
                    'name'   => 'foo',
                    'params' => array(
                        array('type' => 'boolean'),
                    ),
                    'return' => 'boolean',
                ),
                array(
                    'name'   => 'bar',
                    'params' => array(
                        array('type' => 'integer'),
                    ),
                    'return' => 'string',
                ),
            )
        );
    }

    public function validateServiceArray(array $smd, array $options)
    {
        $this->assertTrue(is_array($smd));

        $this->assertTrue(array_key_exists('SMDVersion', $smd));
        $this->assertTrue(array_key_exists('target', $smd));
        $this->assertTrue(array_key_exists('id', $smd));
        $this->assertTrue(array_key_exists('transport', $smd));
        $this->assertTrue(array_key_exists('envelope', $smd));
        $this->assertTrue(array_key_exists('contentType', $smd));
        $this->assertTrue(array_key_exists('services', $smd));

        $this->assertEquals(Smd::SMD_VERSION, $smd['SMDVersion']);
        $this->assertEquals($options['target'], $smd['target']);
        $this->assertEquals($options['id'], $smd['id']);
        $this->assertEquals($this->smd->getTransport(), $smd['transport']);
        $this->assertEquals($this->smd->getEnvelope(), $smd['envelope']);
        $this->assertEquals($this->smd->getContentType(), $smd['contentType']);
        $services = $smd['services'];
        $this->assertEquals(2, count($services));
        $this->assertTrue(array_key_exists('foo', $services));
        $this->assertTrue(array_key_exists('bar', $services));
    }
}
