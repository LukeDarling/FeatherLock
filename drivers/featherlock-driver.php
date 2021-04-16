<?php

class Lock {

    public string $path;
    public string $token;

    function __construct(string $file) {
        global $path, $token;
        $path = strtolower($file);
        $token = null;
    }

    function lock() {

        global $path, $token;

        $failures = 0;

        if(!is_null($token)) {
            throw new Exception("Lock already acquired.");
        }

        while(true) {
            $result = file_get_contents("http://127.0.0.1:1647/?action=lock&path=" . urlencode($path));
            if($result === false) {
                $failures++;
                if($failures >= 3) {
                    throw new Exception("Could not connect to FeatherLock daemon.");
                }
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
            $result = file_get_contents("http://127.0.0.1:1647/?action=unlock&path=" . urlencode($path) . "&token=" . $token);
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
