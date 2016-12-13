<?php



class block_simple_map extends block_base {

    /** @var int */
    public static $navcount;

    /** @var string */
    public $blockname = null;

    /** @var bool */
    protected $contentgenerated = false;

    /** @var bool|null */
    protected $docked = null;

    public function init() {
        global $CFG;
        $this->blockname = get_class($this);
        $this->title = get_string('pluginname', 'block_simple_map');
    }

    public function has_config() {
        return true;
    }

    public function instance_allow_multiple() {
        return false;
    }

    function specialization() {
        $this->title = isset($this->config->title) ? format_string($this->config->title) : format_string(
                get_string('pluginname', 'block_simple_map'));
        if ($this->title == '') {
            $this->title = format_string(get_string('pluginname', 'block_simple_map'));
        }
    }

    public function get_content() {
        global $CFG, $COURSE, $OUTPUT;
        
        require_once ($CFG->libdir . '/pagelib.php');
        
        if ($this->content !== null) {
            return $this->content;
        }
        
        $googleAPIkey = get_config('block_simple_map', 'googleapikey');
        
        if (!empty($this->config->category_of_places)) {
            $category_of_places = $this->config->category_of_places;
        } else {
            $category_of_places = get_string('places', 'block_simple_map');
        }
        
        if (!empty($this->config->limit_search)) {
            $limit_search = $this->config->limit_search;
        } else {
            $limit_search = '';
        }
        
        $data = '<script type="text/javascript" src="' . $CFG->wwwroot .
                 '/blocks/simple_map/javascript/jquery.min.js"></script>
    <script src="http://maps.google.com/maps/api/js?sensor=false" type="text/javascript"></script>
    <script type="text/javascript" src="' .
                 $CFG->wwwroot .
                 '/blocks/simple_map/javascript/gmap3.min.js"></script>
    <script type="text/javascript" src="' .
                 $CFG->wwwroot .
                 '/blocks/simple_map/javascript/jquery-autocomplete.min.js"></script>
    <script type="text/javascript" src="' .
                 $CFG->wwwroot . '/blocks/simple_map/javascript/custom.js"></script>
    <noscript><p>JavaScript must be enabled in order for you to use Google Maps. 
      However, it seems JavaScript is either disabled or not supported by your browser. 
      To view Google Maps, enable JavaScript by changing your browser options, and then 
      try again.</p>
    </noscript>
   <div id="googleapikey" style="display: none;">' . $googleAPIkey . '</div>
   <div id="simplemapsesskey" style="display: none;">' . sesskey() . '</div>
   <div id="simplemaplimitsearch" style="display: none;">' . $limit_search . '</div>
   <div id="simplemapwwwroot" style="display: none;">' . $CFG->wwwroot .
                 '</div>      
   <div id="s_map"> 
    <form action="">
    	<p>' .
                 str_replace("SMap_Places", $category_of_places, 
                        get_string('find_places_near_you', 'block_simple_map')) . '</p>
    	<p>' .
                 get_string('enter_address', 'block_simple_map') . '</p>
    	<div><input type="text" id="address" size="18" style="width: auto;" /></div>
    	<p>' .
                 get_string('set_distance', 'block_simple_map') . '</p>
		<div><select id="distance">
			<option value="1000">1km</option>
			<option value="5000">5km</option>
			<option value="10000" selected="selected">10km</option>
			<option value="20000">20km</option>
		</select></div>
	</form>
    <div id="simple_map" class="gmap3"></div>
   </div>';
        // check Capabilities
        $context = context_course::instance($COURSE->id);
        
        $this->content = new stdClass();
        if (has_capability('moodle/site:manageblocks', $context)) {
            $this->content->text = '';
            $this->content->text = $data;
            if (empty($googleAPIkey)) {
            	$this->content->text .= html_writer::div('Google API key must be specified in the admin settings for the plugin',"alert alert-error");
            }
            $this->content->text .= '<a title="configuration" href="' . $CFG->wwwroot .
                     '/blocks/simple_map/edit_upload_form.php">' .
                     get_string('upload_form', 'block_simple_map') . '</a>';
            $this->content->footer = '';
            
        } else {
            $this->content->text = $data;
            $this->content->footer = '';
        }
        
        return $this->content;
    }
}
?>
