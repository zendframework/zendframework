<?php
namespace Zend\Console\Charset;

/**
 * UTF-8 box drawing (modified to use heavy single lines)
 *
 * @link http://en.wikipedia.org/wiki/Box-drawing_characters
 */
class Utf8Heavy extends Utf8 {

    const LINE_SINGLE_EW = "━";
    const LINE_SINGLE_NS = "┃";
    const LINE_SINGLE_NW = "┏";
    const LINE_SINGLE_NE = "┓";
    const LINE_SINGLE_SE = "┛";
    const LINE_SINGLE_SW = "┗";
    const LINE_SINGLE_CROSS = "╋";

}