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
 * @package   Zend_Config
 * @copyright Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Config;

/**
 * @category  Zend
 * @package   Zend_Config
 * @copyright Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd     New BSD License
 */
class Factory
{
    /**
     * Reader instances used for files.
     *
     * @var array
     */
    protected $readers = array();

    /**
     * Read a config from a file.
     *
     * @param string $filename
     * @return array
     */
    public function fromFile($filename)
    {
        $pathinfo = pathinfo($filename);

        switch (strtolower($pathinfo['extension'])) {
            case 'php':
                return include $filename;
                break;

            case 'ini':
                if (!isset($this->readers['ini'])) {
                    $this->readers['ini'] = new Reader\Ini();
                }

                return $this->readers['ini']->readFile($filename);
                break;

            case 'xml':
                if (!isset($this->readers['xml'])) {
                    $this->readers['xml'] = new Reader\Ini();
                }

                return $this->readers['xml']->readFile($filename);
                break;
        }

        return null;
    }

    /**
     * Read configuration from multiple files and merge them.
     *
     * @param  array $files
     * @return array
     */
    public function fromFiles(array $files)
    {
        $config = array();

        foreach ($files as $file) {
            $config = array_replace_recursive($config, self::fromFile($file));
        }

        return $config;
    }
}
