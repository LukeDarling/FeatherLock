#!/usr/bin/env python3
# FeatherLock written by Luke Darling.
# All rights reserved.

from bottle import route, run, request
import uuid, json

lock = {}

@route("/")
def root():

    print(lock)

    data = request.query

    if "action" in data:

        if data["action"] == "lock":

            if not "path" in data:
                return json.dumps({"action": "lock", "lock-acquired": False, "message": "No path specified."})

            if "token" in data:
                token = data["token"]
            else:
                while True:
                    token = uuid.uuid1().hex.upper()
                    for p in lock:
                        if lock[p] == token:
                            continue
                    break

            if data["path"] in lock:
                if lock[data["path"]] == token:
                    return json.dumps({"action": "lock", "path": data["path"], "lock-acquired": True, "token": token, "message": "Lock already acquired."})
                else:
                    return json.dumps({"action": "lock", "path": data["path"], "lock-acquired": False, "message": "Lock could not be acquired."})

            lock[data["path"]] = token;
            return json.dumps({"action": "lock", "path": data["path"], "lock-acquired": True, "token": token, "message": "Lock acquired."})

        elif data["action"] == "unlock":
    
            if not "path" in data:
                return json.dumps({"action": "unlock", "lock-released": False, "message": "No path specified."})

            if not "token" in data:
                return json.dumps({"action": "unlock", "path": data["path"], "lock-released": False, "message": "No token specified."})

            if not data["path"] in lock:
                return json.dumps({"action": "unlock", "path": data["path"], "lock-released": False, "message": "Path not locked."})

            if lock[data["path"]] == data["token"]:
                del lock[data["path"]]
                return json.dumps({"action": "unlock", "path": data["path"], "lock-released": True, "message": "Lock released."})

            else:
                return json.dumps({"action": "unlock", "path": data["path"], "lock-released": False, "message": "Incorrect token."})

        elif data["action"] == "status":
        
            if not "path" in data:
                return json.dumps({"action": "status", "message": "No path specified."})

            if data["path"] in lock:
                return json.dumps({"action": "status", "path": data["path"], "lock-engaged": True, "token": lock[data["path"]], "message": "Lock engaged."})

            else:
                return json.dumps({"action": "status", "path": data["path"], "lock-engaged": False, "message": "Lock disengaged."})

        else:
            return json.dumps({"message": "Please specify a valid action."})

    else:
        return json.dumps({"message": "Please specify an action."})

run(host="127.0.0.1", port=1647, server="wsgiref")
