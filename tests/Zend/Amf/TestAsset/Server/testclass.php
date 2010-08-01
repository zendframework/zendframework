<?php

namespace ZendTest\Amf\TestAsset\Server;

/**
 * Class to used with Zend_Amf_Server unit tests.
 *
 */
class testclass
{
    public function __construct()
    {
    }

     /**
     * Concatinate a string
     *
     * @param string
     * @return string
     */
    public function test1($string = '')
    {
        return 'String: '. (string) $string;
    }

    /**
     * Test2
     *
     * Returns imploded array
     *
     * @param array $array
     * @return string
     */
    public static function test2($array)
    {
        return implode('; ', (array) $array);
    }

    /**
     * Test3
     *
     * Should not be available...
     *
     * @return void
     */
    protected function _test3()
    {
    }

    /**
     * Test base64 encoding in request and response
     *
     * @param  base64 $data
     * @return base64
     */
    public function base64($data)
    {
        return $data;
    }

    /**
     * Test that invoke arguments are passed
     *
     * @param  string $message message argument for comparisons
     * @return string
     */
    public function checkArgv($message)
    {
        $argv = func_get_args();
        return implode(':', $argv);
    }

    /**
     * Test static usage
     *
     * @param  string $message
     * @return string
     */
    public static function checkStaticUsage($message)
    {
        return $message;
    }

    /**
     * Test throwing exceptions
     *
     * @return void
     */
    public function throwException()
    {
        throw new \Exception('This exception should not be displayed');
    }

    /**
     * test if we can send an array as a paramater without it getting nested two
     * Used to test  ZF-5388
     */
    public function testSingleArrayParamater($inputArray){
        if( $inputArray[0] == 'item1' ){
            return true;
        }
        return false;
    }
    /**
     * This will crash if two arrays are not passed into the function.
     * Used to test  ZF-5388
     */
    public function testMultiArrayParamater($arrayOne, $arrayTwo)
    {
        return array_merge($arrayOne, $arrayTwo);
    }

}

