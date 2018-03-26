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

  echo "<br>Trip: " .$entity->getId()."</br>";

  if ($entity->hasVehicle()){
    echo "Vehicle Trip ID: ".$entity->getVehicle()->getTrip()->getTripId()."<br>";
    echo "Vehicle Route ID: ".$entity->getVehicle()->getTrip()->getRouteId()."<br>";
    echo "Vehicle Direction ID: ".$entity->getVehicle()->getTrip()->getDirectionId()."<br>";
    echo "Vehicle Start Time: ".$entity->getVehicle()->getTrip()->getStartTime()."<br>";
    echo "Vehicle Direction ID: ".$entity->getVehicle()->getTrip()->getDirectionId()."<br>";

    echo "Vehicle ID: ".$entity->getVehicle()->getVehicle()->getId()."<br>";
    echo "Vehicle Latitude: ".$entity->getVehicle()->getPosition()->getLatitude()."<br>";
    echo "Vehicle Longitude: ".$entity->getVehicle()->getPosition()->getLongitude()."<br>";
    echo "Vehicle Speed: ".$entity->getVehicle()->getPosition()->getSpeed()."<br>";
    echo "Vehicle Bearing: ".$entity->getVehicle()->getPosition()->getBearing ()."<br>";
    #echo "(degrees clockwise from True North)<br>"

    echo "Vehicle Stop ID: ".$entity->getVehicle()->getStopId()."<br>";
    echo "Vehicle Status: ".$entity->getVehicle()->getCurrentStatus()."<br>";
    #echo "(enum Status INCOMING_AT, The vehicle about to arrive at the stop; STOPPED_AT, standing at the stop; IN_TRANSIT_TO, in transit <br>";

    echo "Vehicle Info TimeStamp: ".$entity->getVehicle()->getTimeStamp()."<br>";
    echo "Congestion Level: ".$entity->getVehicle()->getCongestionLevel()."<br>";

  }
  echo "alert: ".$entity->getAlert()."<br>";

  if ($entity->hasTripUpdate()) {
    echo "<br>there is an update<br>";
    echo "Delay: ".$entity->getTripUpdate()->getDelay()."<br>";
    error_log("trip: " .$entity->getId());
    $trip = $entity->getTripUpdate();
    error_log("trip id: " . $trip->getTrip()->getTripId());

  }

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
