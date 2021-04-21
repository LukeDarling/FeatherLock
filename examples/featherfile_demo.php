<?php
// Written by Luke Darling.
// All rights reserved.

// This demo demonstrates swapping the contents of two files while using the FeatherFile wrapper to
// protect the files from other FeatherLock-compliant concurrent processes

// An instance of the FeatherLock daemon must be running on the server in order for this demo to work

// Set the content-type header
header("Content-type: text/plain; charset=UTF-8");

// Include the driver
require_once("../drivers/featherlock_driver.php");

// Prepare to access a file shared with another application
$myFile = new FeatherFile("test.txt");

// Read some data from a file safely
$myData = $myFile->read();

// Prepare to access another file shared with another application
$anotherFile = new FeatherFile("test.json");

// Read the original data from another file and replace it with some new data
$moreData = $anotherFile->read();
$anotherFile->write($myData);

// Write some data to the original file safely
$myFile->write($moreData);

print "  My data: " . $myData . "\n";
print "More data: " . $moreData;
