<?php


$dir = 'I:\\Movies\\';
file_put_contents('C:\wamp64\www\debug_file.txt', "\n\n" ."_POST: " . print_r($_POST, true));

$name_change_list = array();
$complete_currnet_list = array();

if (is_dir($dir)) {
	if ($dh = opendir($dir)) {
		while (($current_file_name = readdir($dh)) !== false ) {
			if(!in_array($current_file_name, array('.', '..'))) {

				$clean_file_name = trim(ucwords(str_replace(array('.', '_', '  '), ' ', $current_file_name)));


				$number_regex = array('\d{3,}');
				$file_name_ext_regex = array('m4v', 'avi', 'mkv', 'mp4');
				$exceptions = array_merge($number_regex, $file_name_ext_regex, array( '\[', '\(' ));

				$exceptions = implode('|', $exceptions);
				$file_name_ext_regex = implode('|', $file_name_ext_regex);


				preg_match('/('.$exceptions.')/', strtolower($clean_file_name), $exception_matches, PREG_OFFSET_CAPTURE);
				preg_match('/(240|360|480|720|1080)/', strtolower($clean_file_name), $bit_rate);
				preg_match('/(19|20)\d{2}/', strtolower($clean_file_name), $movie_year_matches);
				preg_match('/('.$file_name_ext_regex.')/', strtolower($clean_file_name), $current_file_name_ext_matches);
				preg_match('/(extended)/i', strtolower($clean_file_name), $extended_matches);

				$new_name = array();

				if(!empty($exception_matches)) {
					$first_number_pos = $exception_matches[0][1];
					$new_name['name'] = trim(substr($clean_file_name, 0, ($first_number_pos)));
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

	foreach ($name_change_list as $old_name => $new_name) {
		$response_array['name_change_list_html'] .= '
		<div class="row">
			<div class="col-sm-6 left-cell alert alert-danger">'.$old_name.'</div>
			<div class="col-sm-6 right-cell alert alert-success">'.$new_name.'</div>
		</div>';
	} 

	$response_array['status'] = 'success';  
	echo json_encode($response_array);


} else {
	// not a directory
}
