<?php
  include_once 'functions.php';
  session_start();
  
  try {
    $pdo = connect(); //'functions.php' connect() to access database
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      // Filter the user text to prevent script injection
      $name = test_input($_POST['name']);
      $state = test_input($_POST['state']);
      $city = test_input($_POST['city']);
      $school = test_input($_POST['school']);
      $major = test_input($_POST['major']);
      $range = test_input($_POST['rate']);
      $validForm = true;

      // Initialize elements for text variables, error IDs, error message "titles"
      $textArr = [$name, $state, $city, $school, $major];
      $idArr = ["nameErr", "homeErr", "liveErr", "schoolErr", "majorErr"];
      $errArr = ["Name cannot be empty", "State cannot be empty", "City cannot be empty", "Must select an option",  "Major cannot be empty"];

      // Loop through each array to assert a nonempty entry. Assign error messages to respective session variables
      for($i = 0; $i < count($textArr); $i++){
        if(!$textArr[$i]){
          $validForm = false;
          $_SESSION[$idArr[$i]] = $errArr[$i];
        }
      }

      // Assert text input types are of a correct character type. Assign error message otherwise
      if(!isset($_SESSION['nameErr']) && !preg_match('/^[a-zA-Z\s.\'-]+$/', $name)){
        $validForm = false;
        $_SESSION['nameErr'] = "Invalid name format"; 
      }
      if(!isset($_SESSION['liveErr']) && !preg_match('/^[a-zA-Z\s.\'-]+$/', $city)){
        $validForm = false;
        $_SESSION['liveErr'] = "Invalid city format"; 
      }
      if(!isset($_SESSION['majorErr']) && !preg_match('/^[a-zA-Z\s]+$/', $major)){
        $validForm = false;
        $_SESSION['majorErr'] = "Major must include only alphabet and space characters"; 
      }
      
      // If form is error free
      if($validForm){
        // Create value array and '?' placeholder string for query safety
        $sqlValues = [$name, $state, $city, $school, $major, $range];
        $valuePlaceholders = rtrim(str_repeat('?,', 6), ',');

        // Prepare and execute insertion query to add application information to database
        $sql = "INSERT INTO applications (name, state, city, school, major, excitement) VALUES ({$valuePlaceholders})";
        $result = $pdo->prepare($sql);
        $result->execute($sqlValues);
        
        // Save response message
        $_SESSION['applyMess'] = "Your application has been submitted";
      }
    }
    header("Location: academics.html");
  }
  // Display database connection issues
  catch (PDOException $e){
    die( $e->getMessage());
  }
  $pdo = null;
?>