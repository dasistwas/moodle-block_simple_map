<?php
 
defined('MOODLE_INTERNAL') || die();


require_once $CFG->dirroot . '/blocks/simple_map/upload_form.php';
require_once $CFG->libdir . '/formslib.php';
require_once $CFG->libdir . '/csvlib.class.php';


$returnurl = new moodle_url('/blocks/simple_map/edit_form.php');

class block_simple_map_edit_form extends block_edit_form {
 
    protected function specific_definition($mform) {
    	global $DB, $CFG;
 		$mform =& $this->_form;
 		
        // Section header title according to language file.
        $mform->addElement('header', 'configheader', get_string('blocksettings', 'block'));
 
 	// A sample string variable with a default value.
    	$mform->addElement('text', 'config_title', get_string('blocktitle', 'block_simple_map'));
    	$mform->setDefault('config_title', 'default value');
    	$mform->setType('config_title', PARAM_TEXT);
 
 
        // A sample string variable with a default value.
        $mform->addElement('text', 'config_googleAPIkey', get_string('googleAPIkey', 'block_simple_map'));
        $mform->setDefault('config_googleAPIkey', 'default value');
        $mform->setType('config_googleAPIkey', PARAM_ALPHANUMEXT);
        
        $mform->addElement('text', 'config_category_of_places', get_string('category_of_places', 'block_simple_map'));
        $mform->setDefault('config_category_of_places', 'default value');
        $mform->setType('config_category_of_places', PARAM_TEXT);
        
        $mform->addElement('text', 'config_limit_search', get_string('limit_search', 'block_simple_map'));
        $mform->setDefault('config_limit_search', 'default value');
        $mform->setType('config_limit_search', PARAM_ALPHANUM);
        
 	}       
}

?>