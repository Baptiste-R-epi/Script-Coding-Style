<?php

const IGNORED_FOLDER = [
	"truc",
];


const REPLACE = [
	"/(\w+ \**\w+\([\w\s,*]+\))\s*{/" => "$1\n{",						// put function braces at a new line
	"@(\t+.*?)//.*@" => "$1",											// delete coms:// at end of line starting with tabs
	"/[^\S\n]*(for|if|while)\s*(\([^{}]*\))\s*{\s*}[^\S\n]*\n/" => "",	// delete empty for/if/while
	"/(for|if|while)\s*(\([^{}]*\))\s*{/" => "$1 $2 {",					// put for/if/while braces at the end of line
	"/}\s*else\s*({?)/" => "} else $1",									// manage else braces including else if : } else {
];


function get_all_files($path) {
	global $argv;
	$files = scandir($path);
	$output = [];
	foreach ($files as $file_path) {
		if (
			$file_path[0] == "." ||
			in_array($path . "/" . $file_path, IGNORED_FOLDER)
		)
			continue;
		if (is_dir($path . "/" . $file_path)) {
			array_push($output, ...get_all_files($path . "/" . $file_path));
		} elseif (substr($file_path, -2) == ".c") {
			array_push($output, $path . "/" . $file_path);
		}
	}
	return $output;
}

$all_files = get_all_files(".");

foreach ($all_files as $file_path) {
	file_put_contents(
		$file_path,
		preg_replace(
			array_keys(REPLACE),
			array_values(REPLACE),
			file_get_contents($file_path)
		)
	);
}