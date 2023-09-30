<?php
$location = realpath(dirname(__FILE__));
require_once $location . '/ritchey_check_if_string_is_all_caps_i1_v1.php';
$return = ritchey_check_if_string_is_all_caps_i1_v1('TEST STRING', TRUE, TRUE, TRUE, TRUE);
if ($return === TRUE){
	echo "TRUE" . PHP_EOL;
} else {
	echo "FALSE" . PHP_EOL;
}
?>