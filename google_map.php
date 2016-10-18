<?php


require_once dirname(__FILE__)."/../../config.php";
require_once($CFG->libdir . '/pagelib.php');
require_once($CFG->libdir . '/blocklib.php');

$googleAPIkey = required_param('googleAPIkey', PARAM_ALPHANUMEXT);
$location = required_param('location', PARAM_TEXT);
$location_type = required_param('location_type', PARAM_ALPHANUMEXT);
$distance = required_param('distance', PARAM_INT);
confirm_sesskey();

// First we clean the values
$location = trim($location, "(");
$location = trim($location, ")");

$called_from_php = true;


// in this file, we populate $results, depending on the distance
include dirname(__FILE__) . '/get_distances.php';

$content = "";

$PAGE->set_context(context_system::instance());
$PAGE->set_url($CFG->wwwroot.'/blocks/simple_map/google_map.php');


if (! empty($CFG->config->limit_search)) {
    	$limit_serach = $CFG->config->limit_search;
	}
	else {
		$limit_search = '';
	}

$content = '
		<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key='.$googleAPIkey.'&amp;sensor=false"></script>
		<script type="text/javascript" src="'. $CFG->wwwroot .'/blocks/simple_map/javascript/jquery.min.js"></script>
		<script type="text/javascript" src="'. $CFG->wwwroot .'/blocks/simple_map/javascript/gmap3.js"></script>
		<script type="text/javascript" src="'. $CFG->wwwroot .'/blocks/simple_map/javascript/jquery-autocomplete.min.js"></script>
    	<script type="text/javascript" src="'. $CFG->wwwroot .'/blocks/simple_map/javascript/simple_map.js"></script>
    	

    <noscript><p>JavaScript must be enabled in order for you to use Google Maps. 
      However, it seems JavaScript is either disabled or not supported by your browser. 
      To view Google Maps, enable JavaScript by changing your browser options, and then 
      try again.</p>
    </noscript>

        <script type="text/javascript">
        //<![CDATA[
 	var markers = [];
	var categories = [];
	

 	$(function(){
          $("#address").autocomplete({
            source: function() {
              $("#gmap3_map").gmap3({
                action:"getAddress",
                 address: {
                	address: $(this).val(),
                	region: "'.$limit_search.'"},
                callback:function(results){
                  if (!results) return;
                  $("#address").autocomplete(
                    "display", 
                    results,
                    false
                  );
                }
              });
            },
            cb:{
              cast: function(item){
                return item.formatted_address;
              },
              select: function(item) {

              	var e = document.getElementById("distance");
 				var distance_value = e.options[e.selectedIndex].value;
				my_location = strip_location(item.geometry.location);
				location_array = String(my_location).split(",");
 				my_location_lat = location_array[0];
 				my_location_lng = location_array[1];
 				my_results = $.ajax({
   					type:"post",
   					url: "get_distances.php?googleAPIkey='.$googleAPIkey.'&location=" + item.geometry.location + "&location_type=" + item.geometry.location_type + "&distance=" + distance_value,
   					dataType: "json",
   					success: function(data) {
      					initialize_map(my_location_lat, my_location_lng, 12, distance_value, data, '.json_encode($labels).');
  					 }
  				});
              }
            }
          })
          //.focus();
          
    });
		
     	$(document).ready(function(){
 		
		initialize_map('.json_encode($location_lat).','.json_encode($location_lng).', '.$map_zoom.', '.$distance.', '.json_encode($results).', '.json_encode($labels).');		
 	});
 	
 	function strip_location(my_loc) {
 		my_loc = String(my_loc).replace("(", "");
 		my_loc = my_loc.replace(")", "");
 		return my_loc;
 	}
 	
//]]> 	  
</script>    
    <div id="s_map">
    <form action="">
    	<p>'.get_string('enter_address', 'block_simple_map').'</p>
    	<div><input type="text" id="address" size="25" /></div>
    	<p>'.get_string('set_distance', 'block_simple_map').'</p>
		<div><select id="distance">
			<option value="1000">1km</option>
			<option value="5000">5km</option>
			<option value="10000" selected="selected">10km</option>
			<option value="20000">20km</option>
		</select></div>
	</form>
		<ul id="category"><li></li></ul>  
		<ul id="list"><li></li></ul>

        <div id="simple_map"></div>
        <div id="gmap3_map" class="gmap3"></div>
    </div>
    </div>';

	$PAGE->navbar->add(get_string("simple_map", "block_simple_map"));

    echo $OUTPUT->header();	
	echo $OUTPUT->box($content);
	echo $OUTPUT->footer();
?>
