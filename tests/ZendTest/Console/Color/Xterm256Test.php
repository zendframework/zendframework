<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Console
 */

namespace ZendTest\Console\Color;

use ReflectionClass;
use Zend\Console\Color\Xterm256;

/**
 * @category   Zend
 * @package    Zend_Console
 * @subpackage UnitTests
 * @group      Zend_Console
 */
class Xterm256Test extends \PHPUnit_Framework_TestCase
{
    public function invalidHexCodes()
    {
        return array(
            'too-long'                       => array('FFFFFF0'),
            'too-long-and-char-out-of-range' => array('ABCDEFG'),
            'too-long-digits'                => array('01048212'),
            'too-long-and-invalid-enc'       => array('ééààööüü'),
            'char-out-of-range'              => array('FF00GG'),
            'null'                           => array(null),
        );
    }

    /**
     * @dataProvider invalidHexCodes
     */
    public function testWrongHexCodeInputs($hex)
    {
        $color = Xterm256::calculate($hex);
        $r     = new ReflectionClass($color);
        $code  = $r->getStaticPropertyValue('color');
        $this->assertNull($code);
    }

    public function approximateHexCodes()
    {
        return array(
            'sixteen'         => array('000100', 16),
            'one-ninety-nine' => array('FF33A0', 199),
        );
    }

    /**
     * @dataProvider approximateHexCodes
     */
    public function testApproximateHexCodeInputs($hex, $gcode)
    {
        $color = Xterm256::calculate($hex);
        $r     = new ReflectionClass($color);
        $gcode = sprintf('%%s;5;%s', $gcode);
        $this->assertEquals($gcode, $r->getStaticPropertyValue('color'));
    }

