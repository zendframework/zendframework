<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\InputFilter;

use ArrayIterator;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use stdClass;
use Zend\InputFilter\BaseInputFilter;
use Zend\InputFilter\CollectionInputFilter;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;

/**
 * @covers Zend\InputFilter\CollectionInputFilter
 */
class CollectionInputFilterTest extends TestCase
{
    /**
     * @var CollectionInputFilter
     */
    protected $inputFilter;

    public function setUp()
    {
        $this->inputFilter = new CollectionInputFilter();
    }

    public function testSetInputFilterWithInvalidTypeThrowsInvalidArgumentException()
    {
        $inputFilter = $this->inputFilter;

        $this->setExpectedException(
            'Zend\InputFilter\Exception\RuntimeException',
            'expects an instance of Zend\InputFilter\BaseInputFilter; received "stdClass"'
        );
        /** @noinspection PhpParamsInspection */
        $inputFilter->setInputFilter(new stdClass());
    }


    /**
     * @dataProvider inputFilterProvider
     */
    public function testSetInputFilter($inputFilter, $expectedType)
    {
        $this->inputFilter->setInputFilter($inputFilter);

        $this->assertInstanceOf($expectedType, $this->inputFilter->getInputFilter(), 'getInputFilter() type not match');
    }

    public function testGetDefaultInputFilter()
    {
        $this->assertInstanceOf('Zend\InputFilter\BaseInputFilter', $this->inputFilter->getInputFilter());
    }

    /**
     * @dataProvider isRequiredProvider
     */
    public function testSetRequired($value)
    {
        $this->inputFilter->setIsRequired($value);
        $this->assertEquals($value, $this->inputFilter->getIsRequired());
    }

    /**
     * @dataProvider countVsDataProvider
     */
    public function testSetCount($count, $data, $expectedCount)
    {
        if ($count !== null) {
            $this->inputFilter->setCount($count);
        }
        if ($data !== null) {
            $this->inputFilter->setData($data);
        }

        $this->assertEquals($expectedCount, $this->inputFilter->getCount(), 'getCount() value not match');
    }

    /**
     * @group 6160
     */
    public function testGetCountReturnsRightCountOnConsecutiveCallsWithDifferentData()
    {
        $collectionData1 = array(
            array('foo' => 'bar'),
            array('foo' => 'baz'),
        );

        $collectionData2 = array(
            array('foo' => 'bar'),
        );

        $this->inputFilter->setData($collectionData1);
        $this->assertEquals(2, $this->inputFilter->getCount());
        $this->inputFilter->setData($collectionData2);
        $this->assertEquals(1, $this->inputFilter->getCount());
    }

    public function testInvalidCollectionIsNotValid()
    {
        $data = 1;
        $this->inputFilter->setData($data);

        $this->assertFalse($this->inputFilter->isValid());
    }

    /**
     * @dataProvider dataVsValidProvider
     */
    public function testDataVsValid(
        $required,
        $count,
        $data,
        $inputFilter,
        $expectedRaw,
        $expecteValues,
        $expectedValid,
        $expectedMessages
    ) {
        $this->inputFilter->setInputFilter($inputFilter);
        $this->inputFilter->setData($data);
        if ($count !== null) {
            $this->inputFilter->setCount($count);
        }
        $this->inputFilter->setIsRequired($required);

        $this->assertEquals(
            $expectedValid,
            $this->inputFilter->isValid(),
            'isValid() value not match. Detail . ' . json_encode($this->inputFilter->getMessages())
        );
        $this->assertEquals($expectedRaw, $this->inputFilter->getRawValues(), 'getRawValues() value not match');
        $this->assertEquals($expecteValues, $this->inputFilter->getValues(), 'getValues() value not match');
        $this->assertEquals($expectedMessages, $this->inputFilter->getMessages(), 'getMessages() value not match');
    }

