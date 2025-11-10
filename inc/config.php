<?php
// inc/config.php
session_start();

define('DB_HOST','localhost');
define('DB_NAME','pisco_ecommerce');
define('DB_USER','root');
define('DB_PASS',''); // change!
define('BASE_URL','/'); // si dans un sous-dossier adapte

// erreurs en dev
ini_set('display_errors', 1);
error_reporting(E_ALL);
