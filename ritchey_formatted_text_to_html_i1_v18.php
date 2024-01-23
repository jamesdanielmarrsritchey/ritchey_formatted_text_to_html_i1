<?php
#Name:Ritchey Formatted Text To HTML i1 v18
#Description:Convert text (written using the custom formatting defined in this document) to HTML. Returns "TRUE" on success. Returns "FALSE" on failure.
#Notes:Optional arguments can be "NULL" to skip them in which case they will use default values.
#Arguments:'source' (required) is the file to read from. 'destination' (required) the path of where to write the HTML file. 'theme_file' (optional) is a path to a css file import into the HTML. 'flat_list_important' (optional) is a number indicating how many items in a flat list should be identified as important. 'display_errors' (optional) indicates if errors should be displayed.
#Arguments (Script Friendly):source:file:required,destination:file:required,theme_file:file:optional,flat_list_important:number:optional,display_errors:bool:optional
#Content:
#<value>
if (function_exists('ritchey_formatted_text_to_html_i1_v18') === FALSE){
function ritchey_formatted_text_to_html_i1_v18($source, $destination, $theme_file = NULL, $flat_list_important = NULL, $display_errors = NULL){
	$errors = array();
	$location = realpath(dirname(__FILE__));
	if (@is_file($source) === FALSE){
		$errors[] = "source";
	}
	if (@is_dir(@dirname($source)) === FALSE){
		$errors[] = 'destination';
	}
	if ($theme_file === NULL){
		$theme_file = "{$location}/custom_dependencies/ritchey-document-theme-i1-v1.css";
	} else if (@is_file($theme_file) === TRUE){
		//Do nothing
	} else {
		$errors[] = "theme_file";
	}
	if ($flat_list_important === NULL){
		$flat_list_important = 5;
	} else if (is_int($flat_list_important) === TRUE){
		#Do Nothing
	} else {
		$errors[] = "flat_list_important";
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
	##Task
	if (@empty($errors) === TRUE){
		###Import custom functions
		$location = realpath(dirname(__FILE__));
		require_once $location . '/custom_dependencies/ritchey_check_if_string_is_all_caps_i1_v1/ritchey_check_if_string_is_all_caps_i1_v1.php';
		###Import CSS
		$css = @file_get_contents($theme_file);
		//###Determine how many sections there are. #text (title page) and ##text (Level 1 heading) define the start of a new section.
		//$n1 = 0;
		//$handle = @fopen($source, 'r');
		//while (@feof($handle) !== TRUE) {
		//	####Get line from file
		//	$current_line = @fgets($handle);
		//	#####Check for # or ##.
		//	if (substr($current_line, 0, 3) !== '###'){
		//		if (substr($current_line, 0, 1) === '#'){
		//			$n1++;
		//		}
		//	}
		//}
		//@fclose($handle);
		//####Check that there is at least one section. If not, error.
		//if ($n1 < 1){
		//	$errors[] = "task(1)";
		//	goto result;
		//}
		###Import Text
		$n2 = 0;
		$text = array();
		$handle = @fopen($source, 'r');
		while (@feof($handle) !== TRUE) {
			####Get line from file
			$current_line = @fgets($handle);
			$current_line = rtrim($current_line, "\n\r\v");
			if (substr($current_line, 2, 1) !== '#' AND substr($current_line, 0, 2) === '##'){
				$n2++;
			} else if (substr($current_line, 1, 1) !== '#' AND substr($current_line, 0, 1) === '#'){
				$n2++;
			}
			if (isset($text[$n2]) === TRUE){
				$text[$n2] = $text[$n2] . $current_line . PHP_EOL;
			} else {
				$text[$n2] = $current_line . PHP_EOL;
			}
		}
		@fclose($handle);
		$html_content_div = $text;
		###Process Text
		foreach ($html_content_div as &$item1){
			$segment_name = NULL;
			$item1 = explode(PHP_EOL, $item1);
			####Process segment for deletions
			foreach ($item1 as &$item2){
				#####Remove empty lines
				if ($item2 === ''){
					$item2 = '';
				}
				#####Remove commented out lines
				else if (substr($item2, 0, 2) === '//'){
					$item2 = '';
				}
			}
			unset($item2);
			$item1 = array_filter($item1);
			####Process segment for starting elements, or enhancements (These will occur at the start of the line, but may have other content after which needs to be preserved.)
			foreach ($item1 as &$item2){
				#####Replace /t with a DIV
				if (substr($item2, 0, 1) === "\t"){
					$item2 = "<div class=\"tab\"></div>" . substr($item2, 1);
				}
				/////These next 2 items should really be moved into the next section since they don't preserve the content.
				#####Replace " " with a BR
				else if ($item2 === " "){
					$item2 = "<br class='br_1'>";
				}
				#####Replace "  " with a BR
				else if ($item2 === "  "){
					$item2 = "<br class='br_2'>";
				}
			}
			unset($item2);
			####Process segment for elements (These will be at the beginning of a line, with nothing else aftewards, so there is no need to preserve the rest of the line)
			foreach ($item1 as &$item2){
				#####Replace # with DIV for title
				if (substr($item2, 1, 1) !== '#' AND substr($item2, 0, 1) === '#'){
					$id = strtolower(substr($item2, 1));
					$id = preg_replace("/[^A-Za-z0-9 ]/", '', $id);
					$id = preg_replace("/ /", '_', $id);
					$id = trim($id);
					//Update segment name. While not used for this replacement, the information is used by other replacements.
					$segment_name = $id;
					$item2 = "<div class=\"title\" id=\"title_{$id}\">" . substr($item2, 1) . "</div>";
				}
				#####Replace ## with h1
				else if (substr($item2, 2, 1) !== '#' AND substr($item2, 0, 2) === '##'){
					$id = strtolower(substr($item2, 2));
					$id = preg_replace("/[^A-Za-z0-9 ]/", '', $id);
					$id = preg_replace("/ /", '_', $id);
					$id = trim($id);
					//Update segment name. While not used for this replacement, the information is used by other replacements.
					$segment_name = $id;
					$item2 = "<h1 class=\"heading_1\" id=\"heading_1_{$id}\">" . substr($item2, 2) . "</h1>";
				}
				#####Replace ### with h2
				else if (substr($item2, 3, 1) !== '#' AND substr($item2, 0, 3) === '###'){
					$id = strtolower(substr($item2, 3));
					$id = preg_replace("/[^A-Za-z0-9 ]/", '', $id);
					$id = preg_replace("/ /", '_', $id);
					$id = trim($id);
					$item2 = "<h2 class=\"heading_2\" id=\"heading_2_{$id}\">" . substr($item2, 3) . "</h2>";
				}
				#####Replace #### with h3
				else if (substr($item2, 4, 1) !== '#' AND substr($item2, 0, 4) === '####'){
					$id = strtolower(substr($item2, 4));
					$id = preg_replace("/[^A-Za-z0-9 ]/", '', $id);
					$id = preg_replace("/ /", '_', $id);
					$id = trim($id);
					$item2 = "<h3 class=\"heading_3\" id=\"heading_3_{$id}\">" . substr($item2, 4) . "</h3>";
				}
				#####Replace ##### with h4
				else if (substr($item2, 5, 1) !== '#' AND substr($item2, 0, 5) === '#####'){
					$id = strtolower(substr($item2, 5));
					$id = preg_replace("/[^A-Za-z0-9 ]/", '', $id);
					$id = preg_replace("/ /", '_', $id);
					$id = trim($id);
					$item2 = "<h4 class=\"heading_4\" id=\"heading_4_{$id}\">" . substr($item2, 5) . "</h4>";
				}
				#####Replace ###### with h5
				else if (substr($item2, 6, 1) !== '#' AND substr($item2, 0, 6) === '######'){
					$id = strtolower(substr($item2, 6));
					$id = preg_replace("/[^A-Za-z0-9 ]/", '', $id);
					$id = preg_replace("/ /", '_', $id);
					$id = trim($id);
					$item2 = "<h5 class=\"heading_5\" id=\"heading_5_{$id}\">" . substr($item2, 6) . "</h5>";
				}
				#####Replace ####### with h6
				else if (substr($item2, 7, 1) !== '#' AND substr($item2, 0, 7) === '#######'){
					$id = strtolower(substr($item2, 7));
					$id = preg_replace("/[^A-Za-z0-9 ]/", '', $id);
					$id = preg_replace("/ /", '_', $id);
					$id = trim($id);
					$item2 = "<h6 class=\"heading_6\" id=\"heading_6_{$id}\">" . substr($item2, 7) . "</h6>";
				}
				#####Replace flat list with UL/LI. A flat list can only contain letters, numbers, and spaces. ' | ' is used to separate entries in the list (e.g., "item1 | item2 | item3").
				else if (is_int(strpos($item2, ' | ')) === TRUE AND strlen($item2) === strlen(preg_replace("/[^A-Za-z0-9 |]/", '', $item2))){
					$item2 = explode('|', $item2);
					$n3 = 0;
					foreach ($item2 as &$item3){
						$n3++;
						$item3 = trim($item3);
						if($n3 <= $flat_list_important) {
							$item3 = "<li class=\"flat_list_item_important\">" . $item3 . "</li>" . PHP_EOL;
						} else {
							$item3 = "<li class=\"flat_list_item\">" . $item3 . "</li>" . PHP_EOL;
						}
					}
					unset($item3);
					$item2 = "<ul class=\"flat_list\">" . PHP_EOL . implode($item2) . "</ul>";
				}
				#####Replace dot list portion with DIV. A dot list starts with spaces, then a dash, followed by a space, and then some text (e.g., ' - Text', "  - Text").
				else if (strpos(trim($item2), '-') === 0 AND strpos(trim($item2), '-', 1) !== 1){
					$class = strpos($item2, '-');
					$start = strpos($item2, '- ') + 2;
					$item2 = "<div class=\"dot_list_{$class}_item\">" . "<div class=\"dot_list_{$class}_item_indicator\">&#x2022; </div>" . substr($item2, $start) . PHP_EOL . "</div>";
				}
				#####Replace notices with DIV. Notices start with at least two dashes followed by a space, and end the same way (e.g., "-- Text --" or "--- Text ---").
				else if (strpos(trim($item2), '--') === 0){
					$class = strpos($item2, '-- ') + 1;
					$start = strpos($item2, '-- ') + 3;
					$end = strpos($item2, ' --');
					$length = $end - $start;
					$item2 = "<div class=\"notice_{$class}\">" . PHP_EOL . "<div class=\"notice_{$class}_item\">" . substr($item2, $start, $length) . "</div>" . PHP_EOL . "</div>";
				}
				#####Replace tags with UL/LI
				else if (substr($item2, 0, 1) === "[" AND is_int(strpos($item2, ']')) === TRUE){
					$item2 = explode('|', $item2);
					foreach ($item2 as &$item4){
						$item4 = trim($item4);
						$item4 = substr($item4, 1, -1);
							$item4 = "<li class=\"tags_item\">" . $item4 . "</li>" . PHP_EOL;
					}
					unset($item4);
					$item2 = "<ul class=\"tags\">" . PHP_EOL . implode($item2) . "</ul>";
				}
				#####Replace label with Div. A label is a string of capitalized text as the start of a line that ends with a colon which is followed by more content (e.g., "LABEL: value").
				else if (is_int(strpos($item2, ': ')) === TRUE AND ctype_upper(preg_replace("/[^[:alpha:]]/", "", substr($item2, 0, strpos($item2, ':')))) === TRUE AND is_int(strpos($item2, '- ')) !== TRUE){
					$start = strpos($item2, ':');
					$start2 = $start + 1;
					$item2 = "<div class=\"label_1\">" . PHP_EOL . "<div class=\"label_1_name\">" . ucwords(strtolower(substr($item2, 0, $start))) . "</div>" . "<div class=\"label_1_indicator\">:&nbsp;</div>" . "<div class=\"label_1_value\">" . substr($item2, $start2) . "</div>" . PHP_EOL . "</div>";
				}
				#####Replace label with Div. A label is a string of capitalized text as the start of a line that ends with a colon (e.g., "LABEL:").
				else if (is_int(strpos($item2, ':')) === TRUE AND ctype_upper(preg_replace("/[^[:alpha:]]/", "", substr($item2, 0, strpos($item2, ':')))) === TRUE AND is_int(strpos($item2, '- ')) !== TRUE){
					$start = strpos($item2, ':');
					$start2 = $start + 1;
					$item2 = "<div class=\"label_3\">" . PHP_EOL . "<div class=\"label_3_name\">" . ucwords(strtolower(substr($item2, 0, $start))) . "</div>" . "<div class=\"label_3_indicator\">:&nbsp;</div>" . "<div class=\"label_3_value\">" . substr($item2, $start2) . "</div>" . PHP_EOL . "</div>";
				}
				#####If not an element, and doesn't contain a br tag, but is in a section titled "References" wrap as a paragraph with a special class.
				else if (is_int(strpos($item2, '<br class=')) !== TRUE AND $segment_name === 'references') {
						$item2 = "<div class=\"references_1\">{$item2}</div>";
				}
				#####If not an element, and doesn't contain a br tag, but is in a section titled "Works Cited" wrap as a paragraph with a special class.
				else if (is_int(strpos($item2, '<br class=')) !== TRUE AND $segment_name === 'works_cited') {
						$item2 = "<div class=\"references_1\">{$item2}</div>";
				}
				#####If not an element, and doesn't contain a br tag, but is in a section titled "Bibliography" wrap as a paragraph with a special class.
				else if (is_int(strpos($item2, '<br class=')) !== TRUE AND $segment_name === 'bibliography') {
						$item2 = "<div class=\"references_1\">{$item2}</div>";
				}
				#####If not an element, doesn't contain a br tag, and doesn't contain a label, wrap as a paragraph.
				else {
					if (is_int(strpos($item2, '<br class=')) !== TRUE AND is_int(strpos($item2, '<div class="label')) !== TRUE){
						
						if ($segment_name !== ''){
							$item2 = "<div class=\"paragraph_1\" data-segment-name=\"{$segment_name}\">{$item2}</div>";
						} else {
							$item2 = "<div class=\"paragraph_1\">{$item2}</div>";
						}
					}
				}
			}
			unset($item2);
			####Process segment for enhancements (These may occur anywhere in the line, so it's important only the portion of text is replaced)
			foreach ($item1 as &$item2){
				#####Links with A element
				if (is_int(strpos($item2, ')}')) === TRUE){
					$item2 = str_replace(")}", ")}|", $item2);
					$item2 = explode('|', $item2);
					foreach ($item2 as &$item5){
						if (is_int(strpos($item5, ')}')) === TRUE){
							//Get start
							@preg_match('/\{.*\(.*\)\}/', $item5, $matches, PREG_OFFSET_CAPTURE);
							if (@empty($matches) === TRUE){
								$start = 0;
							} else {
								$start = $matches[0][1];
							}
							//Get end
							$end = @strpos($item5, ')}');
							$end = $end + 2;
							$length = $end - $start;
							//Replace with element
							$url_text_and_url = substr($item5, $start, $length);
							$url_text_and_url = substr($url_text_and_url, 1, -2);
							$url_text_and_url = explode(' (', $url_text_and_url);
							$part1 = substr($item5, 0, $start);
							$part2 = "<a class=\"link_1\" href=\"{$url_text_and_url[1]}\" target=\"_blank\">{$url_text_and_url[0]}</a>";
							$part3 = substr($item5, $end);
							$item5 = $part1 . $part2 . $part3;
						}	
					}
					unset($item5);
					$item2 = implode($item2);
				}
				#####Replace PNG images with base64 encoded image. Images are a path wrapped in "()". Alternatively, if no path is specified, it will assume the image is in the same place as the source text. There can be multiple images in a line. There can be other content in the line.
				if (is_int(strpos($item2, '.png')) === TRUE AND is_int(strpos($item2, '(')) === TRUE AND strpos($item2, '{') === FALSE){
					$item2 = str_replace(".png)", ".png)|", $item2);
					$item2 = explode('|', $item2);
					foreach ($item2 as &$item6){
						if (is_int(strpos($item6, '.png')) === TRUE){
							//Get start
							@preg_match('/\(.*.png\)/', $item6, $matches, PREG_OFFSET_CAPTURE);
							if (@empty($matches) === TRUE){
								$start = 0;
							} else {
								$start = $matches[0][1];
							}
							//Get end
							$end = @strpos($item6, '.png)');
							$end = $end + 5;
							$length = $end - $start;
							//import the image data to a variable and convert to base64
							$image_path = substr($item6, $start, $length);
							$image_path = substr($image_path, 1, -1);
							if (is_int(strpos($image_path, '/')) !== TRUE){
								$image_path = dirname($source) . '/' . $image_path;
							}
							if (is_file($image_path) === TRUE){
								//get first part
								$part1 = substr($item6, 0, $start);
								//replace middle
								$image_data = base64_encode(file_get_contents($image_path));
								$part2 = "<img class=\"image_1\" src=\"data:image/png;charset=utf-8;base64,{$image_data}\">";
								//get end
								$part3 = substr($item6, $end);
								//combine parts
								$item6 = $part1 . $part2 . $part3;
							}
						}	
					}
					unset($item6);
					$item2 = implode($item2);
				}
				#####Replace JPG images with base64 encoded image. Images are a path wrapped in "()". Alternatively, if no path is specified, it will assume the image is in the same place as the source text. There can be multiple images in a line. There can be other content in the line.
				if (is_int(strpos($item2, '.jpg')) === TRUE AND is_int(strpos($item2, '(')) === TRUE AND strpos($item2, '{') === FALSE){
					$item2 = str_replace(".jpg)", ".jpg)|", $item2);
					$item2 = explode('|', $item2);
					foreach ($item2 as &$item6){
						if (is_int(strpos($item6, '.jpg')) === TRUE){
							//Get start
							@preg_match('/\(.*.jpg\)/', $item6, $matches, PREG_OFFSET_CAPTURE);
							if (@empty($matches) === TRUE){
								$start = 0;
							} else {
								$start = $matches[0][1];
							}
							//Get end
							$end = @strpos($item6, '.jpg)');
							$end = $end + 5;
							$length = $end - $start;
							//import the image data to a variable and convert to base64
							$image_path = substr($item6, $start, $length);
							$image_path = substr($image_path, 1, -1);
							if (is_int(strpos($image_path, '/')) !== TRUE){
								$image_path = dirname($source) . '/' . $image_path;
							}
							if (is_file($image_path) === TRUE){
								//get first part
								$part1 = substr($item6, 0, $start);
								//replace middle
								$image_data = base64_encode(file_get_contents($image_path));
								$part2 = "<img class=\"image_1\" src=\"data:image/jpeg;charset=utf-8;base64,{$image_data}\">";
								//get end
								$part3 = substr($item6, $end);
								//combine parts
								$item6 = $part1 . $part2 . $part3;
							}
						}	
					}
					unset($item6);
					$item2 = implode($item2);
				}
				#####Replace dot list label with Div. A label is a string of capitalized text as the start of a dot list line that ends with a colon (e.g., " - LABEL:").
				if (is_int(strpos($item2, ':')) === TRUE AND is_int(strpos($item2, 'dot_list')) === TRUE AND is_int(strpos($item2, ': ')) === FALSE){
					$start = strpos($item2, '&#x2022; </div>') + 15;
					$end = strpos($item2, ':');
					$start_2 = $end + 1;
					$length = $end - $start;
					if (ritchey_check_if_string_is_all_caps_i1_v1(substr($item2, $start, $length), TRUE, FALSE, FALSE, FALSE) === TRUE) {
						$item2 = substr($item2, 0, $start) . "<div class=\"label_2\">" . "<div class=\"label_2_name\">" . ucwords(strtolower(substr($item2, $start, $length))) . "</div>" . "<div class=\"label_2_indicator\">: </div>" . substr($item2, $start_2) . "</div>";
					}
				}
				#####Warnings
				$item2 = str_replace("(Expired)", "<div class=\"text_warning\">(Expired)</div>", $item2);
				$item2 = str_replace("(Expiring)", "<div class=\"text_caution\">(Expiring)</div>", $item2);
				#####Good
				$item2 = str_replace("(Citizen)", "<div class=\"text_good\">(Citizen)</div>", $item2);
				$item2 = str_replace("(Expires)", "<div class=\"text_good\">(Expires)</div>", $item2);
				#####Other
				$item2 = str_replace("(Preferred)", "<div class=\"text_preferred\">(Preferred)</div>", $item2);
			}
			unset($item2);
			####Convert segment to string
			$item1 = implode(PHP_EOL, $item1);
			####Get segment ID and segment type
			$type = '';
			if (is_int(strpos($item1, 'class="title"')) === TRUE) {
				//Check if there is a title element
				$start = strpos($item1, '>');
				$end = strpos($item1, '<', 2);
				$length = $end - $start;
				$id = substr($item1, $start, $length);
				$id = strtolower($id);
				$id = preg_replace("/[^A-Za-z0-9 ]/", '', $id);
				$id = preg_replace("/ /", '_', $id);
				$id = trim($id);
				$type = '_title';
			} else if (is_int(strpos($item1, 'class="heading_1"')) === TRUE) {
				//Check if there is an h1 heading element
				$start = strpos($item1, '>');
				$end = strpos($item1, '<', 2);
				$length = $end - $start;
				$id = substr($item1, $start, $length);
				$id = strtolower($id);
				$id = preg_replace("/[^A-Za-z0-9 ]/", '', $id);
				$id = preg_replace("/ /", '_', $id);
				$id = trim($id);
			} else {
				$id = FALSE;			
			}
			####Wrap segment with inner/outter divs. It also needs to be identified as either a title segment, or regular segment by class. This is important, because some themes may make segments with titles be on their own page.
			if ($id === FALSE){
				$item1 = "<div class=\"section_outter{$type}\">" . PHP_EOL . "<div class=\"section_inner{$type}\">" . PHP_EOL . $item1 . PHP_EOL . "</div>" . PHP_EOL . "</div>";
			} else {
				$item1 = "<div class=\"section_outter{$type}\" id=\"section_outter_{$id}\">" . PHP_EOL . "<div class=\"section_inner{$type}\" id=\"section_inner_{$id}\">" . PHP_EOL . $item1 . PHP_EOL . "</div>" . PHP_EOL . "</div>";
			}
		}
		unset($item1);
		$html_content_div[] = "<div class='page_heading1'>&#8706; </div>";
		$html_content_div[] = "<div class='page_footer1'>&#8706; </div>";
		###Create HTML document
		$part1 = <<<HEREDOC
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Document</title>
<style>
{$css}
</style>
</head>
<body>
HEREDOC;
		$html_content_div = implode(PHP_EOL, $html_content_div);
		$part2 = <<<HEREDOC
</body>
</html>
HEREDOC;
		$html = $part1 . PHP_EOL . $html_content_div . PHP_EOL . $part2;
		file_put_contents($destination, $html);
	}
	result:
	##Display Errors
	if ($display_errors === TRUE){
		if (@empty($errors) === FALSE){
			$message = @implode(", ", $errors);
			if (function_exists('ritchey_formatted_text_to_html_i1_v18_format_error') === FALSE){
				function ritchey_formatted_text_to_html_i1_v18_format_error($errno, $errstr){
					echo $errstr;
				}
			}
			set_error_handler("ritchey_formatted_text_to_html_i1_v18_format_error");
			trigger_error($message, E_USER_ERROR);
		}
	}
	##Return
	if (@empty($errors) === TRUE){
		return TRUE;
	} else {
		return FALSE;
	}
}
}
#</value>
?>