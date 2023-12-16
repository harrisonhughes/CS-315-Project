<?php
  // Connect to the database, always must be called within a try block. Return PDO connection
  function connect(){
    define('DBHOST', 'localhost');
    define('DBNAME', 'project_tables');
    define('DBUSER', 'root');
    define('DBPASS', 'root');
    define('DBCONNSTRING',"mysql:host=". DBHOST. ";dbname=". DBNAME);
    return new PDO(DBCONNSTRING,DBUSER,DBPASS);
  }

  // Filter input to prevent script injection attacks. Return filtered string
  function test_input($string) {
    $string = trim($string);
    $string = stripslashes($string);
    $string = htmlspecialchars($string);
    return $string;
  }
?>
