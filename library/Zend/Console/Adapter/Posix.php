<?php
namespace Zend\Console\Adapter;

use Zend\Console\Adapter,
    Zend\Console\Color,
    Zend\Console\Charset,
    Zend\Console
;

/**
 * @link http://en.wikipedia.org/wiki/ANSI_escape_code
 */
class Posix extends AbstractAdapter implements Adapter
{
    protected static $hasMBString;

    /**
     * @var \Zend\Console\Charset
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
            Color::NORMAL   => '22;39m',
            Color::RESET    => '22;39m',

            Color::BLACK    => '0;30',
            Color::RED      => '0;31',
            Color::GREEN    => '0;32',
            Color::YELLOW   => '0;33',
            Color::BLUE     => '0;34',
            Color::MAGENTA  => '0;35',
            Color::CYAN     => '0;36',
            Color::WHITE    => '0;37',
            
            Color::GRAY           => '1;30',
            Color::LIGHT_RED      => '1;31',
            Color::LIGHT_GREEN    => '1;32',
            Color::LIGHT_YELLOW   => '1;33',
            Color::LIGHT_BLUE     => '1;34',
            Color::LIGHT_MAGENTA  => '1;35',
            Color::LIGHT_CYAN     => '1;36',
            Color::LIGHT_WHITE    => '1;37',
        ),
        'bg' => array(
            Color::NORMAL   => '0;49m',
            Color::RESET    => '0;49m',

            Color::BLACK    => '40',
            Color::RED      => '41',
            Color::GREEN    => '42',
            Color::YELLOW   => '43',
            Color::BLUE     => '44',
            Color::MAGENTA  => '45',
            Color::CYAN     => '46',
            Color::WHITE    => '47',

            Color::GRAY           => '40',
            Color::LIGHT_RED      => '41',
            Color::LIGHT_GREEN    => '42',
            Color::LIGHT_YELLOW   => '43',
            Color::LIGHT_BLUE     => '44',
            Color::LIGHT_MAGENTA  => '45',
            Color::LIGHT_CYAN     => '46',
            Color::LIGHT_WHITE    => '47',
        ),
    );

    /**
     * Determine and return current console width.
     *
     * @return int
     */
    public function getWidth(){
        static $width;
        if($width > 0){
            return $width;
        }

        /**
         * Try to read env variable
         */
        if(($result = getenv('COLUMNS')) !== false){
            return $width = (int)$result;
        }

        /**
         * Try to read console size from "tput" command
         */
        $result = exec('tput cols',$output, $return);
        if(!$return && is_numeric($result)){
            return $width = (int)$result;
        }else{
            return $width = parent::getWidth();
        }

    }

    /**
     * Determine and return current console height.
     *
     * @return false|int
     */
    public function getHeight(){
        static $height;
        if($height > 0){
            return $height;
        }

        /**
         * Try to read env variable
         */
        if(($result = getenv('LINES')) !== false){
            return $height = (int)$result;
        }

        /**
                 * Try to read console size from "tput" command
                 */
        $result = exec('tput lines',$output, $return);
        if(!$return && is_numeric($result)){
            return $height = (int)$result;
        }else{
            return $height = parent::getHeight();
        }
    }

    protected function runModeCommand(){
        exec('mode',$output,$return);
        if($return || !count($output)){
            $this->modeResult = '';
        }else{
            $this->modeResult = trim(implode('',$output));
        }
    }

    /**
     * Check if console is UTF-8 compatible
     *
     * @return bool
     */
    public function isUtf8(){
        /**
         * Try to retrieve it from LANG env variable
         */
        if(($lang = getenv('LANG')) !== false){
            return stristr($lang,'utf-8') || stristr($lang,'utf8');
        }

        return false;
    }


    /**
     * Show console cursor
     */
    public function showCursor(){
        echo chr(27).'[?25h';
    }

    /**
     * Hide console cursor
     */
    public function hideCursor(){
        echo chr(27).'[?25l';
    }

    /**
     * Set cursor position
     * @param int   $x
     * @param int   $y
     */
    public function setPos($x, $y){
        echo chr(27).'['.$y.';'.$x.'f';
    }

