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
 * @package    Zend_Gdata
 * @subpackage Demos
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/* Load the Zend Gdata classes. */
require_once 'Zend/Loader.php';
Zend_Loader::loadClass('Zend_Gdata_AuthSub');
Zend_Loader::loadClass('Zend_Gdata_Gbase');

/* The items feed URL, used for queries, insertions and batch commands. */
define('ITEMS_FEED_URI', 'http://www.google.com/base/feeds/items');

/* Types of cuisine the user may select when inserting a recipe. */
$cuisines = array('African', 'American', 'Asian', 'Caribbean', 'Chinese',
  'French', 'Greek', 'Indian', 'Italian', 'Japanese', 'Jewish',
  'Mediterranean', 'Mexican', 'Middle Eastern', 'Moroccan',
  'North American', 'Spanish', 'Thai', 'Vietnamese', 'Other');


/**
 * Inserts a new recipe by performing an HTTP POST to the
 * items feed.
 * @param boolean $dryRun (optional) True if this should be a dry run insert
 * @return Zend_Gdata_Gbase_ItemFeed The newly created entry
 */
function postItem($dryRun = false) {
  $client = Zend_Gdata_AuthSub::getHttpClient($_POST['token']);
  $gdata = new Zend_Gdata_Gbase($client);

  $newEntry = $gdata->newItemEntry();

  // Add title
  $newEntry->title = $gdata->newTitle(trim($_POST['recipe_title']));

  // Add some content
  $newEntry->content = $gdata->newContent($_POST['recipe_text']);
  $newEntry->content->type = 'text';

  // Define item type
  $newEntry->itemType = 'testrecipes';
  $newEntry->itemType->type = 'text';

  // Add item-specific attributes
  $newEntry->addGbaseAttribute('cuisine', $_POST['cuisine'], 'text');
  $newEntry->addGbaseAttribute('cooking_time', $_POST['time_val'] . ' ' .
      $_POST['time_units'], 'intUnit');
  $newEntry->addGbaseAttribute('main_ingredient',
                               $_POST['main_ingredient'],
                              'text');
  $newEntry->addGbaseAttribute('serving_count', $_POST['serves'], 'number');

  // Post the item
  $createdEntry = $gdata->insertGbaseItem($newEntry, $dryRun);

  return $createdEntry;
}

/**
 * Updates an existing recipe by performing an HTTP PUT
 * on its feed URI, using the updated values as the data.
 * @return true
 */
function updateItem() {
  $client = Zend_Gdata_AuthSub::getHttpClient($_POST['token']);
  $gdata = new Zend_Gdata_Gbase($client);

  $itemUrl = $_POST['link'];
  $updatedEntry = $gdata->getGbaseItemEntry($itemUrl);

  // Update title
  $updatedEntry->title = $gdata->newTitle(trim($_POST['recipe_title']));

  // Update content
  $updatedEntry->content = $gdata->newContent($_POST['recipe_text']);
  $updatedEntry->content->type = 'text';

  // Update item-specific attributes
  $baseAttributeArr = $updatedEntry->getGbaseAttribute('cuisine');
  if (is_object($baseAttributeArr[0])) {
    $baseAttributeArr[0]->text = $_POST['cuisine'];
  }

  $baseAttributeArr = $updatedEntry->getGbaseAttribute('cooking_time');
  if (is_object($baseAttributeArr[0])) {
    $baseAttributeArr[0]->text =
        $_POST['time_val'] . ' ' . $_POST['time_units'];
  }

  $baseAttributeArr = $updatedEntry->getGbaseAttribute('main_ingredient');
  if (is_object($baseAttributeArr[0])) {
    $baseAttributeArr[0]->text = $_POST['main_ingredient'];
  }

  $baseAttributeArr = $updatedEntry->getGbaseAttribute('serving_count');
  if (is_object($baseAttributeArr[0])) {
    $baseAttributeArr[0]->text = $_POST['serves'];
  }

  $dryRun = false;
  $gdata->updateGbaseItem($updatedEntry, $dryRun);

  // Alternatively, you can call the save() method directly on the entry
  // $updatedEntry->save();

  return true;
}

