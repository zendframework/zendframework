<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Validator
 */

namespace ZendTest\Validator\File;

use Zend\Validator\File;

/**
 * @category   Zend
 * @package    Zend_Validator_File
 * @subpackage UnitTests
 * @group      Zend_Validator
 */
class UploadTest extends \PHPUnit_Framework_TestCase
{
    public function uploadErrorsTestDataProvider()
    {
        $data = array();
        $errorTypes = array(
            0 => 'fileUploadErrorAttack',
            1 => 'fileUploadErrorIniSize',
            2 => 'fileUploadErrorFormSize',
            3 => 'fileUploadErrorPartial',
            4 => 'fileUploadErrorNoFile',
            5 => 'fileUploadErrorUnknown',
            6 => 'fileUploadErrorNoTmpDir',
            7 => 'fileUploadErrorCantWrite',
            8 => 'fileUploadErrorExtension',
            9 => 'fileUploadErrorUnknown',
        );
        $testSizeFile = __DIR__ . '/_files/testsize.mo';

        foreach ($errorTypes as $errorCode => $errorType) {
            $data[] = array(
                // fileInfo
                array(
                    'name'     => 'test' . $errorCode,
                    'type'     => 'text',
                    'size'     => 200 + $errorCode,
                    'tmp_name' => $testSizeFile,
                    'error'    => $errorCode,
                ),
                // messageKey
                $errorType,
            );
        }
        return $data;
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @dataProvider uploadErrorsTestDataProvider
     * @return void
     */
    public function testBasic($fileInfo, $messageKey)
    {
        $validator = new File\Upload();
        $this->assertFalse($validator->isValid($fileInfo));
        $this->assertTrue(array_key_exists($messageKey, $validator->getMessages()));
    }

    /**
     * @return void
     */
    public function testRaisesExceptionWhenValueArrayIsBad()
    {
        $validator = new File\Upload();
        $this->setExpectedException('Zend\Validator\Exception\InvalidArgumentException', '$_FILES format');
        $validator->isValid(array('foo', 'bar'));
    }

    /**
     * @group ZF-11258
     */
    public function testZF11258()
    {
        $validator = new File\Upload();
        $this->assertFalse($validator->isValid(__DIR__ . '/_files/nofile.mo'));
        $this->assertTrue(array_key_exists('fileUploadErrorFileNotFound', $validator->getMessages()));
        $this->assertContains("not found", current($validator->getMessages()));
    }
}