    /**
     * Prepare a string that will be rendered in color.
     *
     * @param string                     $string
     * @param int                        $color
     * @param null|int                   $bgColor
     * @return string
     */
    public function colorize($string, $color = null, $bgColor = null)
    {
        /**
         * Retrieve ansi color codes
         */
        if($color !== null){
            if(!isset(static::$ansiColorMap['fg'][$color])){
                throw new \BadMethodCallException(
                    'Unknown color "'.$color.'". Please use one of Zend\Console\Color constants.'
                );
            }else{
                $color = static::$ansiColorMap['fg'][$color];
            }
        }
        if($bgColor !== null){
            if(!isset(static::$ansiColorMap['bg'][$bgColor])){
                throw new \BadMethodCallException(
                    'Unknown color "'.$bgColor.'". Please use one of Zend\Console\Color constants.'
                );
            }else{
                $bgColor = static::$ansiColorMap['bg'][$bgColor];
            }
        }

        return
            ($color !== null ? chr(27).'['.$color.'m' : '').
            ($bgColor !== null ? chr(27).'['.$bgColor.'m' : '').
            $string.
            chr(27).'[22;39m'.chr(27).'[0;49m'
        ;
    }

    /**
     * Change current drawing color.
     *
     * @param int $color
     */
    public function setColor($color)
    {
        /**
         * Retrieve ansi color code
         */
        if($color !== null){
            if(!isset(static::$ansiColorMap['fg'][$color])){
                throw new \BadMethodCallException(
                    'Unknown color "'.$color.'". Please use one of Zend\Console\Color constants.'
                );
            }else{
                $color = static::$ansiColorMap['fg'][$color];
            }
        }

        echo chr(27).'['.$color.'m';
    }

    /**
     * Change current drawing background color
     *
     * @param int $bgColor
     */
    public function setBgColor($bgColor)
    {
        /**
         * Retrieve ansi color code
         */
        if($bgColor !== null){
            if(!isset(static::$ansiColorMap['bg'][$bgColor])){
                throw new \BadMethodCallException(
                    'Unknown color "'.$bgColor.'". Please use one of Zend\Console\Color constants.'
                );
            }else{
                $bgColor = static::$ansiColorMap['bg'][$bgColor];
            }
        }
        echo chr(27).'['.($bgColor).'m';
    }

    /**
     * Reset color to console default.
     */
    public function resetColor()
    {
        echo chr(27).'[0;49m'; // reset bg color
        echo chr(27).'[22;39m'; // reset fg bold, bright and faint
        echo chr(27).'[25;39m'; // reset fg blink
        echo chr(27).'[24;39m'; // reset fg underline
    }

    /**
     * Return current console window title.
     *
     * @return string
     */
    public function getTitle(){

    }

    /**
     * Set Console charset to use.
     *

     * @param \Zend\Console\Charset $charset
     */
    public function setCharset(Charset $charset){
        $this->charset = $charset;
    }

    /**
     * Get charset currently in use by this adapter.
     *
     * @return \Zend\Console\Charset $charset
     */
    public function getCharset(){
        if($this->charset === null){
            $this->charset = $this->getDefaultCharset();
        }

        return $this->charset;
    }

    /**
     * @return \Zend\Console\Charset
     */
    public function getDefaultCharset(){
        if($this->isUtf8()){
            return new Charset\Utf8;
        }else{
            return new Charset\DECSG();
        }
    }

    /**
     * Read a single character from the console input
     *
     * @param string|null   $mask   A list of allowed chars
     * @return string
     */
    public function readChar($mask = null){
        do{
            $char = trim(`read -s -n1 val; echo \$val`);
        }while(
            !$char ||
            ($mask !== null && !stristr($mask,$char))
        );
        return $char;
    }

    /**
     * Reset color to console default.
     */
    public function clear(){
        echo chr(27).'[2J'; // reset bg color
        $this->setPos(1,1); // reset cursor position
    }

    /**
     * @todo Add GNU readline support
     */

}