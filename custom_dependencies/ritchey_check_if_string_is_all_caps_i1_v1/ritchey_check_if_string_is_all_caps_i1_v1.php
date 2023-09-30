<?php
#Name:Ritchey Check If String Is All Caps i1 v1
#Description:Check if a string only contains uppercase letters. Returns "TRUE" if the string only contains A-Z. Returns "FALSE" for anything else.
#Notes:Optional arguments can be "NULL" to skip them in which case they will use default values.
#Arguments:'string' (required) is a string to check. 'allow_spaces' (optiona) indicates if " " should be allowed in the string. 'display_warnings' (optional) indicates if warning messages should be displayed. 'display_errors' (optional) indicates if errors should be displayed. 'debug' (optional) indicates if additional information should be outputted. This information is useful for developers testing the code.
#Arguments (Script Friendly):string,string,required|allow_spaces,bool,optional|display_warnings,bool,optional|display_errors,bool,optional|debug,bool,optional
#Content:
if (function_exists('ritchey_check_if_string_is_all_caps_i1_v1') === FALSE){
function ritchey_check_if_string_is_all_caps_i1_v1($string, $allow_spaces = NULL, $display_warnings = NULL, $display_errors = NULL, $debug = NULL){
	$errors = array();
	$warnings = array();
	//$progress = array();
	if (@is_string($string) !== TRUE) {
		$errors[] = 'string';
	}
	if ($allow_spaces === NULL){
		$allow_spaces = FALSE;
	} else if ($allow_spaces === TRUE){
		#Do Nothing
	} else if ($allow_spaces === FALSE){
		#Do Nothing
	} else {
		$errors[] = "allow_spaces";
	}
	if ($display_warnings === NULL){
		$display_warnings = FALSE;
	} else if ($display_warnings === TRUE){
		#Do Nothing
	} else if ($display_warnings === FALSE){
		#Do Nothing
	} else {
		$errors[] = "display_warnings";
	}
	if ($display_errors === NULL){
		$display_errors = FALSE;
	} else if ($display_errors === TRUE){
		#Do Nothing
	} else if ($display_errors === FALSE){
		#Do Nothing
	} else {
		$errors[] = "display_errors";
	}
	if ($debug === NULL){
		$debug = FALSE;
	} else if ($debug === TRUE){
		#Do Nothing
	} else if ($debug === FALSE){
		#Do Nothing
	} else {
		$errors[] = "debug";
	}
	##Task
	if (@empty($errors) === TRUE){
		if ($allow_spaces === TRUE){
			$modified_string = preg_replace("/[^A-Z ]/", "", $string);
		} else {
			$modified_string = preg_replace("/[^A-Z]/", "", $string);
		}
		if ($debug === TRUE){
			echo 'Debug (Modified String: ' . $modified_string . ')' . PHP_EOL;
		}
		if ($string === $modified_string){
			$result = TRUE;
		} else {
			$result = FALSE;	
		}
	}
	result:
	##Display Errors
	if ($display_errors === TRUE){
		if (@empty($errors) === FALSE){
			$message = @implode(", ", $errors);
			if (function_exists('ritchey_check_if_string_is_all_caps_i1_v1_format_error') === FALSE){
				function ritchey_check_if_string_is_all_caps_i1_v1_format_error($errno, $errstr){
					echo $errstr;
				}
			}
			set_error_handler("ritchey_check_if_string_is_all_caps_i1_v1_format_error");
			trigger_error($message, E_USER_ERROR);
		}
	}
	##Display Warnings
	if ($display_warnings === TRUE){
		if (@empty($warnings) === FALSE){
			$warnings = @implode(", ", $warnings);
			echo $warnings;
		}
	}
	##Return
	if (@empty($errors) === TRUE){
		return $result;
	} else {
		return FALSE;
	}
}
}
?>