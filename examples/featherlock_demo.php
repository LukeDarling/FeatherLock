<?php
// Written by Luke Darling.
// All rights reserved.

// This demo demonstrates swapping the contents of two files while using FeatherLock to
// protect the files from other FeatherLock-complient concurrent processes

// An instance of the FeatherLock daemon must be running on the server in order for this demo to work

// Set the content-type header
header("Content-type: text/plain; charset=UTF-8");

// Include the driver
require_once("../drivers/featherlock_driver.php");

// Prepare to access a file shared with another application
$myFile = "test.txt";
$myLock = new FeatherLock($myFile);

// Read some data from a file safely
$myLock->lock();
$myData = file_get_contents($myFile);
$myLock->unlock();

// Prepare to access another file shared with another application
$anotherFile = "test.json";
$anotherLock = new FeatherLock($anotherFile);

// Read the original data from another file and replace it with some new data
$anotherLock->lock();
$moreData = file_get_contents($anotherFile);
// This application can do as many read/write operations as you need it to while it
// has the lock, but other applications are forced to wait until you unlock the file
file_put_contents($anotherFile, $myData);
$anotherLock->unlock();

// Write some data to the original file safely
$myLock->lock();
file_put_contents($myFile, $moreData);
$myLock->unlock();

print "  My data: " . $myData . "\n";
print "More data: " . $moreData;
