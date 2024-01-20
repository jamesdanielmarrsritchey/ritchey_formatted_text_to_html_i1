<?php
$location = realpath(dirname(__FILE__));
require_once $location . '/ritchey_formatted_text_to_html_i1_v17.php';
$return = ritchey_formatted_text_to_html_i1_v17("{$location}/temporary/source.txt", "{$location}/temporary/destination.html", "{$location}/custom_dependencies/ritchey-default-theme-i1-v1.css", 5, TRUE);
if ($return === TRUE){
	echo "TRUE" . PHP_EOL;
} else {
	echo "FALSE" . PHP_EOL;
}
?>