    public function dataVsValidProvider()
    {
        $dataRaw = array(
            'fooInput' => 'fooRaw',
        );
        $dataFiltered = array(
            'fooInput' => 'fooFiltered',
        );
        $colRaw = array($dataRaw);
        $colFiltered = array($dataFiltered);
        $errorMessage = array(
            'fooInput' => 'fooError',
        );
        $colMessages = array($errorMessage);

        $self = $this;
        $invalidIF = function () use ($dataRaw, $dataFiltered, $errorMessage, $self) {
            return $self->createBaseInputFilterMock(false, $dataRaw, $dataFiltered, $errorMessage);
        };
        $validIF = function () use ($dataRaw, $dataFiltered, $self) {
            return $self->createBaseInputFilterMock(true, $dataRaw, $dataFiltered);
        };
        $isRequired = true;

        // @codingStandardsIgnoreStart
        $dataSets = array(
            // Description => [$required, $count, $data, $inputFilter, $expectedRaw, $expecteValues, $expectedValid, $expectedMessages]
            'Required: T, Count: N, Valid: T'       => array( $isRequired, null, $colRaw, $validIF  , $colRaw, $colFiltered, true , array()),
            'Required: T, Count: N, Valid: F'       => array( $isRequired, null, $colRaw, $invalidIF, $colRaw, $colFiltered, false, $colMessages),
            'Required: T, Count: +1, Valid: F'      => array( $isRequired,    2, $colRaw, $invalidIF, $colRaw, $colFiltered, false, $colMessages),
            'Required: F, Count: N, Valid: T'       => array(!$isRequired, null, $colRaw, $validIF  , $colRaw, $colFiltered, true , array()),
            'Required: F, Count: N, Valid: F'       => array(!$isRequired, null, $colRaw, $invalidIF, $colRaw, $colFiltered, false, $colMessages),
            'Required: F, Count: +1, Valid: F'      => array(!$isRequired,    2, $colRaw, $invalidIF, $colRaw, $colFiltered, false, $colMessages),
            'Required: T, Data: array(), Valid: X'  => array( $isRequired, null, array(), $invalidIF, array(),      array(), false, array()),
            'Required: F, Data: array(), Valid: X'  => array(!$isRequired, null, array(), $invalidIF, array(),      array(), true , array()),
        );
        // @codingStandardsIgnoreEnd

        array_walk(
            $dataSets,
            function (&$set) {
                // Create unique mock input instances for each set
                $inputFilter = $set[3]();
                $set[3] = $inputFilter;
            }
        );

        return $dataSets;
    }

    public function testSetValidationGroupUsingFormStyle()
    {
        $validationGroup = array(
            'fooGroup',
        );
        $colValidationGroup = array($validationGroup);

        $dataRaw = array(
            'fooInput' => 'fooRaw',
        );

        $dataFiltered = array(
            'fooInput' => 'fooFiltered',
        );
        $colRaw = array($dataRaw);
        $colFiltered = array($dataFiltered);
        $baseInputFilter = $this->createBaseInputFilterMock(true, $dataRaw, $dataFiltered);
        $baseInputFilter->expects($this->once())
            ->method('setValidationGroup')
            ->with($validationGroup)
        ;

        $this->inputFilter->setInputFilter($baseInputFilter);
        $this->inputFilter->setData($colRaw);
        $this->inputFilter->setValidationGroup($colValidationGroup);

        $this->assertTrue(
            $this->inputFilter->isValid(),
            'isValid() value not match. Detail . ' . json_encode($this->inputFilter->getMessages())
        );
        $this->assertEquals($colRaw, $this->inputFilter->getRawValues(), 'getRawValues() value not match');
        $this->assertEquals($colFiltered, $this->inputFilter->getValues(), 'getValues() value not match');
        $this->assertEquals(array(), $this->inputFilter->getMessages(), 'getMessages() value not match');
    }

    public function dataNestingCollection()
    {
        return array(
            'count not specified' => array(
                'count' => null,
                'isValid' => true,
            ),
            'count=0' => array(
                'count' => 0,
                'isValid' => true,
            ),
            'count = 1' =>  array(
                'count' => 1,
                'isValid' => true,
            ),
            'count = 2' => array(
                'count' => 2,
                'isValid' => false,
            ),
            'count = 3' => array(
                'count' => 3,
                'isValid' => false,
            ),
        );
    }

