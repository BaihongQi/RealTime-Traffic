i = 0

file = open("stop_times1.csv","w") 

f=open('stop_times.csv')
lines=f.readlines()

for i in range(0, 100000):
    file.write(lines[i])

print(len(lines))