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
 * @category   Zend
 * @package    Zend_Service_Flickr
 * @subpackage Demos
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Query Yahoo! Web, Image and News searches
 */

/**
 * @see Zend_Service_Flickr
 */
require_once 'Zend/Service/Flickr.php';

if (isset($_POST) && strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
    $keywords = strip_tags($_POST['search_term']);
} else {
    $keywords = '';
}

?>
<!DOCTYPE html public "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <style type="text/css">
        html, body {
            margin: 0px;
            padding: 0px;
            font-family: Tahoma, Verdana, sans-serif;
            font-size: 10px;
        }

        h1 {
            margin-top: 0px;
            background-color: darkblue;
            color: white;
            font-size: 16px;
        }

        form {
            text-align: center;
        }

        label {
            font-weight: bold;
        }

        img {
            border: 0px;
            padding: 5px;
        }

        #composite {
            text-align: center;
            padding: 25px;
            background-color: black;
            margin-left: auto;
            margin-right: auto;
        }

        h2 {
            font-size: 14px;
            color: white;
            text-align: center;
        }

        #poweredby {
            clear: both;
        }
    </style>
</head>
<body>
    <h1>Flickr Compositor</h1>
    <form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post">
        <p>
            <label>Search For: <input type="text" name="search_term" value="<?php echo $keywords; ?>"></label>
            <input type="submit" value="Search!" onclick='this.value="Please Wait..."'>
        </p>
    </form>
<?php
if (strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
    $flickr = new Zend_Service_Flickr('381e601d332ab5ce9c25939570cb5c4b');

    try {
        $results = $flickr->tagSearch($keywords, array('per_page' => 50, 'tag_mode' => 'all'));

        if ($results->totalResults() > 0) {
            $images = array();
            foreach ($results as $result) {
                if (isset($result->Medium)) {
                    $images[] = imagecreatefromjpeg($result->Medium->uri);
                    $heights[] = $result->Medium->height;
                    $widths[] = $result->Medium->width;
                }
            }
            if (sizeof($images) == 0) {
                echo '<p style="color: orange; font-weight: bold">No Results Found.</p>';
            } else {
                sort($heights);
                sort($widths);
                $max_height = array_pop($heights);
                $max_width = array_pop($widths);
                $output = realpath("./temp/") .DIRECTORY_SEPARATOR.mt_rand(). ".jpg";
                foreach ($images as $key => $image) {
                    if (!file_exists('./temp')) {
                        mkdir("./temp");
                    }

                    $tmp = tempnam(realpath('./temp'), 'zflickr');
                    imagejpeg($image, $tmp);

                    chmod($tmp, 0777);

                    if (file_exists($output)) {
                        passthru("composite -dissolve 20 $tmp $output $output");
                        chmod($output, 0777);
                    } elseif (!isset($previous_image)) {
                        $previous_image = "$tmp";
                    } else {
                        passthru("composite -dissolve 20 $tmp $previous_image $output");
                        chmod($output, 0777);
                    }
                    $image_files[] = $tmp;
                }
                foreach ($image_files as $filename) {
                    unlink($filename);
                }
                //copy($output, basename($output));
                //unlink($output);
                $size = getimagesize($output);
                $size[0] += 25;
                $size[1] += 25;
                echo "<div id='composite' style='width: {$size[0]}px; height: {$size[1]}px;'><img src='temp/" .basename($output). "' alt='" .htmlspecialchars($keywords). "'><h2>" .ucwords(htmlspecialchars($keywords)). "</h2></div>";
            }
        } else {
            echo '<p style="color: orange; font-weight: bold">No Results Found</p>';
        }
    }
    catch (Zend_Service_Exception $e) {
        echo '<p style="color: red; font-weight: bold">An error occured, please try again later. (' .$e->getMessage(). ')</p>';
    }
}
?>
<p id="poweredby" style="text-align: center; font-size: 9px;">Powered by the <a href="http://framework.zend.com">Zend Framework</a></p>
</body>
</html>
