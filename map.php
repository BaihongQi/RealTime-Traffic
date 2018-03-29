<!DOCTYPE html >

  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
    <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
    <title>Using MySQL and PHP with Google Maps</title>
    <style>
      /* Always set the map height explicitly to define the size of the div
       * element that contains the map. */
      #map {
        height: 80%;
      }
      /* Optional: Makes the sample page fill the window. */
      html, body {
        height: 90%;
        width:80%;
        margin: 0;
        padding: 0;
      }
    </style>
  </head>

  <body>


    <div id="map"></div>

    <?php
    // php array

    $fruits = array("53.5212", "-113.5133");

    $row = 1;

    $file = "https://data.edmonton.ca/api/views/m6ed-ysu7/rows.csv?accessType=DOWNLOAD";

    if (($handle = fopen($file, "r")) !== FALSE) {
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $num = count($data);
            #echo "<p> $num fields in line $row: <br /></p>\n";
            $row++;

            if ($row != 2){
              $lat = explode(",", substr($data[16],1))[0];
              $long = explode(",", substr($data[16],0,-1))[1];
              #echo $lat. " ". $long."<br>";
              #echo $data[11]."<br>";

              array_push($fruits, $lat);
              array_push($fruits, $long);
              array_push($fruits, $data[11]);


           }
        }
        fclose($handle);
    }

    ?>


    <script>

    var fruits = <?php echo '["' . implode('", "', $fruits) . '"]' ?>;
    var positions = ["53.5212", "-113.5133"];

    var map;
    var markers = [];

    function initMap() {
            var edmonton = {lat: 53.5232, lng: -113.5263};

            map = new google.maps.Map(document.getElementById('map'), {
              zoom: 11,
              center: edmonton,
            });

            // This event listener will call addMarker() when the map is clicked.

            //map.addListener('click', function(event) {
            //  addMarker(event.latLng);
            //});

            // Adds a marker at the center of the map.
            //addMarker(edmonton);
          }

          // Adds a marker to the map and push to the array.

          function addMarker(location, information) {

            var marker = new google.maps.Marker({
              position: location,
              map: map
            });
            markers.push(marker);

            var infowindow = new google.maps.InfoWindow({
              content: information
            });

            marker.addListener('click', function() {
              infowindow.open(map, marker);
            });

          }

          // Sets the map on all markers in the array.
          function setMapOnAll(map) {
            for (var i = 0; i < markers.length; i++) {
              markers[i].setMap(map);
            }
          }

          // Removes the markers from the map, but keeps them in the array.
          function clearMarkers() {
            setMapOnAll(null);
          }

          // Shows any markers currently in the array.
          function showMarkers() {

            var edmonton = {lat: 53.5232, lng: -113.5263};

            var arrayLength = fruits.length;
            for (var i = 0; i < arrayLength; i = i+2) {
              var newMarker = {lat: parseFloat(fruits[i]), lng: parseFloat(fruits[i+1])};
              addMarker(newMarker, fruits[i+2]);
            }


            setMapOnAll(map);
          }

          // Deletes all markers in the array by removing references to them.
          function deleteMarkers() {
            clearMarkers();
            markers = [];
          }


    </script>
    <script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDR4v_523VmlE7u7_6GNw7ODu9Ku9_Ckws&callback=initMap">
    </script>


    <div id="floating-panel">
      <input onclick="clearMarkers();" type=button value="Hide Disruption Locations">
      <input onclick="showMarkers();" type=button value="Show Disruption Locations">
      <!--
      <input onclick="deleteMarkers();" type=button value="Delete Markers">
      -->
    </div>


  </body>
</html>
