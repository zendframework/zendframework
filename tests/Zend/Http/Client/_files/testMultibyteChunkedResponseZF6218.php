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
 * @package    Zend_Http
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

header("Content-type: text/plain; charset=UTF-8");
@ob_end_flush();
@ob_implicit_flush(true);

$text = <<<EOTEXT
לִבִּי בְמִזְרָח וְאָנֹכִי בְּסוֹף מַעֲרָב
אֵיךְ אֶטְעֲמָה אֵת אֲשֶׁר אֹכַל וְאֵיךְ יֶעֱרָב
אֵיכָה אֲשַׁלֵּם נְדָרַי וָאֱסָרַי, בְּעוֹד
צִיּוֹן בְּחֶבֶל אֱדוֹם וַאֲנִי בְּכֶבֶל עֲרָב
יֵקַל בְּעֵינַי עֲזֹב כָּל טוּב סְפָרַד, כְּמוֹ
יֵקַר בְּעֵינַי רְאוֹת עַפְרוֹת דְּבִיר נֶחֱרָב.
EOTEXT;

echo $text;
