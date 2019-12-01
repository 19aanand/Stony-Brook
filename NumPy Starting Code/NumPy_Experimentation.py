import numpy as np
from numpy import pi
from numpy import poly1d
import scipy as optimize


#Arrays a-c utilize the np.array() method to be initialized
#Array d utilizes the np.arange() method to be initialized to all values from [3, 21) while incrementing by a value of 2 each time
#Array e and f utilizes the np.linspace() method to be initialized
    #Note that np.linspace(a, b, c) returns an array of values that lie between the start bound a and the end bound b. 
    #These values increment by a set amount c
#Array g is initialized by generating the sine values of each element in Array f
#Array h is initialized by utilizing the np.r_() method provided by numpy


print("Array A:")
a = np.array([2, 3, 4])
print(a.dtype)
print(a)


print("\nArray B:")

b = np.array([[4, 5], [9, 8]], dtype=complex)
print(b.dtype)
print(b)

print("\nArray C:")
c = np.zeros((4, 5))
print(c.dtype)
print(c)

print("\nArray D:")
d = np.arange(3, 21, 2)
print(d.dtype)
print(d)

print("\nArray E:")
e = np.linspace(0, 1, 21)
print(e.dtype)
print(e)

print("\nArray G:")
endBound = 2*pi
f = np.linspace(0, endBound, 13)
g = np.sin(f)
h = np.sin(f)
print(g.dtype)
print(g)


print(np.array_equal(h, g))

##############################################################################################################
print("\nArray H:")
h = np.r_[3,[0]*5,-1:1:21j]
print(h.dtype)
print(h)

##############################################################################################################
print("\nArray I:")
i = np.mgrid[0:2, 0:2, 0:2]
print(i)


##############################################################################################################
p = poly1d([1, 2, 1])
print("\n\n" + str(p))
print(str(p.integ()))