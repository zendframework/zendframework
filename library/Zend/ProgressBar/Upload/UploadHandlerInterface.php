<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_ProgressBar
 */

namespace Zend\ProgressBar\Upload;

use Traversable;
use Zend\Stdlib\ArrayUtils;

/**
 * Interface for Upload Progress Handlers
 *
 * @category  Zend
 * @package   Zend_ProgressBar
 */
interface UploadHandlerInterface
{
    /**
     * @param  string $id
     * @return array
     */
    public function getProgress($id);
}
