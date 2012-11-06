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
use Zend\ProgressBar\Exception;
use Zend\Stdlib\ArrayUtils;

/**
 * Progress Bar Upload Handler for PHP 5.4+ Session Upload Progress handling
 *
 * @category  Zend
 * @package   Zend_ProgressBar
 */
class SessionProgress extends AbstractUploadHandler
{
    /**
     * @param  string $id
     * @return array|boolean
     * @throws Exception\PhpEnvironmentException
     */
    protected function getUploadProgress($id)
    {
        if (!$this->isSessionUploadProgressAvailable()) {
            throw new Exception\PhpEnvironmentException(
                'Session Upload Progress is not available'
            );
        }

        $uploadInfo = $_SESSION[ini_get('session.upload_progress.prefix') . $id];
        if (!is_array($uploadInfo)) {
            return false;
        }

        $status  = array(
            'total'    => 0,
            'current'  => 0,
            'rate'     => 0,
            'message'  => '',
            'done'     => false
        );
        $status = $uploadInfo + $status;
        $status['total']   = $status['content_length'];
        $status['current'] = $status['bytes_processed'];
        $status['rate']    = $status['bytes_processed'] / (time() - $status['start_time']);
        if (!empty($status['cancel_upload'])) {
            $status['done'] = true;
            $status['message'] = 'The upload has been canceled';
        }

        return $status;
    }

    /**
     * Checks if Session Upload Progress is available
     *
     * @return boolean
     */
    public function isSessionUploadProgressAvailable()
    {
        return (bool) version_compare(PHP_VERSION, '5.4.0rc1', '>=')
            && (bool) ini_get('session.upload_progress.enabled');
    }
}
