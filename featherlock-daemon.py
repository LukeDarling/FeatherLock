#!/usr/bin/env python3
# FeatherLock written by Luke Darling.
# All rights reserved.

from flask import Flask, request, jsonify
import uuid

app = Flask(__name__, threaded=False)
lock = {}

@app.route("/", methods=["GET"])
def root():

    data = request.args

    if "action" in data:

        if data["action"] == "lock":

            if not "path" in data:
                return jsonify({"action": "lock", "lock-acquired": False, "message": "No path specified."})

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
                    return jsonify({"action": "lock", "path": data["path"], "lock-acquired": True, "token": token, "message": "Lock already acquired."})
                else:
                    return jsonify({"action": "lock", "path": data["path"], "lock-acquired": False, "message": "Lock could not be acquired."})

            lock[data["path"]] = token;
            return jsonify({"action": "lock", "path": data["path"], "lock-acquired": True, "token": token, "message": "Lock acquired."})

        elif data["action"] == "unlock":
    
            if not "path" in data:
                return jsonify({"action": "unlock", "lock-released": False, "message": "No path specified."})

            if not "token" in data:
                return jsonify({"action": "unlock", "path": data["path"], "lock-released": False, "message": "No token specified."})

            if not data["path"] in lock:
                return jsonify({"action": "unlock", "path": data["path"], "lock-released": False, "message": "Path not locked."})

            if lock[data["path"]] == data["token"]:
                del lock[data["path"]]
                return jsonify({"action": "unlock", "path": data["path"], "lock-released": True, "message": "Lock released."})

        elif data["action"] == "status":
        
            if not "path" in data:
                return jsonify({"action": "status", "message": "No path specified."})

            if data["path"] in lock:
                return jsonify({"action": "status", "path": data["path"], "lock-engaged": True, "token": lock[data["path"]], "message": "Lock engaged."})

            else:
                return jsonify({"action": "status", "path": data["path"], "lock-engaged": False, "message": "Lock disengaged."})

app.run("127.0.0.1", 1647)
