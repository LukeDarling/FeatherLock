#!/usr/bin/env python3
# Written by Luke Darling.
# All rights reserved.

import os, sys

# This demo demonstrates swapping the contents of two files while using FeatherLock to
# protect the files from other FeatherLock-complient concurrent processes

# An instance of the FeatherLock daemon must be running on the server in order for this demo to work

# Include the driver
sys.path.append(os.path.abspath("../drivers/"))
from featherlock_driver import FeatherLock

# Prepare to access a file shared with another application
myFile = "test.txt"
myLock = FeatherLock(myFile)

# Read some data from a file safely
myLock.lock();
with open(myFile, "r") as f: 
    myData = f.read()
myLock.unlock();

# Prepare to access another file shared with another application
anotherFile = "test.json"
anotherLock = FeatherLock(anotherFile)

# Read the original data from another file and replace it with some new data
anotherLock.lock();
with open(anotherFile, "r") as f: 
    moreData = f.read()
# This application can do as many read/write operations as you need it to while it
# has the lock, but other applications are forced to wait until you unlock the file
with open(anotherFile, "w") as f: 
    f.write(myData)
anotherLock.unlock();

# Write some data to the original file safely
myLock.lock();
with open(myFile, "w") as f: 
    f.write(moreData)
myLock.unlock();

print("  My data: " + myData)
print("More data: " + moreData)
