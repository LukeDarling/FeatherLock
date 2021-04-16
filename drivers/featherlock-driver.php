<?php
// Written by Luke Darling.
// All rights reserved.

class FeatherLock {

    private $path;
    private $token;

    function __construct(string $filename) {
        global $path, $token;
        $path = strtolower($filename);
        $token = null;
    }

    function lock() {

        global $path, $token;

        if(!is_null($token)) {
            throw new Exception("Lock already acquired.");
        }

        while(true) {
            $result = @file_get_contents("http://127.0.0.1:1647/?action=lock&path=" . urlencode($path));
            if($result === false) {
                throw new Exception("Could not connect to FeatherLock daemon.");
            }
            $data = json_decode($result, true);
            if($data["lock-acquired"]) {
                $token = $data["token"];
                return;
            }
        }

    }

    function unlock() {

        global $path, $token;

        $failures = 0;

        if(is_null($token)) {
            throw new Exception("Lock not acquired, so not released.");
        }

        while(true) {
            $result = @file_get_contents("http://127.0.0.1:1647/?action=unlock&path=" . urlencode($path) . "&token=" . $token);
            if($result === false) {
                $failures++;
                if($failures >= 3) {
                    throw new Exception("Could not connect to FeatherLock daemon.");
                }
            }
            $lock = json_decode($result, true);
            if($lock["lock-released"]) {
                $token = null;
                return;
            }
        }
    }

}

class FeatherFile {

    private $lock;
    private $file;

    function __construct(string $filename) {
        global $lock, $file;
        $file = $filename;
        $lock = new FeatherLock($filename);
    }

    function read() {
        global $lock, $file;
        $lock->lock();
        $result = file_get_contents($file);
        $lock->unlock();
        return $result;
    }

    function write(string $data) {
        global $lock, $file;
        $lock->lock();
        $result = file_put_contents($file, $data);
        $lock->unlock();
        return $result;
    }

    function readJSON() {
        return json_decode($this->read(), true);
    }

    function writeJSON($data) {
        return $this->write(json_encode($data));
    }

}
