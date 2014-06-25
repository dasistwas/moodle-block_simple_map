<?php

// to display as moodle page
require_once dirname(__FILE__)."/../../config.php";
//to read the csv file
require_once($CFG->libdir.'/csvlib.class.php');

//here we store the form to upload the csv file
require_once($CFG->dirroot.'/blocks/simple_map/upload_form.php');

$id         = required_param('id', PARAM_INT);
confirm_sesskey();

$url = new moodle_url('/blocks/simple_map/edit_record.php', array('id'=>$id));
require_login();
$context = get_context_instance(CONTEXT_COURSE, $COURSE->id);
require_capability('moodle/site:manageblocks', $context);

$PAGE->set_url($url);
$PAGE->set_context(context_system::instance());

$STD_FIELDS = array('id', 'title', 'description', 'opening_hours', 'address', 'city', 'area_code', 'region', 'country', 'lng', 'lat', 'category', 'link_1', 'link_2', 'link_3', 'link_4', 'link_5', 'contact' 
        );
$place = new stdClass();

// the name of the table in the database
$table = 'block_simple_map_places';

$navlinks[] = array('name' => "Edit records", 'link' => null, 'type' => 'misc');

$navigation = build_navigation($navlinks);


// get a new form object;
$mform = new edit_record_form(null, array('id'=>$id, 'table'=>$table));



if ($formdata = $mform->get_data()) {
	//print_header_simple("simple_map", $COURSE->fullname, $navigation, "", "", true);
	
	// The editor gives back an Array, we have to transform this to be able to write it to the database.
	$formdata->description = $formdata->description['text'];
	$formdata->opening_hours = $formdata->opening_hours['text'];
	
	
	
	if ($formdata->address && !$formdata->lat && !$formdata->lng && $formdata->fetch_geo_codes) {
		$maps_object = get_lat_lng_by_address($formdata->address, $formdata->city, $formdata->country, $table);
		$formdata->lat = $maps_object->lat;
		$formdata->lng = $maps_object->lng;
	}
	
	unset($formdata->fetch_geo_codes);
	
	
	if ($formdata->id == -1 || $formdata->id == '') {
		$formdata->id = null;
		$DB->insert_record($table, $formdata, $returnid=false, $bulk=false);
	}
	else {
		$formdata->id = $id;
		$DB->update_record($table, $formdata, $bulk=false);
	}
	// Back to list-view
	header("Location: edit_upload_form.php");

	
}
else {
	print_header_simple("simple_map", $COURSE->fullname, $navigation, "", "", true);
	$mform->display();
}
// print_object($mform);



//echo $OUTPUT->footer();


?>