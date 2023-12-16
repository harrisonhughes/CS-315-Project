<?php
  include_once 'functions.php';
  session_start();

  try {
    $pdo = connect(); //'functions.php' connect() to access database
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      // Filter the user text to prevent script injection
      $stadium = test_input($_POST['basketball']);
      $names = test_input($_POST['names']);
      $conference = test_input($_POST['conference']);
      $month = test_input($_POST['month']);
      $school = test_input($_POST['extra']);
      $mascot = test_input($_POST['spike']);
      $validForm = true;

      // Initialize elements for text variables, error IDs, error messages
      $textArr = [$stadium, $names, $conference, $month, $school, $mascot];
      $idArr = ["bballErr", "namesErr", "confErr", "monthErr", "extraErr", "mascErr"];
      $errArr = ["Must select a stadium name", "Must enter a value", "Conference cannot be empty", "Must select a month", "Must select an option", 
                "Mascot cannot be empty"];

      // Loop through each array to assert a nonempty entry. Assign error messages to respective session variables
      for($i = 0; $i < count($textArr); $i++){
        if(!$textArr[$i]){
          $validForm = false;
          $_SESSION[$idArr[$i]] = $errArr[$i];
        }
      }

      // Assert text input types are of a correct character type. Assign error message otherwise
      if(!isset($_SESSION['namesErr']) && $names <= 0){
        $validForm = false;
        $_SESSION['namesErr'] = "Value must be a positive integer";
      }
      if(!isset($_SESSION['confErr']) && !preg_match('/^[a-zA-Z\s]+$/', $conference)){
        $validForm = false;
        $_SESSION['confErr'] = "Conference contains only alphabet and space characters"; 
      }
      if(!isset($_SESSION['mascErr']) && !preg_match('/^[a-zA-Z\s]+$/', $mascot)){
        $validForm = false;
        $_SESSION['mascErr'] = "Mascot contains only alphabet and space characters"; 
      }

      // Obtain all checkbox values that are active
      if(isset($_POST['locate'])){
        foreach($_POST['locate'] as $direction){
          $directions[] = test_input($direction);
        }
      }
      // Otherwise, no boxes were checked and the element is invalid
      else{
        $validForm = false;
        $_SESSION['locateErr'] = "Must select at least one option"; 
      }

      // If form is error free
      if($validForm){
        // Normalize month and directions reults, grab username to include in insertion query
        $username = test_input($_SESSION['user']);
        $monthObj = DateTime::createFromFormat('m', substr($month, 5));
        $month = $monthObj->format('F');
        $directions = implode('/', $directions);

        // Create value array and '?' placeholder string for query safety
        $sqlValues = [$username, $stadium, $names, $conference, $month, $school, $directions, $mascot];
        $valuePlaceholders = rtrim(str_repeat('?,', 8), ',');

        // Prepare and execute insertion query to add trivia answers to database
        $sql = "INSERT INTO trivia (username, stadium, num_names, conference, month, school, directions, mascot) VALUES ({$valuePlaceholders})";
        $result = $pdo->prepare($sql);
        $result->execute($sqlValues);

        // Save response message
        $_SESSION['trivMess'] = "Your trivia sheet has been submitted for scoring";
      }
    }
    header("Location: trivia.html");
  }
  // Display database connection issues
  catch (PDOException $e){
    die( $e->getMessage());
  }
  $pdo = null;
?>