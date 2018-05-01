# Detect Traffic Congestion of Edmonton using GTFS Data
***
Hong Chen & Baihong Qi

## Introduction:
This is a web app dectecting and displaying 1. the congestion condition of Edmonton 2. the disruption condition such as construction or road closure. It uses GTFS real time bus 
data and schedued bus data as well as the bus stop location and trip information provided by the city of Edmonton to check if a bus route is running late at current time point in order to detect the congestion. 

## key features of this website:
* An back-end admistration website which allow the adnminstrator to access, set up and modify the mysql database
* Quickly upload the GTFS data into mysql database
* Automaitially collect the corrent bus information on the road
* Fetching disruption information through Edmonton Open Data Portal API in real time
* Set up differant distance threshold for different congestion level
* Include constrcution map and speed limit to accurate the dectection
* Display map of Edmonton and congestion point and road segment congestion to the map
* Display detailed information at each location about the buses running late, or the information about disrpution upon clicking


## lists of files and explaination
[Index Page](https://github.com/BaihongQi/RealTime-Traffic/blob/master/index.php) is the main page of our 
website, it shows the user interface and allows you to login in as a adminstor. After authrization, you could
see the admistration page which allow you to set up a database, upload the bus route times, stop locations, stops within a trip into the dataset. 

```php
  <a href="/upload.php">
  <input type="button" value="Auto Upload Bus Stop Info + create table" />
  </a>
```
***
[Map Page](https://github.com/BaihongQi/RealTime-Traffic/blob/master/map.php) loaed the google map API on to the main page
and adjust the map view centred on the city of Edmonton and adjust the map size to your broswer.
```php
 var edmonton = {lat: 53.5232, lng: -113.5263};
            map = new google.maps.Map(document.getElementById('map'), {
              zoom: 11,
              center: edmonton,
            });

```

***
[Create tables in the database](https://github.com/BaihongQi/RealTime-Traffic/blob/master/upload.php) will automatically create tables in 
the mysql database using query like
```php
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
```
After these tables are created they can be used repeated and even applied to other data set. As long as the data set are 
follows the GTFS format.

***
[Real time information ingestion](https://github.com/BaihongQi/RealTime-Traffic/blob/master/pb.php) loaded the real time
GTFS data to the back-end and save them into the database. Here we are using [google GTFS php library](https://github.com/google/gtfs-realtime-bindings-php) provided Google to read in GTFS format data.
format 
```php
use transit_realtime\FeedMessage;
```
***
[CSV to Mysql](https://github.com/BaihongQi/RealTime-Traffic/blob/master/csv2sql.php) are used to quickly insert the csv file
file into mysql database in one minute. The script will uploads a csv with 2 Million rows in 60 seconds.
![alt text](https://github.com/BaihongQi/RealTime-Traffic/blob/master/Screen%20Shot%202018-05-01%20at%2012.06.09%20AM.png)


***
[Check Latenss](https://github.com/BaihongQi/RealTime-Traffic/blob/master/check_lateness.php) are usued to automitically fetch the bus ID runing on the road and the longitudes and latitudes of each bus in real time, and look up in the databse for the schduled stop location of the bus. After comparing the two loactionss, it
will return the point of congestion and pass to congestion informaition to the map section, which will be displayed on the google map. Upon clicking on the location, the buses header (e.g. 4 Capilano - Lewis Farm) and the distance from the schedule stop will be shown in a text box.
![alt text](https://github.com/BaihongQi/RealTime-Traffic/blob/master/Screen%20Shot%202018-05-01%20at%2012.30.04%20AM.png)

## Instructions:


