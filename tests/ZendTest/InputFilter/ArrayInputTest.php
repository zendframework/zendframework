<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\InputFilter;

use Zend\InputFilter\ArrayInput;

/**
 * @covers Zend\InputFilter\ArrayInput
 */
class ArrayInputTest extends InputTest
{
    public function setUp()
    {
        $this->input = new ArrayInput('foo');
    }

    public function testDefaultGetValue()
    {
        $this->assertCount(0, $this->input->getValue());
    }

    public function testSetValueWithInvalidInputTypeThrowsInvalidArgumentException()
    {
        $this->setExpectedException(
            'Zend\InputFilter\Exception\InvalidArgumentException',
            'Value must be an array, string given'
        );
        $this->input->setValue('bar');
    }

    public function fallbackValueVsIsValidProvider()
    {
        $dataSets = parent::fallbackValueVsIsValidProvider();
        array_walk($dataSets, function (&$set) {
            $set[1] = array($set[1]); // Wrap fallback value into an array.
            $set[2] = array($set[2]); // Wrap value into an array.
            $set[4] = array($set[4]); // Wrap expected value into an array.
        });

        return $dataSets;
    }

    public function emptyValueProvider()
    {
        $dataSets = parent::emptyValueProvider();
        array_walk($dataSets, function (&$set) {
            $set['raw'] = array($set['raw']); // Wrap value into an array.
        });

        return $dataSets;
    }

    public function mixedValueProvider()
    {
        $dataSets = parent::mixedValueProvider();
        array_walk($dataSets, function (&$set) {
            $set['raw'] = array($set['raw']); // Wrap value into an array.
        });

        return $dataSets;
    }

    public function createFilterChainMock($valueRaw = null, $valueFiltered = null)
    {
        // ArrayInput filters per each array value
        if (is_array($valueRaw)) {
            $valueRaw = current($valueRaw);
        }

        if (is_array($valueFiltered)) {
            $valueFiltered = current($valueFiltered);
        }

        return parent::createFilterChainMock($valueRaw, $valueFiltered);
    }

    public function createValidatorChainMock($isValid = null, $value = null, $context = null, $messages = array())
    {
        // ArrayInput validates per each array value
        if (is_array($value)) {
            $value = current($value);
        }

        return parent::createValidatorChainMock($isValid, $value, $context, $messages);
    }

    public function createNonEmptyValidatorMock($isValid, $value, $context = null)
    {
        // ArrayInput validates per each array value
        if (is_array($value)) {
            $value = current($value);
        }

        return parent::createNonEmptyValidatorMock($isValid, $value, $context);
    }

    public function getDummyValue($raw = true)
    {
        return array(parent::getDummyValue($raw));
    }
}
