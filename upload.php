<html>
<body>

<?php


$servername = "127.0.0.1";
$username = "root";
$password = '123456';
$dbname = "buses";

/*
try {
    $conn = new PDO("mysql:host=$servername;dbname=buses", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully";
    }
catch(PDOException $e)
    {
    echo "Connection failed: " . $e->getMessage();
    }
*/

$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$sql = "CREATE TABLE Stops(
StopID INT AUTO_INCREMENT PRIMARY KEY,
Latitude float(9,6),
Longitude float(9,6)
)";

if ($conn->query($sql) === TRUE) {
    echo "Table Stops created successfully<br>";
} else {
    echo "Error creating table: " . $conn->error."<br>";
}

$sql2 = "CREATE TABLE Trips (
TripID INT,
ATime TIME,
DTime TIME,
StopID INT,
StopSequence INT,
BusHeader VARCHAR(32),
Pickup INT,
Dropoff INT,
Shape float
)";

if ($conn->query($sql2) === TRUE) {
    echo "Table Trips created successfully<br>";
} else {
    echo "Error creating table: " . $conn->error."<br>";
}





echo "<br>";
$row = 1;

if (($handle = fopen("stops.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        $num = count($data);
        #echo "<p> $num fields in line $row: <br /></p>\n";
        $row++;

        if ($row != 2){
          /*
           echo "Stop ID: ".$data[0] . "<br />";
           echo "Stop Code: ".$data[1] . "<br />";
           echo "Stop Latitude: ".$data[4] . "<br />";
           echo "Stop Longitude: ".$data[5] . "<br />";
           */

           $sql = "INSERT INTO Stops VALUES "."('".$data[0]."', '".$data[4]."', '".$data[5]."');";
           #echo $sql."<br>";

           if ($conn->query($sql) === TRUE) {
               echo "Stop Information inserted successfully<br>";
           } else {
               echo "Error inserting stop info: " . $conn->error."<br>";
           }

       }
       #echo "<br />";

    }
    fclose($handle);
}

#echo $row."<br>";



#echo $row2."<br>";


$conn->close();





?>


</body>
</html>