/**
 * Deletes a recipe by performing an HTTP DELETE on its feed URI.
 * @return void
 */
function deleteItem() {
  $client = Zend_Gdata_AuthSub::getHttpClient($_POST['token']);
  $gdata = new Zend_Gdata_Gbase($client);

  $itemUrl = $_POST['link'];
  $deleteEntry = $gdata->getGbaseItemEntry($itemUrl);

  $dryRun = false;
  $gdata->deleteGbaseItem($deleteEntry, $dryRun);

  // Alternatively, you can call the save() method directly on the entry
  // $gdata->delete($itemUrl);
}

/**
 * Creates the XML content used to perform a batch delete.
 * @return string The constructed XML to be used for the batch delete
 */
function buildBatchXML() {
  $result =  '<?xml version="1.0" encoding="UTF-8"?>' . "\n" .
             '<feed xmlns="http://www.w3.org/2005/Atom"' . "\n" .
             ' xmlns:g="http://base.google.com/ns/1.0"' . "\n" .
             ' xmlns:batch="http://schemas.google.com/gdata/batch">' . "\n";

  $counter = 0;
  foreach($_POST as $key => $value) {
    if(substr($key, 0, 5) == "link_") {
      $counter++;

      $result .= '<entry>' . "\n" .
                 '<id>' . $value . '</id>' . "\n" .
                 '<batch:operation type="delete"/>' . "\n" .
                 '<batch:id>' . $counter . '</batch:id>' . "\n" .
                 '</entry>' . "\n";
    }
  }
  $result .= '</feed>' . "\n";

  return $result;
}

/**
 * Deletes all recipes by performing an HTTP POST to the
 * batch URI.
 * @return Zend_Http_Response The reponse of the post
 */
function batchDelete() {
  $client = Zend_Gdata_AuthSub::getHttpClient($_POST['token']);
  $gdata = new Zend_Gdata_Gbase($client);

  $response = $gdata->post(buildBatchXML(), ITEMS_FEED_URI . '/batch');

  return $response;
}

/**
 * Writes the HTML header for the demo.
 *
 * NOTE: We would normally keep the HTML/CSS markup separate from the business
 *       logic above, but have decided to include it here for simplicity of
 *       having a single-file sample.
 * @return void
 */
function printHTMLHeader()
{
  print '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"' . "\n" .
        '"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">' . "\n" .
        '<html xmlns="http://www.w3.org/1999/xhtml" lang="en">' . "\n" .
        '<head><meta http-equiv="Content-Type" ' .
        'content="text/html;charset=utf-8"/>' . "\n" .
        '<title>PHP Demo: Google Base API</title>' . "\n" .
        '<link rel="stylesheet" type="text/css" ' .
        'href="http://code.google.com/css/dev_docs.css">' . "\n" .
        '</head>' . "\n" .
        '<body><center>' . "\n";
}

/**
 * Writes the HTML footer for the demo.
 *
 * NOTE: We would normally keep the HTML/CSS markup separate from the business
 *       logic above, but have decided to include it here for simplicity of
 *       having a single-file sample.
 * @return void
 */
function printHTMLFooter() {
  print '</center></body></html>' . "\n";
}

/**
 * We arrive here when the user first comes to the form. The first step is
 * to have them get a single-use token.
 */
function showIntroPage() {
  $next_url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
  $scope = ITEMS_FEED_URI;
  $secure = false;
  $session = true;
  $redirect_url = Zend_Gdata_AuthSub::getAuthSubTokenUri($next_url,
                                                         $scope,
                                                         $secure,
                                                         $session);

  printHTMLHeader();

  print '<table style="width:50%;">' . "\n" .
        '<tr>' . "\n" .
        '<th colspan="2" style="text-align:center;">' .
        'PHP Demo: Google Base data API<br/>' .
        '<small><span style="font-variant: small-caps;">Powered By</span>' .
        ' <a href="http://framework.zend.com/download/gdata">' .
        'Zend Google Data Client Library</a></small></th>' . "\n" .
        '</tr>' . "\n" .
        '<tr><td>Before you get started, please <a href="' . $redirect_url .
        '">sign in</a> to your personal Google Base account.</td></tr>' . "\n" .
        '</table>' . "\n";

  printHTMLFooter();
}

