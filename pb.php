<?php
require_once 'vendor/autoload.php';

use transit_realtime\FeedMessage;

$data = file_get_contents(dirname(__FILE__) . "/VehiclePositions.pb");
$feed = new FeedMessage();
$feed->parse($data);
foreach ($feed->getEntityList() as $entity) {
  if ($entity->hasTripUpdate()) {
    error_log("trip: " .$entity->getId());
  }
}
?>
