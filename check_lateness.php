
<?php
require_once 'vendor/autoload.php';

use transit_realtime\FeedMessage;


echo "<br>Real Time Information Loaded</br>";
include('pb.html');


function distance($lat1, $lon1, $lat2, $lon2, $unit) {

  $theta = $lon1 - $lon2;
  $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
  $dist = acos($dist);
  $dist = rad2deg($dist);
  $miles = $dist * 60 * 1.1515;
  $unit = strtoupper($unit);

  if ($unit == "K") {
    return ($miles * 1.609344);
  } else if ($unit == "N") {
      return ($miles * 0.8684);
  } else {
      return $miles;
  }
}



$servername = "localhost";
$username = "root";
$password = '123456';
$dbname = "buses";
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
#$data = file_get_contents(dirname(__FILE__) . "/VehiclePositions.pb");
$data = file_get_contents("https://data.edmonton.ca/download/7qed-k2fc/application%2Foctet-stream");
$feed = new FeedMessage();
$feed->parse($data);
ini_set('date.timezone', 'America/Edmonton');
$now = date ('H:i', time());
echo "Current time is :".$now."</br>";

$congestion = array();

foreach ($feed->getEntityList() as $entity) {
      #echo "<br>a new bus</br>";

    #echo "<br>Trip: " .$entity->getId()."</br>";
    $cmd= "select * from trips t where t.tripid= ".$entity->getVehicle()->getTrip()->getTripId()." and t.atime='".$now.":00';";
    #echo "query=".$cmd."</br>";
    $result = $conn->query($cmd);

    if ($result->num_rows > 0){
      $row = $result->fetch_assoc();

      $cmd2  = "select * from stops s where s.StopID = ".$row["StopID"];
      $header = $row["BusHeader"];
      #echo "Header: ".sizeof($row)."<br>";

      $result2 = $conn->query($cmd2);

      if ($result2->num_rows > 0){
        $row2 = $result2->fetch_assoc();
        #echo "Stop ID: " . $row2["StopID"]. " - Latitude: " . $row2["Latitude"]. " - Longitude: ".$row2["Longitude"]."<br>";
        #echo "Vechicle Latitude: ".$entity->getVehicle()->getPosition()->getLatitude()." Longitude: ".$entity->getVehicle()->getPosition()->getLongitude()."<br>";

        $distance = distance($row2["Latitude"], $row2["Longitude"], $entity->getVehicle()->getPosition()->getLatitude(), $entity->getVehicle()->getPosition()->getLongitude(), "K");
        #echo $header. " distance from stop: ". $distance*1000 . " Meters<br><br>";

        if ($distance*1000 > 1500){
          array_push($congestion, $entity->getVehicle()->getPosition()->getLatitude());
          array_push($congestion, $entity->getVehicle()->getPosition()->getLongitude());
          if ($header != NULL){
            array_push($congestion, "<h3>".$header."</h3>"."Bus distance from stop: ". $distance*1000 . " Meters<br><br>");
            array_push($congestion, $header);
          }else{
            array_push($congestion, "Congestion");
            array_push($congestion, "Congestion");
          }

        }
      }

    }

    /*
    $j = 0;
    while($j < sizeof($congestion)){
      echo $congestion[$j]. " ".$congestion[$j+1]. " ". $congestion[$j+2]. ' '. $congestion[$j+3]."<br>";
      $j = $j + 4;
    }
    */


/*
    if ($result->num_rows > 0){
        // output data of each row
        $row = $result->fetch_assoc();
        echo "Stop ID: " . $row["StopID"]. " - Latitude: " . $row["Latitude"]. " - Longitude:".$row["Longitude"]."<br>";
        echo $row["Latitude"]
        $distance = distance($row["Latitude"], $row["Longitude"], $entity->getVehicle()->getPosition()->getLatitude(), $entity->getVehicle()->getPosition()->getLongitude(), "K")* 1000;
        echo $distance*1000 ."<br>";


    } else {
        echo "0 results</br>";
    }
    */

}


$conn->close();


$i = 0;

while($i < sizeof($congestion)){
  echo "congestion coordinates: ".$congestion[$i]. " ". $congestion[$i + 1]."<br>";
  $i = $i + 4;
}




?>



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

    $fruits = array();

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
              #echo $data[9]."<br>";

              array_push($fruits, $lat);
              array_push($fruits, $long);
              array_push($fruits, "<h3>".$data[9]."</h3>".$data[11]);
              array_push($fruits, $data[9]);


           }
        }
        fclose($handle);
    }

    ?>


    <script>

    var fruits = <?php echo '["' . implode('", "', $fruits) . '"]' ?>;
    var conges = <?php echo '["' . implode('", "', $congestion) . '"]' ?>;

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

          function addMarker(location, information, atitle) {

            var marker = new google.maps.Marker({
              position: location,
              map: map,
              title: atitle
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
            deleteMarkers();

            var edmonton = {lat: 53.5232, lng: -113.5263};

            var arrayLength = fruits.length;
            for (var i = 0; i < arrayLength; i = i+4) {
              var newMarker = {lat: parseFloat(fruits[i]), lng: parseFloat(fruits[i+1])};
              addMarker(newMarker, fruits[i+2],fruits[i+3]);
            }


            setMapOnAll(map);
          }


          function showCongestion() {

            deleteMarkers();
            var arrayLength = conges.length;
            for (var i = 0; i < arrayLength; i = i+4) {
              var newMarker = {lat: parseFloat(conges[i]), lng: parseFloat(conges[i+1])};
              addMarker(newMarker, conges[i+2],conges[i+3]);
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
      <input onclick="showCongestion();" type=button value="Show Congestion Locations">

      <!--
      <input onclick="deleteMarkers();" type=button value="Delete Markers">
      -->
    </div>


  </body>
</html>
