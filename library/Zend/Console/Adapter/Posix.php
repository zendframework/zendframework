<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Console
 */

namespace Zend\Console\Adapter;

use Zend\Console\Charset;
use Zend\Console\Exception;
use Zend\Console\ColorInterface as Color;

/**
 * @todo Add GNU readline support
 * @category   Zend
 * @package    Zend_Console
 * @subpackage Adapter
 * @link http://en.wikipedia.org/wiki/ANSI_escape_code
 */
class Posix extends AbstractAdapter
{
    /**
     * Whether or not mbstring is enabled
     *
     * @var null|bool
     */
    protected static $hasMBString;

    /**
     * @var Charset\CharsetInterface
     */
    protected $charset;

    /**
     * Map of colors to ANSI codes
     *
     * @todo implement Xterm 256 colors (http://www.frexx.de/xterm-256-notes/)
     * @var array
     */
    protected static $ansiColorMap = array(
        'fg' => array(
            Color::NORMAL        => '22;39',
            Color::RESET         => '22;39',

            Color::BLACK         => '0;30',
            Color::RED           => '0;31',
            Color::GREEN         => '0;32',
            Color::YELLOW        => '0;33',
            Color::BLUE          => '0;34',
            Color::MAGENTA       => '0;35',
            Color::CYAN          => '0;36',
            Color::WHITE         => '0;37',

            Color::GRAY          => '1;30',
            Color::LIGHT_RED     => '1;31',
            Color::LIGHT_GREEN   => '1;32',
            Color::LIGHT_YELLOW  => '1;33',
            Color::LIGHT_BLUE    => '1;34',
            Color::LIGHT_MAGENTA => '1;35',
            Color::LIGHT_CYAN    => '1;36',
            Color::LIGHT_WHITE   => '1;37',
        ),
        'bg' => array(
            Color::NORMAL        => '0;49',
            Color::RESET         => '0;49',

            Color::BLACK         => '40',
            Color::RED           => '41',
            Color::GREEN         => '42',
            Color::YELLOW        => '43',
            Color::BLUE          => '44',
            Color::MAGENTA       => '45',
            Color::CYAN          => '46',
            Color::WHITE         => '47',

            Color::GRAY          => '40',
            Color::LIGHT_RED     => '41',
            Color::LIGHT_GREEN   => '42',
            Color::LIGHT_YELLOW  => '43',
            Color::LIGHT_BLUE    => '44',
            Color::LIGHT_MAGENTA => '45',
            Color::LIGHT_CYAN    => '46',
            Color::LIGHT_WHITE   => '47',
        ),
    );

    /**
     * Last fetched TTY mode
     *
     * @var string|null
     */
    protected $lastTTYMode = null;

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
         * Try to read env variable
         */
        if (($result = getenv('COLUMNS')) !== false) {
            return $width = (int) $result;
        }

        /**
         * Try to read console size from "tput" command
         */
        $result = exec('tput cols', $output, $return);
        if (!$return && is_numeric($result)) {
            return $width = (int) $result;
        }

        return $width = parent::getWidth();
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

        // Try to read env variable
        if (($result = getenv('LINES')) !== false) {
            return $height = (int) $result;
        }

        // Try to read console size from "tput" command
        $result = exec('tput lines', $output, $return);
        if (!$return && is_numeric($result)) {
            return $height = (int) $result;
        }

