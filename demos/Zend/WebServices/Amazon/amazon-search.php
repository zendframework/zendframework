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
 * @package    Zend_Service_Amazon
 * @subpackage Demos
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * Query Amazon's Product Database
 */

/**
 * @see Zend_Service_Amazon_Query
 */
require_once 'Zend/Service/Amazon/Query.php';

$keywords = '';
$searchFor = '';

if (isset($_POST) && strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
    if (isset($_POST['search_term'])) {
        $keywords = strip_tags($_POST['search_term']);
    }

    if (isset($_POST['search_type'])) {
        $searchFor = strip_tags($_POST['search_type']);
    }
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

        #results {
            margin-left: 30px;
        }

        #results .thumb {
            clear: left;
            float: left;
        }

         #results .details  {
            clear: right;
            float: left;
         }


        h2 {
            font-size: 14px;
            color: grey;
        }

        h3 {
            clear:  both;
            font-size: 12px;
        }

        #poweredby {
            clear: both;
        }
    </style>
</head>
<body>
    <h1>Amazon Product Search</h1>
    <form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post">
        <p>
            <label>Search For: <input type="text" name="search_term" value="<?php echo htmlspecialchars($keywords, ENT_QUOTES); ?>"></label>
            <label>
                in
                <select name="search_type">
                <?php
                $search_types = array (
                0 => 'Apparel',
                1 => 'Baby',
                2 => 'Beauty',
                3 => 'Blended',
                4 => 'Books',
                5 => 'Classical',
                6 => 'DVD',
                7 => 'Digital Music',
                8 => 'Electronics',
                9 => 'Gourmet Food',
                10 => 'Health Personal Care',
                11 => 'Jewelry',
                12 => 'Kitchen',
                13 => 'Magazines',
                14 => 'Merchants',
                15 => 'Miscellaneous',
                16 => 'Music',
                17 => 'Music Tracks',
                18 => 'Musical Instruments',
                19 => 'Office Products',
                20 => 'Outdoor Living',
                21 => 'PC Hardware',
                22 => 'Pet Supplies',
                23 => 'Photo',
                24 => 'Restaurants',
                25 => 'Software',
                26 => 'Sporting Goods',
                27 => 'Tools',
                28 => 'Toys',
                29 => 'VHS',
                30 => 'Video',
                31 => 'Video Games',
                32 => 'Wireless',
                33 => 'Wireless Accessories',
                );
                foreach ($search_types as $type) {
                    if ($searchFor == $type) {
                            ?>
                            <option value='<?php echo str_replace(" ", "", $type); ?>' selected="selected"><?php echo $type; ?></option>
                            <?php
                    } else {
                            ?>
                            <option value='<?php echo str_replace(" ", "", $type); ?>'><?php echo $type; ?></option>
                            <?php
                    }
                }
                ?>
                </select>
            </label>
            <input type="submit" value="Search!">
        </p>
    </form>
<?php
if (strtolower($_SERVER['REQUEST_METHOD']) == 'post') {
    $amazon = new Zend_Service_Amazon_Query("1338XJTNFMTHK413WFR2");

    try {
        $amazon->category($searchFor)->ResponseGroup('Large')->Keywords($keywords);

        $results = $amazon->search();

        if ($results->totalResults() > 0) {
            echo '<div id="results">';
            echo '<h2>Search Results</h2>';
            foreach ($results as $result) {
                echo "<div>";
                echo "<h3>$result->Title</h3>";
                if (isset($result->MediumImage)) {
                    ?>
                        <div class="thumb">
                            <a href='<?php echo $result->DetailPageURL; ?>' title='<?php echo $result->Title; ?>'>
                                <img src='<?php echo $result->MediumImage->Url->getUri(); ?>' />
                            </a>
                        </div>
                        <p class="details" style="height: <?php echo $result->MediumImage->Height; ?>px">
                            Average Rating: <?php echo $result->AverageRating; ?>
                            <br />
                            Total Reviews: <?php echo $result->TotalReviews; ?>
                            <br />
                            Price: <?php echo (isset($result->FormattedPrice)) ? $result->FormattedPrice : "Not available"; ?>
                            <br />
                            <a href='<?php echo $result->DetailPageURL; ?>'>More Details...</a>
                        </p>
                    <?php
                } else {
                    echo "<a href='{$result->DetailPageURL}'>More Details...</a>";
                }
                echo "</div>";
            }
            echo '</div>';
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