/**
 * Prints the table of recipes the user has already entered
 * on the left-hand side of the page.
 * @param string $token The session token
 * @return void
 */
function showRecipeListPane($token) {
  $client = Zend_Gdata_AuthSub::getHttpClient($token);
  $gdata = new Zend_Gdata_Gbase($client);
  try {
    $feed = $gdata->getGbaseItemFeed(ITEMS_FEED_URI . '/-/testrecipes');

    print '<td style="width:50%;text-align:center;vertical-align:top">' . "\n" .
          '<a href="http://www.google.com/base/dashboard" target="_blank">' .
          'View all of your published items</a>' .
          '<table>' . "\n" .
          '<tr><th colspan="5" style="text-align:center">' .
          'Recipes you have added that searchable via the API</th></tr>' . "\n";

    if ($feed->count() == 0) {
      print '<tr style="font-style:italic">' .
            '<td colspan="5" style="text-align:center">(none)</td>' .
            '</tr>' . "\n";
    } else {
      print '<tr style="font-style:italic">' . "\n" .
            '<td style="text-align:center">Name</td>' . "\n" .
            '<td style="text-align:center">Cuisine</td>' . "\n" .
            '<td style="text-align:center">Serves</td>' . "\n" .
            '<td colspan="2" style="text-align:center">Actions</td>' . "\n" .
            '</tr>' . "\n";

      foreach ($feed->entries as $feed_entry) {
        $href = $feed_entry->link[0]->href;
        $title = $feed_entry->title->text;
        $id = $feed_entry->id->text;

        $baseAttributeArr = $feed_entry->getGbaseAttribute('cuisine');
        // Only want first cuisine
        if (isset($baseAttributeArr[0]) && is_object($baseAttributeArr[0])) {
          $cuisine = $baseAttributeArr[0]->text;
        }

        $baseAttributeArr = $feed_entry->getGbaseAttribute('serving_count');
        // Only want first serving_count
        if (isset($baseAttributeArr[0]) && is_object($baseAttributeArr[0])) {
          $serving_count = $baseAttributeArr[0]->text;
        }

        print '<tr>' . "\n" .
              '<td align="left" valign="top"><b><a href="' . $href . '">' .
              $title . '</a></b></td>' . "\n" .
              '<td style="text-align:center;vertical-align:top">' .
              $cuisine . '</td>' . "\n" .
              '<td style="text-align:center;vertical-align:top">' .
              $serving_count . '</td>' . "\n";

        /* Create an Edit button for each existing recipe. */
        print '<td style="text-align:center;vertical-align:top">' . "\n" .
              '<form method="post" action="' . $_SERVER['PHP_SELF'] .
              '" style="margin-top:0;margin-bottom:0;">' . "\n" .
              '<input type="hidden" name="action" value="edit">' . "\n" .
              "<input type=\"hidden\" name=\"token\" value=\"$token\">" . "\n" .
              '<input type="hidden" name="edit" value="' . $id . '">' . "\n" .
              '<input type="submit" value="Edit">' . "\n" .
              '</form>' . "\n" .
              '</td>' . "\n";

        /* Create a Delete button for each existing recipe. */
        print '<td style="text-align:center; vertical-align:top">' . "\n" .
              '<form method="post" action="' . $_SERVER['PHP_SELF'] .
              '" style="margin-top:0;margin-bottom:0;">' . "\n" .
              '<input type="hidden" name="action" value="delete">' . "\n" .
              "<input type=\"hidden\" name=\"token\" value=\"$token\">" . "\n" .
              '<input type="hidden" name="link" value="' . $id . '">' . "\n" .
              '<input type="submit" value="Delete">' . "\n" .
              '</form>' . "\n" .
              '</td>' . "\n" .
              '</tr>' . "\n";
      }
    }

    /* Create a "Delete all" button" to demonstrate batch requests. */
    print '<tr><td colspan="5" style="text-align:center">' . "\n" .
          '<form method="post" action="' . $_SERVER['PHP_SELF'] .
          '" style="margin-top:0;margin-bottom:0">' . "\n" .
          '<input type="hidden" name="action" value="delete_all">' . "\n" .
          '<input type="hidden" name="token" value="' . $token . '">' . "\n";

    $i = 0;
    foreach ($feed as $feed_entry) {
      print '<input type="hidden" name="link_' . $i . '" value="' .
            $feed_entry->id->text . '">' . "\n";
      $i++;
    }

    print '<input type="submit" value="Delete All"';
    if ($feed->count() == 0) {
      print ' disabled="true"';
    }
    print '></form></td></tr>' . "\n";
    print '</table>' . "\n";

    print '</td>' . "\n";
  } catch (Zend_Gdata_App_Exception $e) {
    showMainMenu("Error: " . $e->getMessage(), $token);
  }
}

