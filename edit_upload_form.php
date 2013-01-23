<?php

// to display as moodle page
require_once dirname(__FILE__)."/../../config.php";

//to read the csv file
require_once($CFG->libdir.'/csvlib.class.php');

//here we store the form to upload the csv file
require_once('upload_form.php');

require_login();
$context = get_context_instance(CONTEXT_COURSE, $COURSE->id);
require_capability('moodle/site:config', $context);

$PAGE->set_context(context_system::instance());
$PAGE->set_url('/blocks/simple_map/edit_upload_form.php');

$place = new stdClass();

// the name of the table in the database
$table = 'block_simple_map_places';

$navlinks[] = array('name' => "View list of places", 'link' => null, 'type' => 'misc');

$navigation = build_navigation($navlinks);
print_header_simple("simple_map", $COURSE->fullname, $navigation, "", "", true);

$mform = new select_file_form();


if ($formdata = $mform->get_data()) {

	$iid = csv_import_reader::get_new_iid('simple_map');
	$cir = new csv_import_reader($iid, 'simple_map');

	$content = $mform->get_file_content('userfile');

	$readcount = $cir->load_csv_content($content, 'UTF-8', 'semicolon');

	$columns = $cir->get_columns();
	//$result = $DB->delete_records($table);

	$data = array();
	$cir->init();
	$linenum = 0; //column header is first line
	$noerror = true; // Keep status of any error.
	$lines_in_database = $DB->count_records($table);
	while ($linenum <= $readcount and $fields = $cir->next()) {
		 
		$result = null;
		//we create the place Object
		$place->title        	= $fields[0];
		$place->description     = $fields[1];
		$place->opening_hours   = $fields[2];
		$place->address         = $fields[3];
		$place->city         	= $fields[4];
		$place->area_code       = $fields[5];
		$place->country         = $fields[6];
		$place->region         	= $fields[7];
		$place->lat         	= $fields[8];
		$place->lng         	= $fields[9];
		$place->category		= $fields[10];
		$place->link_1			= $fields[11];
		$place->link_2			= $fields[12];
		$place->link_3			= $fields[13];
		$place->link_4			= $fields[14];
		$place->link_5			= $fields[15];
		$place->contact			= $fields[16];
		 
		 
		// is this record already in the database?
		 
		if ($results = $DB->get_records($table,array('title'=>$place->title))) {

			foreach ($results as $result) {
				$place->id = $result->id;
				 
				// make sure the lat and long values are with ".", not with ","
				if ($place->lng || $place->lat) {
					$place->lat = str_replace(",", ".", $place->lat);
					$place->lng = str_replace(",", ".", $place->lng);
				}
				$DB->update_record($table, $place, $bulk=false);
			}
		}
		else {
			// First we check, if the Geo-Coordinates are already submitted. If not, we fetch them now, if there is an address to do so
			if ($place->address && !$place->lat && !$place->lng) {
				$object = get_lat_lng_by_address($place->address, $place->city, $place->country, $table);
				$place->lat = $object['lat'];
				$place->lng = $object['long'];
			}

			$lines_in_database++;
			//$place->id			= $lines_in_database;
			$DB->insert_record($table, $place);
		}
		$linenum++;
	}
	//unset($content);

}

echo get_string('upload_instructions', 'block_simple_map').'
<br /><br />
<a href="export_table.php?sesskey='.sesskey().'&example=true">Download sample csv file (first line only)</a>
<br />
<a href="export_table.php?sesskey='.sesskey().'">'.get_string('download_instructions', 'block_simple_map')."</a>";

//load all the records from the database and display them in a table

//$results = $DB->delete_records($table);
$results = $DB->get_records($table);

echo '<table id="block_simple_map_editentries" border="1"><tr>
<th>ID</th>
<th>Title and description, etc.</th>
<th>
<a href="delete_record.php?id=-2&sesskey='.sesskey().'">Delete all</a>
</th>
</tr>';
foreach ($results as $result) {

	$class = "";
	//before we display the list, we check once again if there are the geo-coordinates available:
	if ($result->address && !$result->lat && !$result->lng) {
		$object = get_lat_lng_by_address($result->address, $result->city, $result->country, $table);
		if (!$object) {
			$class = "class ='red'";
		}
		else {
			$result->lat = $object['lat'];
			$result->lng = $object['long'];
			// and we update the database
			$DB->update_record($table, $result, $bulk=false);
		}
	}

	echo '<tr '.$class.'>';
	echo '
	<td><a href="edit_record.php?id='.$result->id.'&sesskey='.sesskey().'">'.$result->id.' edit</a></td>
	<td><div style="font-weight: bold;">Title: '.$result->title.'</div><div>'.$result->description.'</div>
	<div>'.$result->opening_hours.'</div>
	<div>'.$result->address.'<br />'.$result->city.'<br />'.$result->area_code.'<br />'.$result->country.'<br />'.$result->region.'</div>
	<div>'.$result->lat.', '.$result->lng.'</div>
	<div>'.$result->category.'</div>
	<div>'.$result->link_1.'</div>
	<div>'.$result->link_2.'</div>
	<div>'.$result->link_3.'</div>
	<div>'.$result->link_4.'</div>
	<div>'.$result->link_5.'</div>
	<div>'.$result->contact.'</div></td>
	<td><a href="delete_record.php?id='.$result->id.'&sesskey='.sesskey().'">delete</a></td>';
	echo '</tr>';
}
echo '</table>';

echo '<a href="edit_record.php?id=-1&sesskey='.sesskey().'">'.get_string('insert_new_record', 'block_simple_map')."</a>";


$mform->display();
echo $OUTPUT->footer();

?>