<?php
// redis-test-session.php

// 1) Show what PHP’s session handler is
echo "session.save_handler = " . ini_get('session.save_handler') . "\n";
echo "session.save_path    = " . ini_get('session.save_path') . "\n\n";

// 2) Start a session and write a key/value
session_start();
$_SESSION['check'] = 'ok';
echo "Wrote \$_SESSION['check'] = 'ok'\n";
echo "Session ID: " . session_id() . "\n";