    /**
     * @dataProvider dataNestingCollection
     */
    public function testNestingCollectionCountCached($count, $expectedIsValid)
    {
        $firstInputFilter = new InputFilter();

        $firstCollection = new CollectionInputFilter();
        $firstCollection->setInputFilter($firstInputFilter);

        $someInput = new Input('input');
        $secondInputFilter = new InputFilter();
        $secondInputFilter->add($someInput, 'input');

        $secondCollection = new CollectionInputFilter();
        $secondCollection->setInputFilter($secondInputFilter);
        if (!is_null($count)) {
            $secondCollection->setCount($count);
        }

        $firstInputFilter->add($secondCollection, 'second_collection');

        $mainInputFilter = new InputFilter();
        $mainInputFilter->add($firstCollection, 'first_collection');

        $data = array(
            'first_collection' => array(
                array(
                    'second_collection' => array(
                        array(
                            'input' => 'some value',
                        ),
                        array(
                            'input' => 'some value',
                        ),
                    ),
                ),
                array(
                    'second_collection' => array(
                        array(
                            'input' => 'some value',
                        ),
                    ),
                ),
            ),
        );

        $mainInputFilter->setData($data);
        $this->assertSame($expectedIsValid, $mainInputFilter->isValid());
    }

    public function inputFilterProvider()
    {
        $baseInputFilter = new BaseInputFilter();

        $inputFilterSpecificationAsArray = array();
        $inputSpecificationAsTraversable = new ArrayIterator($inputFilterSpecificationAsArray);

        $inputFilterSpecificationResult = new InputFilter();
        $inputFilterSpecificationResult->getFactory()->getInputFilterManager();

        $dataSets = array(
            // Description => [inputFilter, $expectedType]
            'BaseInputFilter' => array($baseInputFilter, 'Zend\InputFilter\BaseInputFilter'),
            'array' => array($inputFilterSpecificationAsArray, 'Zend\InputFilter\InputFilter'),
            'Traversable' => array($inputSpecificationAsTraversable, 'Zend\InputFilter\InputFilter'),
        );

        return $dataSets;
    }

    public function countVsDataProvider()
    {
        $data0 = array();
        $data1 = array('A' => 'a');
        $data2 = array('A' => 'a', 'B' => 'b');

        // @codingStandardsIgnoreStart
        return array(
            // Description => [$count, $data, $expectedCount]
            'C:   -1, D: null' => array(  -1, null  ,  0),
            'C:    0, D: null' => array(   0, null  ,  0),
            'C:    1, D: null' => array(   1, null  ,  1),
            'C: null, D:    0' => array(null, $data0,  0),
            'C: null, D:    1' => array(null, $data1,  1),
            'C: null, D:    2' => array(null, $data2,  2),
            'C:   -1, D:    0' => array(  -1, $data0,  0),
            'C:    0, D:    0' => array(   0, $data0,  0),
            'C:    1, D:    0' => array(   1, $data0,  1),
            'C:   -1, D:    1' => array(  -1, $data1,  0),
            'C:    0, D:    1' => array(   0, $data1,  0),
            'C:    1, D:    1' => array(   1, $data1,  1),
        );
        // @codingStandardsIgnoreEnd
    }

    public function isRequiredProvider()
    {
        return array(
            'enabled' => array(true),
            'disabled' => array(false),
        );
    }

    /**
     * @param null|bool $isValid
     * @param mixed[] $getRawValues
     * @param mixed[] $getValues
     * @param string[] $getMessages
     *
     * @return MockObject|BaseInputFilter
     */
    public function createBaseInputFilterMock(
        $isValid = null,
        $getRawValues = array(),
        $getValues = array(),
        $getMessages = array()
    ) {
        /** @var BaseInputFilter|MockObject $inputFilter */
        $inputFilter = $this->getMock('Zend\InputFilter\BaseInputFilter');
        $inputFilter->method('getRawValues')
            ->willReturn($getRawValues)
        ;
        $inputFilter->method('getValues')
            ->willReturn($getValues)
        ;
        if (($isValid === false) || ($isValid === true)) {
            $inputFilter->expects($this->once())
                ->method('isValid')
                ->willReturn($isValid)
            ;
        } else {
            $inputFilter->expects($this->never())
                ->method('isValid')
            ;
        }
        $inputFilter->method('getMessages')
            ->willReturn($getMessages)
        ;

        return $inputFilter;
    }
}
