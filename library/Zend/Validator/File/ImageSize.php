<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category  Zend
 * @package   Zend_Validate
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Validator\File;

use Zend\Loader,
    Zend\Validator,
    Zend\Validator\Exception;

/**
 * Validator for the image size of a image file
 *
 * @uses      \Zend\Loader
 * @uses      \Zend\Validator\AbstractValidator
 * @uses      \Zend\Validator\Exception
 * @category  Zend
 * @package   Zend_Validate
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class ImageSize extends Validator\AbstractValidator
{
    /**
     * @const string Error constants
     */
    const WIDTH_TOO_BIG    = 'fileImageSizeWidthTooBig';
    const WIDTH_TOO_SMALL  = 'fileImageSizeWidthTooSmall';
    const HEIGHT_TOO_BIG   = 'fileImageSizeHeightTooBig';
    const HEIGHT_TOO_SMALL = 'fileImageSizeHeightTooSmall';
    const NOT_DETECTED     = 'fileImageSizeNotDetected';
    const NOT_READABLE     = 'fileImageSizeNotReadable';

    /**
     * @var array Error message template
     */
    protected $_messageTemplates = array(
        self::WIDTH_TOO_BIG    => "Maximum allowed width for image '%value%' should be '%maxwidth%' but '%width%' detected",
        self::WIDTH_TOO_SMALL  => "Minimum expected width for image '%value%' should be '%minwidth%' but '%width%' detected",
        self::HEIGHT_TOO_BIG   => "Maximum allowed height for image '%value%' should be '%maxheight%' but '%height%' detected",
        self::HEIGHT_TOO_SMALL => "Minimum expected height for image '%value%' should be '%minheight%' but '%height%' detected",
        self::NOT_DETECTED     => "The size of image '%value%' could not be detected",
        self::NOT_READABLE     => "File '%value%' is not readable or does not exist",
    );

    /**
     * @var array Error message template variables
     */
    protected $_messageVariables = array(
        'minwidth'  => array('options' => 'minWidth'),
        'maxwidth'  => array('options' => 'maxWidth'),
        'minheight' => array('options' => 'minHeight'),
        'maxheight' => array('options' => 'maxHeight'),
        'width'     => '_width',
        'height'    => '_height'
    );

    /**
     * Detected width
     *
     * @var integer
     */
    protected $_width;

    /**
     * Detected height
     *
     * @var integer
     */
    protected $_height;

    /**
     * Options for this validator
     *
     * @var array
     */
    protected $options = array(
        'minWidth'  => null,  // Minimum image width
        'maxWidth'  => null,  // Maximum image width
        'minHeight' => null,  // Minimum image height
        'maxHeight' => null,  // Maximum image height
    );

    /**
     * Sets validator options
     *
     * Accepts the following option keys:
     * - minheight
     * - minwidth
     * - maxheight
     * - maxwidth
     *
     * @param  \Zend\Config\Config|array $options
     * @return void
     */
    public function __construct($options = null)
    {
        if (1 < func_num_args()) {
            if (!is_array($options)) {
                $options = array('minWidth' => $options);
            }

            $argv = func_get_args();
            array_shift($argv);
            $options['minHeight'] = array_shift($argv);
            if (!empty($argv)) {
                $options['maxWidth'] = array_shift($argv);
                if (!empty($argv)) {
                    $options['maxHeight'] = array_shift($argv);
                }
            }
        }

        parent::__construct($options);
    }

    /**
     * Returns the minimum allowed width
     *
     * @return integer
     */
    public function getMinWidth()
    {
        return $this->options['minWidth'];
    }

    /**
     * Sets the minimum allowed width
     *
     * @param  integer $minWidth
     * @throws \Zend\Validator\Exception      When minwidth is greater than maxwidth
     * @return \Zend\Validator\File\ImageSize Provides a fluid interface
     */
    public function setMinWidth($minWidth)
    {
        if (($this->getMaxWidth() !== null) && ($minWidth > $this->getMaxWidth())) {
            throw new Exception\InvalidArgumentException("The minimum image width must be less than or equal to the "
                . " maximum image width, but {$minWidth} > {$this->getMaxWidth()}");
        }

        $this->options['minWidth']  = (int) $minWidth;
        return $this;
    }

    /**
     * Returns the maximum allowed width
     *
     * @return integer
     */
    public function getMaxWidth()
    {
        return $this->options['maxWidth'];
    }

    /**
     * Sets the maximum allowed width
     *
     * @param  integer $maxWidth
     * @throws \Zend\Validator\Exception      When maxwidth is less than minwidth
     * @return \Zend\Validator\File\ImageSize Provides a fluid interface
     */
    public function setMaxWidth($maxWidth)
    {
        if (($this->getMinWidth() !== null) && ($maxWidth < $this->getMinWidth())) {
            throw new Exception\InvalidArgumentException("The maximum image width must be greater than or equal to the "
                . "minimum image width, but {$maxWidth} < {$this->getMinWidth()}");
        }

        $this->options['maxWidth']  = (int) $maxWidth;
        return $this;
    }

    /**
     * Returns the minimum allowed height
     *
     * @return integer
     */
    public function getMinHeight()
    {
        return $this->options['minHeight'];
    }

    /**
     * Sets the minimum allowed height
     *
     * @param  integer $minHeight
     * @throws \Zend\Validator\Exception      When minheight is greater than maxheight
     * @return \Zend\Validator\File\ImageSize Provides a fluid interface
     */
    public function setMinHeight($minHeight)
    {
        if (($this->getMaxHeight() !== null) && ($minHeight > $this->getMaxHeight())) {
            throw new Exception\InvalidArgumentException("The minimum image height must be less than or equal to the "
                . " maximum image height, but {$minHeight} > {$this->getMaxHeight()}");
        }

        $this->options['minHeight']  = (int) $minHeight;
        return $this;
    }

    /**
     * Returns the maximum allowed height
     *
     * @return integer
     */
    public function getMaxHeight()
    {
        return $this->options['maxHeight'];
    }

    /**
     * Sets the maximum allowed height
     *
     * @param  integer $maxHeight
     * @throws \Zend\Validator\Exception      When maxheight is less than minheight
     * @return \Zend\Validator\File\ImageSize Provides a fluid interface
     */
    public function setMaxHeight($maxHeight)
    {
        if (($this->getMinHeight() !== null) && ($maxHeight < $this->getMinHeight())) {
            throw new Exception\InvalidArgumentException("The maximum image height must be greater than or equal to the "
                . "minimum image height, but {$maxHeight} < {$this->getMinHeight()}");
        }

        $this->options['maxHeight']  = (int) $maxHeight;
        return $this;
    }

    /**
     * Returns the set minimum image sizes
     *
     * @return array
     */
    public function getImageMin()
    {
        return array('minWidth' => $this->getMinWidth(), 'minHeight' => $this->getMinHeight());
    }

    /**
     * Returns the set maximum image sizes
     *
     * @return array
     */
    public function getImageMax()
    {
        return array('maxWidth' => $this->getMaxWidth(), 'maxHeight' => $this->getMaxHeight());
    }

    /**
     * Returns the set image width sizes
     *
     * @return array
     */
    public function getImageWidth()
    {
        return array('minWidth' => $this->getMinWidth(), 'maxWidth' => $this->getMaxWidth());
    }

    /**
     * Returns the set image height sizes
     *
     * @return array
     */
    public function getImageHeight()
    {
        return array('minHeight' => $this->getMinHeight(), 'maxHeight' => $this->getMaxHeight());
    }

    /**
     * Sets the minimum image size
     *
     * @param  array $options                 The minimum image dimensions
     * @throws \Zend\Validator\Exception      When minwidth is greater than maxwidth
     * @throws \Zend\Validator\Exception      When minheight is greater than maxheight
     * @return \Zend\Validator\File\ImageSize Provides a fluent interface
     */
    public function setImageMin($options)
    {
        $this->setOptions($options);
//        if (isset($options['minwidth'])) {
//            if (($this->_maxwidth !== null) and ($options['minwidth'] > $this->_maxwidth)) {
//                throw new Exception\InvalidArgumentException("The minimum image width must be less than or equal to the "
//                    . " maximum image width, but {$options['minwidth']} > {$this->_maxwidth}");
//            }
//        }

//        if (isset($options['maxheight'])) {
//            if (($this->_maxheight !== null) and ($options['minheight'] > $this->_maxheight)) {
//                throw new Exception\InvalidArgumentException("The minimum image height must be less than or equal to the "
//                    . " maximum image height, but {$options['minheight']} > {$this->_maxheight}");
//            }
//        }

//        if (isset($options['minWidth'])) {
//            $this->_minwidth  = (int) $options['minwidth'];
//        }

//        if (isset($options['minheight'])) {
//            $this->_minheight = (int) $options['minheight'];
//        }

        return $this;
    }

    /**
     * Sets the maximum image size
     *
     * @param  array $options          The maximum image dimensions
     * @throws \Zend\Validator\Exception When maxwidth is smaller than minwidth
     * @throws \Zend\Validator\Exception When maxheight is smaller than minheight
     * @return \Zend\Validator\StringLength Provides a fluent interface
     */
    public function setImageMax($options)
    {
        $this->setOptions($options);
//        if (isset($options['maxwidth'])) {
//            if (($this->_minwidth !== null) and ($options['maxwidth'] < $this->_minwidth)) {
//                throw new Exception\InvalidArgumentException("The maximum image width must be greater than or equal to the "
//                    . "minimum image width, but {$options['maxwidth']} < {$this->_minwidth}");
//            }
//        }

//        if (isset($options['maxheight'])) {
//            if (($this->_minheight !== null) and ($options['maxheight'] < $this->_minheight)) {
//                throw new Exception\InvalidArgumentException("The maximum image height must be greater than or equal to the "
//                    . "minimum image height, but {$options['maxheight']} < {$this->_minwidth}");
//            }
//        }

//        if (isset($options['maxwidth'])) {
//            $this->_maxwidth  = (int) $options['maxwidth'];
//        }

//        if (isset($options['maxheight'])) {
//            $this->_maxheight = (int) $options['maxheight'];
//        }

        return $this;
    }

    /**
     * Sets the mimimum and maximum image width
     *
     * @param  array $options               The image width dimensions
     * @return \Zend\Validator\File\ImageSize Provides a fluent interface
     */
    public function setImageWidth($options)
    {
        $this->setImageMin($options);
        $this->setImageMax($options);

        return $this;
    }

    /**
     * Sets the mimimum and maximum image height
     *
     * @param  array $options               The image height dimensions
     * @return \Zend\Validator\File\ImageSize Provides a fluent interface
     */
    public function setImageHeight($options)
    {
        $this->setImageMin($options);
        $this->setImageMax($options);

        return $this;
    }

    /**
     * Returns true if and only if the imagesize of $value is at least min and
     * not bigger than max
     *
     * @param  string $value Real file to check for image size
     * @param  array  $file  File data from \Zend\File\Transfer\Transfer
     * @return boolean
     */
    public function isValid($value, $file = null)
    {
        if ($file === null) {
            $file = array('name' => basename($value));
        }

        // Is file readable ?
        if (!Loader::isReadable($value)) {
            return $this->_throw($file, self::NOT_READABLE);
        }

        $size = @getimagesize($value);
        $this->setValue($file);

        if (empty($size) or ($size[0] === 0) or ($size[1] === 0)) {
            return $this->_throw($file, self::NOT_DETECTED);
        }

        $this->_width  = $size[0];
        $this->_height = $size[1];
        if ($this->_width < $this->getMinWidth()) {
            $this->_throw($file, self::WIDTH_TOO_SMALL);
        }

        if (($this->getMaxWidth() !== null) and ($this->getMaxWidth() < $this->_width)) {
            $this->_throw($file, self::WIDTH_TOO_BIG);
        }

        if ($this->_height < $this->getMinHeight()) {
            $this->_throw($file, self::HEIGHT_TOO_SMALL);
        }

        if (($this->getMaxHeight() !== null) and ($this->getMaxHeight() < $this->_height)) {
            $this->_throw($file, self::HEIGHT_TOO_BIG);
        }

        if (count($this->getMessages()) > 0) {
            return false;
        }

        return true;
    }

    /**
     * Throws an error of the given type
     *
     * @param  string $file
     * @param  string $errorType
     * @return false
     */
    protected function _throw($file, $errorType)
    {
        if ($file !== null) {
            if (is_array($file)) {
                if(array_key_exists('name', $file)) {
                    $this->value = $file['name'];
                }
            } else if (is_string($file)) {
                $this->value = $file;
            }
        }

        $this->error($errorType);
        return false;
    }
}
