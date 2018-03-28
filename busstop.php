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
           echo "Stop ID: ".$data[0] . "<br />";
           echo "Stop Code: ".$data[1] . "<br />";
           echo "Stop Latitude: ".$data[4] . "<br />";
           echo "Stop Longitude: ".$data[5] . "<br />";
       }
       echo "<br />";

    }
    fclose($handle);
}

?>


</body>
</html>
