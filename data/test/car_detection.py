import subprocess as sp
import shlex

args = './darknet detect cfg/yolov3.cfg yolov3.weights data/Accident.JPG -thresh 0.1'
cwd ='/mnt/c/Users/Peter/source/repos/YOLOv2/darknet'
result_bytes = sp.check_output(shlex.split(args), cwd = cwd)
#this should run the darknet program
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
car_counter = parsed_string.count('car: %_dontcare_dontcare')
# we need define some dont care values in the

print(car_counter)
# Test level 1
if car_counter <= 3:
    traffic_val = "Low"
    color = "Green"
elif car_counter <= 8:
    traffic_val = "Medium"
    color = "Yellow"
else:
    traffic_val = "High"
    color = "Red"

print("The traffic volume is "+traffic_val)
