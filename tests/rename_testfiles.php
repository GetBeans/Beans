<?php
function read_all_files($baseDirectory = '.'){
	$files  = [];
	$directories  = array();

	$last_letter  = $baseDirectory[strlen($baseDirectory)-1];
	$baseDirectory  = ($last_letter == '\\' || $last_letter == '/') ? $baseDirectory : $baseDirectory
		.DIRECTORY_SEPARATOR;

	$directories[]  = $baseDirectory;

	while (sizeof($directories)) {
		$dir  = array_pop($directories);
		if ($handle = opendir($dir)) {
			while (false !== ($file = readdir($handle))) {
				if ($file == '.' || $file == '..') {
					continue;
				}
				$file  = $dir . $file;
				if (is_dir($file)) {
//					$directory_path = $file.DIRECTORY_SEPARATOR;
//					array_push($directories, $directory_path);
//					$files['dirs'][]  = $directory_path;
					continue;
				} elseif (is_file($file)) {
					$files[]  = 'class-tests-' . basename(mb_strtolower($file));
					rename($file, $baseDirectory . 'class-tests-' . basename(mb_strtolower($file)));
				}
			}
			closedir($handle);
		}
	}

	return $files;
}

//$results = read_all_files('./tests/phpunit/integration/api/actions/');
//$results = read_all_files('./tests/phpunit/integration/api/compiler/');
//$results = read_all_files('./tests/phpunit/integration/api/compiler/beans-compiler');
//$results = read_all_files('./tests/phpunit/integration/api/compiler/beans-compiler-options');
//$results = read_all_files('./tests/phpunit/integration/api/compiler/beans-page-compiler');
//$results = read_all_files('./tests/phpunit/integration/api/fields');
//$results = read_all_files('./tests/phpunit/integration/api/fields/types');
//$results = read_all_files('./tests/phpunit/integration/api/filters');
//$results = read_all_files('./tests/phpunit/integration/api/uikit/');
//$results = read_all_files('./tests/phpunit/integration/api/widget/');
//$results = read_all_files('./tests/phpunit/integration/api/wp-customize/');

//$results = read_all_files('./tests/phpunit/integration/api/actions/');
//$results = read_all_files('./tests/phpunit/integration/api/compiler/');
//$results = read_all_files('./tests/phpunit/integration/api/compiler/beans-compiler');
//$results = read_all_files('./tests/phpunit/integration/api/compiler/beans-compiler-options');
//$results = read_all_files('./tests/phpunit/integration/api/compiler/beans-page-compiler');
$results = read_all_files('./tests/phpunit/unit/api/fields');
$results = read_all_files('./tests/phpunit/unit/api/fields/types');
//$results = read_all_files('./tests/phpunit/integration/api/filters');
//$results = read_all_files('./tests/phpunit/integration/api/uikit/');
//$results = read_all_files('./tests/phpunit/integration/api/widget/');
//$results = read_all_files('./tests/phpunit/integration/api/wp-customize/');

foreach ($results as $result) {
	echo $result . '\n';
}


