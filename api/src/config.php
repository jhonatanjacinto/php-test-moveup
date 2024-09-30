<?php

/**
 * Database configuration
 */
define("DB_HOST", "mysql");
define("DB_NAME", "db_products");
define("DB_USER", "app_usr_dp");
define("DB_PASS", "app_db_password");
define("DB_PORT", "3306");

/**
 * Imports the required files
 */
require "functions/db.php";
require "functions/request-handler.php";