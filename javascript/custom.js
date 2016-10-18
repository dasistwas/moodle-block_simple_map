      $(function(){
	  
          $("#address").autocomplete({
            source: function() {
              $("#simple_map").gmap3({
                action:"getAddress",
                address: {
                	address: $(this).val(),
                	region: $('#simplemaplimitsearch').text()},
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
				var distance_value = $("#distance").val();
				var wwwroot = $('#simplemapwwwroot').text();
              	window.location.href= wwwroot + "/blocks/simple_map/google_map.php?googleAPIkey="+$("#googleapikey").text()+"&location=" + item.geometry.location + "&location_type=" + item.geometry.location_type + "&sesskey=" + $('#simplemapsesskey').text() + "&distance=" + distance_value + "";
              }
            }
          })
      });
