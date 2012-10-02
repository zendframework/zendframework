<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Validator
 */

namespace Zend\Validator\File;

/**
 * Validator which checks if the destination file does not exist
 *
 * @category  Zend
 * @package   Zend_Validator
 */
class NotExists extends Exists
{
    /**
     * @const string Error constants
     */
    const DOES_EXIST = 'fileNotExistsDoesExist';

    /**
     * @var array Error message templates
     */
    protected $messageTemplates = array(
        self::DOES_EXIST => "File '%value%' exists",
    );

    /**
     * Returns true if and only if the file does not exist in the set destinations
     *
     * @param  string|array $value Real file to check for existence
     * @return boolean
     */
    public function isValid($value)
    {
        $file     = (isset($value['tmp_name'])) ? $value['tmp_name'] : $value;
        $filename = (isset($value['name']))     ? $value['name']     : basename($file);
        $this->setValue($filename);

        $check = false;
        $directories = $this->getDirectory(true);
        foreach ($directories as $directory) {
            if (!isset($directory) || '' === $directory) {
                continue;
            }

            $check = true;
            if (file_exists($directory . DIRECTORY_SEPARATOR . $filename)) {
                $this->error(self::DOES_EXIST);
                return false;
            }
        }

        if (!$check) {
            $this->error(self::DOES_EXIST);
            return false;
        }

        return true;
    }
}