/**
 * Prints a small form allowing the user to insert a new
 * recipe.
 * @param string $sessionToken A session token
 * @return void
 */
function showRecipeInsertPane($sessionToken) {
  global $cuisines;

  print '<td valign="top" width="50%">' . "\n" .
        '<table width="90%">' . "\n" .
        '<tr><th colspan="2" style="text-align:center">' .
        'Insert a new recipe</th></tr>' . "\n" .
        '<form method="post" action="' . $_SERVER['PHP_SELF'] . '">' . "\n" .
        '<input type="hidden" name="action" value="insert">' . "\n" .
        "<input type=\"hidden\" name=\"token\" value=\"$sessionToken\">\n" .
        '<tr><td align="right">Title:</td>' . "\n" .
        '<td><input type="text" name="recipe_title" class="half">' .
        '</td></tr>' . "\n" .
        '<tr><td align="right">Main ingredient:</td>' . "\n" .
        '<td><input type="text" name="main_ingredient" class="half">' .
        '</td></tr>' . "\n" .
        '<tr><td align="right">Cuisine:</td>' . "\n" .
        '<td><select name="cuisine" class="half">' . "\n";

  foreach ($cuisines as $curCuisine) {
    print "<option value=\"$curCuisine\">$curCuisine</option>\n";
  }

  print '</select></td></tr>' . "\n" .
        '<tr><td align="right">Cooking Time:</td>' .
        '<td><input type="text" name="time_val" size=2 maxlength=2>&nbsp;' .
        '<select name="time_units"><option value="minutes">minutes</option>' .
        '<option value="hours">hours</option></select></td></tr>' . "\n" .
        '<tr><td align="right">Serves:</td>' . "\n" .
        '<td><input type="text" name="serves" size=2 maxlength=3></td>' .
        '</tr>' . "\n" .
        '<tr><td align="right">Recipe:</td>' . "\n" .
        '<td><textarea class="full" name="recipe_text"></textarea></td>' .
        '</tr>' . "\n" .
        '<td>&nbsp;</td><td><input type="submit" value="Submit"></td>' . "\n" .
        '</form></tr></table>' . "\n" .
        '</td>' . "\n";
}

/**
 * Shows a menu allowing the user to update an existing
 * recipe with the Base API update feature.
 * @return void
 */
