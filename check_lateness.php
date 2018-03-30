<html>
<body>



<?php
require_once 'vendor/autoload.php';

use transit_realtime\FeedMessage;


echo "<br>Real Time Information Loaded</br>";
include('pb.html');

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
echo $now;
foreach ($feed->getEntityList() as $entity) {
  #echo "<br>a new bus</br>";

  echo "<br>Trip: " .$entity->getId()."</br>";
  $cmd= "select * from trips where tripid= ".$entity->getVehicle()->getTrip()->getTripId()." and atime='".$now.":00';";
  echo "query=".$cmd."</br>";
  $result = $conn->query($cmd);
if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        echo "Trip Id: " . $row["TripID"]. " - Arrival Time: " . $row["ATime"]. "<br>";
    }
} else {
    echo "0 results";
}
}


$conn->close();
?>


</body>
</html>