    public function exactHexCodes()
    {
        return array(
            '000000' => array('000000', 16),
            '00005F' => array('00005F', 17),
            '000087' => array('000087', 18),
            '0000AF' => array('0000AF', 19),
            '0000D7' => array('0000D7', 20),
            '0000FF' => array('0000FF', 21),
            '005F00' => array('005F00', 22),
            '005F5F' => array('005F5F', 23),
            '005F87' => array('005F87', 24),
            '005FAF' => array('005FAF', 25),
            '005FD7' => array('005FD7', 26),
            '005FFF' => array('005FFF', 27),
            '008700' => array('008700', 28),
            '00875F' => array('00875F', 29),
            '008787' => array('008787', 30),
            '0087AF' => array('0087AF', 31),
            '0087D7' => array('0087D7', 32),
            '0087FF' => array('0087FF', 33),
            '00AF00' => array('00AF00', 34),
            '00AF5F' => array('00AF5F', 35),
            '00AF87' => array('00AF87', 36),
            '00AFAF' => array('00AFAF', 37),
            '00AFD7' => array('00AFD7', 38),
            '00AFFF' => array('00AFFF', 39),
            '00D700' => array('00D700', 40),
            '00D75F' => array('00D75F', 41),
            '00D787' => array('00D787', 42),
            '00D7AF' => array('00D7AF', 43),
            '00D7D7' => array('00D7D7', 44),
            '00D7FF' => array('00D7FF', 45),
            '00FF00' => array('00FF00', 46),
            '00FF5F' => array('00FF5F', 47),
            '00FF87' => array('00FF87', 48),
            '00FFAF' => array('00FFAF', 49),
            '00FFD7' => array('00FFD7', 50),
            '00FFFF' => array('00FFFF', 51),
            '5F0000' => array('5F0000', 52),
            '5F005F' => array('5F005F', 53),
            '5F0087' => array('5F0087', 54),
            '5F00AF' => array('5F00AF', 55),
            '5F00D7' => array('5F00D7', 56),
            '5F00FF' => array('5F00FF', 57),
            '5F5F00' => array('5F5F00', 58),
            '5F5F5F' => array('5F5F5F', 59),
            '5F5F87' => array('5F5F87', 60),
            '5F5FAF' => array('5F5FAF', 61),
            '5F5FD7' => array('5F5FD7', 62),
            '5F5FFF' => array('5F5FFF', 63),
            '5F8700' => array('5F8700', 64),
            '5F875F' => array('5F875F', 65),
            '5F8787' => array('5F8787', 66),
            '5F87AF' => array('5F87AF', 67),
            '5F87D7' => array('5F87D7', 68),
            '5F87FF' => array('5F87FF', 69),
            '5FAF00' => array('5FAF00', 70),
            '5FAF5F' => array('5FAF5F', 71),
            '5FAF87' => array('5FAF87', 72),
            '5FAFAF' => array('5FAFAF', 73),
            '5FAFD7' => array('5FAFD7', 74),
            '5FAFFF' => array('5FAFFF', 75),
            '5FD700' => array('5FD700', 76),
            '5FD75F' => array('5FD75F', 77),
            '5FD787' => array('5FD787', 78),
            '5FD7AF' => array('5FD7AF', 79),
            '5FD7D7' => array('5FD7D7', 80),
            '5FD7FF' => array('5FD7FF', 81),
            '5FFF00' => array('5FFF00', 82),
            '5FFF5F' => array('5FFF5F', 83),
            '5FFF87' => array('5FFF87', 84),
            '5FFFAF' => array('5FFFAF', 85),
            '5FFFD7' => array('5FFFD7', 86),
            '5FFFFF' => array('5FFFFF', 87),
            '870000' => array('870000', 88),
            '87005F' => array('87005F', 89),
            '870087' => array('870087', 90),
            '8700AF' => array('8700AF', 91),
            '8700D7' => array('8700D7', 92),
            '8700FF' => array('8700FF', 93),
            '875F00' => array('875F00', 94),
            '875F5F' => array('875F5F', 95),
            '875F87' => array('875F87', 96),
            '875FAF' => array('875FAF', 97),
            '875FD7' => array('875FD7', 98),
            '875FFF' => array('875FFF', 99),
            '878700' => array('878700', 100),
            '87875F' => array('87875F', 101),
            '878787' => array('878787', 102),
            '8787AF' => array('8787AF', 103),
            '8787D7' => array('8787D7', 104),
            '8787FF' => array('8787FF', 105),
            '87AF00' => array('87AF00', 106),
            '87AF5F' => array('87AF5F', 107),
            '87AF87' => array('87AF87', 108),
            '87AFAF' => array('87AFAF', 109),
            '87AFD7' => array('87AFD7', 110),
            '87AFFF' => array('87AFFF', 111),
            '87D700' => array('87D700', 112),
            '87D75F' => array('87D75F', 113),
            '87D787' => array('87D787', 114),
            '87D7AF' => array('87D7AF', 115),
            '87D7D7' => array('87D7D7', 116),
            '87D7FF' => array('87D7FF', 117),
            '87FF00' => array('87FF00', 118),
            '87FF5F' => array('87FF5F', 119),
            '87FF87' => array('87FF87', 120),
            '87FFAF' => array('87FFAF', 121),
            '87FFD7' => array('87FFD7', 122),
            '87FFFF' => array('87FFFF', 123),
            'AF0000' => array('AF0000', 124),
            'AF005F' => array('AF005F', 125),
            'AF0087' => array('AF0087', 126),
            'AF00AF' => array('AF00AF', 127),
            'AF00D7' => array('AF00D7', 128),
            'AF00FF' => array('AF00FF', 129),
            'AF5F00' => array('AF5F00', 130),
            'AF5F5F' => array('AF5F5F', 131),
            'AF5F87' => array('AF5F87', 132),
            'AF5FAF' => array('AF5FAF', 133),
            'AF5FD7' => array('AF5FD7', 134),
            'AF5FFF' => array('AF5FFF', 135),
            'AF8700' => array('AF8700', 136),
            'AF875F' => array('AF875F', 137),
            'AF8787' => array('AF8787', 138),
            'AF87AF' => array('AF87AF', 139),
            'AF87D7' => array('AF87D7', 140),
            'AF87FF' => array('AF87FF', 141),
            'AFAF00' => array('AFAF00', 142),
            'AFAF5F' => array('AFAF5F', 143),
            'AFAF87' => array('AFAF87', 144),
            'AFAFAF' => array('AFAFAF', 145),
            'AFAFD7' => array('AFAFD7', 146),
            'AFAFFF' => array('AFAFFF', 147),
            'AFD700' => array('AFD700', 148),
            'AFD75F' => array('AFD75F', 149),
            'AFD787' => array('AFD787', 150),
            'AFD7AF' => array('AFD7AF', 151),
            'AFD7D7' => array('AFD7D7', 152),
            'AFD7FF' => array('AFD7FF', 153),
            'AFFF00' => array('AFFF00', 154),
            'AFFF5F' => array('AFFF5F', 155),
            'AFFF87' => array('AFFF87', 156),
            'AFFFAF' => array('AFFFAF', 157),
            'AFFFD7' => array('AFFFD7', 158),
            'AFFFFF' => array('AFFFFF', 159),
            'D70000' => array('D70000', 160),
            'D7005F' => array('D7005F', 161),
            'D70087' => array('D70087', 162),
            'D700AF' => array('D700AF', 163),
            'D700D7' => array('D700D7', 164),
            'D700FF' => array('D700FF', 165),
            'D75F00' => array('D75F00', 166),
            'D75F5F' => array('D75F5F', 167),
            'D75F87' => array('D75F87', 168),
            'D75FAF' => array('D75FAF', 169),
            'D75FD7' => array('D75FD7', 170),
            'D75FFF' => array('D75FFF', 171),
            'D78700' => array('D78700', 172),
            'D7875F' => array('D7875F', 173),
            'D78787' => array('D78787', 174),
            'D787AF' => array('D787AF', 175),
            'D787D7' => array('D787D7', 176),
            'D787FF' => array('D787FF', 177),
            'D7AF00' => array('D7AF00', 178),
            'D7AF5F' => array('D7AF5F', 179),
            'D7AF87' => array('D7AF87', 180),
            'D7AFAF' => array('D7AFAF', 181),
            'D7AFD7' => array('D7AFD7', 182),
            'D7AFFF' => array('D7AFFF', 183),
            'D7D700' => array('D7D700', 184),
            'D7D75F' => array('D7D75F', 185),
            'D7D787' => array('D7D787', 186),
            'D7D7AF' => array('D7D7AF', 187),
            'D7D7D7' => array('D7D7D7', 188),
            'D7D7FF' => array('D7D7FF', 189),
            'D7FF00' => array('D7FF00', 190),
            'D7FF5F' => array('D7FF5F', 191),
            'D7FF87' => array('D7FF87', 192),
            'D7FFAF' => array('D7FFAF', 193),
            'D7FFD7' => array('D7FFD7', 194),
            'D7FFFF' => array('D7FFFF', 195),
            'FF0000' => array('FF0000', 196),
            'FF005F' => array('FF005F', 197),
            'FF0087' => array('FF0087', 198),
            'FF00AF' => array('FF00AF', 199),
            'FF00D7' => array('FF00D7', 200),
            'FF00FF' => array('FF00FF', 201),
            'FF5F00' => array('FF5F00', 202),
            'FF5F5F' => array('FF5F5F', 203),
            'FF5F87' => array('FF5F87', 204),
            'FF5FAF' => array('FF5FAF', 205),
            'FF5FD7' => array('FF5FD7', 206),
            'FF5FFF' => array('FF5FFF', 207),
            'FF8700' => array('FF8700', 208),
            'FF875F' => array('FF875F', 209),
            'FF8787' => array('FF8787', 210),
            'FF87AF' => array('FF87AF', 211),
            'FF87D7' => array('FF87D7', 212),
            'FF87FF' => array('FF87FF', 213),
            'FFAF00' => array('FFAF00', 214),
            'FFAF5F' => array('FFAF5F', 215),
            'FFAF87' => array('FFAF87', 216),
            'FFAFAF' => array('FFAFAF', 217),
            'FFAFD7' => array('FFAFD7', 218),
            'FFAFFF' => array('FFAFFF', 219),
            'FFD700' => array('FFD700', 220),
            'FFD75F' => array('FFD75F', 221),
            'FFD787' => array('FFD787', 222),
            'FFD7AF' => array('FFD7AF', 223),
            'FFD7D7' => array('FFD7D7', 224),
            'FFD7FF' => array('FFD7FF', 225),
            'FFFF00' => array('FFFF00', 226),
            'FFFF5F' => array('FFFF5F', 227),
            'FFFF87' => array('FFFF87', 228),
            'FFFFAF' => array('FFFFAF', 229),
            'FFFFD7' => array('FFFFD7', 230),
            'FFFFFF' => array('FFFFFF', 231),
            '080808' => array('080808', 232),
            '121212' => array('121212', 233),
            '1C1C1C' => array('1C1C1C', 234),
            '262626' => array('262626', 235),
            '303030' => array('303030', 236),
            '3A3A3A' => array('3A3A3A', 237),
            '444444' => array('444444', 238),
            '4E4E4E' => array('4E4E4E', 239),
            '585858' => array('585858', 240),
            '626262' => array('626262', 241),
            '6C6C6C' => array('6C6C6C', 242),
            '767676' => array('767676', 243),
            '808080' => array('808080', 244),
            '8A8A8A' => array('8A8A8A', 245),
            '949494' => array('949494', 246),
            '9E9E9E' => array('9E9E9E', 247),
            'A8A8A8' => array('A8A8A8', 248),
            'B2B2B2' => array('B2B2B2', 249),
            'BCBCBC' => array('BCBCBC', 250),
            'C6C6C6' => array('C6C6C6', 251),
            'D0D0D0' => array('D0D0D0', 252),
            'DADADA' => array('DADADA', 253),
            'E4E4E4' => array('E4E4E4', 254),
            'EEEEEE' => array('EEEEEE', 255),
        );
    }

    /**
     * @dataProvider exactHexCodes
     */
    public function testExactHexCodeInputs($hex, $gcode)
    {
        $color = Xterm256::calculate($hex);
        $r     = new ReflectionClass($color);
        $gcode = sprintf('%%s;5;%s', $gcode);
        $this->assertEquals($gcode, $r->getStaticPropertyValue('color'));
    }
}
