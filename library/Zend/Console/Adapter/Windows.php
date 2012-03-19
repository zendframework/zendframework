<?php
namespace Zend\Console\Adapter;

use Zend\Console\Adapter,
    Zend\Console\Color,
    Zend\Console\Charset,
    Zend\Console

;

class Windows extends Virtual implements Adapter
{
    protected static $hasMBString;

    protected $probeResult;

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
         * Try to read console size from "mode" command
         */
        if($this->probeResult === null){
            $this->runProbeCommand();
        }

        if(preg_match('/^(\d+)\t/s',$this->probeResult,$matches)){
            $width = $matches[1];
        }else{
            $width = parent::getWidth();
        }

        return $width;
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
         * Try to read console size from "mode" command
         */
        if($this->probeResult === null){
            $this->runProbeCommand();
        }

        if(preg_match('/^\d+\t(\d+)/s',$this->probeResult,$matches)){
            $height = $matches[1];
        }else{
            $height = parent::getheight();
        }

        return $height;
    }

    protected function runProbeCommand(){
        exec(
            'powershell -command "$size = $Host.ui.rawui.windowsize; write ""$($size.width)`t$($size.height)"""',
            $output,
            $return
        );
        if($return || !$output){
            $this->probeResult = '';
        }else{
            $this->probeResult = trim($output);
        }
    }

    /**
     * Check if console is UTF-8 compatible
     *
     * @return bool
     */
    public function isUtf8(){
        /**
         * Try to read code page info from "mode" command
         */
        if($this->probeResult === null){
            $this->runProbeCommand();
        }

        if(preg_match('/Code page\:\s+(\d+)/',$this->probeResult,$matches)){
            return (int)$matches[1] == 65001;
        }else{
            return false;
        }
    }

    /**
     * Set cursor position
     * @param int   $x
     * @param int   $y
     */
    public function setPos($x, $y){

    }

    /**
     * Return current console window title.
     *
     * @return string
     */
    public function getTitle(){
        /**
         * Try to use powershell to retrieve console window title
         */
        exec('powershell -command "write $Host.UI.RawUI.WindowTitle"',$output,$result);
        if($result || !$output){
            return '';
        }else{
            return trim($output,"\r\n");
        }
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
        return new Charset\AsciiExtended;
    }

    protected function switchToUtf8(){
        `mode con cp select=65001`;
    }

    /**
     * Clear console screen
     */
    public function clear()
    {
        echo str_repeat("\r\n",$this->getHeight());
    }

    /**
     * Clear line at cursor position
     */
    public function clearLine()
    {
        echo "\r".str_repeat(' ',$this->getWidth())."\r";
    }


    /**
     * Read a single character from the console input
     *
     * @param string|null   $mask   A list of allowed chars
     * @return string
     */
    public function readChar($mask = null){
        /**
         * Decide if we can use `choice` tool
         */
        $useChoice = $mask !== null && preg_match('/^[a-zA-Z0-9]*$/',$mask);

        do{
            if($useChoice){
                /**
                 * Use the `choice` tool available since windows 2000
                 */
                system('choice /n /cs /c '.escapeshellarg($mask).' >NUL',$return);
                if($return == 255 || $return < 1 || $return > strlen($mask)){
                    throw new \RuntimeException('"choice" command failed to run. Are you using Windows XP or newer?');
                }else{
                    /**
                     * Fetch the char from mask
                     */
                    $char = substr($mask,$return-1,1);
                }
            }else{
                /**
                 * Use a fallback method
                 */
                $char = $this->readLine(1);
                if(!$char){
                    $char = "\n"; // user pressed [enter]
                }
            }
        }while(
            ($mask !== null && !stristr($mask,$char))
        );
        return $char;
    }

    /**
     * Read a single line from the console input.
     *
     * @param int $maxLength        Maximum response length
     * @return string
     */
    public function readLine($maxLength = 2048){
        $f = fopen('php://stdin','r');
        $line = trim(fread($f,$maxLength));
        fclose($f);
        return $line;
    }


}