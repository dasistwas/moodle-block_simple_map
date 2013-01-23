<?php
 
defined('MOODLE_INTERNAL') || die();

require_once $CFG->libdir.'/formslib.php';


class select_file_form extends moodleform {

    function definition() {

        global $CFG;
        
        $maxbytes = 2097152; // 2 MB
        
        $mform =& $this->_form;
        $mform->addElement('filepicker', 'userfile', get_string('labeltext','block_simple_map'), null, array('maxbytes' => $maxbytes,   'accepted_types' => '*'));
        
        $this->add_action_buttons(false, 'Load');
    }
}


class edit_record_form extends moodleform {
    
    function definition() {
		global $CFG, $OUTPUT, $DB;;
        
        
        //this is the way to get the record id
    	
    	
    	$mform =& $this->_form;
    	
    	
    	// We get the record_id and the name of the table
    	if ($this->_customdata['id']) {
    		$record_id = $this->_customdata['id'];
    		$table = $this->_customdata['table'];
    	}
    	// We have to make sure that some values exist to create the form
    	else {
    		die();
    		//$record_id = 1;
    		//$table = "block_simple_map_places";
    	}
    	
    	if ($record_id == -1)  {
    		
    		$mform->addElement('hidden', 'id',$record_id);
    		$mform->setType('id', PARAM_INT);
    		    	
    		$mform->addElement('text', 'title', get_string('title', 'block_simple_map'));
    		$mform->setDefault('title', '');
    		$mform->setType('title', PARAM_TEXT);
    		
    		$mform->addElement('editor', 'description', get_string('description', 'block_simple_map'), 
    			null,
   				array('context' => "")
			)->setValue( array('text' => ''));
   
    		
    		$mform->addElement('editor', 'opening_hours', get_string('opening_hours', 'block_simple_map'), 
    			null,
   				array('context' => "")
			)->setValue( array('text' => ''));
    		
    		$mform->addElement('text', 'address', get_string('address', 'block_simple_map'));
    		$mform->setDefault('address', '');
    		$mform->setType('address', PARAM_TEXT);
    		
    		$mform->addElement('text', 'city', get_string('city', 'block_simple_map'));
    		$mform->setDefault('city', '');
    		$mform->setType('city', PARAM_TEXT);
    		
    		$mform->addElement('text', 'area_code', get_string('area_code', 'block_simple_map'));
    		$mform->setDefault('area_code', '');
    		$mform->setType('area_code', PARAM_TEXT);
    		
    		$mform->addElement('text', 'country', get_string('country', 'block_simple_map'));
    		$mform->setDefault('country', '');
    		$mform->setType('country', PARAM_TEXT);
    		
    		$mform->addElement('text', 'region', get_string('region', 'block_simple_map'));
    		$mform->setDefault('region', '');
    		$mform->setType('region', PARAM_TEXT);
    		
    		$mform->addElement('advcheckbox', 'fetch_geo_codes', get_string('fetch_geo_codes', 'block_simple_map'));
    		$mform->setDefault('fetch_geo_codes', true);
    		
    		$mform->addElement('text', 'lat', get_string('lat', 'block_simple_map'));
    		$mform->setDefault('lat', '');
    		$mform->setType('lat', PARAM_TEXT);
    		
    		$mform->addElement('text', 'lng', get_string('lng', 'block_simple_map'));
    		$mform->setDefault('lng', '');
    		$mform->setType('lng', PARAM_TEXT);
    		
    		$mform->addElement('text', 'category', get_string('category', 'block_simple_map'));
    		$mform->setDefault('category', '');
    		$mform->setType('category', PARAM_TEXT);
    		
    		$mform->addElement('text', 'link_1', get_string('link_1', 'block_simple_map'));
    		$mform->setDefault('link_1', '');
    		$mform->setType('link_1', PARAM_TEXT);
    		
    		$mform->addElement('text', 'link_2', get_string('link_2', 'block_simple_map'));
    		$mform->setDefault('link_2', '');
    		$mform->setType('link_2', PARAM_TEXT);
    		
    		$mform->addElement('text', 'link_3', get_string('link_3', 'block_simple_map'));
    		$mform->setDefault('link_3', '');
    		$mform->setType('link_3', PARAM_TEXT);
    		
    		$mform->addElement('text', 'link_4', get_string('link_4', 'block_simple_map'));
    		$mform->setDefault('link_4', '');
    		$mform->setType('link_4', PARAM_TEXT);
    		
    		$mform->addElement('text', 'link_5', get_string('link_5', 'block_simple_map'));
    		$mform->setDefault('link_5', '');
    		$mform->setType('link_5', PARAM_TEXT);
    		
    		$mform->addElement('text', 'contact', get_string('contact', 'block_simple_map'));
    		$mform->setDefault('contact', '');
    		$mform->setType('contact', PARAM_TEXT);
    		
    		$this->add_action_buttons(true, get_string('savechanges'));
    	
    	}
    	else {
    	//we fetch the record from the database
    	$results = $DB->get_records($table,array('id'=>$record_id));
    	
    	foreach ($results as $result) {
    		
    		$mform->addElement('hidden', 'id',$record_id);
    		$mform->setType('id', PARAM_INT);
    		
    		$mform->addElement('text', 'title', get_string('title', 'block_simple_map'));
    		$mform->setDefault('title', $result->title);
    		$mform->setType('title', PARAM_ALPHANUM);
    		
    		$mform->addElement('editor', 'description', get_string('description', 'block_simple_map'), 
    			null,
   				array('context' => "")
			)->setValue( array('text' => $result->description));
   
    		
    		$mform->addElement('editor', 'opening_hours', get_string('opening_hours', 'block_simple_map'), 
    			null,
   				array('context' => "")
			)->setValue( array('text' => $result->opening_hours));
    		
    		$mform->addElement('text', 'address', get_string('address', 'block_simple_map'));
    		$mform->setDefault('address', $result->address);
    		$mform->setType('address', PARAM_TEXT);
    		
    		$mform->addElement('text', 'city', get_string('city', 'block_simple_map'));
    		$mform->setDefault('city', $result->city);
    		$mform->setType('city', PARAM_ALPHANUM);
    		
    		$mform->addElement('text', 'area_code', get_string('area_code', 'block_simple_map'));
    		$mform->setDefault('area_code', $result->area_code);
    		$mform->setType('area_code', PARAM_ALPHANUM);
    		
    		$mform->addElement('text', 'country', get_string('country', 'block_simple_map'));
    		$mform->setDefault('country', $result->country);
    		$mform->setType('country', PARAM_TEXT);
    		
    		$mform->addElement('text', 'region', get_string('region', 'block_simple_map'));
    		$mform->setDefault('region', $result->region);
    		$mform->setType('region', PARAM_TEXT);
    		
    		$mform->addElement('advcheckbox', 'fetch_geo_codes', get_string('fetch_geo_codes', 'block_simple_map'));
    		$mform->setDefault('fetch_geo_codes', true);
    		
    		$mform->addElement('text', 'lat', get_string('lat', 'block_simple_map'));
    		$mform->setDefault('lat', $result->lat);
    		$mform->setType('lat', PARAM_TEXT);
    		
    		$mform->addElement('text', 'lng', get_string('lng', 'block_simple_map'));
    		$mform->setDefault('lng', $result->lng);
    		$mform->setType('lng', PARAM_TEXT);
    		
    		$mform->addElement('text', 'category', get_string('category', 'block_simple_map'));
    		$mform->setDefault('category', $result->category);
    		$mform->setType('category', PARAM_TEXT);
    		
    		$mform->addElement('text', 'link_1', get_string('link_1', 'block_simple_map'));
    		$mform->setDefault('link_1', $result->link_1);
    		$mform->setType('link_1', PARAM_TEXT);
    		
    		$mform->addElement('text', 'link_2', get_string('link_2', 'block_simple_map'));
    		$mform->setDefault('link_2', $result->link_2);
    		$mform->setType('link_2', PARAM_TEXT);
    		
    		$mform->addElement('text', 'link_3', get_string('link_3', 'block_simple_map'));
    		$mform->setDefault('link_3', $result->link_3);
    		$mform->setType('link_3', PARAM_TEXT);
    		
    		$mform->addElement('text', 'link_4', get_string('link_4', 'block_simple_map'));
    		$mform->setDefault('link_4', $result->link_4);
    		$mform->setType('link_4', PARAM_TEXT);
    		
    		$mform->addElement('text', 'link_5', get_string('link_5', 'block_simple_map'));
    		$mform->setDefault('link_5', $result->link_5);
    		$mform->setType('link_5', PARAM_TEXT);
    		
    		$mform->addElement('text', 'contact', get_string('contact', 'block_simple_map'));
    		$mform->setDefault('contact', $result->contact);
    		$mform->setType('contact', PARAM_TEXT);
    		
    		$this->add_action_buttons(true, get_string('savechanges'));
    	}
    	}    
    }     
}

function get_lat_lng_by_address($address, $city, $country, $table) {

	require_once(dirname(__FILE__) . '/googlehelper/class.googleHelper.php');
	
	global $CFG, $DB;
	
	//now it is a bit complicated to access the config data from here
	$instance = $DB->get_record('block_instances', array('blockname' => 'simple_map'), '*', MUST_EXIST); 
	$block_simple_map = block_instance('simple_map', $instance);	
	
	if ($apiKey = $block_simple_map->config->googleAPIkey) {

		//init our object
		$obj = new googleHelper($apiKey);
 
		//get coordinates and print the debug info
		$check_address = ' '.$address.', '.$city.', '.$country.' ';
	
		$object = $obj->getCoordinates($check_address);
		return $object;
	}
	
}