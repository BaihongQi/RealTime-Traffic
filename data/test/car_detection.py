import subprocess as sp
import shlex
import csv
import datetime
import io

#args = './darknet detect cfg/yolov3.cfg yolov3.weights data/capilano_test.PNG -thresh 0.1'
# args = './darknet detect cfg/yolov3.cfg yolov3.weights data/Accident.JPG -thresh 0.1'
 args = './darknet detect cfg/yolov3.cfg yolov3.weights data/cars.jpg   -thresh 0.1'
cwd ='/mnt/c/Users/Peter/source/repos/YOLOv2/darknet'
result_bytes = sp.check_output(shlex.split(args), cwd = cwd)
#this runs the darknet program:
results_string = result_bytes.decode('utf-8')
parsed_string = results_string.split('\n')
#this outputs an array
#The amount of classes that are outputed is len(parsed_string)-2
#for i in parsed_string:
#    if parsed_string[i] = 'car':
#        car_counter += 1
#    else:
#        break
# apparently this will do the same thing:
#car_counter = parsed_string.count().find()
# we need define some dont care values in the
car_counter = 0
for x in enumerate(parsed_string):
    car_counter += 1

print("The nubmer of detected cars is "+str(car_counter))
# Test level 1
if car_counter <= 10:
    traffic_val = "Low"
    color = "Green"
elif car_counter <= 20:
    traffic_val = "Medium"
    color = "Yellow"
elif car_counter <=30:
    traffic_val = "High"
    color = "Red"
else:
    traffic_val = "Accident"
    color = "Red"

if traffic_val == "Accident":
    print("There is a possible " + traffic_val)
else:
    print("The traffic volume is "+traffic_val)

time = datetime.datetime.now().strftime("%I:%M%p on %B %d, %Y")
with open('test.csv', 'a', encoding='utf-8', newline='') as csvfile:
    traffic_writer = csv.writer(csvfile,delimiter=' ',
    quotechar='|', quoting=csv.QUOTE_MINIMAL)
    traffic_writer.writerow([time, str(car_counter), str(traffic_val)])
    csvfile.close()
