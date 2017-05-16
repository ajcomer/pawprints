<?php
// use CONSTANTS for db connection inputs below, not just variables. It's a better practice
// since these values won't change.
	define("DB_SERVER", "localhost");
	define("DB_USER", "queenBee");
	define("DB_PASS", "root");
	define("DB_NAME", "the_honey_pot");

  // 1. Create a database connection
  $connection = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
  // Test if connection succeeded
  if(mysqli_connect_errno()) {
    die("Database connection failed: " . // Die is a fatal error if there is an error in the connection
         mysqli_connect_error() . 
         " (" . mysqli_connect_errno() . ")"
    );
  }
?>