        return $height = parent::getHeight();
    }

    /**
     * Run a mode command and store results
     *
     * @return void
     */
    protected function runModeCommand()
    {
        exec('mode', $output, $return);
        if ($return || !count($output)) {
            $this->modeResult = '';
        } else {
            $this->modeResult = trim(implode('', $output));
        }
    }

    /**
     * Check if console is UTF-8 compatible
     *
     * @return bool
     */
    public function isUtf8()
    {
        // Try to retrieve it from LANG env variable
        if (($lang = getenv('LANG')) !== false) {
            return stristr($lang, 'utf-8') || stristr($lang, 'utf8');
        }

        return false;
    }

    /**
     * Show console cursor
     */
    public function showCursor()
    {
        echo "\x1b[?25h";
    }

    /**
     * Hide console cursor
     */
    public function hideCursor()
    {
        echo "\x1b[?25l";
    }

    /**
     * Set cursor position
     * @param int $x
     * @param int $y
     */
    public function setPos($x, $y)
    {
        echo "\x1b[" . $y . ';' . $x . 'f';
    }

    /**
     * Prepare a string that will be rendered in color.
     *
     * @param  string   $string
     * @param  int      $color
     * @param  null|int $bgColor
     * @throws Exception\BadMethodCallException
     * @return string
     */
    public function colorize($string, $color = null, $bgColor = null)
    {
        // Retrieve ansi color codes
        if ($color !== null) {
            if (!isset(static::$ansiColorMap['fg'][$color])) {
                throw new Exception\BadMethodCallException(sprintf(
                    'Unknown color "%s". Please use one of the Zend\Console\ColorInterface constants',
                    $color
                ));
            }
            $color = static::$ansiColorMap['fg'][$color];
        }

        if ($bgColor !== null) {
            if (!isset(static::$ansiColorMap['bg'][$bgColor])) {
                throw new Exception\BadMethodCallException(sprintf(
                    'Unknown color "%s". Please use one of the Zend\Console\ColorInterface constants',
                    $bgColor
                ));
            }
            $bgColor = static::$ansiColorMap['bg'][$bgColor];
        }

        return ($color   !== null ? "\x1b[" . $color   . 'm' : '')
            . ($bgColor !== null ? "\x1b[" . $bgColor . 'm' : '')
            . $string
            . "\x1b[22;39m\x1b[0;49m";
    }

    /**
     * Change current drawing color.
     *
     * @param int $color
     * @throws Exception\BadMethodCallException
     */
    public function setColor($color)
    {
        // Retrieve ansi color code
        if ($color !== null) {
            if (!isset(static::$ansiColorMap['fg'][$color])) {
                throw new Exception\BadMethodCallException(sprintf(
                    'Unknown color "%s". Please use one of the Zend\Console\ColorInterface constants',
                    $color
                ));
            }
            $color = static::$ansiColorMap['fg'][$color];
        }

        echo "\x1b[" . $color . 'm';
    }

    /**
     * Change current drawing background color
     *
     * @param int $bgColor
     * @throws Exception\BadMethodCallException
     */
    public function setBgColor($bgColor)
    {
        // Retrieve ansi color code
        if ($bgColor !== null) {
            if (!isset(static::$ansiColorMap['bg'][$bgColor])) {
                throw new Exception\BadMethodCallException(sprintf(
                    'Unknown color "%s". Please use one of the Zend\Console\ColorInterface constants',
                    $bgColor
                ));
            }

            $bgColor = static::$ansiColorMap['bg'][$bgColor];
        }

        echo "\x1b[" . ($bgColor) . 'm';
    }

    /**
     * Reset color to console default.
     */
    public function resetColor()
    {
        echo "\x1b[0;49m";  // reset bg color
        echo "\x1b[22;39m"; // reset fg bold, bright and faint
        echo "\x1b[25;39m"; // reset fg blink
        echo "\x1b[24;39m"; // reset fg underline
    }

    /**
     * Return current console window title.
     *
     * @return string
     */
    public function getTitle()
    {
    }

    /**
     * Set Console charset to use.
     *
     * @param Charset\CharsetInterface $charset
     */
    public function setCharset(Charset\CharsetInterface $charset)
    {
        $this->charset = $charset;
    }

    /**
     * Get charset currently in use by this adapter.
     *
     * @return Charset\CharsetInterface $charset
     */
    public function getCharset()
    {
        if ($this->charset === null) {
            $this->charset = $this->getDefaultCharset();
        }

        return $this->charset;
    }

    /**
     * @return Charset\CharsetInterface
     */
    public function getDefaultCharset()
    {
        if ($this->isUtf8()) {
            return new Charset\Utf8;
        }
        return new Charset\DECSG();
    }

    /**
     * Read a single character from the console input
     *
     * @param  string|null $mask   A list of allowed chars
     * @return string
     */
    public function readChar($mask = null)
    {
        $this->setTTYMode('-icanon -echo');

        $stream = fopen('php://stdin', 'rb');
        do {
            $char = fgetc($stream);
        } while (strlen($char) !== 1 || ($mask !== null && stristr($mask, $char) === false));
        fclose($stream);

        $this->restoreTTYMode();
        return $char;
    }

    /**
     * Reset color to console default.
     */
    public function clear()
    {
        echo "\x1b[2J";      // reset bg color
        $this->setPos(1, 1); // reset cursor position
    }

    /**
     * Restore TTY (Console) mode to previous value.
     *
     * @return void
     */
    protected function restoreTTYMode()
    {
        if ($this->lastTTYMode === null) {
            return;
        }

        shell_exec('stty ' . escapeshellarg($this->lastTTYMode));
    }

    /**
     * Change TTY (Console) mode
     *
     * @link  http://en.wikipedia.org/wiki/Stty
     * @param $mode
     */
    protected function setTTYMode($mode)
    {
        // Store last mode
        $this->lastTTYMode = trim(`stty -g`);

        // Set new mode
        shell_exec('stty '.escapeshellcmd($mode));
    }
}
