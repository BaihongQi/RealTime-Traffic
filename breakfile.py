i = 0

file = open("stop_times9.csv","w") 

with open("stop_times.csv") as f:
    for line in f:
        i = i + 1
        if 1600000 < i <= 1800000:
            file.write(line)
        