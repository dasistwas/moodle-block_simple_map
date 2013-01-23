<?php


// to display as moodle page
require_once dirname(__FILE__)."/../../config.php";
$id = required_param('id', PARAM_INT);
confirm_sesskey();

require_login();
$context = get_context_instance(CONTEXT_COURSE, $COURSE->id);
require_capability('moodle/site:config', $context);

// the name of the table in the database
$table = 'block_simple_map_places';

if ($id === -2) {
	$DB->delete_records($table);
} else if (is_int($id) && $id != 0){
	// delete the record
	$DB->delete_records($table, array('id'=>$id));
} else {
	print_error('No correct id specified');
}

header("Location: edit_upload_form.php");



?>