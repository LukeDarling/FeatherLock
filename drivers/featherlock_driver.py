#!/usr/bin/env python3
# Written by Luke Darling.
# All rights reserved.

import os, requests, json, urllib.parse


class FeatherLock:

    def __init__(self, filename: str) -> None:
        self.file = os.path.abspath(filename)
        self.token = None

    def lock(self) -> None:
    
        if not self.token == None:
            raise Exception("Lock already acquired.")
        
        while True:
            try:
                result = requests.get("http://127.0.0.1:1647/?action=lock&path=" + urllib.parse.quote(self.file)).json()
                if result["lock-acquired"]:
                    self.token = result["token"]
                    return
            except:
                raise Exception("Could not connect to FeatherLock daemon.")

    def unlock(self) -> None:
        
        if self.token == None:
            raise Exception("Lock not acquired, so not released.")

        while True:
            try:
                result = requests.get("http://127.0.0.1:1647/?action=unlock&path=" + urllib.parse.quote(self.file) + "&token=" + self.token).json()
                if result["lock-released"]:
                    self.token = None
                    return
            except:
                raise Exception("Could not connect to FeatherLock daemon.")
                
class FeatherFile:

    def __init__(self, filename: str):
        self.file = os.path.abspath(filename)
        self.lock = FeatherLock(self.file);

    def read(self) -> str:
        self.lock.lock()
        with open(self.file, "r") as f:
            result = f.read()
        self.lock.unlock()
        return result
        
    def write(self, data: str) -> None:
        self.lock.lock()
        with open(self.file, "w") as f:
            f.write(data)
        self.lock.unlock()

    def readJSON(self) -> str:
        return json.loads(self.read())

    def writeJSON(self, data: str) -> None:
        self.write(json.dumps(data))
