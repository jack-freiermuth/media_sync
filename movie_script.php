<?php

if(isset($_POST['directory_path'])) {
	$dir = $_POST['directory_path'];
} else {
	$dir = '';
}

$name_change_list = array();
$complete_currnet_list = array();

if (is_dir($dir)) {
	if ($dh = opendir($dir)) {
		while (($current_file_name = readdir($dh)) !== false ) {
			if(!in_array($current_file_name, array('.', '..'))) {

				$clean_file_name = trim(ucwords(str_replace(array('.', '_', '  '), ' ', $current_file_name)));


				$number_regex = array('\d{3,}');
				$file_name_ext_regex = array('m4v', 'avi', 'mkv', 'mp4');

				// $movies_to_ignore = array(
				// 	'?!1941',
				// 	'?!20,000',
				// 	'?!Gravity',
				// );

				$end_of_name = array_merge(/*$number_regex,*/ $file_name_ext_regex, array( '\[', '\(' )/*, $movies_to_ignore*/);
				$end_of_name = implode('|', $end_of_name);
				$file_name_ext_regex = implode('|', $file_name_ext_regex);


				preg_match('/('.$end_of_name.')/', strtolower($clean_file_name), $exception_matches, PREG_OFFSET_CAPTURE);
				preg_match('/(240|360|480|720|1080)/', strtolower($clean_file_name), $bit_rate);
				preg_match('/(19|20)\d{2}/', strtolower($clean_file_name), $movie_year_matches);
				preg_match('/('.$file_name_ext_regex.')/', strtolower($clean_file_name), $current_file_name_ext_matches);
				preg_match('/(extended)/i', strtolower($clean_file_name), $extended_matches);


				// $movies_to_ignore = implode('|', $movies_to_ignore);

				// preg_match('/('.$movies_to_ignore.')/i', strtolower($clean_file_name), $ignore_matches);

				$new_name = array();

				if(!empty($exception_matches)) {
					$first_number_pos = $exception_matches[0][1];
					$new_name['name'] = trim(substr($clean_file_name, 0, $first_number_pos));
				} else {
					$new_name['name'] = $clean_file_name;
				}
				
				if(!empty($extended_matches) && false === strpos(strtolower($new_name['name']), 'extended')) {
					$new_name[] = '- Extended Edition';
				}

				if(!empty($movie_year_matches)) {
					$new_name[] = '('.$movie_year_matches[0].')';
				}

				if(!empty($bit_rate)) {
					$new_name[] = '['.$bit_rate[0].'p]';
				}

				if(!empty($current_file_name_ext_matches)) {
					$new_name[] = '{'.$current_file_name_ext_matches[0].'}';
				}

				
				// if(!empty($ignore_matches)) {
				// 	file_put_contents('C:\wamp64\www\debug_file.txt', "");
				// 	file_put_contents('C:\wamp64\www\debug_file.txt', "\n" ."new_name: " . print_r($new_name, true), FILE_APPEND);
				// 	file_put_contents('C:\wamp64\www\debug_file.txt', "\n\n" ."end_of_name: " . print_r($end_of_name, true), FILE_APPEND);
				// 	file_put_contents('C:\wamp64\www\debug_file.txt', "\n" ."exception_matches: " . print_r($exception_matches, true), FILE_APPEND);
				// 	file_put_contents('C:\wamp64\www\debug_file.txt', "\n" ."extended_matches: " . print_r($extended_matches, true), FILE_APPEND);
				// 	file_put_contents('C:\wamp64\www\debug_file.txt', "\n" ."movie_year_matches: " . print_r($movie_year_matches, true), FILE_APPEND);
				// 	file_put_contents('C:\wamp64\www\debug_file.txt', "\n" ."bit_rate: " . print_r($bit_rate, true), FILE_APPEND);
				// 	file_put_contents('C:\wamp64\www\debug_file.txt', "\n" ."current_file_name_ext_matches: " . print_r($current_file_name_ext_matches, true), FILE_APPEND);
				// }



				$new_name = implode(' ', $new_name);

				// echo '<br><br>old name: ';print_r($current_file_name);
				// echo '<br>new name: ';print_r($new_name);

				if( $current_file_name != $new_name) {
					$name_change_list[$current_file_name] = $new_name;
				} else {
					$complete_currnet_list[] = $current_file_name;
				}


				// rename($dir.$current_file_name, $dir.$name);
			}
		} //End file while loop
		closedir($dh);
	}


	$response_array = array(
		'name_change_list_html' => '',
		'status' => '',
		);

	$new_name_index = 0;
	foreach ($name_change_list as $old_name => $new_name) {
		$response_array['name_change_list_html'] .= '
		<div class="row">
			<label class="col-sm-12 fancy_checkbox" for="name_change_item_'.$new_name_index.'">
				<input id="name_change_item_'.$new_name_index.'" class="col-sm-2 checkbox-movie-name" type="checkbox" value="">
				<div class="col-sm-5 left-cell alert alert-danger">'.$old_name.'</div>
				<div class="col-sm-5 right-cell alert alert-success">'.$new_name.'</div>
			</label>
		</div>
		';
		$new_name_index++;
	} 

	$response_array['status'] = 'success';  
	echo json_encode($response_array);


} else {
	// not a directory
}
