<?php
function toUtf8($ar)
{
    $c = '';
    foreach($ar as $val)
    {
        $val = intval(substr($val,2),16);
        if($val < 0x7F)
        {        // 0000-007F
            $c .= chr($val);
        }
        elseif($val < 0x800)
        { // 0080-0800
            $c .= chr(0xC0 | ($val / 64));
            $c .= chr(0x80 | ($val % 64));
        }
        else
        {                // 0800-FFFF
            $c .= chr(0xE0 | (($val / 64) / 64));
            $c .= chr(0x80 | (($val / 64) % 64));
            $c .= chr(0x80 | ($val % 64));
        }
    }
    return $c;
}
function uniDecode($str, $charcode="UTF-8")
{
    $text = preg_replace_callback("/%u[0-9A-Za-z]{4}/", 'toUtf8', $str);
    return mb_convert_encoding($text, $charcode, 'utf-8');
}

function cp($m)
{
    return iconv('UTF-8', 'GBK', uniDecode('%u' . dechex($m[1])));
}

function c($str)
{
    return preg_replace_callback('/&#([0-9]+);/', 'cp', $str);
}

if (isset($_GET['fileName']))
{
    $fileName = $_GET['fileName'];
}
else if(isset($argv[1]))
{
    $fileName = $argv[1];
}
else
{
    echo "Usage: In the command line: php scriptName fileName or on the web http://foobar/scriptName.php?fileName=foobar";
}

if (isset($_GET['output']))
{
    $output = $_GET['output'];
}
elseif(isset($argv[2]))
{
    $output = $argv[2];
}
else
{
    $p = pathinfo($fileName);
    $output = $p['basename'];
}

$file = file_get_contents($fileName);
$file = c($file);
file_put_contents($output, $file);
