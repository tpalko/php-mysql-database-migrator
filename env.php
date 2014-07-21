<?php

$db_username = "user";
$db_password = "y00z3r";
$db_host = "localhost";
$db_name = "appdb";

$connections = array('development' => 'mysql://' . $db_username . ':' . $db_password . '@' . $db_host . '/' . $db_name);
$connection_string = " -u " . $db_username . " -p" . $db_password . " -h " . $db_host . " " . $db_name;

$envkey = "development";

// -- this drives the scope of what migrations will be run on a fresh deploy, where the latest
// -- schema may not be present, but there's no migration history - this will tell the migrator from where to start
$last_migration = "201406192101";
