<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Filter\File;

use Zend\Filter\File\RenameUpload;

class RenameUploadMock extends RenameUpload
{
    /**
     * @param  string $sourceFile Source file path
     * @param  string $targetFile Target file path
     * @return boolean
     */
    protected function moveUploadedFile($sourceFile, $targetFile)
    {
        return rename($sourceFile, $targetFile);
    }
}