function showEditMenu() {
  global $cuisines;

  $client = Zend_Gdata_AuthSub::getHttpClient($_POST['token']);
  $gdata = new Zend_Gdata_Gbase($client);

  try {
    $feed = $gdata->getGbaseItemFeed(ITEMS_FEED_URI);
    foreach ($feed->entries as $feed_entry) {
      $editLink = $feed_entry->link[2]->href;

      if ($editLink == $_POST['edit']) {
        $baseAttributeArr = $feed_entry->getGbaseAttribute('cooking_time');
        if (isset($baseAttributeArr[0]) && is_object($baseAttributeArr[0])) {
          $splitCookingTime = explode(' ', $baseAttributeArr[0]->text);
        }

        $baseAttributeArr = $feed_entry->getGbaseAttribute('cuisine');
        // Cuisine can have multiple entries
        if (isset($baseAttributeArr[0]) && is_object($baseAttributeArr[0])) {
          $cuisine = $baseAttributeArr[0]->text;
        }

        $baseAttributeArr = $feed_entry->getGbaseAttribute('serving_count');
        // $serving_count can have multiple entries
        if (isset($baseAttributeArr[0]) && is_object($baseAttributeArr[0])) {
          $serving_count = $baseAttributeArr[0]->text;
        }

        $main_ingredient = $feed_entry->getGbaseAttribute('main_ingredient');
        // Main_ingredient can have multiple entries
        if (is_array($main_ingredient)) {
          $main_ingredient = $main_ingredient[0]->text;
        }

        printHTMLHeader();

        print '<table style="width:50%">' . "\n";
        print '<tr>' .
              '<th colspan="2" style="text-align:center">Edit recipe:</th>' .
              '</tr>' . "\n";

        print "<form method=\"post\" action=\"{$_SERVER['PHP_SELF']}\">\n" .
              '<input type="hidden" name="action" value="update">' . "\n" .
              '<input type="hidden" name="link" value="' .
              $_POST['edit'] . '">' . "\n" .
              '<input type="hidden" name="token" value="' .
              $_POST['token'] . '">' . "\n";

        print '<tr><td align="right">Title:</td>' . "\n" .
              '<td>' .
              '<input type="text" name="recipe_title" class="half" value="' .
              $feed_entry->title->text . '">' .
              '</td></tr>' . "\n";

        print '<tr><td align="right">Main ingredient:</td>' . "\n" .
              '<td><input type="text" name="main_ingredient" value="' .
               $main_ingredient . '" class="half"></td></tr>' . "\n";

        print '<tr><td align="right">Cuisine:</td>' . "\n" .
              '<td><select name="cuisine" class="half">' . "\n";

        foreach ($cuisines as $curCuisine) {
          print '<option value="' . $curCuisine . '"';
          if ($curCuisine == $cuisine) {
            print ' selected="selected"';
          }
          print '>' . $curCuisine . "</option>\n";
        }

        print '</select></td></tr>' . "\n";
        print '<tr><td align="right">Cooking Time:</td>' .
              '<td><input type="text" name="time_val" size="2" maxlength="2" ' .
              'value="' . $splitCookingTime[0] . '">&nbsp;' . "\n" .
              '<select name="time_units">' . "\n";
        if ($splitCookingTime[1] == "minutes") {
          print '<option value="minutes" selected="selected">minutes</option>' .
            "\n";
          print '<option value="hours">hours</option>' . "\n";
        } else {
          print '<option value="minutes">minutes</option>' . "\n";
          print '<option value="hours" selected="selected">hours</option>' .
            "\n";
        }

        print '</select></td></tr>' . "\n" .
              '<tr><td align="right">Serves:</td>' . "\n" .
              '<td><input type="text" name="serves" value="' .
              $serving_count . '" size="2" maxlength="3"></td></tr>' . "\n" .
              '<tr><td align="right">Recipe:</td>' . "\n" .
              '<td><textarea class="full" name="recipe_text">' .
              $feed_entry->content->text . '</textarea></td></tr>' . "\n" .
              '<td>&nbsp;</td><td><input type="submit" value="Update">' .
              '</td>' . "\n" .
              '</form></tr></table>' . "\n";

        printHTMLFooter();

        break;
      }
    }
  } catch (Zend_Gdata_App_Exception $e) {
    showMainMenu($e->getMessage(), $_POST['token']);
  }
}

/**
 * Displays both the "List of current recipes" and
 * "Insert a new recipe" panels in a single table.
 * @param string $tableTitle The title to display in the html table
 * @param string $sessionToken A session token
 * @return void
 */
