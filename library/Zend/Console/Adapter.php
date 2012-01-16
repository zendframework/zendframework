<?php
namespace Zend\Console;

interface Adapter
{
    const LINE_NONE = 1;
    const LINE_SINGLE = 2;
    const LINE_DOUBLE = 3;
    const LINE_BLOCK = 4;
    const FILL_NONE = 0;
    const FILL_SHADE_LIGHT = 1;
    const FILL_SHADE_MEDIUM = 2;
    const FILL_SHADE_DARK = 3;
    const FILL_BLOCK = 10;

    /**
     * Write a chunk of text to console.
     *
     * @param string                   $text
     * @param null|int $color
     * @param null|int $bgColor
     */
    public function write($text, $color = null, $bgColor = null);

    /**
     * Alias for write()
     *
     * @param string                   $text
     * @param null|int $color
     * @param null|int $bgColor
     */
    public function writeText($text, $color = null, $bgColor = null);

    /**
     * Write a single line of text to console and advance cursor to the next line.
     * If the text is longer than console width it will be truncated.
     *
     * @abstract
     * @param string                   $text
     * @param null|int $color
     * @param null|int $bgColor
     */
    public function writeLine($text, $color = null, $bgColor = null);

    /**
     * Write a piece of text at the coordinates of $x and $y
     *
     * @abstract
     * @param string                   $text     Text to write
     * @param int                      $x        Console X coordinate (column)
     * @param int                      $y        Console Y coordinate (row)
     * @param null|int $color
     * @param null|int $bgColor
     */
    public function writeAt($text, $x, $y, $color = null, $bgColor = null);

    /**
     * Write a box at the specified coordinates.
     * If X or Y coordinate value is negative, it will be calculated as the distance from far right or bottom edge
     * of the console (respectively).
     *
     * @abstract
     * @param int                      $x1           Top-left corner X coordinate (column)
     * @param int                      $y1           Top-left corner Y coordinate (row)
     * @param int                      $x2           Bottom-right corner X coordinate (column)
     * @param int                      $y2           Bottom-right corner Y coordinate (row)
     * @param int                      $lineStyle    (optional) Box border style.
     * @param int                      $fillStyle    (optional) Box fill style or a single character to fill it with.
     * @param int      $color        (optional) Foreground color
     * @param int      $bgColor      (optional) Background color
     * @param null|int $fillColor    (optional) Foreground color of box fill
     * @param null|int $fillBgColor  (optional) Background color of box fill
     */
    public function writeBox(
        $x1, $y1, $x2, $y2,
        $lineStyle = self::LINE_SINGLE, $fillStyle = self::FILL_NONE,
        $color = null, $bgColor = null, $fillColor = null, $fillBgColor = null
    );

    /**
     * Write a block of text at the given coordinates, matching the supplied width and height.
     * In case a line of text does not fit desired width, it will be wrapped to the next line.
     * In case the whole text does not fit in desired height, it will be truncated.
     *
     * @abstract
     * @param string                   $text     Text to write
     * @param int                      $width    Maximum block width. Negative value means distance from right edge.
     * @param int|null                 $height   Maximum block height. Negative value means distance from bottom edge.
     * @param int                      $x        Block X coordinate (column)
     * @param int                      $y        Block Y coordinate (row)
     * @param null|int                 $color    (optional) Text color
     * @param null|int $bgColor  (optional) Text background color
     */
    public function writeTextBlock(
        $text,
        $width, $height = null, $x = 0, $y = 0,
        $color = null, $bgColor = null
    );


    /**
     * Determine and return current console width.
     *
     * @abstract
     * @return int
     */
    public function getWidth();

    /**
     * Determine and return current console height.
     *
     * @abstract
     * @return int
     */
    public function getHeight();

    /**
     * Determine and return current console width and height.
     *
     * @abstract
     * @return array        array($width, $height)
     */
    public function getSize();

    /**
     * Check if console is UTF-8 compatible
     *
     * @abstract
     * @return bool
     */
    public function isUtf8();


//    /**
//     * Return current cursor position - array($x, $y)
//     *
//     * @abstract
//     * @return array        array($x, $y);
//     */
//    public function getPos();
//
//    /**
//     * Return current cursor X coordinate (column)
//     *
//     * @abstract
//     * @return  false|int       Integer or false if failed to determine.
//     */
//    public function getX();
//
//    /**
//     * Return current cursor Y coordinate (row)
//     *
//     * @abstract
//     * @return  false|int       Integer or false if failed to determine.
//     */
//    public function getY();

    /**
     * Set cursor position
     *
     * @abstract
     * @param int   $x
     * @param int   $y
     */
    public function setPos($x, $y);

    /**
     * Hide console cursor
     *
     * @abstract
     */
    public function hideCursor();

    /**
     * Show console cursor
     *
     * @abstract
     */
    public function showCursor();

    /**
     * Return current console window title.
     *
     * @abstract
     * @return string
     */
    public function getTitle();

    /**
     * Set console window title
     *
     * @abstract
     * @param $title
     */
    public function setTitle($title);

    /**
     * Reset console window title to previous value.
     *
     * @abstract
     */
    public function resetTitle();


    /**
     * Prepare a string that will be rendered in color.
     *
     * @abstract
     * @param string                     $string
     * @param null|int   $color    Foreground color
     * @param null|int   $bgColor  Background color
     */
    public function colorize($string, $color = null, $bgColor = null);

    /**
     * Change current drawing color.
     *
     * @abstract
     * @param int $color
     */
    public function setColor($color);

    /**
     * Change current drawing background color
     *
     * @abstract
     * @param int $color
     */
    public function setBgColor($color);

    /**
     * Reset color to console default.
     *
     * @abstract
     */
    public function resetColor();


    /**
     * Set Console charset to use.
     *
     * @abstract
     * @param \Zend\Console\Charset $charset
     */
    public function setCharset(Charset $charset);

    /**
     * Get charset currently in use by this adapter.
     *
     * @abstract
     * @return \Zend\Console\Charset $charset
     */
    public function getCharset();

    /**
     * @abstract
     * @return \Zend\Console\Charset
     */
    public function getDefaultCharset();

    /**
     * Clear console screen
     *
     * @abstract
     */
    public function clear();

    /**
     * Clear line at cursor position
     *
     * @abstract
     */
    public function clearLine();

    /**
     * Clear console screen
     *
     * @abstract
     */
    public function clearScreen();
}