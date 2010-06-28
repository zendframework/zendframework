<?php

namespace ZendTest\Form\Element\TestAsset;

use Zend\File\Transfer\Adapter\AbstractAdapter;

class MockFileAdapter extends AbstractAdapter
{
    public $received = false;

    public function __construct()
    {
        $testfile = __DIR__ . '/../../../File/Transfer/Adapter/_files/test.txt';
        $this->_files = array(
            'foo' => array(
                'name'       => 'foo.jpg',
                'type'       => 'image/jpeg',
                'size'       => 126976,
                'tmp_name'   => '/tmp/489127ba5c89c',
                'options'   => array('ignoreNoFile' => false, 'useByteString' => true),
                'validated'  => false,
                'received'   => false,
                'filtered'   => false,
                'validators' => array(),
            ),
            'bar' => array(
                'name'       => 'bar.png',
                'type'       => 'image/png',
                'size'       => 91136,
                'tmp_name'   => '/tmp/489128284b51f',
                'options'   => array('ignoreNoFile' => false, 'useByteString' => true),
                'validated'  => false,
                'received'   => false,
                'filtered'   => false,
                'validators' => array(),
            ),
            'baz' => array(
                'name'       => 'baz.text',
                'type'       => 'text/plain',
                'size'       => 1172,
                'tmp_name'   => $testfile,
                'options'   => array('ignoreNoFile' => false, 'useByteString' => true),
                'validated'  => false,
                'received'   => false,
                'filtered'   => false,
                'validators' => array(),
            ),
            'file_1_' => array(
                'name'       => 'baz.text',
                'type'       => 'text/plain',
                'size'       => 1172,
                'tmp_name'   => '/tmp/4891286cceff3',
                'options'   => array('ignoreNoFile' => false, 'useByteString' => true),
                'validated'  => false,
                'received'   => false,
                'filtered'   => false,
                'validators' => array(),
            ),
            'file_2_' => array(
                'name'       => 'baz.text',
                'type'       => 'text/plain',
                'size'       => 1172,
                'tmp_name'   => '/tmp/4891286cceff3',
                'options'   => array('ignoreNoFile' => false, 'useByteString' => true),
                'validated'  => false,
                'received'   => false,
                'filtered'   => false,
                'validators' => array(),
            ),
            );
    }

    public function send($options = null)
    {
        return;
    }

    public function receive($options = null)
    {
        $this->received = true;
        return;
    }

    public function isSent($file = null)
    {
        return false;
    }

    public function isReceived($file = null)
    {
        return $this->received;
    }

    public function isUploaded($files = null)
    {
        return true;
    }

    public function isFiltered($files = null)
    {
        return true;
    }

    public static function getProgress()
    {
        return;
    }
}
