function initialize_map (location_lat,location_lng, map_zoom, distance, php_places, labels) {	

	// We draw the map with specific Options
	var mapOptions = {
          center: new google.maps.LatLng(location_lat, location_lng),
          zoom: map_zoom,
          mapTypeId: google.maps.MapTypeId.ROADMAP
    };
        var map = new google.maps.Map(document.getElementById("simple_map"),
           	mapOptions);
	
				
		// We need a different color for the home-pin:
		var pinColor = "69fe75"; // green
    	var pinImage = new google.maps.MarkerImage("http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|"+pinColor,
        	new google.maps.Size(21, 34),
        	new google.maps.Point(0,0),
        	new google.maps.Point(10, 34));
        var pinShadow = new google.maps.MarkerImage("http://chart.apis.google.com/chart?chst=d_map_pin_shadow",
        	new google.maps.Size(40, 37),
        	new google.maps.Point(0, 0),
        	new google.maps.Point(12, 35));
        		
        // Place the green home-marker
        var marker = new google.maps.Marker({
    		position: new google.maps.LatLng(location_lat, location_lng), 
    			map: map,
    			title: "Home",
				icon: pinImage,
    			shadow: pinShadow,
		});
		var location = location_lat + "," + location_lng;
		var destinations = [];
		var categories = [];
		
		var prev_infowindow = new google.maps.InfoWindow(); 
        

		var directionsDisplay;
  		var directionsService = new google.maps.DirectionsService();

  		var oldDirections = [];
  		var currentDirections = null;
		
		directionsDisplay = new google.maps.DirectionsRenderer({
        	'map': map,
        	'preserveViewport': true,
        	'draggable': false
    	});

// var travelModes = ["DRIVING":google.maps.TravelMode.DRIVING, "WALKING":google.maps.TravelMode.DRIVING, "BICYCLING":google.maps.TravelMode.DRIVING];
//         
//         $.each(travelModes, function(i, travelMode){
//         	$("<input type='radio'  id='" + travelMode + "'> " + travelMode + "<br />")
// 					.click(function()
// 						echo "test";
// 					})
// 					.appendTo("#category");
//         }

		
        //We remove all List elements, in case the script is called for the second time already
		$('#list').empty();
		$('#category').empty();
        
        j = 0;
		$.each(php_places, function(i, place){
			var place_lat = place.lat.replace(",", ".");
			var place_lng = place.lng.replace(",", ".");
			var point = place_lat + ", " + place_lng;
			destinations.push(point);
			$("#simple_map").gmap3({
				action:"getDistance",
				options:{
					origins: location,
					destinations: [point],
					travelMode: google.maps.TravelMode.WALKING
				},
				callback: function(results){
					if (results) {
						if (results.rows[0].elements[0].distance.value < distance || j < 5) {
							sortPlaces(i, results.rows[0].elements[0].distance.text, results.rows[0].elements[0].distance.value, location, distance, place);
							j++;
						}						
        			}
        		}   
		});			
function sortPlaces(index, distanceText, distanceValue, location, distance, place, labels) {		
        		
       		var marker = new google.maps.Marker({
   				position: new google.maps.LatLng(place.lat, place.lng), 
   				map: map,
   				title: "in " + distanceText + ", " + place.title
			});
			marker.mycategory = place.category.replace(" ", "_");
				
			var infowindow = new google.maps.InfoWindow({
   				content: displayInfoWindow(place, distanceText)
			});
					
			markers.push(marker);
			marker.setMap(map);

			if ((place.category) && ($.inArray(place.category, categories) == -1)) {
				$("<input type='checkbox'  id='" + place.category.replace(" ", "_") + "' checked='true' /> " + place.category + "<br />")
					.click(function()
						{
						if (this.checked) {
    			     		show(this.id);
    		        	} else {
    			     		hide(this.id);
			        	}
					})
					.appendTo("#category");
				categories.push(place.category);
			}
			
					
			$("<li id=" + ", distanceValue=" + distanceValue + " class='" + place.category.replace(" ", "_") + "'/>")
				.html(marker.title)
				.click(function()
					{
					prev_infowindow.close();
					displayPoint(marker, index);
					infowindow.open(map,marker);
					prev_infowindow = infowindow;
					calcRoute(location, marker.position);
					$('.contracted').show();
					$('.extended').hide();	
				})
				.appendTo("#list");
				google.maps.event.addListener(marker, "click", function()
					{
					prev_infowindow.close();
					displayPoint(marker, index);
					infowindow.open(map,marker);
					prev_infowindow = infowindow;
					calcRoute(location, marker.position);
					$('.contracted').show();
					$('.extended').hide();
			});
       		$("#list li").sort(asc_sort).appendTo("#list");
 			function asc_sort(a, b){
    				return ($(b).text()) < ($(a).text());    
 			}
       	}
       	
       	function displayInfoWindow (place, distanceText)
       	{
       		var lines = "";
       		lines = lines.concat("<h3>" + replaceURLWithHTMLLinks(place.link_1,  place.title) + "</h3>");
       		lines = lines.concat("Distanz: " + distanceText + "<br />");

       		if(place.description) 
       		{	
       			lines = lines.concat("<div class='extended'>");
       			lines = lines.concat("<h5>" + labels.description + ":</h5>" + place.description);
       			lines = lines.concat("</div>");	
       		}
       		if(place.opening_hours) 
       		{	
       			lines = lines.concat("<div class='extended'>");
       			lines = lines.concat("<h5>" + labels.opening_hours + ":</h5>" + place.opening_hours);
       			lines = lines.concat("</div>");	
       		}
       		if (place.link_2)
       		{
       				
        		lines = lines.concat("<h5>" + labels.link_2 + "</h5>");
        		var n = place.link_2.split(",");
        		$.each(n, function(i, linkLine)
        		{
        			lines = lines.concat(replaceURLWithHTMLLinks(linkLine,linkLine.substring(0,35) + "...") + "<br />");
        		});	
        	}
       			
       		
       		if (place.link_3)
       		{	
       			lines = lines.concat("<div class='extended'>");
       			lines = lines.concat("<h5>" + labels.link_3 + "</h5>");
        		var n = place.link_3.split(",");
        		$.each(n, function(i, linkLine)
        		{
        			lines = lines.concat(replaceURLWithHTMLLinks(linkLine,linkLine.substring(0,35) + "...") + "<br />");
        		});
        		lines = lines.concat("</div>");		
       		}
       		if (place.link_4)
       		{	
       			lines = lines.concat("<div class='extended'>");
       			lines = lines.concat("<h5>" + labels.link_4 + "</h5>");
        		var n = place.link_4.split(",");
        		$.each(n, function(i, linkLine)
        		{
        			lines = lines.concat(replaceURLWithHTMLLinks(linkLine,linkLine.substring(0,35) + "...") + "<br />");
        		});
        		lines = lines.concat("</div>");	
       		}
       		if (place.link_5)
       		{	
       			lines = lines.concat("<div class='extended'>");
        		lines = lines.concat("<h5>" + labels.link_5 + "</h5>");
        		var n = place.link_5.split(",");
        		$.each(n, function(i, linkLine)
        		{
        			lines = lines.concat(replaceURLWithHTMLLinks(linkLine,linkLine.substring(0,35) + "...") + "<br />");
        		});
        		lines = lines.concat("</div>");
       		}
			if (place.contact)
        	{	
        		lines = lines.concat("<h5>" + labels.contact + ":</h5>");
        		var n = place.contact.split(",");
        		$.each(n, function(i, contactLine)
        			{
        			lines = lines.concat("<a href='mailto:" + contactLine + "'>" + contactLine + "</a><br />");
        			});	
        	}
        	lines = lines.concat("<br /><a href='#' onclick='$(\".extended\").hide();$(\".contracted\").show()' class='extended'>" + labels.show_less + "</a>");
        	lines = lines.concat("<a href='#' onclick='$(\".extended\").show();$(\".contracted\").hide()' class='contracted'>" + labels.show_more + "<br /><br /><br /><br /></a>");
       		return lines;
       	}
       	
       	function replaceURLWithHTMLLinks(text, displayText)
       	{
      		var exp = /(\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/ig;
      		if (displayText)
      		{
      			return text.replace(exp,"<a href='$1'>" + displayText + "</a>");
      		}
      		else
      		{
      			return text.replace(exp,"<a href='$1'>$1</a>");
      		} 
    	}
       			
				
		function displayPoint(marker, index)
		{
					
			var moveEnd = google.maps.event.addListener(map, "moveend", function(){
				var markerOffset = map.fromLatLngToDivPixel(marker.getLatLng());					
					google.maps.event.removeListener(moveEnd);
			});
			map.panTo(marker.getPosition());
		}
		
		function calcRoute(origin_route, destination_route)
		{
    		var start = origin_route;
    		var end = destination_route;
    		var request = {
        		origin:start,
        		destination:end,
        		travelMode: google.maps.DirectionsTravelMode.WALKING
    		};
    		directionsService.route(request, function(response, status)
    		{
   	   			if (status == google.maps.DirectionsStatus.OK)
   	   			{
        			directionsDisplay.setDirections(response);
      			}
			});
		}
		function show(category)
		{	
			$('.' + category).show()
    		for (var i=0; i<markers.length; i++)
   			{
   				if (markers[i].mycategory == category)
   				{	
       				markers[i].setVisible(true);
   				}
   			}
		}

      	// == hides all markers of a particular category, and ensures the checkbox is cleared ==
		function hide(category)
		{	
			$('.' + category).hide()
			for (var i=0; i<markers.length; i++)
   			{	
 				if (markers[i].mycategory == category)
      			{
       				markers[i].setVisible(false);
       			}
     		}
		}
	});
	
}