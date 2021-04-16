<?php

// Include the driver
require_once("../drivers/featherlock-driver.php");

// Prepare to access a file shared with another application
$myFile = "test.txt";
$myLock = new Lock($myFile);

// Read some data from a file safely
$myLock->lock();
$myData = file_get_contents($myFile);
$myLock->unlock();

// Prepare to access another file shared with another application
$anotherFile = "test.json";
$anotherLock = new Lock($anotherFile);

// Read the original data from another file and replace it with some new data
$anotherLock->lock();
$moreData = json_decode(file_get_contents($anotherFile), true);
// This application can do as many read/write operations as you need it to while it
// has the lock, but other applications are forced to wait until you unlock the file
file_put_contents($anotherFile, json_encode($myData));
$anotherLock->unlock();

// Write some data to the original file safely
$myLock->lock();
file_put_contents($myFile, $moreData);
$myLock->unlock();