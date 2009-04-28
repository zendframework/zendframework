<?php

if (! empty($_FILES)) {
	foreach ($_FILES as $name => $file) {
		echo $name . " " . $file['name'] . " " . $file['type'] . " " . $file['size'] . "\n";
	}
}