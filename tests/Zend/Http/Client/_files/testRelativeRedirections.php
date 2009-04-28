<?php

if (! isset($_GET['redirect'])) $_GET['redirect'] = null;

switch ($_GET['redirect']) {
	case 'abpath':
		header("Location: /path/to/fake/file.ext?redirect=abpath");
		break;
		
	case 'relpath':
		header("Location: path/to/fake/file.ext?redirect=relpath");
		break;
		
	default:
		echo "Redirections done.";
		break;
}
