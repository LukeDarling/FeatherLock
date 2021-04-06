## FeatherLock

### API

Request: `GET` `127.0.0.1:1647?action=lock&path=%2Fvar%2Fwww%2Fhtml%2Findex.html&token=ABC123`

Responses:

* `{"action": "lock", "path": "/var/www/html/index.html", "token": "ABC123", "lock-acquired": true, "message": "Lock successfully acquired."}`

* `{"action": "lock", "path": "/var/www/html/index.html", "token": "ABC123", "lock-acquired": false, "message": "Lock could not be acquired."}`

---

Request: `GET` `127.0.0.1:1647?action=unlock&path=%2Fvar%2Fwww%2Fhtml%2Findex.html&token=ABC123`

Responses:

* `{"action": "lock", "path": "/var/www/html/index.html", "token": "ABC123", "lock-released": true, "message": "Lock successfully released."}`

* `{"action": "lock", "path": "/var/www/html/index.html", "token": "ABC123", "lock-released": false, "message": "Lock was not acquired, so not released."}`

---

Request: `GET` `127.0.0.1:1647?action=status&path=%2Fvar%2Fwww%2Fhtml%2Findex.html`

Responses:

* `{"action": "status", "path": "/var/www/html/index.html", "token": "ABC123", "lock-engaged": true, "message": "Lock is currently engaged."}`

* `{"action": "status", "path": "/var/www/html/index.html", "lock-engaged": false, "message": "Lock is currently disengaged."}`