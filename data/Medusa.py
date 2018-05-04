import subprocess as sp
import shlex


# TODO:  grab a frame from the webserver
# Things to get:
# Frame value
# Map location
# Camera direction

# TODO:  run darknet on it -> this outputs the number of classes that have been

# Begine the hackathon code: these are the commnads needed to run darknet from
#python
args = './darknet detect cfg/yolov3.cfg yolov3.weights data/dog.jpg'
cwd ='/mnt/c/Users/Peter/source/repos/YOLOv2/darknet'
result_bytes = sp.check_output([shlex.split(args)], cwd = cwd)
#this should run the darknet program
results_string = result_bytes.decode('utf-8')
parsed_string = results_string.split('\n')
#this outputs an array
#The amount of classes that are outputed is len(parsed_string)-2
for i in parsed_string:
    if parsed_string[i] = 'car':
        car_counter += 1
    else
    return

print("The number of cars detected in the image is" + string(car_counter))
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




# TODO: now you can color the map
# green = low traffic
# yellow = medium traffic
# Red = high traffic
# TODO: Figure out how to post to the map.
