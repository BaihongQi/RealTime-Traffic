<html>
<body>



<?php
require_once 'vendor/autoload.php';

use transit_realtime\FeedMessage;


echo "<br>Real Time Information Loaded</br>";
include('pb.html');


#$data = file_get_contents(dirname(__FILE__) . "/VehiclePositions.pb");
$data = file_get_contents("https://data.edmonton.ca/download/7qed-k2fc/application%2Foctet-stream");
$feed = new FeedMessage();
$feed->parse($data);


foreach ($feed->getEntityList() as $entity) {
  #echo "<br>a new bus</br>";
  if ($entity->hasTripUpdate()) {
    error_log("trip: " .$entity->getId());
    $trip = $entity->getTripUpdate();
    error_log("trip id: " . $trip->getTrip()->getTripId());

  }
  echo "<br>trip: " .$entity->getId()."</br>";

  if ($entity->hasVehicle()){
    echo "Trip ID: ".$entity->getVehicle()->getTrip()->getTripId()."<br>";
    echo "Vehicle ID: ".$entity->getVehicle()->getVehicle()->getId()."<br>";
    echo "Vehicle Latitude: ".$entity->getVehicle()->getPosition()->getLatitude()."<br>";
    echo "Vehicle Longitude: ".$entity->getVehicle()->getPosition()->getLongitude()."<br>";
    echo "Vehicle Speed: ".$entity->getVehicle()->getPosition()->getSpeed()."<br>";




  }
  echo "alert: ".$entity->getAlert()."<br>";


}



#foreach ($feed->getEntityList() as $entity) {
#  if ($entity->hasTripUpdate()) {
#    $trip = $entity->getTripUpdate();
#    error_log("trip id: " . $trip->getTrip()->getTripId());
#    foreach ($trip->getStopTimeUpdateList() as $stu) {
#      if ($stu->hasArrival()) {
#        $ste = $stu->getArrival();
#        error_log("    arrival delay: " . $ste->getDelay());
#        echo"<br> arrival delay: ".$ste->getDelay()."</br>";
#        error_log("    arrival time: " . $ste->getTime());
#      }
#      if ($stu->hasDeparture()) {
#        $ste = $stu->getDeparture();
#        error_log("    departure delay: " . $ste->getDelay());
#        error_log("    departure time: " . $ste->getTime());
#      }
#    }
#  }
#}


?>


</body>
</html>
