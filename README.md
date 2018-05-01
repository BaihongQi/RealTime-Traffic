# RealTime-Traffic
***
Hong Chen & Baihong Qi

## Introduction:
This is a website dectecting and displaying the congestion condition of Edmonton. It uses GTFS real time bus 
data and schedued bus data to check if a bus route is running late in order to detect the congestion. 

## key features of this website:
* An back-end admistration website which allow the adnminstrator to access, set up and modify the mysql database
* Quickly upload the GTFS data into mysql database
* Automaitially collect the corrent bus information on the road
* Set up differant distance threshold for different congestion level
* Include constrcution map and speed limit to accurate the dectection
* Display map of Edmonton and congestion point and road segment congestion to the map


## lists of files and explaination
[Index Page](https://github.com/BaihongQi/RealTime-Traffic/blob/master/index.php) is the main page of our 
website, it shows the user interface and allows you to login in as a adminstor. After authrization, you could
see the admistration page which allow you to set up a database, upload the bus route times into the dataset. 

```php
  <a href="/upload.php">
  <input type="button" value="Auto Upload Bus Stop Info + create table" />
  </a>
```
[Map Page](https://github.com/BaihongQi/RealTime-Traffic/blob/master/map.php) loaed the google map on to the main page
and adjuest the map view into the