function showMainMenu($tableTitle, $sessionToken) {
  printHTMLHeader();

  print '<table style="width: 75%;text-align:center">' . "\n" .
        '<tr>' . "\n" .
        '<th colspan="2" style="text-align:center;">' .
        'PHP Demo: Google Base data API<br />' .
        '<font size="-1">' .
        '<span style="font-variant: small-caps;">Powered By</span> ' .
        '<a href="http://framework.zend.com/download/gdata">' .
        'Zend Google Data Client Library</a></font></th>' . "\n" .
        '</tr>' . "\n" .
        '<tr><td colspan="2" align="center">' . $tableTitle . "</td></tr>\n" .
        '<tr>' . "\n";

  // Create the two sub-tables.
  showRecipeListPane($sessionToken);
  showRecipeInsertPane($sessionToken);

  // Add a "Sign out" link.
  print '<tr><th colspan="2" style="text-align: center">Or click here to' .
        ' <a href="http://www.google.com/accounts/Logout">sign out</a>' .
        ' of your Google account.</th></tr>' . "\n";

  // Close the master table.
  print '</table>' . "\n";

  printHTMLFooter();
}

/**
 * Exchanges the given single-use token for a session
 * token using AuthSubSessionToken, and returns the result.
 * @param string $token The single-use token from AuthSubRequest
 * @return string The upgraded (session) token
 */
function exchangeToken($token) {
  return Zend_Gdata_AuthSub::getAuthSubSessionToken($token);
}

/**
 * We arrive here after the user first authenticates and we get back
 * a single-use token.
 * @return void
 */
function showFirstAuthScreen() {
  $singleUseToken = $_GET['token'];
  $sessionToken = exchangeToken($singleUseToken);

  if (!$sessionToken) {
    showIntroPage();
  } else {
    $tableTitle = "Here's your <b>single use token:</b> " .
      "<code>$singleUseToken</code><br/>" . "\n" .
      "And here's the <b>session token:</b> <code>$sessionToken</code>";

      showMainMenu($tableTitle, $sessionToken);
  }
}

/**
 * Main logic to handle the POST operation of inserting an item.
 * @return void
 */
function handlePost() {
  try {
    $newEntry= postItem();
    if ($newEntry) {
      showMainMenu('Recipe inserted!  It will be searchable by the API soon...',
                    $_POST['token']);
    }
  } catch (Zend_Gdata_App_Exception $e) {
    showMainMenu('Recipe insertion failed: ' . $e->getMessage(),
                 $_POST['token']);
  }
}

/**
 * Main logic to handle deleting an item.
 * @return void
 */
function handleDelete() {
  try {
    deleteItem();
    showMainMenu('Recipe deleted.', $_POST['token']);
  } catch (Zend_Gdata_App_Exception $e) {
    showMainMenu('Recipe deletion failed: ' . $e->getMessage(),
                 $_POST['token']);
  }
}

/**
 * Main logic to handle a batch deletion of items.
 * @return void
 */
function handleBatch() {
  try {
    $batch_response = batchDelete();
    if ($batch_response->isSuccessful()) {
      showMainMenu('All recipes deleted.', $_POST['token']);
    } else {
      showMainMenu('Batch deletion failed: ' . $batch_response->getMessage(),
                   $_POST['token']);
    }
  } catch (Zend_Gdata_App_Exception $e) {
    showMainMenu('Batch deletion failed: ' . $e->getMessage(), $_POST['token']);
  }
}

/**
 * Main logic to handle updating an item
 * @return void
 */
function handleUpdate() {
  try {
    if (updateItem()) {
      showMainMenu('Recipe successfully updated.', $_POST['token']);
    } else {
      showMainMenu('Recipe update failed.', $_POST['token']);
    }
  } catch (Zend_Gdata_App_Exception $e) {
    showMainMenu('Recipe update failed: ' . $e->getMessage(), $_POST['token']);
  }
}

/**
 * Main logic to handle requests
 */
if (count($_GET) == 1 && array_key_exists('token', $_GET)) {
  showFirstAuthScreen();
} else {
  if (count($_POST) == 0) {
    showIntroPage();
  } else {
    if ($_POST['action'] == 'insert') {
      handlePost();
    } else if ($_POST['action'] == 'delete') {
      handleDelete();
    } else if ($_POST['action'] == 'delete_all') {
      handleBatch();
    } else if ($_POST['action'] == 'edit') {
      showEditMenu();
    } else if ($_POST['action'] == 'update') {
      handleUpdate();
    } else {
      showIntroPage();
    }
  }
}

?>
