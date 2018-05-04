import subprocess as sp
import shlex


# TODO:  grab a frame from the webserver
# Things to get:
# Frame value
# Map location
# Camera direction

# TODO:  run darknet on it -> this outputs the number of classes that have been
cmd = ['awk', 'length($0) > 5']
# if passing a single string, either shell must be true or else the string
# must simply name the program to be extecuted without any arguments.
input = 'foo\nfoofoo\n'.encode('utf-8')
# This actually runs the command in the command line

result = sp.run(cmd, stdout=sp.PIPE, input=input)
# decode the output
result.stdout.decode('utf-8')

# Begine the hackathon code. 
args = './darknet detect cfg/yolov3.cfg yolov3.weights data/dog.jpg'
cwd ='/mnt/c/Users/Peter/source/repos/YOLOv2/darknet'
result_bytes = sp.check_output([shlex.split(args)], cwd = cwd)
#this should run the darknet program
results_string = result_bytes.decode('utf-8')
parsed_string = results_string.split('\n')
#this outputs an array
#The amount of classes that are outputed is len(parsed_string)-2




# TODO:  may need to parse the output based on the ouput of the subprocess
##
##
# TODO: count the number of classes
counted_values = parser.parse

num_classes = counted_values
# TODO: based on the number of classes:
# low traffic = 3 cars detected
# Medium traffic = 6 cars
# high traffic = more than 6 :P


if counted_values <= 3:
    traffic_val = "Low"
    color = "Green"
elif counted_values <= 6:
    traffic_val = "Medium"
    color = "Yellow"
else:
    traffic_val = "High"
    color = "Red"


# TODO: now you can color the map
# green = low traffic
# yellow = medium traffic
# Red = high traffic
# TODO: Figure out how to post to the map.
