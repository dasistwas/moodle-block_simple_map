<?php


//we verify if the script is called from Javascript or PHP

if (!$called_from_php) {
	require_once dirname(__FILE__)."/../../config.php";

	//get the values we transmitted
	$googleAPIkey = required_param('googleAPIkey', PARAM_ALPHANUMEXT);
	$location = required_param('location', PARAM_TEXT);
	$location_type = required_param('location_type', PARAM_ALPHANUMEXT);
	$distance = required_param('distance', PARAM_INT);
	
	// First we clean the values
	$location = trim($location, "(");
	$location = trim($location, ")");
	
	$called_from_php = false;
}

//We have to prevent an error due to calling the script without valid values (This happens i.e. when you change the language of the site via the moodle language menu)

if (!$location || !$location_type || !$distance) {
	$location = "48.2222, 16.38230999999996";
	$location_type = "GEOMETRIC_CENTER";
	$distance = 5000;
}




//we want to know if we should zoom in more or less
if ($location_type == "ROOFTOP") {
	$map_zoom = 14;
	}
elseif ($location_type == "RANGE_INTERPOLATED") {
	$map_zoom = 13;
}
elseif ($location_type == "GEOMETRIC_CENTER") {
	$map_zoom = 13;
}
elseif ($location_type == "APPROXIMATE") {
	$map_zoom = 12;
}

//$courseid = optional_param('courseid', 0, PARAM_INT);

//require_login($courseid);

//$PAGE->set_context(context_system::instance());
//$PAGE->set_url('/blocks/simple_map/get_distances.php');

$table = "block_simple_map_places";

//we fetch all the descriptions we want to have ready for the javascript-file
$labels = array('description' => get_string("description", "block_simple_map"), 'opening_hours' => get_string("opening_hours", "block_simple_map"), 'link_1' => get_string("link_1", "block_simple_map"), 'link_2' => get_string("link_2", "block_simple_map"), 'link_3' => get_string("link_3", "block_simple_map"), 'link_4' => get_string("link_4", "block_simple_map"), 'link_5' => get_string("link_5", "block_simple_map"), 'contact' => get_string("contact", "block_simple_map"), 'distance' => get_string("distance", "block_simple_map"), 'show_more' => get_string("show_more", "block_simple_map"), 'show_less' => get_string("show_less", "block_simple_map" ));

// get all the records from the database
$results = $DB->get_records($table);

// We have to exclude those records which are too far away to optimize performance
// First calculate the direct line between the location and the various destinations

// we have and split the distance value
$location_array = explode(",", $location);
$location_lat = $location_array[0];
$location_lng = $location_array[1];

$i = 0;
foreach ($results as $result) {
	// just make sure we have the right format
	$result->lat = str_replace(",", ".", $result->lat);
	$result->lng = str_replace(",", ".", $result->lng);
	
	// calculate the distance. If the calculation doesn't return a valid result, we want to skip this record.
	if (get_distance($location_lat, $location_lng, $result->lat, $result->lng)) {
		$result->distance = get_distance($location_lat, $location_lng, $result->lat, $result->lng);
	}
	else {
		$result->distance = null;
		unset($results[$i]);
	}
	$i++;		
}


//  now we sort all the values
usort($results, function($a, $b)
{
    if ($a->distance == $b->distance) {
    	return 0;
    }
    elseif ($a->distance > $b->distance) {
    	return 1;
    }
    else {
    	return -1;
    }
});

//take all which are within the given distance, but make sure there are at least five, even if they are too far away
$i = 0;
$j = 0;
foreach ($results as $result) {
	if ($result->distance <= $distance || $j < 5) {
		$j++;
	}
	else {
		unset($results[$i]);
	}
	$i++;
}

// We will return the Jason Header only if the script was called from Javascript
if (!$called_from_php) {
	header("Content-type: application/json");
	echo json_encode($results);
	exit();
}

function get_distance($lat1, $lon1, $lat2, $lon2) { 
  	$theta = $lon1 - floatval($lon2); 
  	$dist = sin(deg2rad($lat1)) * sin(deg2rad(floatval($lat2))) +  cos(deg2rad($lat1)) * cos(deg2rad(floatval($lat2))) * cos(deg2rad($theta)); 
  	$dist = acos($dist); 
  	$dist = rad2deg($dist); 
  	$meters = ($dist * 60 * 1.1515 * 1.609344)*1000;
	if (floatval($meters)) {
		return floatval($meters);
	}
	else {
		return null;
	}
}

?>