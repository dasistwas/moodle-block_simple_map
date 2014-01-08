<?php


// to display as moodle page
require_once dirname(__FILE__)."/../../config.php";
$example = optional_param('example', false, PARAM_BOOL);

require_login();
confirm_sesskey();
$context = get_context_instance(CONTEXT_COURSE, $COURSE->id);
require_capability('moodle/site:manageblocks', $context);

$table = "block_simple_map_places";

$results = $DB->get_records($table);
if(!empty($results) && !$example){
	$new_array = array_to_csv($results);
} else if ($example || !empty($results)) {
	$new_array = array_to_csv(array( '0' => array('title' => 'enter your title here (ex: name of the library, our the name of the location', 'Description' => 'enter description here (optional)',	'Opening_hours' => 'opening hours (optional)',	'Address' => 'address',	'City' => 'city',	'Area_code'	=> 'area code', 'Country' => 'Austria',	'Region' => 'Vienna (optional)',	'Latitude' =>'latitude (leave empty if unknown)',	'Longitude'=>'longiude (leave empty if unknown)','Category' =>'category  (optional)','Link 1'=>'http://www.yourlinkg.org',	'Link 2'=>'optional link 2', 'Link 3'=>'optional link 3', 'Link 4'=>'optional link 4', 'Link 5'=>'optional link 5',  'Contact' =>'contact information (optional)')));
}

header("Content-type: text/csv; charset=UTF-8");
header("Content-Disposition: attachment; filename=file.csv");
header("Pragma: no-cache");
header("Expires: 0");

outputCSV($new_array);

function outputCSV($data) {
    $outstream = fopen("php://output", "w");
    function __outputCSV(&$vals, $key, $filehandler) {
        fputcsv($filehandler, $vals, ';', '"'); // add parameters if you want
    }
    array_walk($data, "__outputCSV", $outstream);
    fclose($outstream);
}

function array_to_csv($array) {
	$new_array = array();
	foreach ($array as $key => $val) {
		$row = array();
		foreach ($val as $cell_key => $cell_val) {
			if ($cell_key != "id") {
				array_push($row, $cell_val);
			}
		}
		array_push($new_array, $row);
		unset($row);
	}
	return ($new_array);
}



?>