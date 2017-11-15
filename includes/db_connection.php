<?php
  // 1. Create a database connection
  $dbhost = "localhost";
  $dbuser = "widget_cms";
  $dbpass = "secret";
  $dbname = "widget_corp";
  define('DB_HOST', 'localhost');
  define('DB_USER', 'widget_cms');
  define('DB_PASS', 'secret');
  define('DB_NAME', 'widget_corp');
  $connection = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
  // Test if connection occurred.
  if(mysqli_connect_errno())
  {
    die("Database connection failed: " . mysqli_connect_error() . " (" . mysqli_connect_errno() . ")");
  }
 ?>
