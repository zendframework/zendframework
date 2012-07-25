<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Console
 */

namespace Zend\Console\Adapter;

use Zend\Console\AdapterInterface;
use Zend\Console\ColorInterface;
use Zend\Console\CharsetInterface;
use Zend\Console\Exception\RuntimeException;
use Zend\Console;

/**
 * MS Windows with ANSICON console adapter
 *
 * This adapter requires ANSICON extension to be installed. It can be obtained from:
 *      https://github.com/adoxa/ansicon
 *
 * ANSICON has to be loaded and enabled before using this adapter. It's best to install
 * it using following command:
 *      ansicon -I
 *
 * Console should not run in UTF8 code page (65001), because ANSICON does not behave well with it.
 * It's best to use non-unicode code page 437, 850, 851, 852 or similar. Run "help mode" for more
 * information on how to change Windows console code page.
 *
 * @category   Zend
 * @package    Zend_Console
 * @subpackage Adapter
 */
class WindowsAnsicon extends Posix implements AdapterInterface
{
    protected static $hasMBString;

    protected $modeResult;

    /**
     * Determine and return current console width.
     *
     * @return int
     */
    public function getWidth()
    {
        static $width;
        if ($width > 0) {
            return $width;
        }

        /**
         * Try to read console size from ANSICON env var
         */
        if (preg_match('/\((\d+)x/',getenv('ANSICON'),$matches)) {
            $width = $matches[1];
        } else {
            $width = AbstractAdapter::getWidth();
        }

        return $width;
    }

    /**
     * Determine and return current console height.
     *
     * @return false|int
     */
    public function getHeight()
    {
        static $height;
        if ($height > 0) {
            return $height;
        }

        /**
         * Try to read console size from ANSICON env var
         */
        if (preg_match('/\(\d+x(\d+)/',getenv('ANSICON'),$matches)) {
            $height = $matches[1];
        } else {
            $height = AbstractAdapter::getHeight();
        }
        return $height;
    }

    protected function runModeCommand()
    {
        exec('mode',$output,$return);
        if ($return || !count($output)) {
            $this->modeResult = '';
        } else {
            $this->modeResult = trim(implode('',$output));
        }
    }

    /**
     * Check if console is UTF-8 compatible
     *
     * @return bool
     */
    public function isUtf8()
    {
        /**
         * Try to read code page info from "mode" command
         */
        if ($this->modeResult === null) {
            $this->runModeCommand();
        }

        if (preg_match('/Code page\:\s+(\d+)/', $this->modeResult,$matches)) {
            return (int)$matches[1] == 65001;
        }

        return false;
    }

    /**
     * Return current console window title.
     *
     * @return string
     */
    public function getTitle()
    {
        /**
         * Try to use powershell to retrieve console window title
         */
        exec('powershell -command "write $Host.UI.RawUI.WindowTitle"',$output,$result);
        if ($result || !$output) {
            return '';
        }

        return trim($output,"\r\n");
    }

    /**
     * Clear console screen
     */
    public function clear()
    {
        echo chr(27).'[1J'.chr(27).'[u';
    }

    /**
     * Clear line at cursor position
     */
    public function clearLine()
    {
        echo chr(27).'[1K';
    }


    /**
     * Set Console charset to use.
     *
     * @param \Zend\Console\CharsetInterface $charset
     */
    public function setCharset(CharsetInterface $charset)
    {
        $this->charset = $charset;
    }

    /**
     * Get charset currently in use by this adapter.
     *

     * @return \Zend\Console\CharsetInterface $charset
     */
    public function getCharset()
    {
        if ($this->charset === null) {
            $this->charset = $this->getDefaultCharset();
        }

        return $this->charset;
    }

    /**
     * @return \Zend\Console\Charset\AsciiExtended
     */
    public function getDefaultCharset()
    {
        return new Charset\AsciiExtended();
    }

    /**
     * Read a single character from the console input
     *
     * @param string|null   $mask   A list of allowed chars
     * @return string
     */
    public function readChar($mask = null)
    {
        /**
         * Decide if we can use `choice` tool
         */
        $useChoice = $mask !== null && preg_match('/^[a-zA-Z0-9]$', $mask);

        do {
            if ($useChoice) {
                system('choice /n /cs /c '.$mask,$return);
                if ($return == 255 || $return < 1 || $return > strlen($mask)) {
                    throw new RuntimeException('"choice" command failed to run. Are you using Windows XP or newer?');
                } else {
                    /**
                     * Fetch the char from mask
                     */
                    $char = substr($mask,$return-1,1);
                }
            } else {
                $char = parent::readChar($mask);
            }
        } while (!$char || ($mask !== null && !stristr($mask, $char)));

        return $char;
    }

}
