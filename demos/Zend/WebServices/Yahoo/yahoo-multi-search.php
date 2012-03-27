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
 * @package    Zend_Service_Yahoo
 * @subpackage Demos
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Query Yahoo! Web, Image and News searches
 */

/**
 * @see Zend_Service_Yahoo
 */
require_once 'Zend/Service/Yahoo.php';

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

        #web, #news {
            float: left;
            width: 48%;
            margin-left: 10px;
        }

        #image {
            margin: 10px;
            border: 1px dashed grey;
            background-color: lightgrey;
            text-align: center;
        }

        h2 {
            font-size: 14px;
            color: grey;
        }

        h3 {
            font-size: 12px;
        }

        #poweredby {
            clear: both;
        }
    </style>
</head>
<body>
    <h1>Yahoo! Multi-Search</h1>
    <form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post">
        <p>
            <label>Search For: <input type="text" name="search_term" value="<?php echo htmlspecialchars($keywords, ENT_QUOTES); ?>"></label>
            <input type="submit" value="Search!">
        </p>
    </form>
<?php
    if (strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
        $yahoo = new Zend_Service_Yahoo('zendtesting');

        try {
            $results = $yahoo->imageSearch($keywords, array("results" => 5));

            if ($results->totalResults() > 0) {
                echo '<div id="image">';
                echo '<h2>Image Search Results</h2>';
                foreach ($results as $result) {
                    echo "<a href='{$result->ClickUrl}' title='$result->Title'><img src='{$result->Thumbnail->Url->getUri()}'></a>";
                }
                echo '</div>';
            }


            $results = $yahoo->webSearch($keywords);

            if ($results->totalResults() > 0) {
                echo '<div id="web">';
                echo '<h2>Web Search Results</h2>';
                foreach ($results as $result) {
                    echo "<h3><a href='{$result->ClickUrl}'>{$result->Title}</a></h3>";
                    echo "<p>{$result->Summary} <br> [<a href='{$result->CacheUrl}'>Cached Version</a>]</p>";
                }
                echo '</div>';
            }


            $results = $yahoo->newsSearch($keywords);

            if ($results->totalResults() > 0) {
                echo '<div id="news">';
                echo '<h2>News Search Results</h2>';
                foreach ($results as $result) {
                    echo "<h3><a href='{$result->ClickUrl}'>{$result->Title}</a></h3>";
                    echo "<p>{$result->Summary}</p>";
                }
                echo '</div>';
            }
        }
        catch (Zend_Service_Exception $e) {
            echo '<p style="color: red; font-weight: bold">An error occured, please try again later.</p>';
        }
    }
?>
<p id="poweredby" style="text-align: center; font-size: 9px;">Powered by the <a href="http://framework.zend.com">Zend Framework</a></p>
</body>
</html>