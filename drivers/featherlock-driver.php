<?php
// Written by Luke Darling.
// All rights reserved.

class FeatherLock {

    private $file;
    private $token;

    function __construct(string $filename) {
        $this->file = realpath($filename);
        $this->token = null;
    }

    function lock() {

        if(!is_null($this->token)) {
            throw new Exception("Lock already acquired.");
        }

        while(true) {
            $result = @file_get_contents("http://127.0.0.1:1647/?action=lock&path=" . urlencode($this->file));
            if($result === false) {
                throw new Exception("Could not connect to FeatherLock daemon.");
            }
            $data = json_decode($result, true);
            if($data["lock-acquired"]) {
                $this->token = $data["token"];
                return;
            }
        }

    }

    function unlock() {

        if(is_null($this->token)) {
            throw new Exception("Lock not acquired, so not released.");
        }

        while(true) {
            $result = @file_get_contents("http://127.0.0.1:1647/?action=unlock&path=" . urlencode($this->file) . "&token=" . $this->token);
            if($result === false) {
                throw new Exception("Could not connect to FeatherLock daemon.");
            }
            $lock = json_decode($result, true);
            if($lock["lock-released"]) {
                $this->token = null;
                return;
            }
        }
    }

}

class FeatherFile {

    private $lock;
    private $file;

    function __construct(string $filename) {
        $this->file = realpath($filename);
        $this->lock = new FeatherLock($this->file);
    }

    function read() {
        $this->lock->lock();
        $result = file_get_contents($this->file);
        $this->lock->unlock();
        return $result;
    }

    function write($data) {
        $this->lock->lock();
        $result = file_put_contents($this->file, $data);
        $this->lock->unlock();
        return $result;
    }

    function readJSON() {
        return json_decode($this->read(), true);
    }

    function writeJSON($data) {
        return $this->write(json_encode($data));
    }

}
