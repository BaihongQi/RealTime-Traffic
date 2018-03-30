<html>
<body>



<?php
require_once 'vendor/autoload.php';

use transit_realtime\FeedMessage;


echo "<br>Real Time Information Loaded</br>";
include('pb.html');
/**
 * Calculates the great-circle distance between two points, with
 * the Haversine formula.
 * @param float $latitudeFrom Latitude of start point in [deg decimal]
 * @param float $longitudeFrom Longitude of start point in [deg decimal]
 * @param float $latitudeTo Latitude of target point in [deg decimal]
 * @param float $longitudeTo Longitude of target point in [deg decimal]
 * @param float $earthRadius Mean earth radius in [m]
 * @return float Distance between points in [m] (same as earthRadius)
 */
function haversineGreatCircleDistance(
  $latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000)
{
  // convert from degrees to radians
  $latFrom = deg2rad($latitudeFrom);
  $lonFrom = deg2rad($longitudeFrom);
  $latTo = deg2rad($latitudeTo);
  $lonTo = deg2rad($longitudeTo);

  $latDelta = $latTo - $latFrom;
  $lonDelta = $lonTo - $lonFrom;

  $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
    cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
  return $angle * $earthRadius;
}


$servername = "127.0.0.1";
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
foreach ($feed->getEntityList() as $entity) {
  #echo "<br>a new bus</br>";

  echo "<br>Trip: " .$entity->getId()."</br>";
  $R_lat=$entity->getVehicle()->getPosition()->getLatitude();
  $R_long=$entity->getVehicle()->getPosition()->getLongitude();
  $cmd= "select s.* from stops s join trips t on s.stopid=t.stopid where t.tripid= ".$entity->getVehicle()->getTrip()->getTripId()." and t.atime='".$now.":00';";
  #echo "query=".$cmd."</br>";
  $result = $conn->query($cmd);
if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        $S_lat=$row["Latitude"];
        $S_long=$row["Longitude"];
        #echo "Stop ID: " . $row["StopID"]. " - Latitude: " . $S_lat. " - Longitude:".$S_long."<br>";
        $distance=haversineGreatCircleDistance($S_lat, $S_long, $R_lat, $R_long, $earthRadius = 6371000);
        echo "this is distance".$distance."</br>";
        if ($distance>500) {
           echo "Distance is larger than 500m, congestion may occur at Latitude: ".$R_lat." Longitude: ".$R_long."</br>";
        }
    }
} else {
    echo "0 results</br>";
}
}


$conn->close();
?>


</body>
</html>
