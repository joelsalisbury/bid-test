<?php

// sqlite3 connection to db.sqlite
$pdo = new PDO('sqlite:db.sqlite');
// create table if not exists
$pdo->exec('CREATE TABLE IF NOT EXISTS bids (id INTEGER PRIMARY KEY, name TEXT, bid INTEGER, created_at DATETIME DEFAULT CURRENT_TIMESTAMP)');
// insert a sample bid for user joel of $200
$pdo->exec('INSERT INTO bids (name, bid) VALUES ("joel", 200)');

echo "seeded db.sqlite with a bid for joel of $200\n";