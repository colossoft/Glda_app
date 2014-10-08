<?php

/**
 * Database Configuration
 */

// LOCAL
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_HOST', 'localhost');
define('DB_NAME', 'gilda');
ini_set('error_log','/error.log');

// SERVER
/*define('DB_USERNAME', 'u472938757_atyin');
define('DB_PASSWORD', '');
define('DB_HOST', 'mysql.hostinger.hu');
define('DB_NAME', 'u472938757_1');*/

define('USER_CREATED_SUCCESSFULLY', 0);
define('USER_CREATE_FAILED', 1);
define('USER_ALREADY_EXISTED', 2);

define('RESERVATION_CREATED_SUCCESSFULLY', 3);
define('RESERVATION_CREATE_FAILED', 4);
define('NO_FREE_SPOTS', 5);
define('RESERVATION_CREATE_DEADLINE_EXPIRED', 6);

define('RESERVATION_DELETED_SUCCESSFULLY', 7);
define('RESERVATION_DELETE_FAILED', 8);
define('RESERVATION_DELETE_DEADLINE_EXPIRED', 9);

?>