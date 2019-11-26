import numpy as np
from numpy import pi

#Arrays A-C utilize the np.array() method to be initialized
#Array D utilizes the np.arange() method to be initialized to all values from [3, 21) while incrementing by a value of 2 each time
#Array E and F utilizes the np.linspace() method to be initialized
#Array G is initialized by generating the sine values of each element in Array F


print("Array A:")
a = np.array([2, 3, 4])
print(a)

print("\nArray B:")
print(a.dtype)

b = np.array([[4, 5], [9, 8]], dtype=complex)
print(b)

print("\nArray C:")
c = np.zeros((4, 5))
print(c)

print("\nArray D:")
d = np.arange(3, 21, 2)
print(d)

print("\nArray E:")
e = np.linspace(0, 1, 21)
print(e)

print("\nArray F:")
endBound = 2*pi
f = np.linspace(0, endBound, 13)
g = np.sin(f)
print(g)

f = open("test_text_file.txt", "w+")

for i in range (10):
    f.write("This is line" + str(i))


#r = open(f, "r")

contents = r.read()
print(contents)