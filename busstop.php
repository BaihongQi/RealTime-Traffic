<html>
<body>



<?php

$row = 1;

if (($handle = fopen("stops.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $num = count($data);
        #echo "<p> $num fields in line $row: <br /></p>\n";
        $row++;

        if ($row != 1){
           #echo "Stop ID: ".$data[0] . "<br />";
           #echo "Stop Code: ".$data[1] . "<br />";
           #echo "Stop Latitude: ".$data[4] . "<br />";
           #echo "Stop Longitude: ".$data[5] . "<br />";
       }
       #echo "<br />";

    }
    fclose($handle);
}

echo $row."<br>";





$row2 = 1;

if (($handle = fopen("stop_times.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $num = count($data);
        #echo "<p> $num fields in line $row: <br /></p>\n";
        $row2++;

        if ($row2 != 1){
           #echo "Vehicle Trip ID: ".$data[0] . "<br />";
           #echo "Arrival/Departure Time: ".$data[1] . "<br />";
           #echo "Stop ID: ".$data[3] . "<br />";
           #echo "Stop Sequence: ".$data[4] . "<br />";
       }
       #echo "<br />";

    }
    fclose($handle);
}

echo $row2;




?>


</body>
</html>
