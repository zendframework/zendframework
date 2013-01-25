<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Http
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
